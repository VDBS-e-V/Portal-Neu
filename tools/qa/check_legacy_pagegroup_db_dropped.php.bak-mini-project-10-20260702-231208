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