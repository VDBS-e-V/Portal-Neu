<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);

$tokens = [
    'requirePageGroupAccess',
    'canAccessPageGroup',
    'currentUserCanAccessPageGroup',
    'PageGroupRepository',
    'PageGroupAccessRepository',
    'pt_page_groups',
    'pt_permission_group_page_group_access',
    'page_group_id',
    'pageGroup',
    'page_group',
];

$skipDirs = [
    DIRECTORY_SEPARATOR . '.git' . DIRECTORY_SEPARATOR,
    DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR,
    DIRECTORY_SEPARATOR . 'node_modules' . DIRECTORY_SEPARATOR,
    DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'archive' . DIRECTORY_SEPARATOR,
    DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR,
];

$skipFileFragments = [
    '.bak-',
    'scan_legacy_pagegroup_residue.php',
    'README',
    'database' . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR,
    'database' . DIRECTORY_SEPARATOR . 'seeds' . DIRECTORY_SEPARATOR,
];

$results = [];
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
);

foreach ($iterator as $fileInfo) {
    if (!$fileInfo instanceof SplFileInfo || !$fileInfo->isFile()) {
        continue;
    }

    $path = $fileInfo->getPathname();
    $normalized = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);

    $skip = false;
    foreach ($skipDirs as $skipDir) {
        if (str_contains($normalized, $skipDir)) {
            $skip = true;
            break;
        }
    }
    if ($skip) {
        continue;
    }

    foreach ($skipFileFragments as $fragment) {
        if (str_contains($normalized, $fragment)) {
            $skip = true;
            break;
        }
    }
    if ($skip) {
        continue;
    }

    $ext = strtolower($fileInfo->getExtension());
    if (!in_array($ext, ['php', 'sql', 'md'], true)) {
        continue;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES);
    if ($lines === false) {
        continue;
    }

    foreach ($lines as $lineNo => $line) {
        $trim = trim($line);
        if ($trim === '' || str_starts_with($trim, '//') || str_starts_with($trim, '#')) {
            continue;
        }

        foreach ($tokens as $token) {
            if (str_contains($line, $token)) {
                $relative = str_replace($root . DIRECTORY_SEPARATOR, '', $path);
                $results[] = $relative . ':' . ($lineNo + 1) . ': ' . $token;
                break;
            }
        }
    }
}

if ($results === []) {
    echo "OK: Keine Legacy-PageGroup-Reste im Live-Code gefunden.\n";
    exit(0);
}

echo "Legacy-PageGroup-Reste im Live-Code gefunden, bitte prüfen:\n";
foreach ($results as $result) {
    echo ' - ' . $result . "\n";
}

echo "\nHinweis: Dieser Bericht ist bewusst informativ. Für harte Runtime-Prüfungen bitte run_identity_cleanup_checks.php verwenden.\n";
exit(0);
