<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$legacyFiles = [
    'src/Http/Controller/Verwaltung/GruppenController.php',
    'src/Http/Controller/Verwaltung/BerechtigungenController.php',
    'src/Repository/PageGroupRepository.php',
    'src/Repository/PageGroupAccessRepository.php',
];

foreach ($legacyFiles as $relative) {
    $file = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative);
    if (is_file($file)) {
        fwrite(STDERR, "Legacy-Datei ist noch live vorhanden: {$relative}\n");
        exit(1);
    }
}

echo "OK: Mini-Projekt-7-Legacy-Dateien sind nicht mehr live vorhanden.\n";
