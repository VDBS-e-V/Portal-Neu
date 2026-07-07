<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$stamp = 'mini-project-12-' . date('Ymd-His');

function mp12_write(string $relative, string $content, string $stamp): void
{
    global $root;
    $target = $root . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relative);
    $dir = dirname($target);
    if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
        throw new RuntimeException('Verzeichnis konnte nicht erstellt werden: ' . $dir);
    }
    if (is_file($target)) {
        $backup = $target . '.bak-' . $stamp;
        if (!copy($target, $backup)) {
            throw new RuntimeException('Backup fehlgeschlagen: ' . $target);
        }
        echo 'Backup: ' . $backup . PHP_EOL;
    }
    if (file_put_contents($target, $content) === false) {
        throw new RuntimeException('Datei konnte nicht geschrieben werden: ' . $target);
    }
    echo 'Aktualisiert: ' . $target . PHP_EOL;
}

mp12_write('tools/qa/identity_qa_bootstrap.php', <<<'FILE_0'
<?php

declare(strict_types=1);

function identity_qa_project_root(): string
{
    return dirname(__DIR__, 2);
}

function identity_qa_load_env(string $root): array
{
    $file = $root . DIRECTORY_SEPARATOR . '.env';
    $env = [];

    if (!is_file($file)) {
        return $env;
    }

    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return $env;
    }

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        if (!str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        if ($value !== '' && (($value[0] === '"' && substr($value, -1) === '"') || ($value[0] === "'" && substr($value, -1) === "'"))) {
            $value = substr($value, 1, -1);
        }
        $env[$key] = $value;
    }

    return $env;
}

function identity_qa_env(array $env, array $keys, ?string $default = null): ?string
{
    foreach ($keys as $key) {
        if (array_key_exists($key, $env) && (string)$env[$key] !== '') {
            return (string)$env[$key];
        }
    }
    return $default;
}

function identity_qa_pdo(string $root): PDO
{
    $env = identity_qa_load_env($root);

    $database = identity_qa_env($env, ['DB_NAME', 'DB_DATABASE', 'MYSQL_DATABASE'], 'vdbs_sys');
    $username = identity_qa_env($env, ['DB_USER', 'DB_USERNAME', 'MYSQL_USER'], 'root');
    $password = identity_qa_env($env, ['DB_PASS', 'DB_PASSWORD', 'MYSQL_PASSWORD'], '');
    $host = identity_qa_env($env, ['DB_HOST', 'MYSQL_HOST'], '127.0.0.1');
    $port = identity_qa_env($env, ['DB_PORT', 'MYSQL_PORT'], '3306');

    $hosts = array_values(array_unique([$host, $host === '127.0.0.1' ? 'localhost' : '127.0.0.1']));
    $last = null;

    foreach ($hosts as $candidateHost) {
        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $candidateHost, $port, $database);
        try {
            return new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (Throwable $e) {
            $last = $e;
        }
    }

    throw new RuntimeException('DB-Verbindung fehlgeschlagen: ' . ($last?->getMessage() ?? 'unbekannter Fehler'));
}

function identity_qa_table_exists(PDO $pdo, string $table): bool
{
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = :table');
    $stmt->execute(['table' => $table]);
    return (int)$stmt->fetchColumn() > 0;
}

function identity_qa_column_exists(PDO $pdo, string $table, string $column): bool
{
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = :table AND column_name = :column');
    $stmt->execute(['table' => $table, 'column' => $column]);
    return (int)$stmt->fetchColumn() > 0;
}

function identity_qa_first_existing_column(PDO $pdo, string $table, array $columns): ?string
{
    foreach ($columns as $column) {
        if (identity_qa_column_exists($pdo, $table, $column)) {
            return $column;
        }
    }
    return null;
}

function identity_qa_scalar(PDO $pdo, string $sql, array $params = []): int
{
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return (int)$stmt->fetchColumn();
}

function identity_qa_active_condition(PDO $pdo, string $alias, string $table): string
{
    if (identity_qa_column_exists($pdo, $table, 'status')) {
        return "({$alias}.status IS NULL OR {$alias}.status = '' OR {$alias}.status = 'active')";
    }
    if (identity_qa_column_exists($pdo, $table, 'is_active')) {
        return "({$alias}.is_active IS NULL OR {$alias}.is_active = 1)";
    }
    if (identity_qa_column_exists($pdo, $table, 'active')) {
        return "({$alias}.active IS NULL OR {$alias}.active = 1)";
    }
    return '1=1';
}

