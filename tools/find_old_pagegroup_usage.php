<?php

declare(strict_types=1);

$root = $argv[1] ?? getcwd();
$patterns = [
    'requirePageGroupAccess',
    'currentUserCanAccessPageGroup',
    'canAccessPageGroup',
    'page_group',
    'pageGroup',
    'pt_page_groups',
    'pt_permission_group_page_group_access',
];

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
);

foreach ($iterator as $file) {
    if (!$file instanceof SplFileInfo || !$file->isFile()) {
        continue;
    }

    $path = $file->getPathname();
    if (!preg_match('/\.(php|sql|md|twig|phtml|html)$/', $path)) {
        continue;
    }

    $contents = file_get_contents($path);
    if ($contents === false) {
        continue;
    }

    foreach ($patterns as $pattern) {
        if (str_contains($contents, $pattern)) {
            echo $path . ': ' . $pattern . PHP_EOL;
        }
    }
}
