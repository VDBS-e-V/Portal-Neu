<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$now = date('Ymd-His');

patchRoutes($root, $now);
patchRoutePermissionMap($root, $now);
installQaTools($root, $now);

fwrite(STDOUT, "Mini-Projekt 5.1 Konto-/QA-Fix wurde angewendet.\n");
fwrite(STDOUT, "Bitte ausführen:\n");
fwrite(STDOUT, "  php -l config\\routes.php\n");
fwrite(STDOUT, "  php -l src\\Security\\RoutePermissionMap.php\n");
fwrite(STDOUT, "  php tools\\qa\\check_konto_routes.php\n");
fwrite(STDOUT, "  php tools\\qa\\run_identity_rights_checks.php\n");
fwrite(STDOUT, "Danach testen: /konto und /konto/einstellungen\n");

function patchRoutes(string $root, string $now): void
{
    $file = $root . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'routes.php';
    if (!is_file($file)) {
        fail('config/routes.php nicht gefunden: ' . $file);
    }

    $code = readFileStrict($file);
    $original = $code;

    $use = 'use App\\Http\\Controller\\UserAccountController as KontoController;';
    if (!str_contains($code, $use)) {
        $anchor = "use App\\Http\\Controller\\UserAccountController;";
        if (str_contains($code, $anchor)) {
            $code = str_replace($anchor, $anchor . "\n" . $use, $code);
        } else {
            $code = preg_replace(
                '/(<\?php\s+declare\(strict_types=1\);\s+)/',
                '$1' . "\n" . $use . "\n",
                $code,
                1
            ) ?? $code;
        }
    }

    $lines = preg_split('/\R/', $code) ?: [];
    foreach ($lines as $index => $line) {
        if (str_contains($line, 'new Route(') && preg_match("~['\"]\/konto(?:['\"/])~", $line) === 1) {
            if (!str_contains($line, 'KontoController::class')) {
                $lines[$index] = '// [mini-project-5-1 disabled invalid /konto route] ' . $line;
            }
        }
    }
    $code = implode(PHP_EOL, $lines);

    if (!str_contains($code, 'MINI PROJECT 5.1 KONTO ROUTES')) {
        $block = <<<'PHP_CODE'
    // MINI PROJECT 5.1 KONTO ROUTES START
    // /konto ist ein Login-/Account-Bereich. Er nutzt keine PageGroups und keine feinen
    // Verwaltungspermissions. Der Controller prüft selbst, ob ein User eingeloggt ist.
    new Route('GET', '/konto', KontoController::class, 'profile'),
    new Route('POST', '/konto', KontoController::class, 'updateProfile'),
    new Route('GET', '/konto/einstellungen', KontoController::class, 'settings'),
    new Route('POST', '/konto/einstellungen', KontoController::class, 'updateSettings'),
    new Route('POST', '/konto/passwort', KontoController::class, 'updatePassword'),
    // MINI PROJECT 5.1 KONTO ROUTES END

PHP_CODE;
        $code = insertAfterReturnArrayStart($code, $block);
    }

    if ($code !== $original) {
        backup($file, $now);
        file_put_contents($file, $code);
        fwrite(STDOUT, "config/routes.php wurde angepasst.\n");
    } else {
        fwrite(STDOUT, "config/routes.php war bereits passend.\n");
    }
}

function patchRoutePermissionMap(string $root, string $now): void
{
    $file = $root . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Security' . DIRECTORY_SEPARATOR . 'RoutePermissionMap.php';
    if (!is_file($file)) {
        fwrite(STDOUT, "RoutePermissionMap.php nicht gefunden, überspringe.\n");
        return;
    }

    $code = readFileStrict($file);
    if (str_contains($code, 'MINI PROJECT 5.1 KONTO PUBLIC ACCOUNT ROUTES')) {
        fwrite(STDOUT, "RoutePermissionMap.php enthält /konto-Ausnahme bereits.\n");
        return;
    }

    $needle = "        foreach (\$this->entries() as \$entry) {\n";
    if (!str_contains($code, $needle)) {
        fwrite(STDOUT, "Marker in RoutePermissionMap.php nicht gefunden, überspringe /konto-Ausnahme.\n");
        return;
    }

    $insert = <<<'PHP_CODE'
        // MINI PROJECT 5.1 KONTO PUBLIC ACCOUNT ROUTES START
        // Accountseiten brauchen einen eingeloggten User, aber keine Vereins-/Admin-Permission.
        // Die Login-Prüfung passiert im UserAccountController.
        if ($path === '/konto' || str_starts_with($path, '/konto/')) {
            return null;
        }
        // MINI PROJECT 5.1 KONTO PUBLIC ACCOUNT ROUTES END

PHP_CODE;

    backup($file, $now);
    $code = str_replace($needle, $insert . $needle, $code);
    file_put_contents($file, $code);
    fwrite(STDOUT, "RoutePermissionMap.php wurde um /konto-Ausnahme ergänzt.\n");
}

function installQaTools(string $root, string $now): void
{
    $sourceDir = __DIR__ . DIRECTORY_SEPARATOR . 'qa';
    $targetDir = $root . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'qa';
    if (!is_dir($targetDir) && !mkdir($targetDir, 0777, true) && !is_dir($targetDir)) {
        fail('QA-Zielordner konnte nicht erstellt werden: ' . $targetDir);
    }

    $files = [
        'check_konto_routes.php',
        'run_identity_rights_checks.php',
    ];

    foreach ($files as $fileName) {
        $source = $sourceDir . DIRECTORY_SEPARATOR . $fileName;
        $target = $targetDir . DIRECTORY_SEPARATOR . $fileName;
        if (!is_file($source)) {
            continue;
        }
        if (is_file($target)) {
            backup($target, $now);
        }
        copy($source, $target);
        fwrite(STDOUT, "QA-Tool installiert: tools/qa/{$fileName}\n");
    }
}

function insertAfterReturnArrayStart(string $code, string $block): string
{
    $pos = strpos($code, 'return [');
    if ($pos === false) {
        fail('return [-Array in config/routes.php nicht gefunden.');
    }
    $lineEnd = strpos($code, "\n", $pos);
    if ($lineEnd === false) {
        fail('return [-Zeile in config/routes.php ist ungültig.');
    }
    return substr($code, 0, $lineEnd + 1) . $block . substr($code, $lineEnd + 1);
}

function readFileStrict(string $file): string
{
    $code = file_get_contents($file);
    if ($code === false) {
        fail('Datei konnte nicht gelesen werden: ' . $file);
    }
    return $code;
}

function backup(string $file, string $now): void
{
    $backup = $file . '.bak-mini-project-5-1-' . $now;
    if (!copy($file, $backup)) {
        fail('Backup konnte nicht erstellt werden: ' . $backup);
    }
    fwrite(STDOUT, "Backup: {$backup}\n");
}

function fail(string $message): never
{
    fwrite(STDERR, $message . "\n");
    exit(1);
}
