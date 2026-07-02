<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);

$files = [
    'config/routes.php',
    'src/Http/Controller/Verwaltung/VerwaltungController.php',
    'src/Http/Controller/Verwaltung/PersonenController.php',
    'src/Http/Controller/Verwaltung/AuditLogController.php',
    'src/Http/Controller/Verwaltung/EntityAuditController.php',
    'src/Http/Controller/Verwaltung/DatenschutzController.php',
    'src/Http/Controller/Verwaltung/EinladungenController.php',
    'src/Http/Controller/Verwaltung/SchulverzeichnisController.php',
    'src/Http/Controller/Verwaltung/GruppenController.php',
    'src/Http/Controller/Verwaltung/BerechtigungenController.php',
];

$bad = [];

foreach ($files as $relative) {
    $path = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative);
    if (!is_file($path)) {
        continue;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES);
    if ($lines === false) {
        $bad[] = "{$relative}: konnte nicht gelesen werden";
        continue;
    }

    foreach ($lines as $idx => $line) {
        $trim = trim($line);
        if ($trim === '' || str_starts_with($trim, '//') || str_starts_with($trim, '*')) {
            continue;
        }

        if (str_contains($line, 'requirePageGroupAccess(')
            || str_contains($line, 'canAccessPageGroup(')
            || str_contains($line, 'currentUserCanAccessPageGroup(')
            || str_contains($line, 'pt_page_groups')
            || str_contains($line, 'pt_permission_group_page_group_access')) {
            $bad[] = sprintf('%s:%d: %s', $relative, $idx + 1, trim($line));
        }
    }
}

// Alte Rechteverwaltungscontroller sollen in routes.php nicht mehr aktiv referenziert werden.
$routesPath = $root . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'routes.php';
if (is_file($routesPath)) {
    $routesLines = file($routesPath, FILE_IGNORE_NEW_LINES) ?: [];
    foreach ($routesLines as $idx => $line) {
        $trim = trim($line);
        if ($trim === '' || str_starts_with($trim, '//') || str_starts_with($trim, '*')) {
            continue;
        }
        if (str_contains($line, 'GruppenController::class') || str_contains($line, 'BerechtigungenController::class')) {
            if (!str_contains($line, 'AdministrationGruppenController::class')
                && !str_contains($line, 'AdministrationPermissionsController::class')) {
                $bad[] = sprintf('config/routes.php:%d: alter Rechteverwaltungscontroller aktiv: %s', $idx + 1, trim($line));
            }
        }
    }
}

if ($bad !== []) {
    echo "Aktive PageGroup-/Legacy-Runtime-Treffer gefunden:\n";
    foreach ($bad as $entry) {
        echo " - {$entry}\n";
    }
    exit(1);
}

echo "OK: Keine aktiven PageGroup-Laufzeitaufrufe in bekannten Routen/Controllern gefunden.\n";
