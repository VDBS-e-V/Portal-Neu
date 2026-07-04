<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$routesFile = $root . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'routes.php';
$errors = [];

if (!is_file($routesFile)) {
    $errors[] = 'config/routes.php fehlt.';
} else {
    $content = file_get_contents($routesFile);
    if ($content === false) {
        $errors[] = 'config/routes.php konnte nicht gelesen werden.';
    } else {
        $requiredRoutes = [
            ["GET", "/konto"],
            ["GET", "/konto/einstellungen"],
            ["GET", "/identity/me"],
            ["GET", "/administration"],
            ["GET", "/administration/gruppen"],
            ["GET", "/administration/permissions"],
            ["GET", "/administration/personen"],
            ["GET", "/verwaltung"],
            ["GET", "/verwaltung/personen"],
            ["GET", "/verwaltung/audit"],
            ["GET", "/verwaltung/einladungen"],
            ["GET", "/verwaltung/datenschutz"],
        ];

        foreach ($requiredRoutes as [$method, $path]) {
            $pattern = "/new\\s+Route\\s*\\(\\s*['\"]" . preg_quote($method, '/') . "['\"]\\s*,\\s*['\"]" . preg_quote($path, '/') . "['\"]/";
            if (!preg_match($pattern, $content)) {
                $errors[] = 'Erwartete Route fehlt: ' . $method . ' ' . $path;
            }
        }

        $legacyWebRoutes = [
            "/user",
            "/user/settings",
            "/user/password",
            "/konto/profil",
            "/konto/profil/bearbeiten",
            "/konto/kontakte",
            "/konto/adressen",
            "/konto/sicherheit",
        ];
        foreach ($legacyWebRoutes as $path) {
            $pattern = "/new\\s+Route\\s*\\(\\s*['\"](?:GET|POST)['\"]\\s*,\\s*['\"]" . preg_quote($path, '/') . "['\"]/";
            if (preg_match($pattern, $content)) {
                $errors[] = 'Alte Web-Route ist wieder aktiv: ' . $path;
            }
        }

        $forbiddenControllerFragments = [
            'PageGroupRepository',
            'PageGroupAccessRepository',
            'PermissionGroupRepository::class',
            'PersonPermissionGroupRepository::class',
            'requirePageGroupAccess',
            'BerechtigungenController::class',
        ];
        foreach ($forbiddenControllerFragments as $fragment) {
            if (str_contains($content, $fragment)) {
                $errors[] = 'Legacy-Routen-/Servicefragment in routes.php gefunden: ' . $fragment;
            }
        }
    }
}

if ($errors !== []) {
    echo 'Route-Smoke-Matrix fehlgeschlagen:' . PHP_EOL;
    foreach ($errors as $error) {
        echo ' - ' . $error . PHP_EOL;
    }
    exit(1);
}

echo 'OK: Route-Smoke-Matrix bestanden.' . PHP_EOL;