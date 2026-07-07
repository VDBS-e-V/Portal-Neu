<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$stamp = 'mini-project-15-' . date('Ymd-His');

function project_path(string $relative): string
{
    global $root;
    return $root . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relative);
}

function ensure_dir(string $dir): void
{
    if (!is_dir($dir) && !mkdir($dir, 0777, true) && !is_dir($dir)) {
        throw new RuntimeException('Verzeichnis konnte nicht erstellt werden: ' . $dir);
    }
}

function backup_file(string $file, string $stamp): void
{
    if (!is_file($file)) {
        return;
    }
    $backup = $file . '.bak-' . $stamp;
    if (!copy($file, $backup)) {
        throw new RuntimeException('Backup fehlgeschlagen: ' . $file);
    }
    echo 'Backup: ' . $backup . PHP_EOL;
}

function write_project_file(string $relative, string $content, string $stamp): void
{
    $file = project_path($relative);
    ensure_dir(dirname($file));
    backup_file($file, $stamp);
    if (file_put_contents($file, $content) === false) {
        throw new RuntimeException('Datei konnte nicht geschrieben werden: ' . $file);
    }
    echo 'Aktualisiert: ' . $relative . PHP_EOL;
}

write_project_file('database/seeds/seed_mini_project_15_person_subject_backfill.sql', <<<'SQL'
SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;
DROP TEMPORARY TABLE IF EXISTS tmp_mp15_person_subject_backfill;
CREATE TEMPORARY TABLE tmp_mp15_person_subject_backfill (
    person_id BIGINT UNSIGNED NOT NULL PRIMARY KEY,
    subject_uuid CHAR(36) NOT NULL,
    KEY idx_tmp_mp15_subject_uuid (subject_uuid)
) ENGINE=Memory;
INSERT INTO tmp_mp15_person_subject_backfill (person_id, subject_uuid)
SELECT p.id, UUID()
FROM ids_persons p
WHERE p.subject_id IS NULL;
INSERT INTO ids_subjects (uuid, status, permission_version, created_at, updated_at)
SELECT t.subject_uuid, 'active', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
FROM tmp_mp15_person_subject_backfill t
LEFT JOIN ids_subjects s ON s.uuid = t.subject_uuid
WHERE s.id IS NULL;
UPDATE ids_persons p
INNER JOIN tmp_mp15_person_subject_backfill t ON t.person_id = p.id
INNER JOIN ids_subjects s ON s.uuid = t.subject_uuid
SET p.subject_id = s.id
WHERE p.subject_id IS NULL;
DROP TEMPORARY TABLE IF EXISTS tmp_mp15_person_subject_backfill;
SQL, $stamp);

write_project_file('database/sql/verify_mini_project_15_personen_identity_guard.sql', <<<'SQL'
SELECT 'persons_without_subject' AS check_name, COUNT(*) AS count_value
FROM ids_persons p
LEFT JOIN ids_subjects s ON s.id = p.subject_id
WHERE p.subject_id IS NULL OR s.id IS NULL;
SELECT 'users_with_missing_person' AS check_name, COUNT(*) AS count_value
FROM ids_users u
LEFT JOIN ids_persons p ON p.id = u.person_id
WHERE u.person_id IS NOT NULL AND p.id IS NULL;
SELECT 'subject_groups_with_missing_refs' AS check_name, COUNT(*) AS count_value
FROM ids_subject_groups sg
LEFT JOIN ids_subjects s ON s.id = sg.subject_id
LEFT JOIN ids_groups g ON g.id = sg.group_id
WHERE s.id IS NULL OR g.id IS NULL;
SELECT 'legacy_identity_group_tables' AS check_name, COUNT(*) AS count_value
FROM information_schema.tables
WHERE table_schema = DATABASE()
  AND table_name IN ('ids_permission_groups', 'ids_user_permission_groups', 'ids_person_permission_groups');
SQL, $stamp);

write_project_file('tools/qa/check_person_subject_integrity.php', <<<'PHPFILE'
<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);

function mp15_load_env(string $root): array
{
    $env = [];
    $file = $root . DIRECTORY_SEPARATOR . '.env';
    if (!is_file($file)) {
        return $env;
    }
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return $env;
    }
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        if ((str_starts_with($value, '"') && str_ends_with($value, '"')) || (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
            $value = substr($value, 1, -1);
        }
        $env[$key] = $value;
    }
    return $env;
}

