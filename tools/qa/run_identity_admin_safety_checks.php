<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$php = PHP_BINARY;

$commands = [
    [$php, '-l', $root . '/src/Security/IdentityAdminSafetyService.php'],
    [$php, '-l', $root . '/config/services.php'],
    [$php, $root . '/tools/qa/run_personen_identity_checks.php'],
    [$php, $root . '/tools/qa/check_admin_safety_wiring.php'],
    [$php, $root . '/tools/qa/check_admin_safety_service.php'],
];

$failed = false;
foreach ($commands as $cmd) {
    $escaped = array_map(static fn(string $part): string => escapeshellarg($part), $cmd);
    $line = implode(' ', $escaped);
    echo 'Running: ' . $line . PHP_EOL;
    passthru($line, $exitCode);
    if ($exitCode !== 0) {
        echo 'FAILED: ' . $line . PHP_EOL;
        $failed = true;
    }
}

if ($failed) {
    echo 'Mini-Projekt-16-Admin-Safety-Checks haben Fehler gefunden.' . PHP_EOL;
    exit(1);
}

echo 'OK: Mini-Projekt-16-Admin-Safety-Checks bestanden.' . PHP_EOL;
