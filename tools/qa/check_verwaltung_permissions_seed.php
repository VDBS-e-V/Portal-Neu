<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
require $root . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

/**
 * Lädt einfache KEY=VALUE Einträge aus .env in getenv(), falls sie dort noch nicht gesetzt sind.
 */
function loadDotEnvForQa(string $root): void
{
    $envFile = $root . DIRECTORY_SEPARATOR . '.env';
    if (!is_file($envFile)) {
        return;
    }

    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return;
    }

    foreach ($lines as $line) {
        $trim = trim($line);
        if ($trim === '' || str_starts_with($trim, '#') || !str_contains($trim, '=')) {
            continue;
        }

        [$key, $value] = explode('=', $trim, 2);
        $key = trim($key);
        $value = trim($value);
        $value = trim($value, "\"'");

        if ($key !== '' && getenv($key) === false) {
            putenv($key . '=' . $value);
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }
}

loadDotEnvForQa($root);

$config = $root . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'app.php';
if (is_file($config)) {
    require $config;
}

$host = getenv('DB_HOST') ?: '127.0.0.1';
$port = getenv('DB_PORT') ?: '3306';
$name = getenv('DB_NAME') ?: getenv('DB_DATABASE') ?: 'vdbs';
$user = getenv('DB_USER') ?: getenv('DB_USERNAME') ?: 'root';
$pass = getenv('DB_PASS') ?: getenv('DB_PASSWORD') ?: '';
$charset = getenv('DB_CHARSET') ?: 'utf8mb4';

$dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $host, $port, $name, $charset);

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
    ]);
} catch (PDOException $exception) {
    if ($host === '127.0.0.1') {
        $fallbackDsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', 'localhost', $port, $name, $charset);
        try {
            $pdo = new PDO($fallbackDsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
            ]);
        } catch (PDOException $fallbackException) {
            fwrite(STDERR, "DB-Verbindung fehlgeschlagen. Prüfe DB_HOST, DB_PORT, DB_NAME, DB_USER und DB_PASS in .env.\n");
            fwrite(STDERR, "Original: " . $exception->getMessage() . "\n");
            fwrite(STDERR, "Fallback: " . $fallbackException->getMessage() . "\n");
            exit(1);
        }
    } else {
        fwrite(STDERR, "DB-Verbindung fehlgeschlagen. Prüfe DB_HOST, DB_PORT, DB_NAME, DB_USER und DB_PASS in .env.\n");
        fwrite(STDERR, $exception->getMessage() . "\n");
        exit(1);
    }
}

$required = [
    'portal.verwaltung.dashboard.view',
    'portal.verwaltung.personen.view',
    'portal.verwaltung.audit.view',
    'portal.verwaltung.entity-audit.view',
    'portal.verwaltung.datenschutz.view',
    'portal.verwaltung.einladungen.view',
    'portal.verwaltung.schulverzeichnis.view',
];

$missing = [];
$stmt = $pdo->prepare('SELECT id FROM ids_permissions WHERE key_name = ? AND is_active = 1 LIMIT 1');
foreach ($required as $key) {
    $stmt->execute([$key]);
    if (!$stmt->fetch()) {
        $missing[] = $key;
    }
}

if ($missing !== []) {
    echo "Fehlende Verwaltung-Permissions:\n";
    foreach ($missing as $key) {
        echo " - {$key}\n";
    }
    exit(1);
}

echo "OK: Verwaltung-Runtime-Permissions existieren.\n";
