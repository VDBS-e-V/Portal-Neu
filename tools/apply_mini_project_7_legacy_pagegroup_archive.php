<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$stamp = date('Ymd-His');
$suffix = 'mini-project-7-' . $stamp;

function mp7_path(string ...$parts): string
{
    return implode(DIRECTORY_SEPARATOR, $parts);
}

function mp7_rel_to_abs(string $root, string $relative): string
{
    return mp7_path($root, ...explode('/', $relative));
}

function mp7_backup_file(string $file, string $suffix): void
{
    if (!is_file($file)) {
        return;
    }

    $backup = $file . '.bak-' . $suffix;
    if (!copy($file, $backup)) {
        throw new RuntimeException('Backup fehlgeschlagen: ' . $file);
    }
    echo 'Backup: ' . $backup . PHP_EOL;
}

function mp7_write_file_with_backup(string $file, string $content, string $suffix): void
{
    if (is_file($file)) {
        mp7_backup_file($file, $suffix);
    }

    $dir = dirname($file);
    if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
        throw new RuntimeException('Verzeichnis konnte nicht erstellt werden: ' . $dir);
    }

    if (file_put_contents($file, $content) === false) {
        throw new RuntimeException('Datei konnte nicht geschrieben werden: ' . $file);
    }

    echo 'Aktualisiert: ' . $file . PHP_EOL;
}

function mp7_copy_patch_file(string $root, string $relativeSource, string $relativeTarget, string $suffix): void
{
    $source = mp7_path($root, 'patches', 'mini_project_7', ...explode('/', $relativeSource));
    $target = mp7_path($root, ...explode('/', $relativeTarget));

    if (!is_file($source)) {
        throw new RuntimeException('Quelldatei fehlt: ' . $source);
    }

    $content = file_get_contents($source);
    if ($content === false) {
        throw new RuntimeException('Quelldatei konnte nicht gelesen werden: ' . $source);
    }

    mp7_write_file_with_backup($target, $content, $suffix);
}

function mp7_is_comment_or_blank(string $line): bool
{
    $trim = trim($line);
    return $trim === '' || str_starts_with($trim, '//') || str_starts_with($trim, '#') || str_starts_with($trim, '*') || str_starts_with($trim, '|');
}

function mp7_patch_routes(string $root, string $suffix): void
{
    $file = mp7_rel_to_abs($root, 'config/routes.php');
    if (!is_file($file)) {
        echo 'Hinweis: config/routes.php nicht gefunden, Routen-Cleanup übersprungen.' . PHP_EOL;
        return;
    }

    $content = file_get_contents($file);
    if ($content === false) {
        throw new RuntimeException('config/routes.php konnte nicht gelesen werden.');
    }

    $lines = preg_split('/\R/', $content);
    if ($lines === false) {
        throw new RuntimeException('config/routes.php konnte nicht in Zeilen zerlegt werden.');
    }

    $out = [];
    foreach ($lines as $line) {
        $trim = trim($line);

        // Alte, nicht mehr geroutete PageGroup-Controller-Imports deaktivieren.
        if ($trim === 'use App\\Http\\Controller\\Verwaltung\\GruppenController;' || $trim === 'use App\\Http\\Controller\\Verwaltung\\BerechtigungenController;') {
            $out[] = '// [mini-project-7 archived legacy pagegroup controller import] ' . $line;
            continue;
        }

        // Bereits deaktivierte alte PageGroup-Routen entfernen, damit routes.php wieder lesbarer wird.
        if (str_contains($line, 'Deaktiviert durch Mini-Projekt 3 Legacy-Verwaltung-Alias')
            && (str_contains($line, 'GruppenController::class') || str_contains($line, 'BerechtigungenController::class'))
        ) {
            continue;
        }

        $out[] = $line;
    }

    $newContent = implode(PHP_EOL, $out);
    if ($newContent !== $content) {
        mp7_write_file_with_backup($file, $newContent, $suffix);
    } else {
        echo 'config/routes.php: keine Änderungen nötig.' . PHP_EOL;
    }
}

function mp7_archive_file(string $root, string $relative, string $archiveRoot, array &$manifest): void
{
    $source = mp7_rel_to_abs($root, $relative);
    if (!is_file($source)) {
        echo 'Nicht vorhanden, übersprungen: ' . $relative . PHP_EOL;
        return;
    }

    $target = mp7_path($archiveRoot, ...explode('/', $relative));
    $targetDir = dirname($target);
    if (!is_dir($targetDir) && !mkdir($targetDir, 0775, true) && !is_dir($targetDir)) {
        throw new RuntimeException('Archiv-Verzeichnis konnte nicht erstellt werden: ' . $targetDir);
    }

    $manifest[] = [
        'original' => $relative,
        'archived_to' => str_replace($root . DIRECTORY_SEPARATOR, '', $target),
        'size' => filesize($source) ?: 0,
        'sha256' => hash_file('sha256', $source),
        'archived_at' => date(DATE_ATOM),
    ];

    if (!rename($source, $target)) {
        throw new RuntimeException('Archivierung fehlgeschlagen: ' . $relative);
    }

    echo 'Archiviert: ' . $relative . ' -> ' . str_replace($root . DIRECTORY_SEPARATOR, '', $target) . PHP_EOL;
}