function mp15_pdo(string $root): PDO
{
    $env = mp15_load_env($root);
    $host = $env['DB_HOST'] ?? '127.0.0.1';
    $port = $env['DB_PORT'] ?? '3306';
    $db = $env['DB_NAME'] ?? $env['DB_DATABASE'] ?? 'vdbs_sys';
    $user = $env['DB_USER'] ?? $env['DB_USERNAME'] ?? 'root';
    $pass = $env['DB_PASS'] ?? $env['DB_PASSWORD'] ?? '';
    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $db);

    try {
        return new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    } catch (PDOException $e) {
        if ($host !== 'localhost') {
            $dsn = sprintf('mysql:host=localhost;port=%s;dbname=%s;charset=utf8mb4', $port, $db);
            return new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        }
        throw $e;
    }
}

function mp15_scalar(PDO $pdo, string $sql, array $params = []): int
{
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return (int) $stmt->fetchColumn();
}

function mp15_table_exists(PDO $pdo, string $table): bool
{
    return mp15_scalar($pdo, 'SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = :table', ['table' => $table]) > 0;
}

function mp15_column_exists(PDO $pdo, string $table, string $column): bool
{
    return mp15_scalar($pdo, 'SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = :table AND column_name = :column', ['table' => $table, 'column' => $column]) > 0;
}

$pdo = mp15_pdo($root);
$errors = [];

foreach (['ids_subjects', 'ids_persons', 'ids_users', 'ids_groups', 'ids_subject_groups', 'ids_permissions', 'ids_group_permissions'] as $table) {
    if (!mp15_table_exists($pdo, $table)) {
        $errors[] = 'Pflicht-Tabelle fehlt: ' . $table;
    }
}

foreach ([
    ['ids_persons', 'subject_id'],
    ['ids_users', 'person_id'],
    ['ids_subject_groups', 'subject_id'],
    ['ids_subject_groups', 'group_id'],
    ['ids_groups', 'system_id'],
    ['ids_permissions', 'system_id'],
] as [$table, $column]) {
    if (!mp15_column_exists($pdo, $table, $column)) {
        $errors[] = 'Pflicht-Spalte fehlt: ' . $table . '.' . $column;
    }
}

foreach (['ids_permission_groups', 'ids_user_permission_groups', 'ids_person_permission_groups'] as $table) {
    if (mp15_table_exists($pdo, $table)) {
        $errors[] = 'Legacy-Identity-Gruppentabelle existiert noch: ' . $table;
    }
}

if ($errors === []) {
    $personsWithoutSubject = mp15_scalar($pdo, 'SELECT COUNT(*) FROM ids_persons p LEFT JOIN ids_subjects s ON s.id = p.subject_id WHERE p.subject_id IS NULL OR s.id IS NULL');
    if ($personsWithoutSubject > 0) {
        $errors[] = 'Personen ohne gültiges Subject: ' . $personsWithoutSubject . ' (php bin\\console seed ausführen, danach erneut prüfen)';
    }

    $usersWithMissingPerson = mp15_scalar($pdo, 'SELECT COUNT(*) FROM ids_users u LEFT JOIN ids_persons p ON p.id = u.person_id WHERE u.person_id IS NOT NULL AND p.id IS NULL');
    if ($usersWithMissingPerson > 0) {
        $errors[] = 'User mit fehlender Person-Referenz: ' . $usersWithMissingPerson;
    }

    $subjectGroupRefs = mp15_scalar($pdo, 'SELECT COUNT(*) FROM ids_subject_groups sg LEFT JOIN ids_subjects s ON s.id = sg.subject_id LEFT JOIN ids_groups g ON g.id = sg.group_id WHERE s.id IS NULL OR g.id IS NULL');
    if ($subjectGroupRefs > 0) {
        $errors[] = 'Subject-Gruppen mit fehlenden Referenzen: ' . $subjectGroupRefs;
    }

    $duplicateSubjects = mp15_scalar($pdo, 'SELECT COUNT(*) FROM (SELECT subject_id FROM ids_persons WHERE subject_id IS NOT NULL GROUP BY subject_id HAVING COUNT(*) > 1) x');
    if ($duplicateSubjects > 0) {
        $errors[] = 'Mehrere Personen teilen sich dasselbe Subject: ' . $duplicateSubjects;
    }

    $crossSystemPermissionAssignments = mp15_scalar($pdo, 'SELECT COUNT(*) FROM ids_group_permissions gp INNER JOIN ids_groups g ON g.id = gp.group_id INNER JOIN ids_permissions p ON p.id = gp.permission_id WHERE g.system_id <> p.system_id');
    if ($crossSystemPermissionAssignments > 0) {
        $errors[] = 'Gruppen haben systemfremde Permissions: ' . $crossSystemPermissionAssignments;
    }
}

