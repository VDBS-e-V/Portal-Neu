<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$php = PHP_BINARY;

$commands = [
    ['-l', $root . '/config/services.php'],
    ['-l', $root . '/src/Repository/PersonRepository.php'],
    ['-l', $root . '/src/Repository/EntityAuditRepository.php'],
    ['-l', $root . '/src/Repository/PersonErasureRepository.php'],
    [$root . '/tools/qa/run_identity_permission_group_cleanup_checks.php'],
    [$root . '/tools/qa/check_no_legacy_identity_group_seed_writes.php'],
    [$root . '/tools/qa/check_legacy_identity_group_db_dropped.php'],
    [$root . '/tools/qa/check_identity_model_integrity.php'],
    [$root . '/tools/qa/check_initial_admin_authorization.php'],
];

$failed = false;
foreach ($commands as $args) {
    $parts = array_merge([$php], $args);
    $display = implode(' ', array_map(static fn (string $part): string => '"' . $part . '"', $parts));
    echo "Running: {$display}\n";
    $cmd = implode(' ', array_map(static fn (string $part): string => '"' . $part . '"', $parts));
    passthru($cmd, $exitCode);
    if ($exitCode !== 0) {
        echo "FAILED: {$display}\n";
        $failed = true;
    }
}

if ($failed) {
    echo "Mini-Projekt-14-Legacy-Identity-Gruppen-Drop-Checks haben Fehler gefunden.\n";
    exit(1);
}

echo "OK: Mini-Projekt-14-Legacy-Identity-Gruppen-Drop-Checks bestanden.\n";