function mp7_remove_empty_dirs(string $root, array $dirs): void
{
    foreach ($dirs as $relativeDir) {
        $dir = mp7_rel_to_abs($root, $relativeDir);
        if (!is_dir($dir)) {
            continue;
        }

        $items = array_values(array_diff(scandir($dir) ?: [], ['.', '..']));
        if ($items === []) {
            @rmdir($dir);
            echo 'Leeres Verzeichnis entfernt: ' . $relativeDir . PHP_EOL;
        }
    }
}

function mp7_archive_legacy_files(string $root, string $suffix): void
{
    $archiveRoot = mp7_path($root, 'var', 'archive', 'mini-project-7-legacy-pagegroup-' . $suffix);
    if (!is_dir($archiveRoot) && !mkdir($archiveRoot, 0775, true) && !is_dir($archiveRoot)) {
        throw new RuntimeException('Archiv-Verzeichnis konnte nicht erstellt werden: ' . $archiveRoot);
    }

    $targets = [
        'src/Http/Controller/Verwaltung/GruppenController.php',
        'src/Http/Controller/Verwaltung/BerechtigungenController.php',
        'src/Repository/PageGroupRepository.php',
        'src/Repository/PageGroupAccessRepository.php',
        'resources/views/pages/verwaltung/berechtigungen/index.php',
        'resources/views/pages/verwaltung/berechtigungen/group.php',
        'resources/views/pages/verwaltung/berechtigungen/page_groups.php',
    ];

    $manifest = [
        'project' => 'identity-rights-mini-project-7',
        'created_at' => date(DATE_ATOM),
        'archive_root' => str_replace($root . DIRECTORY_SEPARATOR, '', $archiveRoot),
        'files' => [],
    ];

    foreach ($targets as $relative) {
        mp7_archive_file($root, $relative, $archiveRoot, $manifest['files']);
    }

    $manifestFile = mp7_path($archiveRoot, 'manifest.json');
    $json = json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    if ($json === false || file_put_contents($manifestFile, $json . PHP_EOL) === false) {
        throw new RuntimeException('Manifest konnte nicht geschrieben werden: ' . $manifestFile);
    }
    echo 'Manifest: ' . str_replace($root . DIRECTORY_SEPARATOR, '', $manifestFile) . PHP_EOL;

    mp7_remove_empty_dirs($root, [
        'resources/views/pages/verwaltung/berechtigungen',
    ]);
}

mp7_patch_routes($root, $suffix);
mp7_archive_legacy_files($root, $suffix);

mp7_copy_patch_file($root, 'tools/qa/check_legacy_pagegroup_archive.php', 'tools/qa/check_legacy_pagegroup_archive.php', $suffix);
mp7_copy_patch_file($root, 'tools/qa/check_menu_items_no_page_group_id.php', 'tools/qa/check_menu_items_no_page_group_id.php', $suffix);
mp7_copy_patch_file($root, 'tools/qa/scan_legacy_pagegroup_residue.php', 'tools/qa/scan_legacy_pagegroup_residue.php', $suffix);
mp7_copy_patch_file($root, 'tools/qa/run_identity_cleanup_checks.php', 'tools/qa/run_identity_cleanup_checks.php', $suffix);
mp7_copy_patch_file($root, 'database/seeds/seed_mini_project_7_clear_page_group_menu_links.sql', 'database/seeds/seed_mini_project_7_clear_page_group_menu_links.sql', $suffix);

echo PHP_EOL;
echo 'Mini-Projekt 7 Legacy-PageGroup-Archivierung wurde angewendet.' . PHP_EOL;
echo 'Bitte ausführen:' . PHP_EOL;
echo '  php -l config\\routes.php' . PHP_EOL;
echo '  php bin\\console seed' . PHP_EOL;
echo '  php tools\\qa\\check_legacy_pagegroup_archive.php' . PHP_EOL;
echo '  php tools\\qa\\check_menu_items_no_page_group_id.php' . PHP_EOL;
echo '  php tools\\qa\\run_identity_cleanup_checks.php' . PHP_EOL;
echo PHP_EOL;
echo 'Optionaler Bericht:' . PHP_EOL;
echo '  php tools\\qa\\scan_legacy_pagegroup_residue.php' . PHP_EOL;