function identity_qa_row_label(array $row, array $preferredColumns): string
{
    foreach ($preferredColumns as $column) {
        if (isset($row[$column]) && (string)$row[$column] !== '') {
            return (string)$row[$column];
        }
    }
    return '#' . (string)($row['id'] ?? '?');
}

FILE_0, $stamp);

mp12_write('tools/qa/check_identity_model_integrity.php', <<<'FILE_1'
<?php

declare(strict_types=1);

require_once __DIR__ . '/identity_qa_bootstrap.php';

$root = identity_qa_project_root();
$pdo = identity_qa_pdo($root);

$errors = [];
$warnings = [];

$requiredTables = [
    'ids_subjects',
    'ids_systems',
    'ids_groups',
    'ids_permissions',
    'ids_group_permissions',
    'ids_subject_groups',
    'ids_users',
];

foreach ($requiredTables as $table) {
    if (!identity_qa_table_exists($pdo, $table)) {
        $errors[] = "Tabelle fehlt: {$table}";
    }
}

if ($errors !== []) {
    echo "Identity-Modell-Integritätscheck fehlgeschlagen:\n";
    foreach ($errors as $error) {
        echo " - {$error}\n";
    }
    exit(1);
}

$checks = [
    'ids_groups ohne existierendes ids_systems' => "SELECT COUNT(*) FROM ids_groups g LEFT JOIN ids_systems s ON s.id = g.system_id WHERE s.id IS NULL",
    'ids_permissions ohne existierendes ids_systems' => "SELECT COUNT(*) FROM ids_permissions p LEFT JOIN ids_systems s ON s.id = p.system_id WHERE s.id IS NULL",
    'ids_group_permissions ohne Gruppe oder Permission' => "SELECT COUNT(*) FROM ids_group_permissions gp LEFT JOIN ids_groups g ON g.id = gp.group_id LEFT JOIN ids_permissions p ON p.id = gp.permission_id WHERE g.id IS NULL OR p.id IS NULL",
    'ids_subject_groups ohne Subject oder Gruppe' => "SELECT COUNT(*) FROM ids_subject_groups sg LEFT JOIN ids_subjects s ON s.id = sg.subject_id LEFT JOIN ids_groups g ON g.id = sg.group_id WHERE s.id IS NULL OR g.id IS NULL",
];

foreach ($checks as $label => $sql) {
    try {
        $count = identity_qa_scalar($pdo, $sql);
        if ($count > 0) {
            $errors[] = "{$label}: {$count}";
        }
    } catch (Throwable $e) {
        $errors[] = "{$label}: Query fehlgeschlagen ({$e->getMessage()})";
    }
}

try {
    $stmt = $pdo->query(
        'SELECT gp.group_id, gp.permission_id, g.system_id AS group_system_id, p.system_id AS permission_system_id '
        . 'FROM ids_group_permissions gp '
        . 'INNER JOIN ids_groups g ON g.id = gp.group_id '
        . 'INNER JOIN ids_permissions p ON p.id = gp.permission_id '
        . 'WHERE g.system_id <> p.system_id '
        . 'LIMIT 20'
    );
    $mismatches = $stmt->fetchAll();
    if ($mismatches !== []) {
        foreach ($mismatches as $row) {
            $errors[] = sprintf(
                'System-Mismatch in ids_group_permissions: group_id=%s system=%s, permission_id=%s system=%s',
                $row['group_id'],
                $row['group_system_id'],
                $row['permission_id'],
                $row['permission_system_id']
            );
        }
    }
} catch (Throwable $e) {
    $errors[] = 'System-Mismatch-Prüfung fehlgeschlagen: ' . $e->getMessage();
}

if (identity_qa_column_exists($pdo, 'ids_subject_groups', 'expires_at')) {
    try {
        $expired = identity_qa_scalar($pdo, "SELECT COUNT(*) FROM ids_subject_groups WHERE expires_at IS NOT NULL AND expires_at < NOW()");
        if ($expired > 0) {
            $warnings[] = "Abgelaufene Subject-Gruppen-Mitgliedschaften vorhanden: {$expired}";
        }
    } catch (Throwable $e) {
        $warnings[] = 'Ablauf-Prüfung fehlgeschlagen: ' . $e->getMessage();
    }
}

if (identity_qa_column_exists($pdo, 'ids_groups', 'is_default')) {
    try {
        $defaults = identity_qa_scalar($pdo, "SELECT COUNT(*) FROM ids_groups WHERE is_default = 1");
        if ($defaults === 0) {
            $warnings[] = 'Keine Default-Gruppe in ids_groups markiert.';
        }
    } catch (Throwable $e) {
        $warnings[] = 'Default-Gruppen-Prüfung fehlgeschlagen: ' . $e->getMessage();
    }
}

if ($errors !== []) {
    echo "Identity-Modell-Integritätscheck fehlgeschlagen:\n";
    foreach ($errors as $error) {
        echo " - {$error}\n";
    }
    if ($warnings !== []) {
        echo "\nWarnungen:\n";
        foreach ($warnings as $warning) {
            echo " - {$warning}\n";
        }
    }
    exit(1);
}

if ($warnings !== []) {
    echo "OK: Identity-Modell-Integrität bestanden, mit Warnungen:\n";
    foreach ($warnings as $warning) {
        echo " - {$warning}\n";
    }
    exit(0);
}

echo "OK: Identity-Modell-Integrität bestanden.\n";

FILE_1, $stamp);

