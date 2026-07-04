<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);

function mp18_2_load_env(string $root): array
{
    $env = [];
    $file = $root . DIRECTORY_SEPARATOR . '.env';
    if (!is_file($file)) {
        return $env;
    }

    foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
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

function mp18_2_pdo(string $root): PDO
{
    $env = mp18_2_load_env($root);

    $host = $env['DB_HOST'] ?? '127.0.0.1';
    $port = $env['DB_PORT'] ?? '3306';
    $db = $env['DB_DATABASE'] ?? ($env['DB_NAME'] ?? 'vdbs_sys');
    $user = $env['DB_USERNAME'] ?? ($env['DB_USER'] ?? 'root');
    $pass = $env['DB_PASSWORD'] ?? ($env['DB_PASS'] ?? '');

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci',
    ];

    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $db);
    try {
        return new PDO($dsn, $user, $pass, $options);
    } catch (PDOException $e) {
        if ($host !== 'localhost') {
            $fallback = sprintf('mysql:host=localhost;port=%s;dbname=%s;charset=utf8mb4', $port, $db);
            return new PDO($fallback, $user, $pass, $options);
        }
        throw $e;
    }
}

function mp18_2_table_exists(PDO $pdo, string $table): bool
{
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table');
    $stmt->execute(['table' => $table]);
    return (int) $stmt->fetchColumn() > 0;
}

function mp18_2_column_exists(PDO $pdo, string $table, string $column): bool
{
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table AND COLUMN_NAME = :column');
    $stmt->execute(['table' => $table, 'column' => $column]);
    return (int) $stmt->fetchColumn() > 0;
}

$pdo = mp18_2_pdo($root);
$errors = [];

if (!mp18_2_table_exists($pdo, 'pt_menu_items')) {
    $errors[] = 'Tabelle pt_menu_items fehlt.';
} else {
    if (!mp18_2_column_exists($pdo, 'pt_menu_items', 'permission_key')) {
        $errors[] = 'pt_menu_items.permission_key fehlt.';
    }
    if (mp18_2_column_exists($pdo, 'pt_menu_items', 'page_group_id')) {
        $errors[] = 'Legacy-Spalte pt_menu_items.page_group_id existiert noch.';
    }
}

if (mp18_2_table_exists($pdo, 'pt_menu_items') && mp18_2_column_exists($pdo, 'pt_menu_items', 'permission_key') && mp18_2_table_exists($pdo, 'ids_permissions')) {
    $permissionKeyColumn = mp18_2_column_exists($pdo, 'ids_permissions', 'key_name') ? 'key_name' : (mp18_2_column_exists($pdo, 'ids_permissions', 'permission_key') ? 'permission_key' : null);
    if ($permissionKeyColumn !== null) {
        $sql = sprintf(
            "SELECT COUNT(*)\n             FROM pt_menu_items mi\n             LEFT JOIN ids_permissions p ON p.%s COLLATE utf8mb4_unicode_ci = mi.permission_key COLLATE utf8mb4_unicode_ci\n             WHERE mi.permission_key IS NOT NULL\n               AND mi.permission_key <> ''\n               AND p.id IS NULL",
            $permissionKeyColumn
        );
        $missing = (int) $pdo->query($sql)->fetchColumn();
        if ($missing > 0) {
            $errors[] = 'pt_menu_items enthält permission_key-Werte ohne passende ids_permissions-Einträge: ' . $missing;
        }
    }
}

if ($errors !== []) {
    echo "Navigation-Permission-Modell-Check fehlgeschlagen:\n";
    foreach ($errors as $error) {
        echo ' - ' . $error . "\n";
    }
    exit(1);
}

echo "OK: Navigation nutzt pt_menu_items.permission_key ohne Legacy-Spalten.\n";