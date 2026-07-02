<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$targets = [
    'src/Repository/PersonRepository.php',
    'src/Repository/PersonErasureRepository.php',
    'src/Repository/EntityAuditRepository.php',
    'src/Repository/VerwaltungStatsRepository.php',
    'src/Http/Controller/Verwaltung/PersonenController.php',
    'src/Http/Controller/Verwaltung/EntityAuditController.php',
    'config/services.php',
];
$legacyNeedles = [
    'ids_permission_groups',
    'ids_user_permission_groups',
    'ids_person_permission_groups',
    'PermissionGroupRepository',
    'PersonPermissionGroupRepository',
    'permission_group_id',
];
$errors = [];

foreach ($targets as $relative) {
    $file = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative);
    if (!is_file($file)) {
        continue;
    }
    $content = file_get_contents($file);
    if ($content === false) {
        $errors[] = 'Datei konnte nicht gelesen werden: ' . $relative;
        continue;
    }
    foreach ($legacyNeedles as $needle) {
        if (str_contains($content, $needle)) {
            $errors[] = $relative . ': Legacy-Verweis gefunden: ' . $needle;
        }
    }
}

$personRepository = $root . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Repository' . DIRECTORY_SEPARATOR . 'PersonRepository.php';
if (is_file($personRepository)) {
    $content = file_get_contents($personRepository) ?: '';
    foreach (['ids_persons', 'ids_subject_groups', 'ids_groups'] as $needle) {
        if (!str_contains($content, $needle)) {
            $errors[] = 'PersonRepository nutzt erwarteten neuen Identity-Baustein nicht sichtbar: ' . $needle;
        }
    }
}

if ($errors !== []) {
    echo 'Personen-Code-Identity-Check fehlgeschlagen:' . PHP_EOL;
    foreach ($errors as $error) {
        echo ' - ' . $error . PHP_EOL;
    }
    exit(1);
}

echo 'OK: Personen-Code ist frei von Legacy-PermissionGroup-Verweisen und nutzt das neue Identity-Modell.' . PHP_EOL;