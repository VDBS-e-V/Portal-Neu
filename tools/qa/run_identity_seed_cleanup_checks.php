<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$php = PHP_BINARY;

$commands = [
    ['-l', $root . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'routes.php'],
    [$root . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'qa' . DIRECTORY_SEPARATOR . 'check_no_page_group_id_in_active_seeds.php'],
    [$root . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'qa' . DIRECTORY_SEPARATOR . 'run_identity_integrity_checks.php'],
];

$failed = false;

foreach ($commands as $args) {
    $cmd = array_merge([$php], $args);
    $display = implode(' ', array_map(static fn(string $part): string => '"' . $part . '"', $cmd));
    echo "Running: {$display}\n";
    passthru($display, $exitCode);
    if ($exitCode !== 0) {
        echo "FAILED: {$display}\n";
        $failed = true;
    }
}

if ($failed) {
    echo "Mini-Projekt-12.1-Seed-Cleanup-Checks haben Fehler gefunden.\n";
    exit(1);
}

echo "OK: Mini-Projekt-12.1-Seed-Cleanup-Checks bestanden.\n";