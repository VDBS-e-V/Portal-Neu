<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$routesFile = $root . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'routes.php';
if (!is_file($routesFile)) {
    fwrite(STDERR, "config/routes.php nicht gefunden.\n");
    exit(1);
}

$content = file_get_contents($routesFile);
if ($content === false) {
    fwrite(STDERR, "config/routes.php konnte nicht gelesen werden.\n");
    exit(1);
}

$withoutLineComments = preg_replace('/^[ \t]*\/\/.*$/m', '', $content) ?? $content;
$errors = [];

foreach ([
    "'/verwaltung/personen'",
    "'/verwaltung/personen/{id}'",
    "'/verwaltung/personen/{id}/gruppen'",
    "'/administration/personen'",
    "'/administration/personen/{id}/gruppen'",
] as $needle) {
    if (!str_contains($withoutLineComments, $needle)) {
        $errors[] = 'Erwartete Personen-/Gruppen-Route fehlt: ' . $needle;
    }
}

foreach ([
    'PermissionGroupRepository',
    'PersonPermissionGroupRepository',
    'ids_permission_groups',
    'ids_user_permission_groups',
    'ids_person_permission_groups',
    'Verwaltung\\GruppenController',
    'Verwaltung\\BerechtigungenController',
] as $legacyNeedle) {
    if (str_contains($withoutLineComments, $legacyNeedle)) {
        $errors[] = 'Legacy-Verweis in aktiver routes.php gefunden: ' . $legacyNeedle;
    }
}

if (!preg_match('/\/administration\/personen[^\n]+(?:AdminPersonenGruppenController|PersonenGruppenController)::class/', $withoutLineComments)) {
    $errors[] = 'Administration-Personenroute zeigt nicht erkennbar auf PersonenGruppenController/AdminPersonenGruppenController.';
}

if ($errors !== []) {
    echo 'Personen-Routen-Check fehlgeschlagen:' . PHP_EOL;
    foreach ($errors as $error) {
        echo ' - ' . $error . PHP_EOL;
    }
    exit(1);
}

echo 'OK: Personen-Routen nutzen das neue Identity-/Administration-Modell ohne alte PermissionGroup-Routen.' . PHP_EOL;