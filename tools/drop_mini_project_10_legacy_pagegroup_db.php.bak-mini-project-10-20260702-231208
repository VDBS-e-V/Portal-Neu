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