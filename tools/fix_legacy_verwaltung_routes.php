<?php

declare(strict_types=1);

$projectRoot = dirname(__DIR__);
$routesFile = $projectRoot . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'routes.php';

if (!is_file($routesFile)) {
    fwrite(STDERR, "config/routes.php wurde nicht gefunden: {$routesFile}\n");
    exit(1);
}

$code = file_get_contents($routesFile);
if ($code === false) {
    fwrite(STDERR, "config/routes.php konnte nicht gelesen werden.\n");
    exit(1);
}

$backupFile = $routesFile . '.bak-legacy-verwaltung-' . date('Ymd-His');
if (!copy($routesFile, $backupFile)) {
    fwrite(STDERR, "Backup konnte nicht erstellt werden.\n");
    exit(1);
}

$uses = [
    "use App\\Http\\Controller\\Administration\\GruppenController as AdministrationGruppenController;",
    "use App\\Http\\Controller\\Administration\\PermissionsController as AdministrationPermissionsController;",
    "use App\\Http\\Controller\\Administration\\PersonenGruppenController as AdministrationPersonenGruppenController;",
    "use App\\Http\\Controller\\Administration\\SystemeController as AdministrationSystemeController;",
];

foreach ($uses as $use) {
    if (!str_contains($code, $use)) {
        $lastUsePosition = strrpos($code, "use ");
        if ($lastUsePosition === false) {
            fwrite(STDERR, "Keine use-Statements gefunden. Bitte manuell patchen.\n");
            exit(1);
        }

        $lineEnd = strpos($code, "\n", $lastUsePosition);
        if ($lineEnd === false) {
            fwrite(STDERR, "use-Statement-Block konnte nicht erkannt werden.\n");
            exit(1);
        }

        $code = substr($code, 0, $lineEnd + 1) . $use . "\n" . substr($code, $lineEnd + 1);
    }
}

$aliasBlockMarker = 'Mini-Projekt 3 Legacy-Verwaltung-Aliase';
$aliasBlock = <<<'PHP'

    /*
    |--------------------------------------------------------------------------
    | Mini-Projekt 3 Legacy-Verwaltung-Aliase
    |--------------------------------------------------------------------------
    | Diese Routen müssen vor alten /verwaltung/gruppen- und
    | /verwaltung/berechtigungen-Routen stehen, damit nicht mehr die alten
    | PageGroup-Controller geladen werden.
    */
    new Route('GET', '/verwaltung/gruppen', AdministrationGruppenController::class, 'index'),
    new Route('GET', '/verwaltung/gruppen/create', AdministrationGruppenController::class, 'createForm'),
    new Route('POST', '/verwaltung/gruppen/create', AdministrationGruppenController::class, 'create'),
    new Route('GET', '/verwaltung/gruppen/{id}', AdministrationGruppenController::class, 'show'),
    new Route('GET', '/verwaltung/gruppen/{id}/edit', AdministrationGruppenController::class, 'editForm'),
    new Route('POST', '/verwaltung/gruppen/{id}/edit', AdministrationGruppenController::class, 'edit'),
    new Route('GET', '/verwaltung/gruppen/{id}/permissions', AdministrationGruppenController::class, 'permissionsForm'),
    new Route('POST', '/verwaltung/gruppen/{id}/permissions', AdministrationGruppenController::class, 'permissions'),
    new Route('POST', '/verwaltung/gruppen/{id}/delete', AdministrationGruppenController::class, 'delete'),

    new Route('GET', '/verwaltung/berechtigungen', AdministrationPermissionsController::class, 'index'),
    new Route('GET', '/verwaltung/permissions', AdministrationPermissionsController::class, 'index'),
    new Route('GET', '/verwaltung/permissions/create', AdministrationPermissionsController::class, 'createForm'),
    new Route('POST', '/verwaltung/permissions/create', AdministrationPermissionsController::class, 'create'),
    new Route('GET', '/verwaltung/permissions/{id}', AdministrationPermissionsController::class, 'show'),
    new Route('GET', '/verwaltung/permissions/{id}/edit', AdministrationPermissionsController::class, 'editForm'),
    new Route('POST', '/verwaltung/permissions/{id}/edit', AdministrationPermissionsController::class, 'edit'),
    new Route('POST', '/verwaltung/permissions/{id}/deactivate', AdministrationPermissionsController::class, 'deactivate'),

    new Route('GET', '/verwaltung/personen-gruppen', AdministrationPersonenGruppenController::class, 'index'),
    new Route('GET', '/verwaltung/personen/{id}/gruppen', AdministrationPersonenGruppenController::class, 'groups'),
    new Route('POST', '/verwaltung/personen/{id}/gruppen', AdministrationPersonenGruppenController::class, 'assign'),
    new Route('POST', '/verwaltung/personen/{id}/gruppen/{groupId}/remove', AdministrationPersonenGruppenController::class, 'remove'),

    new Route('GET', '/verwaltung/systeme', AdministrationSystemeController::class, 'index'),

