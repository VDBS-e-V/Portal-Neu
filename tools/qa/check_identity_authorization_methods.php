<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$file = $root . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Security' . DIRECTORY_SEPARATOR . 'AuthorizationService.php';

if (!is_file($file)) {
    fwrite(STDERR, "AuthorizationService.php fehlt.\n");
    exit(1);
}

$content = file_get_contents($file);
if ($content === false) {
    fwrite(STDERR, "AuthorizationService.php konnte nicht gelesen werden.\n");
    exit(1);
}

$requiredFragments = [
    'class AuthorizationService',
];

$missing = [];
foreach ($requiredFragments as $fragment) {
    if (!str_contains($content, $fragment)) {
        $missing[] = $fragment;
    }
}

if ($missing !== []) {
    echo "AuthorizationService-Struktur wirkt beschädigt. Fehlend:\n";
    foreach ($missing as $fragment) {
        echo ' - ' . $fragment . PHP_EOL;
    }
    exit(1);
}

$hasPermissionApi = false;
foreach (['requirePermission', 'hasPermission', 'can(', 'canPermission', 'hasAnyPermission'] as $fragment) {
    if (str_contains($content, $fragment)) {
        $hasPermissionApi = true;
        break;
    }
}

if (!$hasPermissionApi) {
    echo "WARN: Keine bekannte Permission-API-Fragmente gefunden. Das ist kein harter Fehler, bitte bei Browser-Fehlern prüfen.\n";
} else {
    echo "OK: AuthorizationService enthält weiterhin Permission-API-Fragmente.\n";
}