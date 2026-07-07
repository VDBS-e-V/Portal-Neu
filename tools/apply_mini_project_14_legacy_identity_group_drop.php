<?php

declare(strict_types=1);

$root = dirname(__DIR__);

$required = [
    'tools/drop_mini_project_14_legacy_identity_group_db.php',
    'tools/qa/check_no_legacy_identity_group_seed_writes.php',
    'tools/qa/check_legacy_identity_group_db_dropped.php',
    'tools/qa/run_identity_legacy_identity_group_drop_checks.php',
    'database/sql/verify_mini_project_14_legacy_identity_groups_dropped.sql',
];

$missing = [];
foreach ($required as $relative) {
    $path = $root . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relative);
    if (!is_file($path)) {
        $missing[] = $relative;
    }
}

if ($missing !== []) {
    fwrite(STDERR, "Mini-Projekt 14 ist nicht vollständig entpackt. Fehlende Dateien:\n");
    foreach ($missing as $relative) {
        fwrite(STDERR, " - {$relative}\n");
    }
    fwrite(STDERR, "\nBitte ZIP erneut ins Projekt-Stammverzeichnis entpacken.\n");
    exit(1);
}

foreach ($required as $relative) {
    echo "Vorhanden: {$relative}\n";
}

echo "\nMini-Projekt 14 Legacy-Identity-Gruppentabellen-Drop wurde vorbereitet.\n";
echo "Bitte ausführen:\n";
echo "  php tools\\drop_mini_project_14_legacy_identity_group_db.php --dry-run\n";
echo "  php tools\\drop_mini_project_14_legacy_identity_group_db.php\n";
echo "  php tools\\qa\\check_legacy_identity_group_db_dropped.php\n";
echo "  php bin\\console seed\n";
echo "  php tools\\qa\\run_identity_legacy_identity_group_drop_checks.php\n";
