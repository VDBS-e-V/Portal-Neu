<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$php = PHP_BINARY;
$failed = false;

function run_command(array $command, bool $required = true): void
{
    global $failed;

    $display = implode(' ', array_map(static fn(string $part): string => escapeshellarg($part), $command));
    echo "Running: {$display}\n";

    $process = proc_open($command, [
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ], $pipes);

    if (!is_resource($process)) {
        echo "FAILED: Prozess konnte nicht gestartet werden.\n";
        if ($required) {
            $failed = true;
        }
        return;
    }

    echo stream_get_contents($pipes[1]);
    $err = stream_get_contents($pipes[2]);
    if ($err !== '') {
        echo $err;
    }
    fclose($pipes[1]);
    fclose($pipes[2]);

    $exitCode = proc_close($process);
    if ($exitCode !== 0) {
        echo ($required ? 'FAILED' : 'WARNING') . ": {$display}\n";
        if ($required) {
            $failed = true;
        }
    }
}

$syntaxFiles = [
    'config/routes.php',
    'config/services.php',
    'src/Security/AuthorizationService.php',
    'src/Security/RoutePermissionMap.php',
];

foreach ($syntaxFiles as $relative) {
    $file = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative);
    if (is_file($file)) {
        run_command([$php, '-l', $file]);
    }
}

$optionalPreviousChecks = [
    'tools/qa/run_identity_service_cleanup_checks.php',
    'tools/qa/run_identity_db_legacy_checks.php',
];

foreach ($optionalPreviousChecks as $relative) {
    $file = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative);
    if (is_file($file)) {
        run_command([$php, $file]);
    }
}

run_command([$php, $root . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'qa' . DIRECTORY_SEPARATOR . 'check_identity_model_integrity.php']);
run_command([$php, $root . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'qa' . DIRECTORY_SEPARATOR . 'check_initial_admin_authorization.php']);
run_command([$php, $root . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'qa' . DIRECTORY_SEPARATOR . 'check_legacy_identity_permission_group_usage.php'], false);

if ($failed) {
    echo "Mini-Projekt-12-Identity-Integritätschecks haben Fehler gefunden.\n";
    exit(1);
}

echo "OK: Mini-Projekt-12-Identity-Integritätschecks bestanden.\n";
