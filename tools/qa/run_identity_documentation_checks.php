<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$php = PHP_BINARY;

$commands = [
    [$php, '-l', $root . '/tools/qa/check_identity_documentation.php'],
    [$php, $root . '/tools/qa/check_identity_documentation.php'],
];

$failed = false;

foreach ($commands as $command) {
    $display = implode(' ', array_map(static fn (string $part): string => '"' . $part . '"', $command));
    echo "Running: {$display}\n";

    $escaped = array_map('escapeshellarg', $command);
    passthru(implode(' ', $escaped), $code);

    if ($code !== 0) {
        echo "FAILED: {$display}\n";
        $failed = true;
    }
}

if ($failed) {
    echo "Mini-Projekt-20-Dokumentationschecks haben Fehler gefunden.\n";
    exit(1);
}

echo "OK: Mini-Projekt-20-Dokumentationschecks bestanden.\n";
