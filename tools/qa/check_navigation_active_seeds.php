<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$seedDir = $root . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'seeds';

$legacyNavigationPatterns = [
    'page_group_id',
    'pt_page_groups',
    'pt_permission_group_page_group_access',
];

$ignoredFiles = [
    'seed_mini_project_7_clear_page_group_menu_links.sql',
    'seed_mini_project_8_deprecate_legacy_pagegroup_db.sql',
    'seed_mini_project_12_identity_cleanup_markers.sql',
    'seed_mini_project_13_deprecate_legacy_identity_permission_groups.sql',
];

$errors = [];

foreach (glob($seedDir . DIRECTORY_SEPARATOR . '*.sql') ?: [] as $file) {
    $name = basename($file);
    if (in_array($name, $ignoredFiles, true)) {
        continue;
    }

    $content = file_get_contents($file);
    if ($content === false) {
        continue;
    }

    $lines = preg_split('/\R/', $content) ?: [];
    foreach ($lines as $index => $line) {
        $trimmed = trim($line);
        if ($trimmed === '' || str_starts_with($trimmed, '--') || str_starts_with($trimmed, '/*') || str_starts_with($trimmed, '*')) {
            continue;
        }
        foreach ($legacyNavigationPatterns as $pattern) {
            if (stripos($line, $pattern) !== false) {
                $errors[] = sprintf('%s:%d: enthält Legacy-Navigationsfeld/-tabelle %s', $name, $index + 1, $pattern);
            }
        }
    }
}

if ($errors !== []) {
    echo "Aktive Seed-Dateien enthalten noch PageGroup-Navigationsverweise:\n";
    foreach ($errors as $error) {
        echo ' - ' . $error . "\n";
    }
    exit(1);
}

echo "OK: Aktive Seeds enthalten keine Legacy-Navigationsverweise mehr.\n";