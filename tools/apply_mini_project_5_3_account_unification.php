<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$stamp = date('Ymd-His');

function backupFile(string $path, string $stamp): void
{
    if (is_file($path)) {
        $backup = $path . '.bak-mini-project-5-3-' . $stamp;
        copy($path, $backup);
        echo "Backup: {$backup}\n";
    }
}

function writeFile(string $path, string $content, string $stamp): void
{
    $dir = dirname($path);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    backupFile($path, $stamp);
    file_put_contents($path, $content);
    echo "Geschrieben: {$path}\n";
}

function readPackageFile(string $relative): string
{
    $packageRoot = dirname(__DIR__);
    $path = $packageRoot . DIRECTORY_SEPARATOR . $relative;
    if (!is_file($path)) {
        throw new RuntimeException("Paketdatei fehlt: {$path}");
    }
    $content = file_get_contents($path);
    if ($content === false) {
        throw new RuntimeException("Paketdatei konnte nicht gelesen werden: {$path}");
    }
    return $content;
}

function ensureAccountRoutes(string $routesPath, string $stamp): void
{
    if (!is_file($routesPath)) {
        throw new RuntimeException("config/routes.php nicht gefunden: {$routesPath}");
    }

    $content = file_get_contents($routesPath);
    if ($content === false) {
        throw new RuntimeException('config/routes.php konnte nicht gelesen werden.');
    }

    backupFile($routesPath, $stamp);

    $block = <<<'PHPROUTES'
    // MINI PROJECT 5.3 ACCOUNT ROUTES START
    // Kanonisch ist /konto. /user bleibt als Legacy-Alias erhalten.
    new Route('GET', '/konto', \App\Http\Controller\UserAccountController::class, 'profile'),
    new Route('POST', '/konto', \App\Http\Controller\UserAccountController::class, 'updateProfile'),
    new Route('GET', '/konto/einstellungen', \App\Http\Controller\UserAccountController::class, 'settings'),
    new Route('POST', '/konto/einstellungen', \App\Http\Controller\UserAccountController::class, 'updateSettings'),
    new Route('POST', '/konto/passwort', \App\Http\Controller\UserAccountController::class, 'updatePassword'),

    new Route('GET', '/user', \App\Http\Controller\UserAccountController::class, 'redirectUserProfile'),
    new Route('POST', '/user', \App\Http\Controller\UserAccountController::class, 'updateProfile'),
    new Route('GET', '/user/settings', \App\Http\Controller\UserAccountController::class, 'redirectUserSettings'),
    new Route('POST', '/user/settings', \App\Http\Controller\UserAccountController::class, 'updateSettings'),

    new Route('GET', '/konto/profil', \App\Http\Controller\UserAccountController::class, 'redirectLegacyProfile'),
    new Route('GET', '/konto/profil/bearbeiten', \App\Http\Controller\UserAccountController::class, 'redirectLegacyProfile'),
    new Route('GET', '/konto/sicherheit', \App\Http\Controller\UserAccountController::class, 'redirectLegacySettings'),
    // MINI PROJECT 5.3 ACCOUNT ROUTES END
PHPROUTES;

    $patterns = [
        '/\s*\/\/ MINI PROJECT 5\.3 ACCOUNT ROUTES START.*?\/\/ MINI PROJECT 5\.3 ACCOUNT ROUTES END\s*/s',
        '/\s*\/\/ MINI PROJECT 5\.1 KONTO ROUTES START.*?\/\/ MINI PROJECT 5\.1 KONTO ROUTES END\s*/s',
    ];

    $replaced = false;
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $content) === 1) {
            $content = preg_replace($pattern, "\n" . $block . "\n", $content, 1) ?? $content;
            $replaced = true;
            break;
        }
    }

    if (!$replaced) {
        $pos = strpos($content, 'return [');
        if ($pos === false) {
            throw new RuntimeException('return [ in config/routes.php nicht gefunden.');
        }
        $insertAt = $pos + strlen('return [');
        $content = substr($content, 0, $insertAt) . "\n" . $block . "\n" . substr($content, $insertAt);
    }

    $legacyNeedles = [
        "new Route('GET', '/user', UserAccountController::class, 'profile')",
        "new Route('POST', '/user', UserAccountController::class, 'updateProfile')",
        "new Route('GET', '/user/settings', UserAccountController::class, 'settings')",
        "new Route('POST', '/user/settings', UserAccountController::class, 'updateSettings')",
        "new Route('POST', '/user/password', UserAccountController::class, 'updatePassword')",
        "new Route('GET', '/konto', AccountController::class, 'index')",
        "new Route('GET', '/konto/passwort', AccountController::class, 'passwordForm')",
        "new Route('POST', '/konto/passwort', AccountController::class, 'changePassword')",
    ];

    foreach ($legacyNeedles as $needle) {
        $content = str_replace($needle, '// [mini-project-5-3 disabled duplicate account route] ' . $needle, $content);
    }

    file_put_contents($routesPath, $content);
    echo "config/routes.php wurde auf kanonische /konto-Routen vereinheitlicht.\n";
}

function patchRoutePermissionMap(string $path, string $stamp): void
{
    if (!is_file($path)) {
        return;
    }
    $content = file_get_contents($path);
    if ($content === false) {
        return;
    }
    $original = $content;

    foreach (['/konto', '/konto/einstellungen', '/konto/passwort', '/user', '/user/settings'] as $route) {
        if (str_contains($content, "'{$route}'") || str_contains($content, "\"{$route}\"")) {
            continue;
        }

        if (str_contains($content, "'/konto'")) {
            $content = str_replace("'/konto'", "'/konto',\n            '{$route}'", $content);
            continue;
        }

        if (preg_match('/\$publicRoutes\s*=\s*\[/m', $content) === 1) {
            $content = preg_replace('/(\$publicRoutes\s*=\s*\[)/m', "$1\n            '{$route}',", $content, 1) ?? $content;
            continue;
        }
    }

    if ($content !== $original) {
        backupFile($path, $stamp);
        file_put_contents($path, $content);
        echo "RoutePermissionMap.php wurde um Konto-/User-Ausnahmen ergänzt.\n";
    }
}

writeFile(
    $root . DIRECTORY_SEPARATOR . 'src/Http/Controller/UserAccountController.php',
    readPackageFile('src/Http/Controller/UserAccountController.php'),
    $stamp
);

writeFile(
    $root . DIRECTORY_SEPARATOR . 'resources/views/pages/konto/profile.php',
    readPackageFile('resources/views/pages/konto/profile.php'),
    $stamp
);

writeFile(
    $root . DIRECTORY_SEPARATOR . 'resources/views/pages/konto/settings.php',
    readPackageFile('resources/views/pages/konto/settings.php'),
    $stamp
);

ensureAccountRoutes($root . DIRECTORY_SEPARATOR . 'config/routes.php', $stamp);
patchRoutePermissionMap($root . DIRECTORY_SEPARATOR . 'src/Security/RoutePermissionMap.php', $stamp);

$qaPath = $root . DIRECTORY_SEPARATOR . 'tools/qa/check_account_unification.php';
writeFile($qaPath, readPackageFile('tools/qa/check_account_unification.php'), $stamp);

echo "Mini-Projekt 5.3 Konto-/User-Vereinheitlichung wurde angewendet.\n";
echo "Bitte ausführen:\n";
echo "  php -l src\\Http\\Controller\\UserAccountController.php\n";
echo "  php -l config\\routes.php\n";
echo "  php tools\\qa\\check_account_unification.php\n";
echo "Danach im Browser testen: /konto, /konto/einstellungen, /user, /user/settings\n";
