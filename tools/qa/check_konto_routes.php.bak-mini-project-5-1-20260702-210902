<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$routesFile = $root . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'routes.php';

if (!is_file($routesFile)) {
    fwrite(STDERR, "FEHLER: config/routes.php nicht gefunden.\n");
    exit(1);
}

$code = (string) file_get_contents($routesFile);
$errors = [];

if (!str_contains($code, 'use App\\Http\\Controller\\UserAccountController as KontoController;')) {
    $errors[] = 'KontoController-use-Alias fehlt.';
}

$required = [
    "new Route('GET', '/konto', KontoController::class, 'profile')",
    "new Route('POST', '/konto', KontoController::class, 'updateProfile')",
    "new Route('GET', '/konto/einstellungen', KontoController::class, 'settings')",
    "new Route('POST', '/konto/einstellungen', KontoController::class, 'updateSettings')",
    "new Route('POST', '/konto/passwort', KontoController::class, 'updatePassword')",
];

foreach ($required as $needle) {
    if (!str_contains($code, $needle)) {
        $errors[] = 'Konto-Route fehlt: ' . $needle;
    }
}

$lines = preg_split('/\R/', $code) ?: [];
foreach ($lines as $lineNumber => $line) {
    $trimmed = ltrim($line);
    if (str_starts_with($trimmed, '//')) {
        continue;
    }
    if (str_contains($line, 'new Route(') && preg_match("~['\"]\/konto(?:['\"/])~", $line) === 1) {
        if (!str_contains($line, 'KontoController::class')) {
            $errors[] = 'Aktive /konto-Route nutzt nicht KontoController::class in Zeile ' . ($lineNumber + 1) . ': ' . trim($line);
        }
    }
}

if ($errors !== []) {
    fwrite(STDERR, "FEHLER: Konto-Routen sind nicht sauber konfiguriert.\n");
    foreach ($errors as $error) {
        fwrite(STDERR, ' - ' . $error . "\n");
    }
    exit(1);
}

fwrite(STDOUT, "OK: Konto-Routen zeigen auf UserAccountController/KontoController.\n");
