<?php

declare(strict_types=1);

$root = dirname(__DIR__);

function projectPath(string $relative): string
{
    global $root;
    return $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative);
}

function backupFile(string $path, string $suffix): void
{
    if (!is_file($path)) {
        return;
    }

    $backup = $path . '.bak-' . $suffix . '-' . date('Ymd-His');
    if (!copy($path, $backup)) {
        throw new RuntimeException('Backup konnte nicht erstellt werden: ' . $path);
    }

    echo "Backup: {$backup}\n";
}

function ensureUse(string $content, string $useLine): string
{
    if (str_contains($content, $useLine)) {
        return $content;
    }

    $lines = preg_split('/\R/', $content) ?: [];
    $lastUseIndex = null;
    foreach ($lines as $i => $line) {
        if (preg_match('/^use\s+[^;]+;\s*$/', trim($line))) {
            $lastUseIndex = $i;
        }
    }

    if ($lastUseIndex === null) {
        return $content;
    }

    array_splice($lines, $lastUseIndex + 1, 0, [$useLine]);
    return implode(PHP_EOL, $lines);
}

function replaceRouteControllerForPathPrefix(string $content, string $pathPrefix, string $controllerAlias): string
{
    $pattern = '~new\s+Route\(\s*([\'\"](?:GET|POST|PUT|PATCH|DELETE)[\'\"]\s*,\s*[\'\"]' . preg_quote($pathPrefix, '~') . '[^\'\"]*[\'\"]\s*,\s*)([A-Za-z0-9_\\\\]+)::class~';

    return preg_replace_callback($pattern, static function (array $m) use ($controllerAlias): string {
        return 'new Route(' . $m[1] . $controllerAlias . '::class';
    }, $content) ?? $content;
}

function copyTool(string $relative): void
{
    $source = __DIR__ . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative);
    $target = projectPath($relative);

    if (!is_file($source)) {
        throw new RuntimeException('Quelldatei fehlt: ' . $source);
    }

    $targetDir = dirname($target);
    if (!is_dir($targetDir) && !mkdir($targetDir, 0775, true) && !is_dir($targetDir)) {
        throw new RuntimeException('Zielordner konnte nicht erstellt werden: ' . $targetDir);
    }

    backupFile($target, 'mini-project-6-1');
    if (!copy($source, $target)) {
        throw new RuntimeException('Datei konnte nicht kopiert werden: ' . $relative);
    }
    echo "Aktualisiert: {$target}\n";
}

$routesPath = projectPath('config/routes.php');
if (!is_file($routesPath)) {
    throw new RuntimeException('config/routes.php nicht gefunden.');
}

$routes = file_get_contents($routesPath);
if ($routes === false) {
    throw new RuntimeException('config/routes.php konnte nicht gelesen werden.');
}

backupFile($routesPath, 'mini-project-6-1');

$adminUses = [
    'use App\\Http\\Controller\\Administration\\AdministrationController as AdminAdministrationController;',
    'use App\\Http\\Controller\\Administration\\SystemeController as AdminSystemeController;',
    'use App\\Http\\Controller\\Administration\\GruppenController as AdminGruppenController;',
    'use App\\Http\\Controller\\Administration\\PermissionsController as AdminPermissionsController;',
    'use App\\Http\\Controller\\Administration\\PersonenGruppenController as AdminPersonenGruppenController;',
    'use App\\Http\\Controller\\Administration\\SubjectsController as AdminSubjectsController;',
];

foreach ($adminUses as $use) {
    $routes = ensureUse($routes, $use);
}

$routes = replaceRouteControllerForPathPrefix($routes, '/administration/systeme', 'AdminSystemeController');
$routes = replaceRouteControllerForPathPrefix($routes, '/administration/gruppen', 'AdminGruppenController');
$routes = replaceRouteControllerForPathPrefix($routes, '/administration/permissions', 'AdminPermissionsController');
$routes = replaceRouteControllerForPathPrefix($routes, '/administration/personen', 'AdminPersonenGruppenController');
$routes = replaceRouteControllerForPathPrefix($routes, '/administration/subjects', 'AdminSubjectsController');
$routes = preg_replace(
    '~new\s+Route\(\s*([\'\"]GET[\'\"]\s*,\s*[\'\"]/administration[\'\"]\s*,\s*)([A-Za-z0-9_\\\\]+)::class~',
    'new Route($1AdminAdministrationController::class',
    $routes
) ?? $routes;

$routes = replaceRouteControllerForPathPrefix($routes, '/verwaltung/gruppen', 'AdminGruppenController');
$routes = replaceRouteControllerForPathPrefix($routes, '/verwaltung/berechtigungen', 'AdminPermissionsController');
$routes = replaceRouteControllerForPathPrefix($routes, '/verwaltung/permissions', 'AdminPermissionsController');
$routes = replaceRouteControllerForPathPrefix($routes, '/verwaltung/personen-gruppen', 'AdminPersonenGruppenController');
$routes = replaceRouteControllerForPathPrefix($routes, '/verwaltung/personen/{id}/gruppen', 'AdminPersonenGruppenController');
$routes = replaceRouteControllerForPathPrefix($routes, '/verwaltung/systeme', 'AdminSystemeController');

if (file_put_contents($routesPath, $routes) === false) {
    throw new RuntimeException('config/routes.php konnte nicht geschrieben werden.');
}
echo "Aktualisiert: {$routesPath}\n";

copyTool('tools/qa/check_active_pagegroup_runtime.php');
copyTool('tools/qa/check_verwaltung_permissions_seed.php');
copyTool('tools/qa/run_identity_runtime_checks.php');

echo "\nMini-Projekt 6.1 Runtime-QA-/Route-Fix wurde angewendet.\n";
echo "Bitte ausführen:\n";
echo "  php -l config\\routes.php\n";
echo "  php tools\\qa\\check_active_pagegroup_runtime.php\n";
echo "  php tools\\qa\\check_verwaltung_permissions_seed.php\n";
echo "  php tools\\qa\\run_identity_runtime_checks.php\n";
