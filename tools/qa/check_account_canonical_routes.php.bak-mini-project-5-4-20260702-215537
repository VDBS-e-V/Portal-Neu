<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$errors = [];

$routesFile = $root . '/config/routes.php';
$routeMapFile = $root . '/src/Security/RoutePermissionMap.php';
$controllerFile = $root . '/src/Http/Controller/UserAccountController.php';
$profileView = $root . '/resources/views/pages/konto/profile.php';
$settingsView = $root . '/resources/views/pages/konto/settings.php';

foreach ([$routesFile, $routeMapFile, $controllerFile, $profileView, $settingsView] as $file) {
    if (!is_file($file)) {
        $errors[] = 'Datei fehlt: ' . str_replace($root . '/', '', $file);
    }
}

$routes = is_file($routesFile) ? (string) file_get_contents($routesFile) : '';
$routeMap = is_file($routeMapFile) ? (string) file_get_contents($routeMapFile) : '';
$controller = is_file($controllerFile) ? (string) file_get_contents($controllerFile) : '';
$profile = is_file($profileView) ? (string) file_get_contents($profileView) : '';
$settings = is_file($settingsView) ? (string) file_get_contents($settingsView) : '';

$requiredRoutes = [
    "new Route('GET', '/konto'",
    "new Route('POST', '/konto'",
    "new Route('GET', '/konto/einstellungen'",
    "new Route('POST', '/konto/einstellungen'",
    "new Route('POST', '/konto/passwort'",
];

foreach ($requiredRoutes as $needle) {
    if (!str_contains($routes, $needle)) {
        $errors[] = "Kanonische Route fehlt: {$needle}";
    }
}

$forbiddenRouteNeedles = [
    "'/user'",
    '"/user"',
    "'/user/settings'",
    '"/user/settings"',
    "'/user/password'",
    '"/user/password"',
    "'/konto/profil'",
    "'/konto/profil/bearbeiten'",
    "'/konto/kontakte'",
    "'/konto/adressen'",
    "'/konto/sicherheit'",
    'redirectUserProfile',
    'redirectUserSettings',
    'redirectLegacyProfile',
    'redirectLegacySettings',
];

foreach ($forbiddenRouteNeedles as $needle) {
    if (str_contains($routes, $needle)) {
        $errors[] = "Alte URL/Legacy-Route steht noch in config/routes.php: {$needle}";
    }
}

$forbiddenCodeNeedles = [
    "'/user'",
    '"/user"',
    "'/user/settings'",
    '"/user/settings"',
    'redirectUserProfile',
    'redirectUserSettings',
    'redirectLegacyProfile',
    'redirectLegacySettings',
    "'/konto/profil'",
    "'/konto/sicherheit'",
];

foreach ($forbiddenCodeNeedles as $needle) {
    if (str_contains($controller, $needle)) {
        $errors[] = "Alte URL/Legacy-Methode steht noch im UserAccountController: {$needle}";
    }
    if (str_contains($routeMap, $needle)) {
        $errors[] = "Alte URL steht noch in RoutePermissionMap: {$needle}";
    }
}

if (!str_contains($routeMap, '$path === \'/konto\'')) {
    $errors[] = 'RoutePermissionMap enthält keine /konto-Ausnahme.';
}

if (!str_contains($profile, 'action="<?= $e($profileUrl) ?>"') && !str_contains($profile, 'action="/konto"')) {
    $errors[] = 'Profil-View postet nicht auf /konto.';
}

if (!str_contains($settings, 'action="<?= $e($settingsUrl) ?>"') && !str_contains($settings, 'action="/konto/einstellungen"')) {
    $errors[] = 'Settings-View postet nicht auf /konto/einstellungen.';
}

if (!str_contains($settings, 'action="/konto/passwort"')) {
    $errors[] = 'Settings-View enthält kein Passwortformular auf /konto/passwort.';
}

if ($errors !== []) {
    foreach ($errors as $error) {
        echo "FAILED: {$error}\n";
    }
    exit(1);
}

echo "OK: Konto-URLs sind kanonisch auf /konto und /konto/einstellungen vereinheitlicht.\n";
