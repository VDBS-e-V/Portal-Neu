<?php

declare(strict_types=1);

$projectRoot = dirname(__DIR__);
$stamp = date('Ymd-His');

installFile('src/Security/RoutePermissionMap.php', $projectRoot, $stamp);
installFile('src/Http/Controller/UserAccountController.php', $projectRoot, $stamp);
installFile('resources/views/pages/konto/profile.php', $projectRoot, $stamp);
installFile('resources/views/pages/konto/settings.php', $projectRoot, $stamp);
patchRoutes($projectRoot . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'routes.php', $stamp);
installFile('tools/qa/check_account_canonical_routes.php', $projectRoot, $stamp);

echo "Mini-Projekt 5.4 wurde angewendet: alte /user-/Legacy-Konto-URLs entfernt, /konto bleibt kanonisch.\n";
echo "Bitte ausführen:\n";
echo "  php -l src\\Security\\RoutePermissionMap.php\n";
echo "  php -l src\\Http\\Controller\\UserAccountController.php\n";
echo "  php -l config\\routes.php\n";
echo "  php tools\\qa\\check_account_canonical_routes.php\n";
echo "Danach testen: /konto und /konto/einstellungen\n";

function installFile(string $relative, string $projectRoot, string $stamp): void
{
    $source = $projectRoot . DIRECTORY_SEPARATOR . $relative;
    $target = $projectRoot . DIRECTORY_SEPARATOR . $relative;

    if (!is_file($source)) {
        echo "Hinweis: {$relative} wurde nicht als Paketdatei gefunden, überspringe Kopie.\n";
        return;
    }

    backup($target, $stamp);
    // Quelle und Ziel sind identisch, wenn das Paket bereits ins Projekt entpackt wurde.
    // In diesem Fall reicht das Backup; die Datei ist schon ersetzt.
    if (realpath($source) !== realpath($target)) {
        $dir = dirname($target);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        copy($source, $target);
        echo "Geschrieben: {$relative}\n";
    } else {
        echo "Vorhanden: {$relative}\n";
    }
}

function patchRoutes(string $file, string $stamp): void
{
    if (!is_file($file)) {
        fail('config/routes.php nicht gefunden: ' . $file);
    }

    $code = readStrict($file);
    backup($file, $stamp);

    $code = preg_replace('/\s*\/\/ MINI PROJECT 5\.3 ACCOUNT ROUTES START.*?\/\/ MINI PROJECT 5\.3 ACCOUNT ROUTES END\s*/s', "\n", $code) ?? $code;
    $code = preg_replace('/\s*\/\/ MINI PROJECT 5\.1 KONTO ROUTES START.*?\/\/ MINI PROJECT 5\.1 KONTO ROUTES END\s*/s', "\n", $code) ?? $code;

    $lines = preg_split('/\R/', $code) ?: [];
    $filtered = [];
    foreach ($lines as $line) {
        $remove = false;

        // Alte Konto-/User-URLs vollständig aus Routen und Kommentaren entfernen.
        foreach ([
            "'/user'",
            '"/user"',
            "'/user/settings'",
            '"/user/settings"',
            "'/user/password'",
            '"/user/password"',
            "'/konto/profil'",
            '"/konto/profil"',
            "'/konto/profil/bearbeiten'",
            '"/konto/profil/bearbeiten"',
            "'/konto/kontakte'",
            '"/konto/kontakte"',
            "'/konto/adressen'",
            '"/konto/adressen"',
            "'/konto/sicherheit'",
            '"/konto/sicherheit"',
        ] as $needle) {
            if (str_contains($line, $needle)) {
                $remove = true;
                break;
            }
        }

        // Alias aus einem früheren Fix wird nicht mehr gebraucht.
        if (str_contains($line, 'UserAccountController as KontoController')) {
            $remove = true;
        }

        if (!$remove) {
            $filtered[] = $line;
        }
    }
    $code = implode(PHP_EOL, $filtered);

    $block = <<<'PHPROUTES'
    // ACCOUNT ROUTES CANONICAL START
    // Kanonische Konto-URLs. Es gibt keine /user- oder Legacy-Konto-Aliase mehr.
    new Route('GET', '/konto', \App\Http\Controller\UserAccountController::class, 'profile'),
    new Route('POST', '/konto', \App\Http\Controller\UserAccountController::class, 'updateProfile'),
    new Route('GET', '/konto/einstellungen', \App\Http\Controller\UserAccountController::class, 'settings'),
    new Route('POST', '/konto/einstellungen', \App\Http\Controller\UserAccountController::class, 'updateSettings'),
    new Route('POST', '/konto/passwort', \App\Http\Controller\UserAccountController::class, 'updatePassword'),
    // ACCOUNT ROUTES CANONICAL END

PHPROUTES;

    if (!str_contains($code, 'ACCOUNT ROUTES CANONICAL START')) {
        $pos = strpos($code, 'return [');
        if ($pos === false) {
            fail('return [ in config/routes.php nicht gefunden.');
        }
        $lineEnd = strpos($code, "\n", $pos);
        if ($lineEnd === false) {
            fail('return [-Zeile in config/routes.php ist ungültig.');
        }
        $code = substr($code, 0, $lineEnd + 1) . $block . substr($code, $lineEnd + 1);
    }

    file_put_contents($file, $code);
    echo "config/routes.php wurde bereinigt und auf kanonische /konto-Routen gesetzt.\n";
}

function readStrict(string $file): string
{
    $content = file_get_contents($file);
    if ($content === false) {
        fail('Datei konnte nicht gelesen werden: ' . $file);
    }
    return $content;
}

function backup(string $file, string $stamp): void
{
    if (!is_file($file)) {
        return;
    }
    $backup = $file . '.bak-mini-project-5-4-' . $stamp;
    copy($file, $backup);
    echo "Backup: {$backup}\n";
}

function fail(string $message): never
{
    fwrite(STDERR, $message . "\n");
    exit(1);
}
