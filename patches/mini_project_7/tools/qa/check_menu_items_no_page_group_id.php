<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);

function load_env_file(string $file): array
{
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

$env = array_merge($_ENV, load_env_file($root . DIRECTORY_SEPARATOR . '.env'));

$host = $env['DB_HOST'] ?? '127.0.0.1';
$port = $env['DB_PORT'] ?? '3306';
$name = $env['DB_NAME'] ?? ($env['DB_DATABASE'] ?? 'vdbs_sys');
$user = $env['DB_USER'] ?? ($env['DB_USERNAME'] ?? 'root');
$pass = $env['DB_PASS'] ?? ($env['DB_PASSWORD'] ?? '');

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

$hostsToTry = [$host];
if ($host === '127.0.0.1') {
    $hostsToTry[] = 'localhost';
} elseif ($host === 'localhost') {
    $hostsToTry[] = '127.0.0.1';
}

$pdo = null;
$lastError = null;
foreach (array_unique($hostsToTry) as $candidateHost) {
    try {
        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $candidateHost, $port, $name);
        $pdo = new PDO($dsn, $user, $pass, $options);
        break;
    } catch (Throwable $e) {
        $lastError = $e;
    }
}

if (!$pdo instanceof PDO) {
    fwrite(STDERR, 'DB-Verbindung fehlgeschlagen: ' . ($lastError ? $lastError->getMessage() : 'unbekannt') . PHP_EOL);
    exit(1);
}

$table = $pdo->query("SHOW TABLES LIKE 'pt_menu_items'")->fetchColumn();
if (!$table) {
    echo "OK: pt_menu_items existiert nicht, PageGroup-Menülink-Check übersprungen.\n";
    exit(0);
}

$columnStmt = $pdo->query("SHOW COLUMNS FROM pt_menu_items LIKE 'page_group_id'");
$column = $columnStmt ? $columnStmt->fetch() : false;
if (!$column) {
    echo "OK: pt_menu_items.page_group_id existiert nicht.\n";
    exit(0);
}

$count = (int) $pdo->query('SELECT COUNT(*) FROM pt_menu_items WHERE page_group_id IS NOT NULL')->fetchColumn();
if ($count > 0) {
    echo "FEHLER: {$count} pt_menu_items enthalten noch page_group_id.\n";
    echo "Bitte ausführen: php bin\\console seed\n";
    exit(1);
}

echo "OK: pt_menu_items enthält keine page_group_id-Verknüpfungen mehr.\n";
