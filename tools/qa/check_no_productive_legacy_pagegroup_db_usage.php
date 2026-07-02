<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);

$scanRoots = [
    'src',
    'config',
    'resources/views',
    'public',
];

$terms = [
    'pt_page_groups',
    'pt_permission_group_page_group_access',
    'page_group_id',
];

$ignorePathFragments = [
    DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'archive' . DIRECTORY_SEPARATOR,
    DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR,
    DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR,
    DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'seeds' . DIRECTORY_SEPARATOR,
    DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'sql' . DIRECTORY_SEPARATOR,
    DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR,
    DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'qa' . DIRECTORY_SEPARATOR,
];

$ignoreSuffixes = [
    '.bak',
    '.old',
    '.orig',
    '.tmp',
];

$findings = [];

foreach ($scanRoots as $scanRoot) {
    $dir = $root . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $scanRoot);
    if (!is_dir($dir)) {
        continue;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS)
    );

    foreach ($iterator as $file) {
        if (!$file instanceof SplFileInfo || !$file->isFile()) {
            continue;
        }

        $path = $file->getPathname();
        $normalizedPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);

        $ignored = false;
        foreach ($ignorePathFragments as $fragment) {
            if (str_contains($normalizedPath, $fragment)) {
                $ignored = true;
                break;
            }
        }
        if ($ignored) {
            continue;
        }

        foreach ($ignoreSuffixes as $suffix) {
            if (str_ends_with($path, $suffix) || str_contains($path, $suffix . '-mini-project-')) {
                $ignored = true;
                break;
            }
        }
        if ($ignored) {
            continue;
        }

        $extension = strtolower($file->getExtension());
        if (!in_array($extension, ['php', 'phtml', 'inc'], true)) {
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
                if (stripos($line, $term) !== false) {
                    $relative = str_replace($root . DIRECTORY_SEPARATOR, '', $path);
                    $findings[] = $relative . ':' . ($index + 1) . ': ' . $term . ': ' . trim($line);
                }
            }
        }
    }
}

if ($findings !== []) {
    echo 'Produktive Legacy-PageGroup-DB-Verweise gefunden:' . PHP_EOL;
    foreach ($findings as $finding) {
        echo ' - ' . $finding . PHP_EOL;
    }
    echo PHP_EOL;
    echo 'Diese Verweise müssen entfernt werden, bevor alte PageGroup-Tabellen später gedroppt werden können.' . PHP_EOL;
    exit(1);
}

echo 'OK: Keine produktiven Legacy-PageGroup-DB-Verweise gefunden.' . PHP_EOL;
