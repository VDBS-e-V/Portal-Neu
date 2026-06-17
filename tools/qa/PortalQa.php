<?php

declare(strict_types=1);

final class PortalQa
{
    /**
     * @return array<string, string>
     */
    public static function loadEnv(string $root): array
    {
        $env = [];

        foreach ([$root . '/.env.local', $root . '/.env'] as $file) {
            if (!is_file($file)) {
                continue;
            }

            foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
                $line = trim($line);

                if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
                    continue;
                }

                [$key, $value] = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                $value = trim($value, "\"'");

                if ($key !== '') {
                    $env[$key] = $value;
                }
            }
        }

        foreach ($_ENV as $key => $value) {
            if (is_string($key) && is_scalar($value)) {
                $env[$key] = (string) $value;
            }
        }

        foreach ($_SERVER as $key => $value) {
            if (is_string($key) && is_scalar($value)) {
                $env[$key] = (string) $value;
            }
        }

        return $env;
    }

    public static function pdo(string $root): PDO
    {
        $env = self::loadEnv($root);

        $dsn = $env['DB_DSN'] ?? $env['DATABASE_DSN'] ?? '';

        if ($dsn === '') {
            $driver = $env['DB_CONNECTION'] ?? 'mysql';
            $host = $env['DB_HOST'] ?? '127.0.0.1';
            $port = $env['DB_PORT'] ?? '3306';
            $database = $env['DB_DATABASE'] ?? $env['MYSQL_DATABASE'] ?? '';

            if ($database === '') {
                throw new RuntimeException('DB_DATABASE oder DB_DSN fehlt.');
            }

            $dsn = sprintf('%s:host=%s;port=%s;dbname=%s;charset=utf8mb4', $driver, $host, $port, $database);
        }

        $user = $env['DB_USERNAME']
            ?? $env['DB_USER']
            ?? $env['MYSQL_USER']
            ?? 'root';

        $password = $env['DB_PASSWORD']
            ?? $env['MYSQL_PASSWORD']
            ?? $env['MYSQL_ROOT_PASSWORD']
            ?? '';

        return new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    public static function ok(string $message): void
    {
        echo '[OK]   ' . $message . PHP_EOL;
    }

    public static function fail(string $message): void
    {
        echo '[FAIL] ' . $message . PHP_EOL;
    }

    public static function info(string $message): void
    {
        echo '[INFO] ' . $message . PHP_EOL;
    }

    public static function assertTrue(bool $condition, string $message, array &$failures): void
    {
        if ($condition) {
            self::ok($message);
            return;
        }

        self::fail($message);
        $failures[] = $message;
    }

    public static function tableExists(PDO $pdo, string $table): bool
    {
        $stmt = $pdo->prepare(
            'SELECT COUNT(*)
             FROM information_schema.TABLES
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = :table_name'
        );
        $stmt->execute(['table_name' => $table]);

        return (int) $stmt->fetchColumn() > 0;
    }

    public static function columnExists(PDO $pdo, string $table, string $column): bool
    {
        $stmt = $pdo->prepare(
            'SELECT COUNT(*)
             FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = :table_name
               AND COLUMN_NAME = :column_name'
        );
        $stmt->execute([
            'table_name' => $table,
            'column_name' => $column,
        ]);

        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * @return array<int, string>
     */
    public static function readProjectFiles(string $root, array $directories, array $extensions): array
    {
        $files = [];

        foreach ($directories as $directory) {
            $path = $root . '/' . trim((string) $directory, '/');

            if (!is_dir($path)) {
                continue;
            }

            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));

            foreach ($iterator as $file) {
                if (!$file instanceof SplFileInfo || !$file->isFile()) {
                    continue;
                }

                $extension = strtolower($file->getExtension());

                if (!in_array($extension, $extensions, true)) {
                    continue;
                }

                $files[] = $file->getPathname();
            }
        }

        sort($files);

        return $files;
    }

    public static function projectRoot(): string
    {
        return dirname(__DIR__, 2);
    }
}
