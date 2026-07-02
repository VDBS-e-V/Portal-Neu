<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
require $root . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

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

$pdo = new PDO(
    sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $host, $port, $name, $charset),
    $user,
    $pass,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
);

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
