<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$php = PHP_BINARY;
$commands = [
    [$php, '-l', $root . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Security' . DIRECTORY_SEPARATOR . 'AuthorizationService.php'],
    [$php, '-l', $root . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Security' . DIRECTORY_SEPARATOR . 'RoutePermissionMap.php'],
    [$php, $root . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'qa' . DIRECTORY_SEPARATOR . 'identity_rights_health_check.php'],
    [$php, $root . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'qa' . DIRECTORY_SEPARATOR . 'find_runtime_pagegroup_usage.php'],
];

$failed = false;
foreach ($commands as $command) {
    $display = implode(' ', array_map('escapeshellarg', $command));
    fwrite(STDOUT, "Running: {$display}\n");
    $exitCode = run($command);
    if ($exitCode !== 0) {
        $failed = true;
        fwrite(STDERR, "FAILED: {$display}\n");
    }
}

if ($failed) {
    fwrite(STDERR, "Mini-Projekt-5-Checks haben Fehler gefunden.\n");
    exit(1);
}

fwrite(STDOUT, "OK: Mini-Projekt-5-Checks bestanden.\n");

/** @param list<string> $command */
function run(array $command): int
{
    $descriptorSpec = [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ];

    $process = proc_open($command, $descriptorSpec, $pipes);
    if (!is_resource($process)) {
        fwrite(STDERR, "Konnte Prozess nicht starten.\n");
        return 1;
    }

    fclose($pipes[0]);
    fwrite(STDOUT, stream_get_contents($pipes[1]) ?: '');
    fwrite(STDERR, stream_get_contents($pipes[2]) ?: '');
    fclose($pipes[1]);
    fclose($pipes[2]);

    return proc_close($process);
}
