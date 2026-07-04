<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$scan = [
    'src/Repository/AuthorizedMenuRepository.php',
    'src/Navigation',
    'resources/views/layouts',
    'resources/views/partials',
];

$legacyPatterns = [
    'page_group_id',
    'pt_page_groups',
    'pt_permission_group_page_group_access',
    'ids_permission_groups',
    'ids_user_permission_groups',
    'ids_person_permission_groups',
    'permission_group_id',
];

$errors = [];

foreach ($scan as $relative) {
    $path = $root . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relative);
    if (is_file($path)) {
        $files = [$path];
    } elseif (is_dir($path)) {
        $files = [];
        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS));
        foreach ($it as $file) {
            if ($file->isFile() && strtolower($file->getExtension()) === 'php') {
                $files[] = $file->getPathname();
            }
        }
    } else {
        continue;
    }

    foreach ($files as $file) {
        $content = file_get_contents($file);
        if ($content === false) {
            continue;
        }
        foreach ($legacyPatterns as $pattern) {
            if (stripos($content, $pattern) !== false) {
                $rel = str_replace($root . DIRECTORY_SEPARATOR, '', $file);
                $errors[] = $rel . ': enthält Legacy-Navigationsbegriff ' . $pattern;
            }
        }
    }
}

if ($errors !== []) {
    echo "Navigation-Code-Check fehlgeschlagen:\n";
    foreach ($errors as $error) {
        echo ' - ' . $error . "\n";
    }
    exit(1);
}

echo "OK: Navigation-Code nutzt keine Legacy-Gruppen-/PageGroup-Begriffe produktiv.\n";