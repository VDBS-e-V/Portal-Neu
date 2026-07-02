<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$stamp = 'mini-project-10-' . date('Ymd-His');

function writeFileWithBackup(string $root, string $relative, string $content, string $stamp): void
{
    $target = $root . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relative);
    $dir = dirname($target);
    if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
        throw new RuntimeException('Verzeichnis konnte nicht erstellt werden: ' . $dir);
    }

    if (is_file($target)) {
        $backup = $target . '.bak-' . $stamp;
        if (!copy($target, $backup)) {
            throw new RuntimeException('Backup konnte nicht erstellt werden: ' . $backup);
        }
        echo 'Backup: ' . $backup . PHP_EOL;
    }

    if (file_put_contents($target, $content) === false) {
        throw new RuntimeException('Datei konnte nicht geschrieben werden: ' . $target);
    }

    echo 'Aktualisiert: ' . $target . PHP_EOL;
}

$dropTool = <<<'PHPFILE'
<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$dryRun = in_array('--dry-run', $argv, true);
$force = in_array('--force', $argv, true);
$skipJsonArchive = in_array('--no-json-archive', $argv, true);

function loadEnvMini10(string $root): array
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
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        if (!str_contains($line, '=')) {
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

function envValueMini10(array $env, array $keys, ?string $default = null): ?string
{
    foreach ($keys as $key) {
        if (array_key_exists($key, $env) && $env[$key] !== '') {
            return $env[$key];
        }
        $system = getenv($key);
        if ($system !== false && $system !== '') {
            return $system;
        }
    }

    return $default;
}

function connectMini10(string $root): PDO
{
    $env = loadEnvMini10($root);

    $dsn = envValueMini10($env, ['DB_DSN']);
    $user = envValueMini10($env, ['DB_USER', 'DB_USERNAME'], 'root') ?? 'root';
    $pass = envValueMini10($env, ['DB_PASS', 'DB_PASSWORD'], '') ?? '';

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    if ($dsn !== null && $dsn !== '') {
        return new PDO($dsn, $user, $pass, $options);
    }

    $host = envValueMini10($env, ['DB_HOST'], '127.0.0.1') ?? '127.0.0.1';
    $port = envValueMini10($env, ['DB_PORT'], '3306') ?? '3306';
    $db = envValueMini10($env, ['DB_NAME', 'DB_DATABASE'], 'vdbs_sys') ?? 'vdbs_sys';
    $charset = envValueMini10($env, ['DB_CHARSET'], 'utf8mb4') ?? 'utf8mb4';

    $hostsToTry = [$host];
    if ($host === '127.0.0.1') {
        $hostsToTry[] = 'localhost';
    } elseif ($host === 'localhost') {
        $hostsToTry[] = '127.0.0.1';
    }

    $last = null;
    foreach (array_unique($hostsToTry) as $candidateHost) {
        try {
            return new PDO(
                sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $candidateHost, $port, $db, $charset),
                $user,
                $pass,
                $options
            );
        } catch (PDOException $e) {
            $last = $e;
        }
    }

    throw $last ?? new RuntimeException('Keine Datenbankverbindung möglich.');
}

function quoteIdentMini10(string $identifier): string
{
    return '`' . str_replace('`', '``', $identifier) . '`';
}

function tableExistsMini10(PDO $pdo, string $table): bool
{
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?');
    $stmt->execute([$table]);
    return (int)$stmt->fetchColumn() > 0;
}

function columnExistsMini10(PDO $pdo, string $table, string $column): bool
{
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?');
    $stmt->execute([$table, $column]);
    return (int)$stmt->fetchColumn() > 0;
}

function execMini10(PDO $pdo, string $sql, bool $dryRun): void
{
    echo ($dryRun ? 'DRY-RUN: ' : 'SQL: ') . $sql . PHP_EOL;
    if (!$dryRun) {
        $pdo->exec($sql);
    }
}

