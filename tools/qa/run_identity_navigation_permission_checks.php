<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$php = PHP_BINARY;

$commands = [
    ['-l', $root . '/config/routes.php'],
    ['-l', $root . '/config/services.php'],
    [$root . '/tools/qa/check_navigation_permission_model.php'],
    [$root . '/tools/qa/check_navigation_code_permission_model.php'],
    [$root . '/tools/qa/check_navigation_active_seeds.php'],
    [$root . '/tools/qa/run_identity_admin_safety_checks.php'],
];

$failed = false;

foreach ($commands as $args) {
    $cmd = array_merge([$php], $args);
    $display = implode(' ', array_map(static fn (string $part): string => '"' . $part . '"', $cmd));
    echo 'Running: ' . $display . PHP_EOL;
    passthru($display, $exitCode);
    if ($exitCode !== 0) {
        echo 'FAILED: ' . $display . PHP_EOL;
        $failed = true;
    }
}

if ($failed) {
    echo "Mini-Projekt-18-Navigation-Permission-Checks haben Fehler gefunden.\n";
    exit(1);
}

echo "OK: Mini-Projekt-18-Navigation-Permission-Checks bestanden.\n";