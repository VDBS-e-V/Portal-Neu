<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);

$checks = [
    'check_mini_project_files.php',
    'check_database_schema.php',
    'check_seed_logins.php',
    'check_permissions.php',
    'check_routes_config.php',
    'check_csrf_in_forms.php',
];

$failed = [];

foreach ($checks as $check) {
    echo PHP_EOL;
    echo '============================================================' . PHP_EOL;
    echo 'QA CHECK: ' . $check . PHP_EOL;
    echo '============================================================' . PHP_EOL;

    $command = PHP_BINARY . ' ' . escapeshellarg(__DIR__ . '/' . $check);
    passthru($command, $exitCode);

    if ($exitCode !== 0) {
        $failed[] = $check;
    }
}

echo PHP_EOL;
echo '============================================================' . PHP_EOL;

if ($failed === []) {
    echo '[OK] Alle QA-Checks erfolgreich.' . PHP_EOL;
    exit(0);
}

echo '[FAIL] Fehlgeschlagene QA-Checks:' . PHP_EOL;

foreach ($failed as $check) {
    echo '- ' . $check . PHP_EOL;
}

exit(1);