function dumpTableJsonMini10(PDO $pdo, string $archiveDir, string $table): void
{
    if (!tableExistsMini10($pdo, $table)) {
        file_put_contents($archiveDir . DIRECTORY_SEPARATOR . $table . '.json', json_encode([
            'table' => $table,
            'exists' => false,
            'rows' => [],
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        return;
    }

    $rows = $pdo->query('SELECT * FROM ' . quoteIdentMini10($table))->fetchAll();
    file_put_contents($archiveDir . DIRECTORY_SEPARATOR . $table . '.json', json_encode([
        'table' => $table,
        'exists' => true,
        'row_count' => count($rows),
        'rows' => $rows,
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
}

function createJsonArchiveMini10(PDO $pdo, string $root): string
{
    $archiveDir = $root . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'archive' . DIRECTORY_SEPARATOR . 'mini-project-10-legacy-pagegroup-db-' . date('Ymd-His');
    if (!is_dir($archiveDir) && !mkdir($archiveDir, 0775, true) && !is_dir($archiveDir)) {
        throw new RuntimeException('Archivverzeichnis konnte nicht erstellt werden: ' . $archiveDir);
    }

    dumpTableJsonMini10($pdo, $archiveDir, 'pt_permission_group_page_group_access');
    dumpTableJsonMini10($pdo, $archiveDir, 'pt_page_groups');

    $menuColumnInfo = [
        'table' => 'pt_menu_items',
        'column' => 'page_group_id',
        'column_exists' => columnExistsMini10($pdo, 'pt_menu_items', 'page_group_id'),
        'rows_with_value' => [],
    ];
    if ($menuColumnInfo['column_exists']) {
        $menuColumnInfo['rows_with_value'] = $pdo->query('SELECT id, page_group_id FROM pt_menu_items WHERE page_group_id IS NOT NULL')->fetchAll();
    }
    file_put_contents($archiveDir . DIRECTORY_SEPARATOR . 'pt_menu_items_page_group_id.json', json_encode($menuColumnInfo, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

    file_put_contents($archiveDir . DIRECTORY_SEPARATOR . 'manifest.json', json_encode([
        'created_at' => date(DATE_ATOM),
        'purpose' => 'Mini-Projekt 10 JSON-Sicherheitsarchiv vor Legacy-PageGroup-Drop',
        'note' => 'Dieses JSON-Archiv ersetzt keinen mysqldump.',
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

    return $archiveDir;
}

function dropForeignKeysReferencingMini10(PDO $pdo, string $referencedTable, bool $dryRun): void
{
    $stmt = $pdo->prepare(
        'SELECT DISTINCT TABLE_NAME, CONSTRAINT_NAME
         FROM information_schema.KEY_COLUMN_USAGE
         WHERE TABLE_SCHEMA = DATABASE()
           AND REFERENCED_TABLE_NAME = ?
           AND CONSTRAINT_NAME IS NOT NULL'
    );
    $stmt->execute([$referencedTable]);
    foreach ($stmt->fetchAll() as $row) {
        if (!tableExistsMini10($pdo, (string)$row['TABLE_NAME'])) {
            continue;
        }
        execMini10(
            $pdo,
            'ALTER TABLE ' . quoteIdentMini10((string)$row['TABLE_NAME']) . ' DROP FOREIGN KEY ' . quoteIdentMini10((string)$row['CONSTRAINT_NAME']),
            $dryRun
        );
    }
}

function dropLegacyPageGroupDbMini10(PDO $pdo, bool $dryRun, bool $force): void
{
    if (tableExistsMini10($pdo, 'pt_menu_items') && columnExistsMini10($pdo, 'pt_menu_items', 'page_group_id')) {
        $count = (int)$pdo->query('SELECT COUNT(*) FROM pt_menu_items WHERE page_group_id IS NOT NULL')->fetchColumn();
        if ($count > 0 && !$force) {
            throw new RuntimeException('pt_menu_items.page_group_id enthält noch ' . $count . ' gesetzte Werte. Erst Seed/Checks aus Mini-Projekt 7 ausführen oder mit --force bewusst fortfahren.');
        }
    }

    dropForeignKeysReferencingMini10($pdo, 'pt_page_groups', $dryRun);

    if (tableExistsMini10($pdo, 'pt_menu_items') && columnExistsMini10($pdo, 'pt_menu_items', 'page_group_id')) {
        execMini10($pdo, 'ALTER TABLE `pt_menu_items` DROP COLUMN `page_group_id`', $dryRun);
    } else {
        echo 'OK/SKIP: Spalte pt_menu_items.page_group_id existiert bereits nicht mehr.' . PHP_EOL;
    }

    if (tableExistsMini10($pdo, 'pt_permission_group_page_group_access')) {
        execMini10($pdo, 'DROP TABLE `pt_permission_group_page_group_access`', $dryRun);
    } else {
        echo 'OK/SKIP: Tabelle pt_permission_group_page_group_access existiert bereits nicht mehr.' . PHP_EOL;
    }

    if (tableExistsMini10($pdo, 'pt_page_groups')) {
        execMini10($pdo, 'DROP TABLE `pt_page_groups`', $dryRun);
    } else {
        echo 'OK/SKIP: Tabelle pt_page_groups existiert bereits nicht mehr.' . PHP_EOL;
    }
}

$pdo = connectMini10($root);

echo 'Mini-Projekt 10 Legacy-PageGroup-DB-Drop' . PHP_EOL;
echo $dryRun ? 'Modus: DRY-RUN, keine Änderungen werden geschrieben.' . PHP_EOL : 'Modus: LIVE, Datenbank wird geändert.' . PHP_EOL;

if (!$dryRun && !$skipJsonArchive) {
    $archive = createJsonArchiveMini10($pdo, $root);
    echo 'JSON-Sicherheitsarchiv: ' . $archive . PHP_EOL;
}

dropLegacyPageGroupDbMini10($pdo, $dryRun, $force);

if ($dryRun) {
    echo 'DRY-RUN abgeschlossen. Für die echte Änderung ohne --dry-run ausführen.' . PHP_EOL;
    exit(0);
}

echo 'Legacy-PageGroup-DB-Strukturen wurden entfernt.' . PHP_EOL;
echo 'Bitte ausführen: php tools\\qa\\check_legacy_pagegroup_db_dropped.php' . PHP_EOL;
PHPFILE;

$checkDropped = <<<'PHPFILE'
<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);

function loadEnvMini10Check(string $root): array
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
        $value = trim($value);
        if ((str_starts_with($value, '"') && str_ends_with($value, '"')) || (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
            $value = substr($value, 1, -1);
        }
        $env[trim($key)] = $value;
    }
    return $env;
}

function envValueMini10Check(array $env, array $keys, ?string $default = null): ?string
{
    foreach ($keys as $key) {
        if (isset($env[$key]) && $env[$key] !== '') {
            return $env[$key];
        }
        $system = getenv($key);
        if ($system !== false && $system !== '') {
            return $system;
        }
    }
    return $default;
}

function connectMini10Check(string $root): PDO
{
    $env = loadEnvMini10Check($root);
    $user = envValueMini10Check($env, ['DB_USER', 'DB_USERNAME'], 'root') ?? 'root';
    $pass = envValueMini10Check($env, ['DB_PASS', 'DB_PASSWORD'], '') ?? '';
    $dsn = envValueMini10Check($env, ['DB_DSN']);
    $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC];
    if ($dsn) {
        return new PDO($dsn, $user, $pass, $options);
    }
    $host = envValueMini10Check($env, ['DB_HOST'], '127.0.0.1') ?? '127.0.0.1';
    $port = envValueMini10Check($env, ['DB_PORT'], '3306') ?? '3306';
    $db = envValueMini10Check($env, ['DB_NAME', 'DB_DATABASE'], 'vdbs_sys') ?? 'vdbs_sys';
    $charset = envValueMini10Check($env, ['DB_CHARSET'], 'utf8mb4') ?? 'utf8mb4';
    $hosts = $host === '127.0.0.1' ? ['127.0.0.1', 'localhost'] : ($host === 'localhost' ? ['localhost', '127.0.0.1'] : [$host]);
    $last = null;
    foreach ($hosts as $candidate) {
        try {
            return new PDO(sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $candidate, $port, $db, $charset), $user, $pass, $options);
        } catch (PDOException $e) {
            $last = $e;
        }
    }
    throw $last ?? new RuntimeException('Keine Datenbankverbindung möglich.');
}

function tableExistsMini10Check(PDO $pdo, string $table): bool
{
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?');
    $stmt->execute([$table]);
    return (int)$stmt->fetchColumn() > 0;
}

function columnExistsMini10Check(PDO $pdo, string $table, string $column): bool
{
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?');
    $stmt->execute([$table, $column]);
    return (int)$stmt->fetchColumn() > 0;
}

$pdo = connectMini10Check($root);
$errors = [];

if (tableExistsMini10Check($pdo, 'pt_permission_group_page_group_access')) {
    $errors[] = 'Tabelle existiert noch: pt_permission_group_page_group_access';
}
if (tableExistsMini10Check($pdo, 'pt_page_groups')) {
    $errors[] = 'Tabelle existiert noch: pt_page_groups';
}
if (columnExistsMini10Check($pdo, 'pt_menu_items', 'page_group_id')) {
    $errors[] = 'Spalte existiert noch: pt_menu_items.page_group_id';
}

if ($errors !== []) {
    echo 'Legacy-PageGroup-DB-Drop-Check fehlgeschlagen:' . PHP_EOL;
    foreach ($errors as $error) {
        echo ' - ' . $error . PHP_EOL;
    }
    exit(1);
}

echo 'OK: Legacy-PageGroup-Tabellen und pt_menu_items.page_group_id sind aus der laufenden DB entfernt.' . PHP_EOL;
PHPFILE;

$runChecks = <<<'PHPFILE'
<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$php = PHP_BINARY;

$commands = [
    [$php, '-l', $root . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'routes.php'],
    [$php, '-l', $root . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'services.php'],
    [$php, $root . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'qa' . DIRECTORY_SEPARATOR . 'run_identity_service_cleanup_checks.php'],
    [$php, $root . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'qa' . DIRECTORY_SEPARATOR . 'check_no_productive_legacy_pagegroup_db_usage.php'],
    [$php, $root . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'qa' . DIRECTORY_SEPARATOR . 'check_legacy_pagegroup_db_dropped.php'],
];

$failed = false;
foreach ($commands as $command) {
    $printable = implode(' ', array_map(static fn (string $part): string => '"' . $part . '"', $command));
    echo 'Running: ' . $printable . PHP_EOL;
    $escaped = implode(' ', array_map('escapeshellarg', $command));
    passthru($escaped, $code);
    if ($code !== 0) {
        echo 'FAILED: ' . $printable . PHP_EOL;
        $failed = true;
    }
}

if ($failed) {
    echo 'Mini-Projekt-10-Legacy-Drop-Checks haben Fehler gefunden.' . PHP_EOL;
    exit(1);
}

echo 'OK: Mini-Projekt-10-Legacy-Drop-Checks bestanden.' . PHP_EOL;
PHPFILE;

$scanResidue = <<<'PHPFILE'
<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$needles = [
    'pt_page_groups',
    'pt_permission_group_page_group_access',
    'page_group_id',
    'PageGroupRepository',
    'PageGroupAccessRepository',
    'requirePageGroupAccess',
];

$allowedPrefixes = [
    'var' . DIRECTORY_SEPARATOR . 'archive' . DIRECTORY_SEPARATOR,
    'database' . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR,
];
$allowedFiles = [
    'tools' . DIRECTORY_SEPARATOR . 'drop_mini_project_10_legacy_pagegroup_db.php',
    'tools' . DIRECTORY_SEPARATOR . 'qa' . DIRECTORY_SEPARATOR . 'check_legacy_pagegroup_db_dropped.php',
    'tools' . DIRECTORY_SEPARATOR . 'qa' . DIRECTORY_SEPARATOR . 'run_identity_legacy_drop_checks.php',
    'tools' . DIRECTORY_SEPARATOR . 'qa' . DIRECTORY_SEPARATOR . 'scan_post_drop_legacy_pagegroup_residue.php',
    'README_MINI_PROJECT_10.md',
    'database' . DIRECTORY_SEPARATOR . 'sql' . DIRECTORY_SEPARATOR . 'verify_mini_project_10_legacy_pagegroup_drop.sql',
];

$dirs = ['src', 'config', 'resources', 'tools', 'database'];
$hits = [];
foreach ($dirs as $dir) {
    $base = $root . DIRECTORY_SEPARATOR . $dir;
    if (!is_dir($base)) {
        continue;
    }
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base, FilesystemIterator::SKIP_DOTS));
    foreach ($it as $file) {
        if (!$file->isFile()) {
            continue;
        }
        $path = $file->getPathname();
        $relative = str_replace($root . DIRECTORY_SEPARATOR, '', $path);
        $skip = false;
        foreach ($allowedPrefixes as $prefix) {
            if (str_starts_with($relative, $prefix)) {
                $skip = true;
                break;
            }
        }
        if ($skip || in_array($relative, $allowedFiles, true)) {
            continue;
        }
        $content = file_get_contents($path);
        if ($content === false) {
            continue;
        }
        foreach ($needles as $needle) {
            if (stripos($content, $needle) !== false) {
                $hits[] = $relative . ': ' . $needle;
            }
        }
    }
}

if ($hits === []) {
    echo 'OK: Keine unerwarteten Post-Drop-Legacy-PageGroup-Residuen in produktiven Dateien gefunden.' . PHP_EOL;
    exit(0);
}

echo 'Post-Drop-Legacy-Residuen gefunden:' . PHP_EOL;
foreach ($hits as $hit) {
    echo ' - ' . $hit . PHP_EOL;
}
echo 'Hinweis: Treffer können in QA-/Diagnose-Tools erlaubt sein, produktive Treffer bitte prüfen.' . PHP_EOL;
exit(1);
PHPFILE;

$verifySql = <<<'SQLFILE'
SELECT TABLE_NAME
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME IN ('pt_page_groups', 'pt_permission_group_page_group_access');

SELECT TABLE_NAME, COLUMN_NAME
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'pt_menu_items'
  AND COLUMN_NAME = 'page_group_id';
SQLFILE;

writeFileWithBackup($root, 'tools/drop_mini_project_10_legacy_pagegroup_db.php', $dropTool, $stamp);
writeFileWithBackup($root, 'tools/qa/check_legacy_pagegroup_db_dropped.php', $checkDropped, $stamp);
writeFileWithBackup($root, 'tools/qa/run_identity_legacy_drop_checks.php', $runChecks, $stamp);
writeFileWithBackup($root, 'tools/qa/scan_post_drop_legacy_pagegroup_residue.php', $scanResidue, $stamp);
writeFileWithBackup($root, 'database/sql/verify_mini_project_10_legacy_pagegroup_drop.sql', $verifySql, $stamp);

echo PHP_EOL;
echo 'Mini-Projekt 10 Legacy-PageGroup-Drop-Tools wurden installiert.' . PHP_EOL;
echo 'Bitte ausführen:' . PHP_EOL;
echo '  php tools\\drop_mini_project_10_legacy_pagegroup_db.php --dry-run' . PHP_EOL;
echo '  php tools\\drop_mini_project_10_legacy_pagegroup_db.php' . PHP_EOL;
echo '  php tools\\qa\\check_legacy_pagegroup_db_dropped.php' . PHP_EOL;
echo '  php tools\\qa\\run_identity_legacy_drop_checks.php' . PHP_EOL;
