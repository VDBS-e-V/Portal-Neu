<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$errors = [];

$files = [
    'config/routes.php',
    'src/Http/Controller/UserAccountController.php',
    'resources/views/pages/konto/profile.php',
    'resources/views/pages/konto/settings.php',
];

foreach ($files as $file) {
    if (!is_file($root . DIRECTORY_SEPARATOR . $file)) {
        $errors[] = "Datei fehlt: {$file}";
    }
}

$routes = is_file($root . '/config/routes.php') ? (string) file_get_contents($root . '/config/routes.php') : '';
$controller = is_file($root . '/src/Http/Controller/UserAccountController.php')
    ? (string) file_get_contents($root . '/src/Http/Controller/UserAccountController.php')
    : '';

$requiredRoutes = [
    "'/konto'",
    "'/konto/einstellungen'",
    "'/konto/passwort'",
    "'/user'",
    "'/user/settings'",
];

foreach ($requiredRoutes as $route) {
    if (!str_contains($routes, $route)) {
        $errors[] = "Route fehlt in config/routes.php: {$route}";
    }
}

$requiredControllerSnippets = [
    "'pages/konto/profile'",
    "'pages/konto/settings'",
    "redirect('/konto?saved=profile')",
    "redirect('/konto/einstellungen?saved=settings')",
    "redirect('/konto/einstellungen?saved=password')",
    'function redirectUserProfile',
    'function redirectUserSettings',
];

foreach ($requiredControllerSnippets as $snippet) {
    if (!str_contains($controller, $snippet)) {
        $errors[] = "Controller-Snippet fehlt: {$snippet}";
    }
}

if (str_contains($controller, "'pages/user/profile'") || str_contains($controller, "'pages/user/settings'")) {
    $errors[] = 'UserAccountController rendert noch alte pages/user/* Views.';
}

if ($errors !== []) {
    foreach ($errors as $error) {
        echo "FAILED: {$error}\n";
    }
    exit(1);
}

echo "OK: Konto-/User-Routen und Profilviews sind vereinheitlicht.\n";