PHP;

if (!str_contains($code, $aliasBlockMarker)) {
    $code = preg_replace('/return\s*\[\s*/', "return [" . $aliasBlock, $code, 1, $count);
    if ($count !== 1) {
        fwrite(STDERR, "return [ konnte in config/routes.php nicht eindeutig gefunden werden.\n");
        exit(1);
    }
}

// Alte direkte Routen auskommentieren, damit bei einfachen Routern keine alte Route gewinnt.
$lines = preg_split('/\R/', $code);
if ($lines === false) {
    fwrite(STDERR, "config/routes.php konnte nicht in Zeilen zerlegt werden.\n");
    exit(1);
}

$patchedLines = [];
$insideAliasBlock = false;
foreach ($lines as $line) {
    if (str_contains($line, $aliasBlockMarker)) {
        $insideAliasBlock = true;
        $patchedLines[] = $line;
        continue;
    }

    if ($insideAliasBlock && str_contains($line, 'new Route(') && str_contains($line, "/verwaltung/systeme")) {
        $patchedLines[] = $line;
        $insideAliasBlock = false;
        continue;
    }

    if (!$insideAliasBlock) {
        $isOldGruppenRoute = str_contains($line, "'/verwaltung/gruppen")
            && str_contains($line, 'GruppenController::class')
            && !str_contains($line, 'AdministrationGruppenController::class');

        $isOldBerechtigungenRoute = str_contains($line, "'/verwaltung/berechtigungen")
            && str_contains($line, 'BerechtigungenController::class')
            && !str_contains($line, 'AdministrationPermissionsController::class');

        if ($isOldGruppenRoute || $isOldBerechtigungenRoute) {
            $patchedLines[] = '    // Deaktiviert durch Mini-Projekt 3 Legacy-Verwaltung-Alias: ' . ltrim($line);
            continue;
        }
    }

    $patchedLines[] = $line;
}

$code = implode(PHP_EOL, $patchedLines);

if (file_put_contents($routesFile, $code) === false) {
    fwrite(STDERR, "config/routes.php konnte nicht geschrieben werden.\n");
    exit(1);
}

$lintCommand = PHP_BINARY . ' -l ' . escapeshellarg($routesFile);
exec($lintCommand, $lintOutput, $lintExitCode);

if ($lintExitCode !== 0) {
    copy($backupFile, $routesFile);
    fwrite(STDERR, "Patch hat einen Syntaxfehler erzeugt. Backup wurde wiederhergestellt.\n");
    fwrite(STDERR, implode("\n", $lintOutput) . "\n");
    exit(1);
}

echo "Legacy-Verwaltung-Routen wurden angepasst.\n";
echo "Backup: {$backupFile}\n";
echo "Bitte testen:\n";
echo "  /verwaltung/gruppen\n";
echo "  /administration/gruppen\n";
