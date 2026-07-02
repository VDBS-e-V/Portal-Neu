<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$scanRoots = [
    'src',
    'config',
    'resources/views',
];

$tokens = [
    'ids_permission_groups',
    'ids_user_permission_groups',
    'ids_person_permission_groups',
    'PermissionGroupRepository',
    'PersonPermissionGroupRepository',
    'permission_group_id',
];

$allowedRelativeFiles = [
    // Keine produktiven Ausnahmen mehr. Historische Dateien liegen in var/archive und werden hier nicht gescannt.
];

$hits = [];

foreach ($scanRoots as $scanRoot) {
    $base = $root . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $scanRoot);
    if (!is_dir($base)) {
        continue;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($base, FilesystemIterator::SKIP_DOTS)
    );

    foreach ($iterator as $file) {
        if (!$file instanceof SplFileInfo || !$file->isFile()) {
            continue;
        }

        $path = $file->getPathname();
        $extension = strtolower($file->getExtension());
        if (!in_array($extension, ['php', 'phtml', 'inc'], true)) {
            continue;
        }

        $relative = str_replace($root . DIRECTORY_SEPARATOR, '', $path);
        $relative = str_replace(DIRECTORY_SEPARATOR, '/', $relative);
        if (in_array($relative, $allowedRelativeFiles, true)) {
            continue;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES);
        if ($lines === false) {
            continue;
        }

        foreach ($lines as $lineNumber => $line) {
            foreach ($tokens as $token) {
                if (str_contains($line, $token)) {
                    $hits[] = sprintf('%s:%d: %s: %s', $relative, $lineNumber + 1, $token, trim($line));
                }
            }
        }
    }
}

if ($hits !== []) {
    echo "Produktive Legacy-Identity-PermissionGroup-Verweise gefunden:\n";
    foreach ($hits as $hit) {
        echo ' - ' . $hit . "\n";
    }
    exit(1);
}

echo "OK: Keine produktiven Legacy-Identity-PermissionGroup-Codeverweise gefunden.\n";