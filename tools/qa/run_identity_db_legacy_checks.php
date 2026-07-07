<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$php = PHP_BINARY;

$commands = [
    [$php, '-l', $root . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'routes.php'],
    [$php, '-l', $root . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Repository' . DIRECTORY_SEPARATOR . 'VerwaltungStatsRepository.php'],
    [$php, '-l', $root . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Repository' . DIRECTORY_SEPARATOR . 'EntityAuditRepository.php'],
    [$php, $root . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'qa' . DIRECTORY_SEPARATOR . 'run_identity_cleanup_checks.php'],
    [$php, $root . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'qa' . DIRECTORY_SEPARATOR . 'check_no_productive_legacy_pagegroup_db_usage.php'],
    [$php, $root . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'qa' . DIRECTORY_SEPARATOR . 'check_legacy_db_deprecation_markers.php'],
    [$php, $root . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'qa' . DIRECTORY_SEPARATOR . 'check_legacy_service_references.php'],
];

$failed = false;

foreach ($commands as $command) {
    $display = implode(' ', array_map(static fn (string $part): string => escapeshellarg($part), $command));
    echo 'Running: ' . $display . PHP_EOL;

    $process = proc_open(
        $command,
        [
            0 => STDIN,
            1 => STDOUT,
            2 => STDERR,
        ],
        $pipes,
        $root
    );

    if (!is_resource($process)) {
        echo 'FAILED: Prozess konnte nicht gestartet werden.' . PHP_EOL;
        $failed = true;
        continue;
    }

    $exitCode = proc_close($process);
    if ($exitCode !== 0) {
        echo 'FAILED: ' . $display . PHP_EOL;
        $failed = true;
    }
}

if ($failed) {
    echo 'Mini-Projekt-8-Legacy-DB-Checks haben Fehler gefunden.' . PHP_EOL;
    exit(1);
}

echo 'OK: Mini-Projekt-8-Legacy-DB-Checks bestanden.' . PHP_EOL;
