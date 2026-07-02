<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$needles = [
    'pt_page_groups',
    'pt_permission_group_page_group_access',
    'page_group_id',
    'PageGroupRepository',
    'PageGroupAccessRepository',
    'requirePageGroupAccess',
];

$allowedPrefixes = [
    'var' . DIRECTORY_SEPARATOR . 'archive' . DIRECTORY_SEPARATOR,
    'database' . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR,
];
$allowedFiles = [
    'tools' . DIRECTORY_SEPARATOR . 'drop_mini_project_10_legacy_pagegroup_db.php',
    'tools' . DIRECTORY_SEPARATOR . 'qa' . DIRECTORY_SEPARATOR . 'check_legacy_pagegroup_db_dropped.php',
    'tools' . DIRECTORY_SEPARATOR . 'qa' . DIRECTORY_SEPARATOR . 'run_identity_legacy_drop_checks.php',
    'tools' . DIRECTORY_SEPARATOR . 'qa' . DIRECTORY_SEPARATOR . 'scan_post_drop_legacy_pagegroup_residue.php',
    'README_MINI_PROJECT_10.md',
    'database' . DIRECTORY_SEPARATOR . 'sql' . DIRECTORY_SEPARATOR . 'verify_mini_project_10_legacy_pagegroup_drop.sql',
];

$dirs = ['src', 'config', 'resources', 'tools', 'database'];
$hits = [];
foreach ($dirs as $dir) {
    $base = $root . DIRECTORY_SEPARATOR . $dir;
    if (!is_dir($base)) {
        continue;
    }
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base, FilesystemIterator::SKIP_DOTS));
    foreach ($it as $file) {
        if (!$file->isFile()) {
            continue;
        }
        $path = $file->getPathname();
        $relative = str_replace($root . DIRECTORY_SEPARATOR, '', $path);
        $skip = false;
        foreach ($allowedPrefixes as $prefix) {
            if (str_starts_with($relative, $prefix)) {
                $skip = true;
                break;
            }
        }
        if ($skip || in_array($relative, $allowedFiles, true)) {
            continue;
        }
        $content = file_get_contents($path);
        if ($content === false) {
            continue;
        }
        foreach ($needles as $needle) {
            if (stripos($content, $needle) !== false) {
                $hits[] = $relative . ': ' . $needle;
            }
        }
    }
}

if ($hits === []) {
    echo 'OK: Keine unerwarteten Post-Drop-Legacy-PageGroup-Residuen in produktiven Dateien gefunden.' . PHP_EOL;
    exit(0);
}

echo 'Post-Drop-Legacy-Residuen gefunden:' . PHP_EOL;
foreach ($hits as $hit) {
    echo ' - ' . $hit . PHP_EOL;
}
echo 'Hinweis: Treffer können in QA-/Diagnose-Tools erlaubt sein, produktive Treffer bitte prüfen.' . PHP_EOL;
exit(1);