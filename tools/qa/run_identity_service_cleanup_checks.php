<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$php = PHP_BINARY;

$commands = [
    [$php, '-l', $root . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'services.php'],
    [$php, $root . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'qa' . DIRECTORY_SEPARATOR . 'run_identity_db_legacy_checks.php'],
    [$php, $root . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'qa' . DIRECTORY_SEPARATOR . 'check_legacy_service_references.php'],
];

$failed = false;

foreach ($commands as $command) {
    $display = implode(' ', array_map(static fn (string $part): string => '"' . $part . '"', $command));
    echo 'Running: ' . $display . PHP_EOL;

    $escaped = array_map('escapeshellarg', $command);
    passthru(implode(' ', $escaped), $code);

    if ($code !== 0) {
        echo 'FAILED: ' . $display . PHP_EOL;
        $failed = true;
    }
}

if ($failed) {
    echo "Mini-Projekt-9-Service-Cleanup-Checks haben Fehler gefunden.\n";
    exit(1);
}

echo "OK: Mini-Projekt-9-Service-Cleanup-Checks bestanden.\n";