<?php

declare(strict_types=1);

require_once __DIR__ . '/identity_smoke_bootstrap.php';

$root = dirname(__DIR__, 2);
$php = PHP_BINARY;
$args = array_slice($argv, 1);
$withSeed = in_array('--with-seed', $args, true);
$exitCode = 0;

$commands = [];
$commands[] = [$php, '-l', $root . '/config/routes.php'];
$commands[] = [$php, '-l', $root . '/config/services.php'];
$commands[] = [$php, '-l', $root . '/src/Security/AuthorizationService.php'];
$commands[] = [$php, '-l', $root . '/src/Security/RoutePermissionMap.php'];

if ($withSeed) {
    $commands[] = [$php, $root . '/bin/console', 'seed'];
}

$optionalExistingChecks = [
    'tools/qa/run_identity_navigation_permission_checks.php',
    'tools/qa/run_identity_admin_safety_checks.php',
    'tools/qa/run_identity_audit_erasure_checks.php',
    'tools/qa/run_personen_identity_checks.php',
    'tools/qa/run_identity_permission_group_cleanup_checks.php',
    'tools/qa/run_identity_legacy_identity_group_drop_checks.php',
];

foreach ($optionalExistingChecks as $relative) {
    $path = $root . '/' . $relative;
    if (is_file($path)) {
        $commands[] = [$php, $path];
    }
}

$commands[] = [$php, $root . '/tools/qa/check_identity_core_smoke.php'];
$commands[] = [$php, $root . '/tools/qa/check_route_smoke_matrix.php'];
$commands[] = [$php, $root . '/tools/qa/check_identity_http_smoke.php'];

foreach ($commands as $command) {
    $code = mp19_run_command($command);
    if ($code !== 0) {
        $exitCode = 1;
    }
}

if ($exitCode !== 0) {
    echo 'Mini-Projekt-19-Smoke-Test-Suite hat Fehler gefunden.' . PHP_EOL;
    exit(1);
}

echo 'OK: Mini-Projekt-19-Smoke-Test-Suite bestanden.' . PHP_EOL;