mp12_write('tools/qa/check_initial_admin_authorization.php', <<<'FILE_2'
<?php

declare(strict_types=1);

require_once __DIR__ . '/identity_qa_bootstrap.php';

$root = identity_qa_project_root();
$env = identity_qa_load_env($root);
$pdo = identity_qa_pdo($root);

$adminEmail = identity_qa_env($env, ['INITIAL_ADMIN_EMAIL'], 'local@admin.com');
$errors = [];
$warnings = [];

if (!identity_qa_table_exists($pdo, 'ids_users') || !identity_qa_table_exists($pdo, 'ids_subjects')) {
    echo "Initial-Admin-Check übersprungen: ids_users oder ids_subjects fehlt.\n";
    exit(1);
}

$subjectId = null;
$userRow = null;

try {
    $stmt = $pdo->prepare('SELECT * FROM ids_users WHERE email = :email LIMIT 1');
    $stmt->execute(['email' => $adminEmail]);
    $userRow = $stmt->fetch();
} catch (Throwable $e) {
    $errors[] = 'Admin-User konnte nicht geladen werden: ' . $e->getMessage();
}

if (!$userRow) {
    $errors[] = "Initial-Admin nicht in ids_users gefunden: {$adminEmail}";
} else {
    if (array_key_exists('subject_id', $userRow) && $userRow['subject_id'] !== null && (string)$userRow['subject_id'] !== '') {
        $subjectId = (int)$userRow['subject_id'];
    } elseif (array_key_exists('person_id', $userRow) && identity_qa_table_exists($pdo, 'ids_persons') && identity_qa_column_exists($pdo, 'ids_persons', 'subject_id')) {
        $stmt = $pdo->prepare('SELECT subject_id FROM ids_persons WHERE id = :person_id LIMIT 1');
        $stmt->execute(['person_id' => $userRow['person_id']]);
        $value = $stmt->fetchColumn();
        if ($value !== false && $value !== null) {
            $subjectId = (int)$value;
        }
    }

    if ($subjectId === null) {
        $errors[] = "Initial-Admin {$adminEmail} hat kein auflösbares subject_id.";
    }
}

if ($errors !== []) {
    echo "Initial-Admin-Autorisierungscheck fehlgeschlagen:\n";
    foreach ($errors as $error) {
        echo " - {$error}\n";
    }
    exit(1);
}

$activeSystemCondition = identity_qa_active_condition($pdo, 's', 'ids_systems');
$stmt = $pdo->query("SELECT * FROM ids_systems s WHERE {$activeSystemCondition} ORDER BY id");
$systems = $stmt->fetchAll();

if ($systems === []) {
    $errors[] = 'Keine aktiven Systeme in ids_systems gefunden.';
}

$groupLabelColumns = ['slug', 'code', 'name', 'group_key', 'label', 'title'];
$permissionLabelColumns = ['permission_key', 'key', 'code', 'name', 'slug'];

foreach ($systems as $system) {
    $systemId = (int)$system['id'];
    $systemLabel = identity_qa_row_label($system, ['slug', 'code', 'name', 'system_key', 'label']);

    $stmt = $pdo->prepare('SELECT * FROM ids_groups WHERE system_id = :system_id ORDER BY id');
    $stmt->execute(['system_id' => $systemId]);
    $groups = $stmt->fetchAll();

    $adminGroups = [];
    foreach ($groups as $group) {
        $haystack = strtolower(implode(' ', array_map(static fn($v): string => is_scalar($v) ? (string)$v : '', $group)));
        foreach ($groupLabelColumns as $column) {
            if (isset($group[$column]) && strtolower((string)$group[$column]) === 'administrator') {
                $adminGroups[] = $group;
                continue 2;
            }
        }
        if (str_contains($haystack, 'administrator')) {
            $adminGroups[] = $group;
        }
    }

    if ($adminGroups === []) {
        $errors[] = "Keine Administrator-Gruppe für System {$systemLabel} gefunden.";
        continue;
    }

    $adminGroupIds = array_map(static fn(array $row): int => (int)$row['id'], $adminGroups);
    $placeholders = implode(',', array_fill(0, count($adminGroupIds), '?'));

    $params = [$subjectId, ...$adminGroupIds];
    $membershipSql = 'SELECT COUNT(*) FROM ids_subject_groups WHERE subject_id = ? AND group_id IN (' . $placeholders . ')';
    if (identity_qa_column_exists($pdo, 'ids_subject_groups', 'expires_at')) {
        $membershipSql .= ' AND (expires_at IS NULL OR expires_at >= NOW())';
    }
    $stmt = $pdo->prepare($membershipSql);
    $stmt->execute($params);
    $membershipCount = (int)$stmt->fetchColumn();

    if ($membershipCount === 0) {
        $errors[] = "Initial-Admin {$adminEmail} ist in keiner aktiven Administrator-Gruppe für System {$systemLabel}.";
    }

    $activePermissionCondition = identity_qa_active_condition($pdo, 'p', 'ids_permissions');
    $stmt = $pdo->prepare("SELECT * FROM ids_permissions p WHERE p.system_id = :system_id AND {$activePermissionCondition}");
    $stmt->execute(['system_id' => $systemId]);
    $permissions = $stmt->fetchAll();

    if ($permissions === []) {
        $warnings[] = "Keine aktiven Permissions für System {$systemLabel} gefunden.";
        continue;
    }

    $permissionIds = array_map(static fn(array $row): int => (int)$row['id'], $permissions);
    $permissionPlaceholders = implode(',', array_fill(0, count($permissionIds), '?'));
    $groupPlaceholders = implode(',', array_fill(0, count($adminGroupIds), '?'));

    $stmt = $pdo->prepare(
        'SELECT DISTINCT permission_id FROM ids_group_permissions '
        . 'WHERE group_id IN (' . $groupPlaceholders . ') AND permission_id IN (' . $permissionPlaceholders . ')'
    );
    $stmt->execute([...$adminGroupIds, ...$permissionIds]);
    $assigned = array_map('intval', array_column($stmt->fetchAll(), 'permission_id'));
    $missing = array_values(array_diff($permissionIds, $assigned));

    if ($missing !== []) {
        $labelsById = [];
        foreach ($permissions as $permission) {
            $labelsById[(int)$permission['id']] = identity_qa_row_label($permission, $permissionLabelColumns);
        }
        $shown = array_slice(array_map(static fn(int $id): string => $labelsById[$id] ?? ('#' . $id), $missing), 0, 10);
        $errors[] = "Administrator-Gruppe für {$systemLabel} enthält nicht alle aktiven Permissions. Fehlend: " . implode(', ', $shown) . (count($missing) > 10 ? ' ...' : '');
    }
}

if ($errors !== []) {
    echo "Initial-Admin-Autorisierungscheck fehlgeschlagen:\n";
    foreach ($errors as $error) {
        echo " - {$error}\n";
    }
    if ($warnings !== []) {
        echo "\nWarnungen:\n";
        foreach ($warnings as $warning) {
            echo " - {$warning}\n";
        }
    }
    exit(1);
}

if ($warnings !== []) {
    echo "OK: Initial-Admin-Autorisierung bestanden, mit Warnungen:\n";
    foreach ($warnings as $warning) {
        echo " - {$warning}\n";
    }
    exit(0);
}

echo "OK: Initial-Admin hat aktive Administrator-Zuordnung und Administrator-Gruppen decken aktive System-Permissions ab.\n";

FILE_2, $stamp);

