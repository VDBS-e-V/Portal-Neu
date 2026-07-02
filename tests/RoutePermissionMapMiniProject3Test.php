<?php

declare(strict_types=1);

use App\Security\RoutePermissionMap;

require_once __DIR__ . '/../src/Security/RoutePermissionMap.php';

$map = new RoutePermissionMap();

$cases = [
    ['GET', '/administration/gruppen/7/permissions', 'identity.gruppen.permissions.manage'],
    ['POST', '/administration/personen/3/gruppen/9/remove', 'identity.subjects.groups.remove'],
    ['POST', '/administration/permissions/5/deactivate', 'identity.permissions.delete'],
    ['GET', '/administration/systeme/create', 'identity.systeme.create'],
];

foreach ($cases as [$method, $path, $expected]) {
    $actual = $map->permissionFor($method, $path);
    if ($actual !== $expected) {
        fwrite(STDERR, sprintf("%s %s expected %s got %s\n", $method, $path, $expected, (string) $actual));
        exit(1);
    }
}

echo "OK: RoutePermissionMap covers Mini-Projekt 3 routes\n";
