<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$php = PHP_BINARY;

$requiredCommands = [
    [$php, '-l', $root . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Security' . DIRECTORY_SEPARATOR . 'AuthorizationService.php'],
    [$php, '-l', $root . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Security' . DIRECTORY_SEPARATOR . 'RoutePermissionMap.php'],
    [$php, '-l', $root . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'routes.php'],
    [$php, $root . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'qa' . DIRECTORY_SEPARATOR . 'identity_rights_health_check.php'],
    [$php, $root . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'qa' . DIRECTORY_SEPARATOR . 'check_konto_routes.php'],
];

$ok = true;
foreach ($requiredCommands as $command) {
    $ok = runCommand($command, true) && $ok;
}

$pageGroupFinder = $root . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'qa' . DIRECTORY_SEPARATOR . 'find_runtime_pagegroup_usage.php';
if (is_file($pageGroupFinder)) {
    fwrite(STDOUT, "Running PageGroup-Runtime-Suche als Warnung, nicht als harter Fehler.\n");
    runCommand([$php, $pageGroupFinder], false);
}

if (!$ok) {
    fwrite(STDERR, "Mini-Projekt-5.1-Checks haben harte Fehler gefunden.\n");
    exit(1);
}

fwrite(STDOUT, "OK: Mini-Projekt-5.1-Checks bestanden. PageGroup-Treffer bleiben Cleanup-Aufgaben.\n");

/**
 * @param list<string> $command
 */
function runCommand(array $command, bool $required): bool
{
    $display = implode(' ', array_map(static fn (string $part): string => escapeshellarg($part), $command));
    fwrite(STDOUT, "Running: {$display}\n");

    $descriptorSpec = [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ];

    $process = proc_open($command, $descriptorSpec, $pipes);
    if (!is_resource($process)) {
        fwrite(STDERR, "FAILED: Prozess konnte nicht gestartet werden: {$display}\n");
        return false;
    }

    fclose($pipes[0]);
    $stdout = stream_get_contents($pipes[1]) ?: '';
    $stderr = stream_get_contents($pipes[2]) ?: '';
    fclose($pipes[1]);
    fclose($pipes[2]);

    $exitCode = proc_close($process);

    if ($stdout !== '') {
        fwrite(STDOUT, $stdout);
    }
    if ($stderr !== '') {
        fwrite(STDERR, $stderr);
    }

    if ($exitCode !== 0) {
        if ($required) {
            fwrite(STDERR, "FAILED: {$display}\n");
            return false;
        }
        fwrite(STDOUT, "WARNING: {$display} meldete Treffer/Exit-Code {$exitCode}.\n");
    }

    return true;
}