if ($errors !== []) {
    echo 'Personen-/Subject-Integritätscheck fehlgeschlagen:' . PHP_EOL;
    foreach ($errors as $error) {
        echo ' - ' . $error . PHP_EOL;
    }
    exit(1);
}

echo 'OK: Personen, User, Subjects und Gruppen-Zuordnungen sind im neuen Identity-Modell konsistent.' . PHP_EOL;
PHPFILE, $stamp);

write_project_file('tools/qa/check_personen_routes_identity_model.php', <<<'PHPFILE'
<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$routesFile = $root . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'routes.php';
if (!is_file($routesFile)) {
    fwrite(STDERR, "config/routes.php nicht gefunden.\n");
    exit(1);
}

$content = file_get_contents($routesFile);
if ($content === false) {
    fwrite(STDERR, "config/routes.php konnte nicht gelesen werden.\n");
    exit(1);
}

$withoutLineComments = preg_replace('/^[ \t]*\/\/.*$/m', '', $content) ?? $content;
$errors = [];

foreach ([
    "'/verwaltung/personen'",
    "'/verwaltung/personen/{id}'",
    "'/verwaltung/personen/{id}/gruppen'",
    "'/administration/personen'",
    "'/administration/personen/{id}/gruppen'",
] as $needle) {
    if (!str_contains($withoutLineComments, $needle)) {
        $errors[] = 'Erwartete Personen-/Gruppen-Route fehlt: ' . $needle;
    }
}

foreach ([
    'PermissionGroupRepository',
    'PersonPermissionGroupRepository',
    'ids_permission_groups',
    'ids_user_permission_groups',
    'ids_person_permission_groups',
    'Verwaltung\\GruppenController',
    'Verwaltung\\BerechtigungenController',
] as $legacyNeedle) {
    if (str_contains($withoutLineComments, $legacyNeedle)) {
        $errors[] = 'Legacy-Verweis in aktiver routes.php gefunden: ' . $legacyNeedle;
    }
}

if (!preg_match('/\/administration\/personen[^\n]+(?:AdminPersonenGruppenController|PersonenGruppenController)::class/', $withoutLineComments)) {
    $errors[] = 'Administration-Personenroute zeigt nicht erkennbar auf PersonenGruppenController/AdminPersonenGruppenController.';
}

if ($errors !== []) {
    echo 'Personen-Routen-Check fehlgeschlagen:' . PHP_EOL;
    foreach ($errors as $error) {
        echo ' - ' . $error . PHP_EOL;
    }
    exit(1);
}

echo 'OK: Personen-Routen nutzen das neue Identity-/Administration-Modell ohne alte PermissionGroup-Routen.' . PHP_EOL;
PHPFILE, $stamp);

write_project_file('tools/qa/check_personen_code_identity_model.php', <<<'PHPFILE'
<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$targets = [
    'src/Repository/PersonRepository.php',
    'src/Repository/PersonErasureRepository.php',
    'src/Repository/EntityAuditRepository.php',
    'src/Repository/VerwaltungStatsRepository.php',
    'src/Http/Controller/Verwaltung/PersonenController.php',
    'src/Http/Controller/Verwaltung/EntityAuditController.php',
    'config/services.php',
];
$legacyNeedles = [
    'ids_permission_groups',
    'ids_user_permission_groups',
    'ids_person_permission_groups',
    'PermissionGroupRepository',
    'PersonPermissionGroupRepository',
    'permission_group_id',
];
$errors = [];

foreach ($targets as $relative) {
    $file = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative);
    if (!is_file($file)) {
        continue;
    }
    $content = file_get_contents($file);
    if ($content === false) {
        $errors[] = 'Datei konnte nicht gelesen werden: ' . $relative;
        continue;
    }
    foreach ($legacyNeedles as $needle) {
        if (str_contains($content, $needle)) {
            $errors[] = $relative . ': Legacy-Verweis gefunden: ' . $needle;
        }
    }
}

