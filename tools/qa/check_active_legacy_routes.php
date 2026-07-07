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

$legacyControllers = [
    'Verwaltung\\GruppenController',
    'Verwaltung\\BerechtigungenController',
    'Verwaltung\\VerwaltungController',
    'Verwaltung\\PersonenController',
    'Verwaltung\\AuditLogController',
    'Verwaltung\\EntityAuditController',
    'Verwaltung\\DatenschutzController',
    'Verwaltung\\EinladungenController',
    'Verwaltung\\SchulverzeichnisController',
];

$found = false;

foreach ($legacyControllers as $controller) {
    if (str_contains($content, $controller)) {
        echo "WARN: Aktiver/alter Controller-Verweis gefunden: {$controller}\n";
        $found = true;
    }
}

$routePatterns = [
    '/verwaltung/gruppen',
    '/verwaltung/berechtigungen',
];

foreach ($routePatterns as $route) {
    if (preg_match_all('/new\s+Route\s*\([^;]*' . preg_quote($route, '/') . '[^;]*\)/m', $content, $matches)) {
        foreach ($matches[0] as $match) {
            echo "\nRoute gefunden für {$route}:\n";
            echo trim($match) . "\n";
        }
    }
}

if ($found) {
    echo "\nERGEBNIS: Es gibt noch alte Verwaltung-Controller in config/routes.php.\n";
    echo "Diese Routen müssen auf Administration-Controller umgebogen oder entfernt werden.\n";
    exit(1);
}

echo "OK: Keine bekannten alten Verwaltung-Controller in config/routes.php gefunden.\n";