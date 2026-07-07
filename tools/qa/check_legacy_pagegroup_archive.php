<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$errors = [];
$warnings = [];

function projectPath(string $relative): string
{
    global $root;
    return $root . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relative);
}

function isActivePhpLine(string $line): bool
{
    $trim = trim($line);

    if ($trim === '') {
        return false;
    }

    if (str_starts_with($trim, '//') || str_starts_with($trim, '#')) {
        return false;
    }

    if (str_starts_with($trim, '/*') || str_starts_with($trim, '*') || str_starts_with($trim, '*/')) {
        return false;
    }

    return true;
}

$filesThatMustBeArchived = [
    'src/Http/Controller/Verwaltung/GruppenController.php',
    'src/Http/Controller/Verwaltung/BerechtigungenController.php',
    'src/Repository/PageGroupRepository.php',
    'src/Repository/PageGroupAccessRepository.php',
    'resources/views/pages/verwaltung/berechtigungen/index.php',
    'resources/views/pages/verwaltung/berechtigungen/group.php',
    'resources/views/pages/verwaltung/berechtigungen/page_groups.php',
];

foreach ($filesThatMustBeArchived as $relative) {
    if (is_file(projectPath($relative))) {
        $errors[] = $relative . ': Datei liegt noch im aktiven Projekt und wurde nicht archiviert.';
    }
}

$viewDirectory = projectPath('resources/views/pages/verwaltung/berechtigungen');
if (is_dir($viewDirectory)) {
    $entries = array_values(array_filter(scandir($viewDirectory) ?: [], static fn (string $entry): bool => $entry !== '.' && $entry !== '..'));
    if ($entries !== []) {
        $errors[] = 'resources/views/pages/verwaltung/berechtigungen: Verzeichnis enthält noch aktive Dateien.';
    }
}

$archiveRoot = projectPath('var/archive');
$archives = [];
if (is_dir($archiveRoot)) {
    foreach (scandir($archiveRoot) ?: [] as $entry) {
        if ($entry === '.' || $entry === '..') {
            continue;
        }
        if (str_starts_with($entry, 'mini-project-7-legacy-pagegroup-')) {
            $manifest = $archiveRoot . DIRECTORY_SEPARATOR . $entry . DIRECTORY_SEPARATOR . 'manifest.json';
            if (is_file($manifest)) {
                $archives[] = $manifest;
            }
        }
    }
}

if ($archives === []) {
    $warnings[] = 'Kein Mini-Projekt-7-Archivmanifest gefunden. Das ist nur kritisch, wenn die Dateien nicht absichtlich vorher gelöscht wurden.';
}

$routesFile = projectPath('config/routes.php');
if (!is_file($routesFile)) {
    $errors[] = 'config/routes.php fehlt.';
} else {
    $lines = preg_split('/\R/', (string) file_get_contents($routesFile));

    foreach ($lines as $index => $line) {
        $lineNo = $index + 1;
        if (!isActivePhpLine($line)) {
            continue;
        }

        if (preg_match('/^\s*use\s+App\\\\Http\\\\Controller\\\\Verwaltung\\\\(GruppenController|BerechtigungenController)\s*;/', $line)) {
            $errors[] = 'config/routes.php:' . $lineNo . ': alter PageGroup-Controller wird noch importiert: ' . trim($line);
            continue;
        }

        if (!str_contains($line, 'new Route(')) {
            continue;
        }

        // Nur die alten, archivierten Controller sind verboten. Neue Admin-Aliase wie
        // AdminGruppenController und AdminPersonenGruppenController sind korrekt und
        // dürfen nicht als PageGroup-Reste gewertet werden.
        if (preg_match('/\b(GruppenController|BerechtigungenController)::class\b/', $line)) {
            $errors[] = 'config/routes.php:' . $lineNo . ': Route zeigt noch auf archivierten PageGroup-Controller: ' . trim($line);
        }

        if (str_contains($line, '/verwaltung/berechtigungen/page-groups')) {
            $errors[] = 'config/routes.php:' . $lineNo . ': alte PageGroup-URL ist noch aktiv: ' . trim($line);
        }
    }
}

if ($warnings !== []) {
    echo "Hinweise:\n";
    foreach ($warnings as $warning) {
        echo ' - ' . $warning . PHP_EOL;
    }
}

if ($errors !== []) {
    echo "Legacy-PageGroup-Archiv-Check fehlgeschlagen:\n";
    foreach ($errors as $error) {
        echo ' - ' . $error . PHP_EOL;
    }
    exit(1);
}

echo "OK: Legacy-PageGroup-Dateien sind archiviert; neue Administration-/Personengruppen-Routen sind erlaubt.\n";