mp12_write('tools/qa/check_legacy_identity_permission_group_usage.php', <<<'FILE_3'
<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$strict = in_array('--strict', $argv, true);

$directories = [
    'src',
    'config',
    'resources/views',
];

$patterns = [
    'ids_permission_groups',
    'ids_user_permission_groups',
    'PermissionGroupRepository',
    'UserPermissionGroup',
    'permission_group_id',
    'permissionGroup',
];

$hits = [];

foreach ($directories as $directory) {
    $base = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $directory);
    if (!is_dir($base)) {
        continue;
    }

    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base, FilesystemIterator::SKIP_DOTS));
    foreach ($iterator as $file) {
        if (!$file instanceof SplFileInfo || !$file->isFile()) {
            continue;
        }
        $path = $file->getPathname();
        if (!preg_match('/\.(php|sql|phtml|inc)$/i', $path)) {
            continue;
        }

        $relative = str_replace($root . DIRECTORY_SEPARATOR, '', $path);
        $content = file($path, FILE_IGNORE_NEW_LINES);
        if ($content === false) {
            continue;
        }
        foreach ($content as $lineNumber => $line) {
            foreach ($patterns as $pattern) {
                if (stripos($line, $pattern) !== false) {
                    $hits[] = sprintf('%s:%d: %s', str_replace('\\', '/', $relative), $lineNumber + 1, trim($line));
                    continue 2;
                }
            }
        }
    }
}

