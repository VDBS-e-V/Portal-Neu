<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$dryRun = in_array('--dry-run', $argv, true);
$force = in_array('--force', $argv, true);

$legacyTables = [
    'ids_user_permission_groups',
    'ids_person_permission_groups',
    'ids_permission_groups',
];

function readEnvFile(string $root): array
{
    $envFile = $root . DIRECTORY_SEPARATOR . '.env';
    $env = [];

    if (!is_file($envFile)) {
        return $env;
    }

    $lines = file($envFile, FILE_IGNORE_NEW_LINES);
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
        if ($value !== '' && (($value[0] === '"' && str_ends_with($value, '"')) || ($value[0] === "'" && str_ends_with($value, "'")))) {
            $value = substr($value, 1, -1);
        }
        $env[$key] = $value;
    }

    return $env;
}

function envValue(array $env, array $keys, string $default = ''): string
{
    foreach ($keys as $key) {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
        if ($value !== false && $value !== null && $value !== '') {
            return (string) $value;
        }
        if (array_key_exists($key, $env) && $env[$key] !== '') {
            return (string) $env[$key];
        }
    }

    return $default;
}

function connectPdo(string $root): PDO
{
    $env = readEnvFile($root);
    $host = envValue($env, ['DB_HOST'], '127.0.0.1');
    $port = envValue($env, ['DB_PORT'], '3306');
    $db = envValue($env, ['DB_DATABASE', 'DB_NAME'], 'vdbs_sys');
    $user = envValue($env, ['DB_USERNAME', 'DB_USER'], 'root');
    $pass = envValue($env, ['DB_PASSWORD', 'DB_PASS'], '');

    $hosts = [$host];
    if ($host === '127.0.0.1') {
        $hosts[] = 'localhost';
    } elseif ($host === 'localhost') {
        $hosts[] = '127.0.0.1';
    }

    $last = null;
    foreach (array_unique($hosts) as $tryHost) {
        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $tryHost, $port, $db);
        try {
            return new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $exception) {
            $last = $exception;
        }
    }

    throw $last ?? new RuntimeException('DB-Verbindung fehlgeschlagen.');
}

function tableExists(PDO $pdo, string $table): bool
{
    $stmt = $pdo->prepare('SHOW TABLES LIKE :table_name');
    $stmt->execute(['table_name' => $table]);
    return (bool) $stmt->fetchColumn();
}

function countTable(PDO $pdo, string $table): int
{
    if (!tableExists($pdo, $table)) {
        return 0;
    }
    return (int) $pdo->query('SELECT COUNT(*) FROM `' . str_replace('`', '``', $table) . '`')->fetchColumn();
}

function fetchRows(PDO $pdo, string $table): array
{
    if (!tableExists($pdo, $table)) {
        return [];
    }
    return $pdo->query('SELECT * FROM `' . str_replace('`', '``', $table) . '`')->fetchAll();
}

function findExternalForeignKeys(PDO $pdo, array $legacyTables): array
{
    $db = (string) $pdo->query('SELECT DATABASE()')->fetchColumn();
    $placeholders = [];
    $params = ['schema_name' => $db];
    foreach ($legacyTables as $index => $table) {
        $key = 'table_' . $index;
        $placeholders[] = ':' . $key;
        $params[$key] = $table;
    }

    $sql = '
        SELECT TABLE_NAME, CONSTRAINT_NAME, REFERENCED_TABLE_NAME
        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
        WHERE TABLE_SCHEMA = :schema_name
          AND REFERENCED_TABLE_NAME IN (' . implode(', ', $placeholders) . ')
        ORDER BY TABLE_NAME, CONSTRAINT_NAME
    ';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $rows = $stmt->fetchAll();
    return array_values(array_filter($rows, static function (array $row) use ($legacyTables): bool {
        return !in_array($row['TABLE_NAME'], $legacyTables, true);
    }));
}

