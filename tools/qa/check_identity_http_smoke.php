<?php

declare(strict_types=1);

require_once __DIR__ . '/identity_smoke_bootstrap.php';

$args = $argv;
array_shift($args);
$baseUrl = null;
foreach ($args as $arg) {
    if (str_starts_with($arg, '--base-url=')) {
        $baseUrl = substr($arg, strlen('--base-url='));
    }
}

if ($baseUrl === null || $baseUrl === '') {
    $env = mp19_load_env(mp19_project_root());
    $baseUrl = mp19_env($env, ['SMOKE_BASE_URL'], null);
}

if ($baseUrl === null || trim($baseUrl) === '') {
    echo 'OK: HTTP-Smoke-Test übersprungen. Setze SMOKE_BASE_URL oder nutze --base-url=..., um Endpunkte zu prüfen.' . PHP_EOL;
    exit(0);
}

$baseUrl = rtrim($baseUrl, '/');
$paths = [
    '/',
    '/login',
    '/konto',
    '/konto/einstellungen',
    '/identity/me',
    '/administration',
    '/administration/gruppen',
    '/administration/permissions',
    '/administration/personen',
    '/verwaltung',
    '/verwaltung/personen',
    '/verwaltung/audit',
    '/verwaltung/einladungen',
    '/verwaltung/datenschutz',
];

$errors = [];
foreach ($paths as $path) {
    $url = $baseUrl . $path;
    $headers = [];
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'ignore_errors' => true,
            'timeout' => 10,
            'header' => "User-Agent: VDBS Identity Smoke Test\r\n",
        ],
    ]);

    $body = @file_get_contents($url, false, $context);
    $headers = $http_response_header ?? [];
    $status = 0;
    foreach ($headers as $header) {
        if (preg_match('/^HTTP\/\S+\s+(\d{3})/', $header, $matches)) {
            $status = (int) $matches[1];
            break;
        }
    }

    if ($body === false && $status === 0) {
        $errors[] = $path . ': keine Antwort von ' . $url;
        continue;
    }

    echo $path . ' -> HTTP ' . $status . PHP_EOL;
    if ($status >= 500 || $status === 0) {
        $errors[] = $path . ': unerwarteter HTTP-Status ' . $status;
    }
}

if ($errors !== []) {
    echo 'HTTP-Smoke-Test fehlgeschlagen:' . PHP_EOL;
    foreach ($errors as $error) {
        echo ' - ' . $error . PHP_EOL;
    }
    exit(1);
}

echo 'OK: HTTP-Smoke-Test bestanden.' . PHP_EOL;