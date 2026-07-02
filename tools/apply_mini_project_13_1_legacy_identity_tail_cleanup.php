<?php

declare(strict_types=1);

/**
 * Mini-Projekt 13.1
 * Entfernt die letzten produktiven Legacy-Identity-PermissionGroup-Reste:
 * - fehlerhaften Seed ohne nicht vorhandene Spalte `note` ersetzen
 * - EntityAuditRepository: ids_person_permission_groups aus altem Audit-Fallback entfernen
 * - PersonErasureRepository: Legacy-Löschblock für ids_person_permission_groups entfernen
 * - QA-Tools aktualisieren
 */

$root = dirname(__DIR__);
$stamp = 'mini-project-13-1-' . date('Ymd-His');
$packageRoot = __DIR__;

function path_join(string ...$parts): string
{
    return implode(DIRECTORY_SEPARATOR, array_map(static fn (string $part): string => trim($part, "\\/"), $parts));
}

function backup_file(string $path, string $stamp): void
{
    if (!is_file($path)) {
        return;
    }

    $backup = $path . '.bak-' . $stamp;
    if (!copy($path, $backup)) {
        throw new RuntimeException('Backup fehlgeschlagen: ' . $path);
    }
    echo 'Backup: ' . $backup . PHP_EOL;
}

function write_file(string $path, string $content, string $stamp): void
{
    $dir = dirname($path);
    if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
        throw new RuntimeException('Verzeichnis konnte nicht erstellt werden: ' . $dir);
    }

    backup_file($path, $stamp);

    if (file_put_contents($path, $content) === false) {
        throw new RuntimeException('Datei konnte nicht geschrieben werden: ' . $path);
    }

    echo 'Aktualisiert: ' . $path . PHP_EOL;
}

function install_project_file(string $relative, string $root, string $packageRoot, string $stamp): void
{
    $source = $packageRoot . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relative);
    $target = $root . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relative);

    if (!is_file($source)) {
        throw new RuntimeException('Quelldatei fehlt: ' . $source);
    }

    write_file($target, (string) file_get_contents($source), $stamp);
}

function read_required(string $path): string
{
    if (!is_file($path)) {
        throw new RuntimeException('Datei fehlt: ' . $path);
    }
    $content = file_get_contents($path);
    if ($content === false) {
        throw new RuntimeException('Datei konnte nicht gelesen werden: ' . $path);
    }
    return $content;
}

function remove_if_table_exists_block(string $content, string $table): array
{
    $changed = false;

    while (true) {
        $needle1 = "tableExists('" . $table . "')";
        $needle2 = 'tableExists("' . $table . '")';
        $pos = strpos($content, $needle1);
        if ($pos === false) {
            $pos = strpos($content, $needle2);
        }
        if ($pos === false) {
            break;
        }

        $ifPos = strrpos(substr($content, 0, $pos), 'if');
        if ($ifPos === false) {
            break;
        }

        $braceStart = strpos($content, '{', $ifPos);
        if ($braceStart === false || $braceStart > $pos + 250) {
            break;
        }

        $length = strlen($content);
        $depth = 0;
        $end = null;
        $inSingle = false;
        $inDouble = false;
        $escape = false;

        for ($i = $braceStart; $i < $length; $i++) {
            $ch = $content[$i];

            if ($escape) {
                $escape = false;
                continue;
            }

            if (($inSingle || $inDouble) && $ch === '\\') {
                $escape = true;
                continue;
            }

            if (!$inDouble && $ch === "'") {
                $inSingle = !$inSingle;
                continue;
            }

            if (!$inSingle && $ch === '"') {
                $inDouble = !$inDouble;
                continue;
            }

            if ($inSingle || $inDouble) {
                continue;
            }

            if ($ch === '{') {
                $depth++;
            } elseif ($ch === '}') {
                $depth--;
                if ($depth === 0) {
                    $end = $i + 1;
                    break;
                }
            }
        }

        if ($end === null) {
            break;
        }

        while ($end < strlen($content) && ($content[$end] === "\r" || $content[$end] === "\n")) {
            $end++;
        }

        $content = substr($content, 0, $ifPos)
            . "// Mini-Projekt 13.1: Legacy-Tabelle {$table} wird nicht mehr produktiv bereinigt.\n"
            . substr($content, $end);
        $changed = true;
    }

    return [$content, $changed];
}

// 1) Seed ohne nicht portable/nicht vorhandene `note`-Spalte installieren.
install_project_file('database/seeds/seed_mini_project_13_deprecate_legacy_identity_permission_groups.sql', $root, $packageRoot, $stamp);

// 2) EntityAuditRepository bereinigen.
$entityAuditPath = path_join($root, 'src', 'Repository', 'EntityAuditRepository.php');
if (is_file($entityAuditPath)) {
    $content = read_required($entityAuditPath);
    $original = $content;

    $lines = preg_split('/\R/', $content);
    $filtered = [];
    foreach ($lines as $line) {
        if (stripos($line, 'ids_person_permission_groups') !== false) {
            continue;
        }
        $filtered[] = $line;
    }
    $content = implode(PHP_EOL, $filtered);

    if ($content !== $original) {
        write_file($entityAuditPath, $content, $stamp);
    } else {
        echo 'Keine Änderung nötig: ' . $entityAuditPath . PHP_EOL;
    }
}

// 3) PersonErasureRepository: alten Cleanup-Block entfernen.
$erasurePath = path_join($root, 'src', 'Repository', 'PersonErasureRepository.php');
if (is_file($erasurePath)) {
    $content = read_required($erasurePath);
    [$newContent, $changed] = remove_if_table_exists_block($content, 'ids_person_permission_groups');

    if (!$changed) {
        // Fallback: einzelne Delete-Statements/Zeilen entfernen, falls der lokale Code anders formatiert ist.
        $lines = preg_split('/\R/', $content);
        $filtered = [];
        foreach ($lines as $line) {
            if (stripos($line, 'ids_person_permission_groups') !== false) {
                $changed = true;
                continue;
            }
            $filtered[] = $line;
        }
        $newContent = implode(PHP_EOL, $filtered);
    }

    if ($changed && $newContent !== $content) {
        write_file($erasurePath, $newContent, $stamp);
    } else {
        echo 'Keine Änderung nötig: ' . $erasurePath . PHP_EOL;
    }
}

// 4) QA-Tools installieren.
install_project_file('tools/qa/check_no_productive_legacy_identity_permission_group_usage.php', $root, $packageRoot, $stamp);
install_project_file('tools/qa/run_identity_permission_group_cleanup_checks.php', $root, $packageRoot, $stamp);
install_project_file('tools/qa/check_mini_project_13_1_tail_cleanup.php', $root, $packageRoot, $stamp);
install_project_file('database/sql/verify_mini_project_13_1_legacy_identity_tail_cleanup.sql', $root, $packageRoot, $stamp);

echo PHP_EOL;
echo 'Mini-Projekt 13.1 Legacy-Identity-Tail-Cleanup wurde angewendet.' . PHP_EOL;
echo 'Bitte ausführen:' . PHP_EOL;
echo '  php -l src\\Repository\\EntityAuditRepository.php' . PHP_EOL;
echo '  php -l src\\Repository\\PersonErasureRepository.php' . PHP_EOL;
echo '  php bin\\console seed' . PHP_EOL;
echo '  php tools\\qa\\check_mini_project_13_1_tail_cleanup.php' . PHP_EOL;
echo '  php tools\\qa\\check_no_productive_legacy_identity_permission_group_usage.php' . PHP_EOL;
echo '  php tools\\qa\\run_identity_permission_group_cleanup_checks.php' . PHP_EOL;
