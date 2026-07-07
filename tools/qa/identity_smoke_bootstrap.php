<?php

declare(strict_types=1);

function mp19_project_root(): string
{
    return dirname(__DIR__, 2);
}

/** @return array<string,string> */
function mp19_load_env(string $root): array
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
        if ($value !== '' && (($value[0] === '"' && str_ends_with($value, '"')) || ($value[0] === "'" && str_ends_with($value, "'")))) {
            $value = substr($value, 1, -1);
        }
        $env[$key] = $value;
    }

    return $env;
}

function mp19_env(array $env, array $keys, ?string $default = null): ?string
{
    foreach ($keys as $key) {
        if (array_key_exists($key, $env) && $env[$key] !== '') {
            return $env[$key];
        }
        $value = getenv($key);
        if (is_string($value) && $value !== '') {
            return $value;
        }
    }
    return $default;
}

function mp19_pdo(string $root): PDO
{
    $env = mp19_load_env($root);
    $host = mp19_env($env, ['DB_HOST'], '127.0.0.1') ?? '127.0.0.1';
    $port = mp19_env($env, ['DB_PORT'], '3306') ?? '3306';
    $db = mp19_env($env, ['DB_DATABASE', 'DB_NAME'], 'vdbs_sys') ?? 'vdbs_sys';
    $user = mp19_env($env, ['DB_USERNAME', 'DB_USER'], 'root') ?? 'root';
    $pass = mp19_env($env, ['DB_PASSWORD', 'DB_PASS'], '') ?? '';

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $db);
    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
    } catch (PDOException $exception) {
        if ($host !== 'localhost') {
            $dsn = sprintf('mysql:host=localhost;port=%s;dbname=%s;charset=utf8mb4', $port, $db);
            $pdo = new PDO($dsn, $user, $pass, $options);
        } else {
            throw $exception;
        }
    }

    $pdo->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
    return $pdo;
}

function mp19_table_exists(PDO $pdo, string $table): bool
{
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = :table');
    $stmt->execute(['table' => $table]);
    return (int) $stmt->fetchColumn() > 0;
}

function mp19_column_exists(PDO $pdo, string $table, string $column): bool
{
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = :table AND column_name = :column');
    $stmt->execute(['table' => $table, 'column' => $column]);
    return (int) $stmt->fetchColumn() > 0;
}

function mp19_count(PDO $pdo, string $sql): int
{
    return (int) $pdo->query($sql)->fetchColumn();
}

/** @param array<int,string> $errors */
function mp19_finish(array $errors, string $successMessage): void
{
    if ($errors !== []) {
        echo "Check fehlgeschlagen:" . PHP_EOL;
        foreach ($errors as $error) {
            echo ' - ' . $error . PHP_EOL;
        }
        exit(1);
    }
    echo $successMessage . PHP_EOL;
}

function mp19_run_command(array $command): int
{
    $display = implode(' ', array_map(static fn(string $p): string => '"' . $p . '"', $command));
    echo 'Running: ' . $display . PHP_EOL;

    $descriptorSpec = [
        0 => STDIN,
        1 => STDOUT,
        2 => STDERR,
    ];

    $process = proc_open($command, $descriptorSpec, $pipes);
    if (!is_resource($process)) {
        echo 'FAILED: Prozess konnte nicht gestartet werden.' . PHP_EOL;
        return 1;
    }

    $exitCode = proc_close($process);
    if ($exitCode !== 0) {
        echo 'FAILED: ' . $display . PHP_EOL;
    }
    return $exitCode;
}