if ($hits === []) {
    echo "OK: Keine produktiven Legacy-Identity-PermissionGroup-Codeverweise gefunden.\n";
    exit(0);
}

if ($strict) {
    echo "Legacy-Identity-PermissionGroup-Codeverweise gefunden:\n";
    foreach ($hits as $hit) {
        echo " - {$hit}\n";
    }
    exit(1);
}

echo "WARN: Legacy-Identity-PermissionGroup-Codeverweise gefunden. Diese werden in Mini-Projekt 13 bewertet/entfernt:\n";
foreach ($hits as $hit) {
    echo " - {$hit}\n";
}
exit(0);

FILE_3, $stamp);

mp12_write('tools/qa/run_identity_integrity_checks.php', <<<'FILE_4'
<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$php = PHP_BINARY;
$failed = false;

function run_command(array $command, bool $required = true): void
{
    global $failed;

    $display = implode(' ', array_map(static fn(string $part): string => escapeshellarg($part), $command));
    echo "Running: {$display}\n";

    $process = proc_open($command, [
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ], $pipes);

    if (!is_resource($process)) {
        echo "FAILED: Prozess konnte nicht gestartet werden.\n";
        if ($required) {
            $failed = true;
        }
        return;
    }

    echo stream_get_contents($pipes[1]);
    $err = stream_get_contents($pipes[2]);
    if ($err !== '') {
        echo $err;
    }
    fclose($pipes[1]);
    fclose($pipes[2]);

    $exitCode = proc_close($process);
    if ($exitCode !== 0) {
        echo ($required ? 'FAILED' : 'WARNING') . ": {$display}\n";
        if ($required) {
            $failed = true;
        }
    }
}

$syntaxFiles = [
    'config/routes.php',
    'config/services.php',
    'src/Security/AuthorizationService.php',
    'src/Security/RoutePermissionMap.php',
];

foreach ($syntaxFiles as $relative) {
    $file = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative);
    if (is_file($file)) {
        run_command([$php, '-l', $file]);
    }
}

$optionalPreviousChecks = [
    'tools/qa/run_identity_service_cleanup_checks.php',
    'tools/qa/run_identity_db_legacy_checks.php',
];

foreach ($optionalPreviousChecks as $relative) {
    $file = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative);
    if (is_file($file)) {
        run_command([$php, $file]);
    }
}

