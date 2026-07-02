<?php

declare(strict_types=1);

use App\Security\RoutePermissionMap;

require_once __DIR__ . '/../src/Security/RoutePermissionMap.php';

$map = new RoutePermissionMap();

$cases = [
    ['GET', '/verwaltung/entity-audit', 'portal.verwaltung.entity-audit.view'],
    ['GET', '/verwaltung/datenschutz', 'portal.verwaltung.datenschutz.view'],
    ['GET', '/verwaltung/einladungen', 'portal.verwaltung.einladungen.view'],
    ['GET', '/verwaltung/schulverzeichnis', 'portal.verwaltung.schulverzeichnis.view'],
    ['GET', '/administration', 'portal.verwaltung.dashboard.view'],
    ['GET', '/identity/me', null],
];

foreach ($cases as [$method, $path, $expected]) {
    $actual = $map->permissionFor($method, $path);
    if ($actual !== $expected) {
        fwrite(STDERR, sprintf("%s %s expected %s got %s\n", $method, $path, var_export($expected, true), var_export($actual, true)));
        exit(1);
    }
}

echo "OK: RoutePermissionMap Mini-Projekt 5\n";