$personRepository = $root . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Repository' . DIRECTORY_SEPARATOR . 'PersonRepository.php';
if (is_file($personRepository)) {
    $content = file_get_contents($personRepository) ?: '';
    foreach (['ids_persons', 'ids_subject_groups', 'ids_groups'] as $needle) {
        if (!str_contains($content, $needle)) {
            $errors[] = 'PersonRepository nutzt erwarteten neuen Identity-Baustein nicht sichtbar: ' . $needle;
        }
    }
}

if ($errors !== []) {
    echo 'Personen-Code-Identity-Check fehlgeschlagen:' . PHP_EOL;
    foreach ($errors as $error) {
        echo ' - ' . $error . PHP_EOL;
    }
    exit(1);
}

echo 'OK: Personen-Code ist frei von Legacy-PermissionGroup-Verweisen und nutzt das neue Identity-Modell.' . PHP_EOL;
PHPFILE, $stamp);

write_project_file('tools/qa/run_personen_identity_checks.php', <<<'PHPFILE'
<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$php = PHP_BINARY;

$commands = [
    [$php, '-l', $root . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'routes.php'],
    [$php, '-l', $root . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'services.php'],
    [$php, '-l', $root . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Repository' . DIRECTORY_SEPARATOR . 'PersonRepository.php'],
    [$php, '-l', $root . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Repository' . DIRECTORY_SEPARATOR . 'PersonErasureRepository.php'],
    [$php, '-l', $root . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Http' . DIRECTORY_SEPARATOR . 'Controller' . DIRECTORY_SEPARATOR . 'Verwaltung' . DIRECTORY_SEPARATOR . 'PersonenController.php'],
    [$php, $root . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'qa' . DIRECTORY_SEPARATOR . 'run_identity_permission_group_cleanup_checks.php'],
    [$php, $root . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'qa' . DIRECTORY_SEPARATOR . 'check_person_subject_integrity.php'],
    [$php, $root . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'qa' . DIRECTORY_SEPARATOR . 'check_personen_routes_identity_model.php'],
    [$php, $root . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'qa' . DIRECTORY_SEPARATOR . 'check_personen_code_identity_model.php'],
];

$failed = false;
foreach ($commands as $command) {
    $display = implode(' ', array_map(static fn(string $part): string => '"' . $part . '"', $command));
    echo 'Running: ' . $display . PHP_EOL;
    $process = proc_open($command, [STDIN, STDOUT, STDERR], $pipes, $root);
    if (!is_resource($process)) {
        echo 'FAILED: Prozess konnte nicht gestartet werden.' . PHP_EOL;
        $failed = true;
        continue;
    }
    $exitCode = proc_close($process);
    if ($exitCode !== 0) {
        echo 'FAILED: ' . $display . PHP_EOL;
        $failed = true;
    }
}

if ($failed) {
    echo 'Mini-Projekt-15-Personen-Identity-Checks haben Fehler gefunden.' . PHP_EOL;
    exit(1);
}

echo 'OK: Mini-Projekt-15-Personen-Identity-Checks bestanden.' . PHP_EOL;
PHPFILE, $stamp);

echo PHP_EOL . 'Mini-Projekt 15 Personen-Identity-Guard wurde angewendet.' . PHP_EOL;
echo 'Bitte ausführen:' . PHP_EOL;
echo '  php bin\\console seed' . PHP_EOL;
echo '  php tools\\qa\\check_person_subject_integrity.php' . PHP_EOL;
echo '  php tools\\qa\\check_personen_routes_identity_model.php' . PHP_EOL;
echo '  php tools\\qa\\check_personen_code_identity_model.php' . PHP_EOL;
echo '  php tools\\qa\\run_personen_identity_checks.php' . PHP_EOL;
