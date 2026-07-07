<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);

function load_env_file(string $path): array
{
    if (!is_file($path)) {
        return [];
    }

    $env = [];
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
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

function env_value(array $env, array $keys, ?string $default = null): ?string
{
    foreach ($keys as $key) {
        $value = getenv($key);
        if ($value !== false && $value !== '') {
            return $value;
        }
        if (isset($env[$key]) && $env[$key] !== '') {
            return $env[$key];
        }
    }

    return $default;
}

function connect_db(array $env): PDO
{
    $host = env_value($env, ['DB_HOST'], '127.0.0.1') ?: '127.0.0.1';
    $port = env_value($env, ['DB_PORT'], '3306') ?: '3306';
    $database = env_value($env, ['DB_NAME', 'DB_DATABASE'], 'vdbs_sys') ?: 'vdbs_sys';
    $user = env_value($env, ['DB_USER', 'DB_USERNAME'], 'root') ?: 'root';
    $password = env_value($env, ['DB_PASS', 'DB_PASSWORD'], '') ?? '';

    $hostsToTry = array_values(array_unique([$host, $host === '127.0.0.1' ? 'localhost' : '127.0.0.1']));
    $last = null;

    foreach ($hostsToTry as $candidateHost) {
        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $candidateHost, $port, $database);
        try {
            return new PDO($dsn, $user, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $exception) {
            $last = $exception;
        }
    }

    throw $last ?? new RuntimeException('Datenbankverbindung fehlgeschlagen.');
}

function table_exists(PDO $pdo, string $table): bool
{
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table_name');
    $stmt->execute(['table_name' => $table]);
    return (int) $stmt->fetchColumn() > 0;
}

function column_exists(PDO $pdo, string $table, string $column): bool
{
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table_name AND COLUMN_NAME = :column_name');
    $stmt->execute(['table_name' => $table, 'column_name' => $column]);
    return (int) $stmt->fetchColumn() > 0;
}

$env = load_env_file($root . DIRECTORY_SEPARATOR . '.env');
$pdo = connect_db($env);

$errors = [];

if (!table_exists($pdo, 'ids_legacy_deprecations')) {
    $errors[] = 'Tabelle ids_legacy_deprecations fehlt. Bitte php bin\\console seed ausführen.';
} else {
    $requiredObjects = [
        'pt_page_groups',
        'pt_permission_group_page_group_access',
        'pt_menu_items.page_group_id',
    ];

    $stmt = $pdo->prepare('SELECT COUNT(*) FROM ids_legacy_deprecations WHERE object_name = :object_name');
    foreach ($requiredObjects as $objectName) {
        $stmt->execute(['object_name' => $objectName]);
        if ((int) $stmt->fetchColumn() <= 0) {
            $errors[] = 'Deprecation-Marker fehlt für: ' . $objectName;
        }
    }
}

if (table_exists($pdo, 'pt_menu_items') && column_exists($pdo, 'pt_menu_items', 'page_group_id')) {
    $count = (int) $pdo->query('SELECT COUNT(*) FROM pt_menu_items WHERE page_group_id IS NOT NULL')->fetchColumn();
    if ($count > 0) {
        $errors[] = 'pt_menu_items enthält noch ' . $count . ' page_group_id-Verknüpfung(en). Bitte Seed erneut ausführen.';
    }
}

if ($errors !== []) {
    echo 'Legacy-DB-Deprecation-Check fehlgeschlagen:' . PHP_EOL;
    foreach ($errors as $error) {
        echo ' - ' . $error . PHP_EOL;
    }
    exit(1);
}

$legacyCounts = [];
foreach (['pt_page_groups', 'pt_permission_group_page_group_access'] as $table) {
    if (table_exists($pdo, $table)) {
        $legacyCounts[$table] = (int) $pdo->query('SELECT COUNT(*) FROM `' . $table . '`')->fetchColumn();
    }
}

if ($legacyCounts !== []) {
    echo 'Hinweis: Legacy-Tabellen existieren noch und sind als deprecated markiert:' . PHP_EOL;
    foreach ($legacyCounts as $table => $count) {
        echo ' - ' . $table . ': ' . $count . ' Datensatz/Datensätze' . PHP_EOL;
    }
}

echo 'OK: Legacy-DB-Deprecation-Marker existieren; Menü-Links auf page_group_id sind leer.' . PHP_EOL;