function runGuard(string $root, string $relativeScript): void
{
    $script = $root . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativeScript);
    if (!is_file($script)) {
        echo "Hinweis: Guard nicht gefunden, übersprungen: {$relativeScript}\n";
        return;
    }

    $php = PHP_BINARY;
    $cmd = '"' . $php . '" "' . $script . '"';
    passthru($cmd, $exitCode);
    if ($exitCode !== 0) {
        throw new RuntimeException("Guard fehlgeschlagen: {$relativeScript}");
    }
}

function createArchive(string $root, PDO $pdo, array $legacyTables): string
{
    $stamp = date('Ymd-His');
    $dir = $root . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'archive' . DIRECTORY_SEPARATOR . 'mini-project-14-legacy-identity-groups-db-' . $stamp;
    if (!is_dir($dir) && !mkdir($dir, 0777, true) && !is_dir($dir)) {
        throw new RuntimeException('Archivverzeichnis konnte nicht erstellt werden: ' . $dir);
    }

    $manifest = [
        'created_at' => date(DATE_ATOM),
        'mini_project' => '14',
        'description' => 'JSON-Sicherheitsarchiv vor Drop alter Identity-Gruppentabellen.',
        'tables' => [],
    ];

    foreach ($legacyTables as $table) {
        $rows = fetchRows($pdo, $table);
        $file = $dir . DIRECTORY_SEPARATOR . $table . '.json';
        file_put_contents($file, json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        $manifest['tables'][$table] = [
            'exists' => tableExists($pdo, $table),
            'rows' => count($rows),
            'file' => basename($file),
        ];
    }

    file_put_contents($dir . DIRECTORY_SEPARATOR . 'manifest.json', json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    return $dir;
}

try {
    echo "Mini-Projekt 14: Legacy-Identity-Gruppentabellen Drop" . ($dryRun ? " (Dry-Run)" : "") . "\n\n";

    if (!$force) {
        runGuard($root, 'tools/qa/check_no_legacy_identity_group_seed_writes.php');
        runGuard($root, 'tools/qa/check_no_productive_legacy_identity_permission_group_usage.php');
    } else {
        echo "--force gesetzt: Produktiv-/Seed-Guards werden übersprungen.\n";
    }

    $pdo = connectPdo($root);

    echo "\nAktueller DB-Zustand:\n";
    foreach ($legacyTables as $table) {
        $exists = tableExists($pdo, $table);
        $count = $exists ? countTable($pdo, $table) : 0;
        echo sprintf(' - %s: %s, %d Zeilen', $table, $exists ? 'existiert' : 'fehlt', $count) . "\n";
    }

    $externalFks = findExternalForeignKeys($pdo, $legacyTables);
    if ($externalFks !== []) {
        echo "\nExterne Foreign Keys auf Legacy-Tabellen gefunden:\n";
        foreach ($externalFks as $fk) {
            echo sprintf(
                ' - %s.%s -> %s',
                $fk['TABLE_NAME'],
                $fk['CONSTRAINT_NAME'],
                $fk['REFERENCED_TABLE_NAME']
            ) . "\n";
        }
        throw new RuntimeException('Drop abgebrochen, weil externe Foreign Keys existieren.');
    }

    if ($dryRun) {
        echo "\nDry-Run: Es wurde nichts geändert.\n";
        echo "Zum echten Drop ausführen:\n  php tools\\drop_mini_project_14_legacy_identity_group_db.php\n";
        exit(0);
    }

    $archiveDir = createArchive($root, $pdo, $legacyTables);
    echo "\nArchiv erstellt: " . $archiveDir . "\n";

    $pdo->exec('SET FOREIGN_KEY_CHECKS=0');
    try {
        foreach ($legacyTables as $table) {
            $pdo->exec('DROP TABLE IF EXISTS `' . str_replace('`', '``', $table) . '`');
            echo "Gedroppt: {$table}\n";
        }
    } finally {
        $pdo->exec('SET FOREIGN_KEY_CHECKS=1');
    }

    echo "\nOK: Legacy-Identity-Gruppentabellen wurden entfernt.\n";
} catch (Throwable $throwable) {
    fwrite(STDERR, "FEHLER: " . $throwable->getMessage() . "\n");
    exit(1);
}
