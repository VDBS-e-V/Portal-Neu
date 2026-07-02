<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$stamp = date('Ymd-His');

function path_join(string ...$parts): string
{
    return implode(DIRECTORY_SEPARATOR, $parts);
}

function backup_file(string $file, string $suffix): void
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

function write_file_with_backup(string $file, string $content, string $suffix): void
{
    if (is_file($file)) {
        backup_file($file, $suffix);
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

function copy_patch_file(string $root, string $relativeSource, string $relativeTarget, string $suffix): void
{
    $source = path_join($root, 'patches', 'mini_project_6_2', ...explode('/', $relativeSource));
    $target = path_join($root, ...explode('/', $relativeTarget));

    if (!is_file($source)) {
        throw new RuntimeException('Quelldatei fehlt: ' . $source);
    }

    $content = file_get_contents($source);
    if ($content === false) {
        throw new RuntimeException('Quelldatei konnte nicht gelesen werden: ' . $source);
    }

    write_file_with_backup($target, $content, $suffix);
}

function is_comment_or_blank(string $line): bool
{
    $trim = trim($line);
    return $trim === '' || str_starts_with($trim, '//') || str_starts_with($trim, '#') || str_starts_with($trim, '*') || str_starts_with($trim, '|');
}

function patch_routes(string $root, string $suffix): void
{
    $file = path_join($root, 'config', 'routes.php');
    if (!is_file($file)) {
        throw new RuntimeException('config/routes.php fehlt.');
    }

    $content = file_get_contents($file);
    if ($content === false) {
        throw new RuntimeException('config/routes.php konnte nicht gelesen werden.');
    }

    // Korrektur aus Mini-Projekt 6.1: Audit-Route darf nicht auf AdminGruppenController zeigen.
    $content = str_replace(
        "new Route('GET', '/verwaltung/gruppen/{id}/audit', AdminGruppenController::class, 'group'),",
        "new Route('GET', '/verwaltung/gruppen/{id}/audit', EntityAuditController::class, 'group'),",
        $content
    );
    $content = str_replace(
        "new Route('GET', '/verwaltung/gruppen/{id}/audit', AdministrationGruppenController::class, 'group'),",
        "new Route('GET', '/verwaltung/gruppen/{id}/audit', EntityAuditController::class, 'group'),",
        $content
    );

    // Doppelte identische METHOD+PATH-Routen deaktivieren. Die ersten Routen gewinnen.
    $lines = preg_split('/\R/', $content);
    if ($lines === false) {
        throw new RuntimeException('routes.php konnte nicht in Zeilen zerlegt werden.');
    }

    $seen = [];
    $out = [];

    foreach ($lines as $line) {
        if (!is_comment_or_blank($line) && preg_match("~new\s+Route\s*\(\s*'([^']+)'\s*,\s*'([^']+)'~", $line, $m)) {
            $key = strtoupper($m[1]) . ' ' . $m[2];
            if (isset($seen[$key])) {
                $out[] = '// [mini-project-6-2 disabled duplicate route: first at line ' . $seen[$key] . '] ' . $line;
                continue;
            }
            $seen[$key] = count($out) + 1;
        }
        $out[] = $line;
    }

    $newContent = implode(PHP_EOL, $out);

    if ($newContent !== $content) {
        write_file_with_backup($file, $newContent, $suffix);
    } else {
        echo 'config/routes.php: keine Änderungen nötig.' . PHP_EOL;
    }
}

$suffix = 'mini-project-6-2-' . $stamp;

patch_routes($root, $suffix);
copy_patch_file($root, 'tools/qa/check_active_pagegroup_runtime.php', 'tools/qa/check_active_pagegroup_runtime.php', $suffix);
copy_patch_file($root, 'tools/qa/run_identity_runtime_checks.php', 'tools/qa/run_identity_runtime_checks.php', $suffix);

echo PHP_EOL;
echo 'Mini-Projekt 6.2 Runtime-QA-Cleanup wurde angewendet.' . PHP_EOL;
echo 'Bitte ausführen:' . PHP_EOL;
echo '  php -l config\\routes.php' . PHP_EOL;
echo '  php tools\\qa\\check_active_pagegroup_runtime.php' . PHP_EOL;
echo '  php tools\\qa\\check_verwaltung_permissions_seed.php' . PHP_EOL;
echo '  php tools\\qa\\run_identity_runtime_checks.php' . PHP_EOL;
