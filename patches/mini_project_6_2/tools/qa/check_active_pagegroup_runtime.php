<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$failures = [];

function read_file_or_fail(string $file): string
{
    if (!is_file($file)) {
        throw new RuntimeException('Datei fehlt: ' . $file);
    }

    $content = file_get_contents($file);
    if ($content === false) {
        throw new RuntimeException('Datei konnte nicht gelesen werden: ' . $file);
    }

    return $content;
}

function is_in_block_comment_state(string $line, bool &$inBlockComment): bool
{
    $trim = trim($line);

    if ($inBlockComment) {
        if (str_contains($trim, '*/')) {
            $inBlockComment = false;
        }
        return true;
    }

    if (str_starts_with($trim, '/*')) {
        if (!str_contains($trim, '*/')) {
            $inBlockComment = true;
        }
        return true;
    }

    return false;
}

function is_inactive_line(string $line): bool
{
    $trim = trim($line);
    return $trim === '' || str_starts_with($trim, '//') || str_starts_with($trim, '#') || str_starts_with($trim, '*') || str_starts_with($trim, '|');
}

function scan_routes(string $root, array &$failures): void
{
    $file = $root . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'routes.php';
    $lines = preg_split('/\R/', read_file_or_fail($file));
    if ($lines === false) {
        $failures[] = 'config/routes.php konnte nicht gelesen werden.';
        return;
    }

    $inBlockComment = false;

    foreach ($lines as $index => $line) {
        $lineNo = $index + 1;
        if (is_in_block_comment_state($line, $inBlockComment) || is_inactive_line($line)) {
            continue;
        }

        if (!str_contains($line, 'new Route(')) {
            continue;
        }

        // Erlaubt: Administration-Controller-Aliase. Diese sind das Zielsystem und kein PageGroup-Rest.
        $allowedAdminAliases = [
            'AdminGruppenController::class',
            'AdminPermissionsController::class',
            'AdminPersonenGruppenController::class',
            'AdminSystemeController::class',
            'AdminSubjectsController::class',
            'AdminAdministrationController::class',
            'AdministrationGruppenController::class',
            'AdministrationPermissionsController::class',
            'AdministrationPersonenGruppenController::class',
            'AdministrationSystemeController::class',
        ];

        $isAllowedAdmin = false;
        foreach ($allowedAdminAliases as $alias) {
            if (str_contains($line, $alias)) {
                $isAllowedAdmin = true;
                break;
            }
        }
        if ($isAllowedAdmin) {
            continue;
        }

        $badRouteTokens = [
            "BerechtigungenController::class" => 'alter BerechtigungenController aktiv',
            "GruppenController::class" => 'alter GruppenController aktiv',
            "'/verwaltung/berechtigungen/page-groups'" => 'alte PageGroup-Route aktiv',
            "'/verwaltung/berechtigungen/gruppen/{id}'" => 'alte Gruppen-Berechtigungen-Route aktiv',
        ];

        foreach ($badRouteTokens as $token => $reason) {
            if (str_contains($line, $token)) {
                $failures[] = 'config/routes.php:' . $lineNo . ': ' . $reason . ': ' . trim($line);
            }
        }
    }
}

function scan_controller_for_pagegroup_calls(string $root, string $relative, array &$failures): void
{
    $file = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative);
    if (!is_file($file)) {
        return;
    }

    $lines = preg_split('/\R/', read_file_or_fail($file));
    if ($lines === false) {
        $failures[] = $relative . ': konnte nicht gelesen werden.';
        return;
    }

    $inBlockComment = false;
    foreach ($lines as $index => $line) {
        $lineNo = $index + 1;
        if (is_in_block_comment_state($line, $inBlockComment) || is_inactive_line($line)) {
            continue;
        }

        foreach (['requirePageGroupAccess(', 'canAccessPageGroup(', 'currentUserCanAccessPageGroup('] as $token) {
            if (str_contains($line, $token)) {
                $failures[] = $relative . ':' . $lineNo . ': alter PageGroup-Aufruf: ' . trim($line);
            }
        }
    }
}

scan_routes($root, $failures);

$activeControllerFiles = [
    'src/Http/Controller/Verwaltung/VerwaltungController.php',
    'src/Http/Controller/Verwaltung/PersonenController.php',
    'src/Http/Controller/Verwaltung/AuditLogController.php',
    'src/Http/Controller/Verwaltung/EntityAuditController.php',
    'src/Http/Controller/Verwaltung/DatenschutzController.php',
    'src/Http/Controller/Verwaltung/EinladungenController.php',
    'src/Http/Controller/Verwaltung/SchulverzeichnisController.php',
    'src/Http/Controller/Administration/AdministrationController.php',
    'src/Http/Controller/Administration/GruppenController.php',
    'src/Http/Controller/Administration/PermissionsController.php',
    'src/Http/Controller/Administration/PersonenGruppenController.php',
    'src/Http/Controller/Administration/SystemeController.php',
    'src/Http/Controller/Administration/SubjectsController.php',
];

foreach ($activeControllerFiles as $relative) {
    scan_controller_for_pagegroup_calls($root, $relative, $failures);
}

if ($failures !== []) {
    echo 'Aktive PageGroup-/Legacy-Runtime-Treffer gefunden:' . PHP_EOL;
    foreach ($failures as $failure) {
        echo ' - ' . $failure . PHP_EOL;
    }
    exit(1);
}

echo 'OK: Keine aktiven PageGroup-Runtime-Aufrufe oder alten PageGroup-Routen gefunden.' . PHP_EOL;
