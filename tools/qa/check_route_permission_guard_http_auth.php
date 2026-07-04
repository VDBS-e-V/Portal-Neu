<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$file = $root . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Security' . DIRECTORY_SEPARATOR . 'RoutePermissionGuard.php';
$errors = [];

if (!is_file($file)) {
    $errors[] = 'RoutePermissionGuard.php fehlt.';
} else {
    $code = (string) file_get_contents($file);
    $required = [
        'respondUnauthenticated' => 'Guard behandelt anonyme Zugriffe explizit.',
        'respondForbidden' => 'Guard behandelt fehlende Permissions explizit.',
        'Location: ' => 'Guard leitet Web-401 auf Login um.',
        'jsonAndExit' => 'Guard beantwortet API-401/403 als JSON.',
        'isLoggedIn()' => 'Guard prüft Login vor requirePermission().',
    ];

    foreach ($required as $needle => $message) {
        if (!str_contains($code, $needle)) {
            $errors[] = $message . ' Erwarteter Marker fehlt: ' . $needle;
        }
    }

    if (str_contains($code, 'pageGroup') || str_contains($code, 'PageGroup')) {
        $errors[] = 'RoutePermissionGuard enthält noch PageGroup-Begriffe.';
    }
}

if ($errors !== []) {
    echo 'RoutePermissionGuard-HTTP-Auth-Check fehlgeschlagen:' . PHP_EOL;
    foreach ($errors as $error) {
        echo ' - ' . $error . PHP_EOL;
    }
    exit(1);
}

echo 'OK: RoutePermissionGuard behandelt anonyme HTTP-Zugriffe ohne 500er.' . PHP_EOL;