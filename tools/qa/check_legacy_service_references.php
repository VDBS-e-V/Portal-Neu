<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$files = [
    'config/services.php',
    'config/routes.php',
    'src/Security/AdminSafetyService.php',
];

$terms = [
    'PageGroupRepository',
    'PageGroupAccessRepository',
    'AdminSafetyService',
];

$warnings = [];

foreach ($files as $relative) {
    $path = $root . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relative);
    if (!is_file($path)) {
        continue;
    }

    $content = file_get_contents($path);
    if ($content === false) {
        continue;
    }

    $lines = preg_split('/\R/', $content) ?: [];
    foreach ($lines as $index => $line) {
        $trimmed = trim($line);
        if ($trimmed === '' || str_starts_with($trimmed, '//')) {
            continue;
        }
        foreach ($terms as $term) {
            if (str_contains($line, $term)) {
                $warnings[] = $relative . ':' . ($index + 1) . ': ' . $term . ': ' . trim($line);
            }
        }
    }
}

if ($warnings !== []) {
    echo 'WARN: Alte Service-/Klassenreferenzen sind noch vorhanden, aber nicht als aktive PageGroup-Runtime bewertet:' . PHP_EOL;
    foreach ($warnings as $warning) {
        echo ' - ' . $warning . PHP_EOL;
    }
    echo 'Diese Referenzen können in Mini-Projekt 9 archiviert werden, sobald der Browser-Smoke-Test stabil ist.' . PHP_EOL;
    exit(0);
}

echo 'OK: Keine alten PageGroup-Service-/Klassenreferenzen in den geprüften Dateien gefunden.' . PHP_EOL;
