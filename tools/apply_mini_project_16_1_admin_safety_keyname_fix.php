<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$stamp = date('Ymd-His');

function mp161_path(string $root, string $relative): string
{
    return $root . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relative);
}

function mp161_backup(string $file, string $stamp): void
{
    if (!is_file($file)) {
        return;
    }
    $backup = $file . '.bak-mini-project-16-1-' . $stamp;
    if (!copy($file, $backup)) {
        throw new RuntimeException('Backup fehlgeschlagen: ' . $file);
    }
    echo 'Backup: ' . $backup . PHP_EOL;
}

function mp161_patch_file(string $root, string $relative, string $stamp): void
{
    $file = mp161_path($root, $relative);
    if (!is_file($file)) {
        throw new RuntimeException('Datei fehlt: ' . $file);
    }

    $content = file_get_contents($file);
    if ($content === false) {
        throw new RuntimeException('Datei konnte nicht gelesen werden: ' . $file);
    }

    $patched = str_replace(
        [
            's.system_key',
            'g.group_key',
            'p.permission_key',
            'sys.system_key',
            'SELECT permission_key FROM ids_permissions',
            'SELECT p.permission_key',
            'AND s.key_name = :system_key\n               AND g.key_name = :group_key',
        ],
        [
            's.key_name',
            'g.key_name',
            'p.key_name',
            'sys.key_name',
            'SELECT key_name FROM ids_permissions',
            'SELECT p.key_name',
            'AND s.key_name = :system_key\n               AND g.key_name = :group_key',
        ],
        $content
    );

    // Repair the old QA SELECT so the row still exposes system_key to array-based service tests.
    $patched = preg_replace(
        '/SELECT\s+g\.id,\s*g\.key_name,\s*s\.key_name\s+FROM ids_groups g/s',
        'SELECT g.id, g.key_name, g.is_system, s.key_name AS system_key FROM ids_groups g',
        $patched
    ) ?? $patched;

    // If the ZIP was extracted with overwrite, the file is already the fixed version. Still keep a backup for traceability.
    if ($patched !== $content) {
        mp161_backup($file, $stamp);
        if (file_put_contents($file, $patched) === false) {
            throw new RuntimeException('Datei konnte nicht geschrieben werden: ' . $file);
        }
        echo 'Aktualisiert: ' . $relative . PHP_EOL;
    } else {
        echo 'OK: ' . $relative . ' enthält bereits key_name-kompatible Abfragen.' . PHP_EOL;
    }
}

function mp161_remove_useless_pdo_import(string $root, string $stamp): void
{
    $file = mp161_path($root, 'config/services.php');
    if (!is_file($file)) {
        return;
    }
    $content = file_get_contents($file);
    if ($content === false) {
        throw new RuntimeException('config/services.php konnte nicht gelesen werden.');
    }
    $patched = preg_replace('/^use\s+PDO;\R/m', '', $content) ?? $content;
    if ($patched !== $content) {
        mp161_backup($file, $stamp);
        if (file_put_contents($file, $patched) === false) {
            throw new RuntimeException('config/services.php konnte nicht geschrieben werden.');
        }
        echo 'Entfernt: wirkungsloses use PDO; in config/services.php' . PHP_EOL;
    }
}

mp161_patch_file($root, 'src/Security/IdentityAdminSafetyService.php', $stamp);
mp161_patch_file($root, 'tools/qa/check_admin_safety_service.php', $stamp);
mp161_remove_useless_pdo_import($root, $stamp);

echo PHP_EOL;
echo 'Mini-Projekt 16.1 Admin-Safety-key_name-Fix wurde angewendet.' . PHP_EOL;
echo 'Bitte ausführen:' . PHP_EOL;
echo '  php -l src\\Security\\IdentityAdminSafetyService.php' . PHP_EOL;
echo '  php -l config\\services.php' . PHP_EOL;
echo '  php tools\\qa\\check_admin_safety_service.php' . PHP_EOL;
echo '  php tools\\qa\\run_identity_admin_safety_checks.php' . PHP_EOL;
