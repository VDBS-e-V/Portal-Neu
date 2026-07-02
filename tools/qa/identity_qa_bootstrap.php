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
