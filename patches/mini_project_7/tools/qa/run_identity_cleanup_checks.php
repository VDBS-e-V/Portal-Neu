<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$php = PHP_BINARY;
$failed = false;

function run_cmd(array $cmd, bool $required = true): bool
{
    global $failed;

    echo 'Running: ' . implode(' ', array_map(static fn (string $part): string => '"' . $part . '"', $cmd)) . PHP_EOL;
    $descriptorSpec = [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ];

    $process = proc_open($cmd, $descriptorSpec, $pipes);
    if (!is_resource($process)) {
        echo "FAILED: Prozess konnte nicht gestartet werden.\n";
        if ($required) {
            $failed = true;
        }
        return false;
    }

    fclose($pipes[0]);
    $stdout = stream_get_contents($pipes[1]);
    $stderr = stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);

    $code = proc_close($process);
    if ($stdout !== false && $stdout !== '') {
        echo $stdout;
    }
    if ($stderr !== false && $stderr !== '') {
        echo $stderr;
    }

    if ($code !== 0) {
        echo 'FAILED: ' . implode(' ', array_map(static fn (string $part): string => '"' . $part . '"', $cmd)) . PHP_EOL;
        if ($required) {
            $failed = true;
        }
        return false;
    }

    return true;
}

run_cmd([$php, '-l', $root . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'routes.php']);

$runtimeChecks = $root . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'qa' . DIRECTORY_SEPARATOR . 'run_identity_runtime_checks.php';
if (is_file($runtimeChecks)) {
    run_cmd([$php, $runtimeChecks]);
} else {
    echo "Hinweis: run_identity_runtime_checks.php nicht gefunden, Runtime-Sammelcheck übersprungen.\n";
}

run_cmd([$php, $root . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'qa' . DIRECTORY_SEPARATOR . 'check_legacy_pagegroup_archive.php']);
run_cmd([$php, $root . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'qa' . DIRECTORY_SEPARATOR . 'check_menu_items_no_page_group_id.php']);

if ($failed) {
    echo "Mini-Projekt-7-Cleanup-Checks haben Fehler gefunden.\n";
    exit(1);
}

echo "OK: Mini-Projekt-7-Cleanup-Checks bestanden.\n";
