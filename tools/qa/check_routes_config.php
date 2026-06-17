<?php

declare(strict_types=1);

require_once __DIR__ . '/PortalQa.php';

$root = PortalQa::projectRoot();
$failures = [];

$routeFiles = PortalQa::readProjectFiles($root, ['config'], ['php']);

$content = '';

foreach ($routeFiles as $file) {
    if (str_contains(basename($file), 'route') || basename($file) === 'routes.php') {
        $content .= "\n/* " . $file . " */\n" . (string) file_get_contents($file);
    }
}

$requiredRoutes = [
    '/verwaltung',
    '/verwaltung/personen',
    '/verwaltung/gruppen',
    '/verwaltung/berechtigungen',
    '/verwaltung/audit',
];

$optionalRoutes = [
    '/einladung/{token}',
    '/verwaltung/einladungen',
    '/verwaltung/datenschutz',
    '/konto',
    '/konto/passwort',
    '/passwort/vergessen',
    '/konto/profil',
    '/konto/kontakte',
    '/konto/adressen',
    '/konto/sicherheit',
    '/verwaltung/personen/{id}/audit',
    '/verwaltung/gruppen/{id}/audit',
    '/verwaltung/datenschutz/{id}/audit',
];

foreach ($requiredRoutes as $route) {
    PortalQa::assertTrue(
        str_contains($content, $route),
        'Route konfiguriert: ' . $route,
        $failures
    );
}

foreach ($optionalRoutes as $route) {
    if (str_contains($content, $route)) {
        PortalQa::ok('Optionale Route konfiguriert: ' . $route);
    } else {
        PortalQa::info('Optionale Route fehlt noch: ' . $route);
    }
}

exit($failures === [] ? 0 : 1);