run_command([$php, $root . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'qa' . DIRECTORY_SEPARATOR . 'check_identity_model_integrity.php']);
run_command([$php, $root . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'qa' . DIRECTORY_SEPARATOR . 'check_initial_admin_authorization.php']);
run_command([$php, $root . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'qa' . DIRECTORY_SEPARATOR . 'check_legacy_identity_permission_group_usage.php'], false);

if ($failed) {
    echo "Mini-Projekt-12-Identity-Integritätschecks haben Fehler gefunden.\n";
    exit(1);
}

echo "OK: Mini-Projekt-12-Identity-Integritätschecks bestanden.\n";

FILE_4, $stamp);

mp12_write('database/seeds/seed_mini_project_12_identity_cleanup_markers.sql', <<<'FILE_5'
CREATE TABLE IF NOT EXISTS ids_identity_cleanup_markers (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    marker_key VARCHAR(191) NOT NULL,
    object_type VARCHAR(64) NOT NULL,
    object_name VARCHAR(191) NOT NULL,
    replacement VARCHAR(191) NULL,
    status VARCHAR(32) NOT NULL DEFAULT 'deprecated',
    notes TEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_ids_identity_cleanup_markers_marker_key (marker_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO ids_identity_cleanup_markers (marker_key, object_type, object_name, replacement, status, notes)
VALUES
    ('legacy-table.ids_permission_groups', 'table', 'ids_permission_groups', 'ids_groups', 'deprecated', 'Altes gruppenbasiertes Login-Rechtesystem. Das neue Modell nutzt ids_groups pro System.'),
    ('legacy-table.ids_user_permission_groups', 'table', 'ids_user_permission_groups', 'ids_subject_groups', 'deprecated', 'Alte User-Gruppen-Zuordnung. Das neue Modell nutzt ids_subject_groups mit subject_id.')
ON DUPLICATE KEY UPDATE
    replacement = VALUES(replacement),
    status = VALUES(status),
    notes = VALUES(notes),
    updated_at = CURRENT_TIMESTAMP;

FILE_5, $stamp);

mp12_write('database/sql/verify_mini_project_12_identity_integrity.sql', <<<'FILE_6'
SELECT 'groups_without_system' AS check_name, COUNT(*) AS count_value
FROM ids_groups g
LEFT JOIN ids_systems s ON s.id = g.system_id
WHERE s.id IS NULL;

SELECT 'permissions_without_system' AS check_name, COUNT(*) AS count_value
FROM ids_permissions p
LEFT JOIN ids_systems s ON s.id = p.system_id
WHERE s.id IS NULL;

SELECT 'group_permissions_missing_refs' AS check_name, COUNT(*) AS count_value
FROM ids_group_permissions gp
LEFT JOIN ids_groups g ON g.id = gp.group_id
LEFT JOIN ids_permissions p ON p.id = gp.permission_id
WHERE g.id IS NULL OR p.id IS NULL;

SELECT 'group_permission_system_mismatch' AS check_name, COUNT(*) AS count_value
FROM ids_group_permissions gp
INNER JOIN ids_groups g ON g.id = gp.group_id
INNER JOIN ids_permissions p ON p.id = gp.permission_id
WHERE g.system_id <> p.system_id;

SELECT 'subject_groups_missing_refs' AS check_name, COUNT(*) AS count_value
FROM ids_subject_groups sg
LEFT JOIN ids_subjects s ON s.id = sg.subject_id
LEFT JOIN ids_groups g ON g.id = sg.group_id
WHERE s.id IS NULL OR g.id IS NULL;

SELECT *
FROM ids_identity_cleanup_markers
WHERE marker_key IN ('legacy-table.ids_permission_groups', 'legacy-table.ids_user_permission_groups');

FILE_6, $stamp);


echo PHP_EOL;
echo "Mini-Projekt 12 Identity-Integritätsguard wurde angewendet." . PHP_EOL;
echo "Bitte ausführen:" . PHP_EOL;
echo "  php -l tools\qa\check_identity_model_integrity.php" . PHP_EOL;
echo "  php bin\console seed" . PHP_EOL;
echo "  php tools\qa\check_identity_model_integrity.php" . PHP_EOL;
echo "  php tools\qa\check_initial_admin_authorization.php" . PHP_EOL;
echo "  php tools\qa\check_legacy_identity_permission_group_usage.php" . PHP_EOL;
echo "  php tools\qa\run_identity_integrity_checks.php" . PHP_EOL;
