<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$php = PHP_BINARY;

$commands = [
    [$php, '-l', $root . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'routes.php'],
    [$php, '-l', $root . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Security' . DIRECTORY_SEPARATOR . 'AuthorizationService.php'],
    [$php, '-l', $root . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Security' . DIRECTORY_SEPARATOR . 'RoutePermissionMap.php'],
    [$php, $root . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'qa' . DIRECTORY_SEPARATOR . 'identity_rights_health_check.php'],
    [$php, $root . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'qa' . DIRECTORY_SEPARATOR . 'check_active_pagegroup_runtime.php'],
    [$php, $root . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'qa' . DIRECTORY_SEPARATOR . 'check_verwaltung_permissions_seed.php'],
];

$failed = false;
foreach ($commands as $cmd) {
    $line = implode(' ', array_map(static fn (string $part): string => escapeshellarg($part), $cmd));
    echo "Running: {$line}\n";
    passthru($line, $code);
    if ($code !== 0) {
        echo "FAILED: {$line}\n";
        $failed = true;
    }
}

if ($failed) {
    echo "Mini-Projekt-6-Runtime-Checks haben Fehler gefunden.\n";
    exit(1);
}

echo "OK: Mini-Projekt-6-Runtime-Checks bestanden.\n";
