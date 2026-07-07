<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$needles = [
    'requirePageGroupAccess',
    'currentUserCanAccessPageGroup',
    'canAccessPageGroup',
    'pt_page_groups',
    'pt_permission_group_page_group_access',
    'page_group_id',
    'pageGroup',
    'page_group',
];

$ignoredPathFragments = [
    DIRECTORY_SEPARATOR . '.git' . DIRECTORY_SEPARATOR,
    DIRECTORY_SEPARATOR . 'docs' . DIRECTORY_SEPARATOR,
    DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR,
    DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'seeds' . DIRECTORY_SEPARATOR,
    DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'sql' . DIRECTORY_SEPARATOR,
    DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR,
    DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR,
    DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR,
];

$allowedFiles = [
    normalize($root . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Security' . DIRECTORY_SEPARATOR . 'AuthorizationService.php'),
];

$extensions = ['php'];
$hits = [];
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS));

foreach ($iterator as $file) {
    if (!$file instanceof SplFileInfo || !$file->isFile()) {
        continue;
    }

    $path = normalize($file->getPathname());
    if (!in_array(strtolower($file->getExtension()), $extensions, true)) {
        continue;
    }

    if (in_array($path, $allowedFiles, true)) {
        continue;
    }

    foreach ($ignoredPathFragments as $fragment) {
        if (str_contains($path, normalize($fragment))) {
            continue 2;
        }
    }

    $content = file_get_contents($path);
    if ($content === false) {
        continue;
    }

    foreach ($needles as $needle) {
        if (str_contains($content, $needle)) {
            $hits[] = [relative($root, $path), $needle];
        }
    }
}

if ($hits === []) {
    fwrite(STDOUT, "OK: Keine produktiven PageGroup-Runtime-Treffer gefunden.\n");
    exit(0);
}

fwrite(STDERR, "Produktive PageGroup-Runtime-Treffer gefunden:\n");
foreach ($hits as [$path, $needle]) {
    fwrite(STDERR, sprintf(" - %s: %s\n", $path, $needle));
}

fwrite(STDERR, "\nHinweis: Treffer in alten, nicht gerouteten Klassen können später gelöscht werden. Treffer in aktiv gerouteten Klassen müssen auf Permissions oder /administration umgestellt werden.\n");
exit(1);

function normalize(string $path): string
{
    return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
}

function relative(string $root, string $path): string
{
    $root = rtrim(normalize($root), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    $path = normalize($path);
    return str_starts_with($path, $root) ? substr($path, strlen($root)) : $path;
}
