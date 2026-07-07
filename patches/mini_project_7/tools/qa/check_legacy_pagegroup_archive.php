<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);

function rel(string $path): string
{
    return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
}

function is_comment_or_blank(string $line): bool
{
    $trim = trim($line);
    return $trim === '' || str_starts_with($trim, '//') || str_starts_with($trim, '#') || str_starts_with($trim, '*') || str_starts_with($trim, '|');
}

$errors = [];

$shouldBeArchived = [
    'src/Http/Controller/Verwaltung/GruppenController.php',
    'src/Http/Controller/Verwaltung/BerechtigungenController.php',
    'src/Repository/PageGroupRepository.php',
    'src/Repository/PageGroupAccessRepository.php',
    'resources/views/pages/verwaltung/berechtigungen/index.php',
    'resources/views/pages/verwaltung/berechtigungen/group.php',
    'resources/views/pages/verwaltung/berechtigungen/page_groups.php',
];

foreach ($shouldBeArchived as $relative) {
    $file = $root . DIRECTORY_SEPARATOR . rel($relative);
    if (is_file($file)) {
        $errors[] = 'Legacy-Datei ist noch live vorhanden: ' . $relative;
    }
}

$routesFile = $root . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'routes.php';
if (is_file($routesFile)) {
    $lines = file($routesFile, FILE_IGNORE_NEW_LINES);
    if ($lines === false) {
        $errors[] = 'config/routes.php konnte nicht gelesen werden.';
    } else {
        foreach ($lines as $index => $line) {
            if (is_comment_or_blank($line)) {
                continue;
            }

            $lineNo = $index + 1;
            if (str_contains($line, 'use App\\Http\\Controller\\Verwaltung\\GruppenController;')
                || str_contains($line, 'use App\\Http\\Controller\\Verwaltung\\BerechtigungenController;')
            ) {
                $errors[] = "config/routes.php:{$lineNo}: alter PageGroup-Controller-Import aktiv: " . trim($line);
            }

            if (str_contains($line, 'new Route')
                && (preg_match('/(?<!Admin)(?<!Administration)GruppenController::class/', $line)
                    || preg_match('/(?<!Admin)(?<!Administration)BerechtigungenController::class/', $line))
            ) {
                $errors[] = "config/routes.php:{$lineNo}: alte PageGroup-Route aktiv: " . trim($line);
            }
        }
    }
} else {
    $errors[] = 'config/routes.php fehlt.';
}

$archiveBase = $root . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'archive';
$manifests = is_dir($archiveBase) ? glob($archiveBase . DIRECTORY_SEPARATOR . 'mini-project-7-legacy-pagegroup-*' . DIRECTORY_SEPARATOR . 'manifest.json') : [];
if (!$manifests) {
    $errors[] = 'Kein Mini-Projekt-7-Archivmanifest gefunden unter var/archive/mini-project-7-legacy-pagegroup-*/manifest.json';
}

if ($errors !== []) {
    echo "Legacy-PageGroup-Archiv-Check fehlgeschlagen:\n";
    foreach ($errors as $error) {
        echo ' - ' . $error . "\n";
    }
    exit(1);
}

echo "OK: Legacy-PageGroup-Dateien sind archiviert und keine alten PageGroup-Routen sind aktiv.\n";
