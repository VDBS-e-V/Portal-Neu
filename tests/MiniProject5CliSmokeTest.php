<?php

declare(strict_types=1);

$files = [
    __DIR__ . '/../tools/apply_mini_project_5_hardening_patch.php',
    __DIR__ . '/../tools/qa/identity_rights_health_check.php',
    __DIR__ . '/../tools/qa/find_runtime_pagegroup_usage.php',
    __DIR__ . '/../tools/qa/run_identity_rights_checks.php',
];

foreach ($files as $file) {
    if (!is_file($file)) {
        fwrite(STDERR, "Datei fehlt: {$file}\n");
        exit(1);
    }

    $command = PHP_BINARY . ' -l ' . escapeshellarg($file);
    exec($command, $output, $exitCode);
    if ($exitCode !== 0) {
        fwrite(STDERR, "Syntaxfehler in {$file}:\n" . implode("\n", $output) . "\n");
        exit(1);
    }
}

echo "OK: Mini-Projekt-5 CLI-Dateien syntaktisch gültig\n";
