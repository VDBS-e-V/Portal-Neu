<?php

declare(strict_types=1);

/**
 * Mini-Projekt 6: PageGroup-Cleanup aus aktiv erreichbaren Laufzeitpfaden.
 *
 * Dieses Tool ist bewusst konservativ:
 * - Es ersetzt requirePageGroupAccess(...) in bekannten aktiven Verwaltung-Controllern.
 * - Es bereinigt config/routes.php von toten Gruppen-/Berechtigungen-Controller-Imports
 *   und alten deaktivierten Kommentar-Routen.
 * - Es verändert keine Datenbankmigrationen und löscht keine alten Dateien.
 */

$root = dirname(__DIR__);
$timestamp = date('Ymd-His');

function path_join(string ...$parts): string
{
    return implode(DIRECTORY_SEPARATOR, $parts);
}

function read_file_required(string $path): string
{
    if (!is_file($path)) {
        throw new RuntimeException("Datei nicht gefunden: {$path}");
    }

    $content = file_get_contents($path);
    if ($content === false) {
        throw new RuntimeException("Datei konnte nicht gelesen werden: {$path}");
    }

    return $content;
}

function write_with_backup(string $path, string $content, string $timestamp): void
{
    $current = is_file($path) ? (string) file_get_contents($path) : '';
    if ($current === $content) {
        echo "Unverändert: {$path}\n";
        return;
    }

    if (is_file($path)) {
        $backup = $path . '.bak-mini-project-6-' . $timestamp;
        if (!copy($path, $backup)) {
            throw new RuntimeException("Backup konnte nicht erstellt werden: {$backup}");
        }
        echo "Backup: {$backup}\n";
    }

    if (file_put_contents($path, $content) === false) {
        throw new RuntimeException("Datei konnte nicht geschrieben werden: {$path}");
    }

    echo "Aktualisiert: {$path}\n";
}

function replace_require_page_group_access(string $content, string $permission): string
{
    $replacement = '$this->authorization->requirePermission(\'' . $permission . '\')';

    // Einzeilige Aufrufe wie:
    // $this->authorization->requirePageGroupAccess('verwaltung', 'personen');
    $content = preg_replace(
        '/\$this->authorization->requirePageGroupAccess\s*\([^;]*?\)\s*;/s',
        $replacement . ';',
        $content
    ) ?? $content;

    // Falls ältere Helper direkt aufgerufen werden.
    $content = preg_replace(
        '/\$this->authorization->canAccessPageGroup\s*\([^;]*?\)/s',
        '$this->authorization->can(\'' . $permission . '\')',
        $content
    ) ?? $content;

    return $content;
}

$controllerPermissions = [
    'src/Http/Controller/Verwaltung/VerwaltungController.php' => 'portal.verwaltung.dashboard.view',
    'src/Http/Controller/Verwaltung/PersonenController.php' => 'portal.verwaltung.personen.view',
    'src/Http/Controller/Verwaltung/AuditLogController.php' => 'portal.verwaltung.audit.view',
    'src/Http/Controller/Verwaltung/EntityAuditController.php' => 'portal.verwaltung.entity-audit.view',
    'src/Http/Controller/Verwaltung/DatenschutzController.php' => 'portal.verwaltung.datenschutz.view',
    'src/Http/Controller/Verwaltung/EinladungenController.php' => 'portal.verwaltung.einladungen.view',
    'src/Http/Controller/Verwaltung/SchulverzeichnisController.php' => 'portal.verwaltung.schulverzeichnis.view',

    // Diese alten Controller sollten nicht mehr geroutet sein. Trotzdem bereinigen wir sie,
    // damit versehentliche Aufrufe nicht mehr am entfernten PageGroup-System hängen.
    'src/Http/Controller/Verwaltung/GruppenController.php' => 'identity.gruppen.view',
    'src/Http/Controller/Verwaltung/BerechtigungenController.php' => 'identity.permissions.view',
];

foreach ($controllerPermissions as $relative => $permission) {
    $path = path_join($root, str_replace('/', DIRECTORY_SEPARATOR, $relative));
    if (!is_file($path)) {
        echo "Übersprungen, nicht vorhanden: {$relative}\n";
        continue;
    }

    $content = read_file_required($path);
    $updated = replace_require_page_group_access($content, $permission);
    write_with_backup($path, $updated, $timestamp);
}

$routesPath = path_join($root, 'config', 'routes.php');
$routes = read_file_required($routesPath);

// Nicht mehr benötigte alte Rechteverwaltungs-Imports entfernen. Die neuen Aliase
// AdministrationGruppenController / AdministrationPermissionsController bleiben bestehen.
$routes = preg_replace(
    '/^use\s+App\\Http\\Controller\\Verwaltung\\GruppenController;\R/m',
    '',
    $routes
) ?? $routes;

$routes = preg_replace(
    '/^use\s+App\\Http\\Controller\\Verwaltung\\BerechtigungenController;\R/m',
    '',
    $routes
) ?? $routes;

// Alte bereits deaktivierte Kommentar-Routen komplett entfernen, damit QA/findstr nicht mehr
// fälschlich aktive Controller erkennt.
$routes = preg_replace(
    '/^\s*\/\/\s*Deaktiviert durch Mini-Projekt 3 Legacy-Verwaltung-Alias:.*(?:GruppenController|BerechtigungenController)::class.*\R/m',
    '',
    $routes
) ?? $routes;

// Kommentartext ohne technischen PageGroup-Begriff halten, damit die alte Suche weniger rauscht.
$routes = str_replace('PageGroup-Controller', 'Legacy-Controller', $routes);
$routes = str_replace('pageGroup', 'legacyAccess', $routes);

write_with_backup($routesPath, $routes, $timestamp);

$seedSource = path_join($root, 'database', 'seeds', 'seed_verwaltung_runtime_permissions.sql');
if (!is_file($seedSource)) {
    echo "Hinweis: seed_verwaltung_runtime_permissions.sql wurde durch das ZIP noch nicht kopiert oder liegt nicht unter database/seeds.\n";
}

echo "\nMini-Projekt 6 PageGroup-Runtime-Cleanup wurde angewendet.\n";
echo "Bitte ausführen:\n";
echo "  php -l src\\Http\\Controller\\Verwaltung\\VerwaltungController.php\n";
echo "  php -l src\\Http\\Controller\\Verwaltung\\PersonenController.php\n";
echo "  php -l src\\Http\\Controller\\Verwaltung\\AuditLogController.php\n";
echo "  php -l src\\Http\\Controller\\Verwaltung\\EntityAuditController.php\n";
echo "  php -l src\\Http\\Controller\\Verwaltung\\DatenschutzController.php\n";
echo "  php -l src\\Http\\Controller\\Verwaltung\\EinladungenController.php\n";
echo "  php -l config\\routes.php\n";
echo "  php bin\\console seed\n";
echo "  php tools\\qa\\check_active_pagegroup_runtime.php\n";
