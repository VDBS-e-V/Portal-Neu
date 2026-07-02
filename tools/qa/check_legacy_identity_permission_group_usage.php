<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$strict = in_array('--strict', $argv, true);

$directories = [
    'src',
    'config',
    'resources/views',
];

$patterns = [
    'ids_permission_groups',
    'ids_user_permission_groups',
    'PermissionGroupRepository',
    'UserPermissionGroup',
    'permission_group_id',
    'permissionGroup',
];

$hits = [];

foreach ($directories as $directory) {
    $base = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $directory);
    if (!is_dir($base)) {
        continue;
    }

    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base, FilesystemIterator::SKIP_DOTS));
    foreach ($iterator as $file) {
        if (!$file instanceof SplFileInfo || !$file->isFile()) {
            continue;
        }
        $path = $file->getPathname();
        if (!preg_match('/\.(php|sql|phtml|inc)$/i', $path)) {
            continue;
        }

        $relative = str_replace($root . DIRECTORY_SEPARATOR, '', $path);
        $content = file($path, FILE_IGNORE_NEW_LINES);
        if ($content === false) {
            continue;
        }
        foreach ($content as $lineNumber => $line) {
            foreach ($patterns as $pattern) {
                if (stripos($line, $pattern) !== false) {
                    $hits[] = sprintf('%s:%d: %s', str_replace('\\', '/', $relative), $lineNumber + 1, trim($line));
                    continue 2;
                }
            }
        }
    }
}

if ($hits === []) {
    echo "OK: Keine produktiven Legacy-Identity-PermissionGroup-Codeverweise gefunden.\n";
    exit(0);
}

if ($strict) {
    echo "Legacy-Identity-PermissionGroup-Codeverweise gefunden:\n";
    foreach ($hits as $hit) {
        echo " - {$hit}\n";
    }
    exit(1);
}

echo "WARN: Legacy-Identity-PermissionGroup-Codeverweise gefunden. Diese werden in Mini-Projekt 13 bewertet/entfernt:\n";
foreach ($hits as $hit) {
    echo " - {$hit}\n";
}
exit(0);
