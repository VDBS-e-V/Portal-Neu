<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$php = PHP_BINARY;

$commands = [
    [$php, '-l', $root . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'routes.php'],
    [$php, $root . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'qa' . DIRECTORY_SEPARATOR . 'run_identity_runtime_checks.php'],
    [$php, $root . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'qa' . DIRECTORY_SEPARATOR . 'check_legacy_pagegroup_archive.php'],
    [$php, $root . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'qa' . DIRECTORY_SEPARATOR . 'check_menu_items_no_page_group_id.php'],
];

$failed = false;

foreach ($commands as $command) {
    $display = implode(' ', array_map(static fn (string $part): string => '"' . $part . '"', $command));
    echo 'Running: ' . $display . PHP_EOL;

    $process = proc_open($command, [
        0 => STDIN,
        1 => STDOUT,
        2 => STDERR,
    ], $pipes, $root);

    if (!is_resource($process)) {
        echo 'FAILED: Prozess konnte nicht gestartet werden: ' . $display . PHP_EOL;
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
    echo "Mini-Projekt-7-Cleanup-Checks haben Fehler gefunden.\n";
    exit(1);
}

echo "OK: Mini-Projekt-7-Cleanup-Checks bestanden.\n";