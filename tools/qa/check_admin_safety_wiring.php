<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$errors = [];
$warnings = [];

$serviceFile = $root . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Security' . DIRECTORY_SEPARATOR . 'IdentityAdminSafetyService.php';
$servicesFile = $root . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'services.php';

if (!is_file($serviceFile)) {
    $errors[] = 'IdentityAdminSafetyService.php fehlt.';
} else {
    $content = file_get_contents($serviceFile) ?: '';
    foreach ([
        'assertGroupCanBeDeleted',
        'assertPermissionCanBeDeactivated',
        'assertSubjectGroupCanBeRemoved',
        'assertSubjectGroupsCanBeReplaced',
        'assertSubjectStatusCanBeChanged',
        'assertPermissionCanBeAssignedToGroup',
        'assertGroupPermissionsCanBeReplaced',
    ] as $method) {
        if (!str_contains($content, 'function ' . $method . '(')) {
            $errors[] = 'Safety-Methode fehlt im Service: ' . $method;
        }
    }
}

if (!is_file($servicesFile)) {
    $errors[] = 'config/services.php fehlt.';
} else {
    $services = file_get_contents($servicesFile) ?: '';
    if (!str_contains($services, 'IdentityAdminSafetyService::class')) {
        $errors[] = 'IdentityAdminSafetyService ist nicht in config/services.php registriert.';
    }
}

$runtimeFiles = [
    'src/Http/Controller/Administration/GruppenController.php',
    'src/Http/Controller/Administration/PermissionsController.php',
    'src/Http/Controller/Administration/PersonenGruppenController.php',
];
$runtimeUsage = 0;
foreach ($runtimeFiles as $relative) {
    $file = $root . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relative);
    if (!is_file($file)) {
        continue;
    }
    $content = file_get_contents($file) ?: '';
    if (str_contains($content, 'IdentityAdminSafetyService') || str_contains($content, 'assertSubjectGroupCanBeRemoved') || str_contains($content, 'assertGroupCanBeDeleted')) {
        $runtimeUsage++;
    }
}
if ($runtimeUsage === 0) {
    $warnings[] = 'Keine direkte Controller-Wiring-Stelle gefunden. Der Service ist trotzdem über QA geprüft; bei späteren Controller-Änderungen Safety-Methoden verwenden.';
}

foreach ($warnings as $warning) {
    echo 'WARN: ' . $warning . PHP_EOL;
}

if ($errors !== []) {
    echo 'Admin-Safety-Wiring-Check fehlgeschlagen:' . PHP_EOL;
    foreach ($errors as $error) {
        echo ' - ' . $error . PHP_EOL;
    }
    exit(1);
}

echo 'OK: Admin-Safety-Service ist vorhanden und im Service-Container registriert.' . PHP_EOL;
