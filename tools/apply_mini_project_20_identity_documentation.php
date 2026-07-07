<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$timestamp = date('Ymd-His');
$markerDir = $root . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'mini-project-markers';
$docsDir = $root . DIRECTORY_SEPARATOR . 'docs' . DIRECTORY_SEPARATOR . 'identity';

$required = [
    'docs/identity/README_IDENTITY_RECHTESYSTEM.md',
    'docs/identity/BERECHTIGUNGSKONZEPT.md',
    'docs/identity/ADMIN_HANDBUCH.md',
    'docs/identity/BETRIEB_BACKUP_RESTORE.md',
    'docs/identity/QA_CHECKLISTE.md',
    'docs/identity/MIGRATIONEN_MINI_PROJEKTE_1_20.md',
    'docs/identity/API_IDENTITY_ME.md',
    'docs/identity/SMOKE_TESTS.md',
    'tools/qa/check_identity_documentation.php',
    'tools/qa/run_identity_documentation_checks.php',
    'database/sql/verify_mini_project_20_identity_documentation.sql',
];

$missing = [];
foreach ($required as $relative) {
    $path = $root . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relative);
    if (!is_file($path)) {
        $missing[] = $relative;
    }
}

if ($missing !== []) {
    fwrite(STDERR, "Mini-Projekt 20 ist nicht vollständig entpackt. Fehlende Dateien:\n");
    foreach ($missing as $file) {
        fwrite(STDERR, " - {$file}\n");
    }
    exit(1);
}

if (!is_dir($markerDir) && !mkdir($markerDir, 0775, true) && !is_dir($markerDir)) {
    throw new RuntimeException('Marker-Verzeichnis konnte nicht erstellt werden: ' . $markerDir);
}

$marker = $markerDir . DIRECTORY_SEPARATOR . 'mini-project-20-identity-documentation-' . $timestamp . '.txt';
file_put_contents($marker, "Mini-Projekt 20 Identity-Dokumentation angewendet: {$timestamp}\n");

echo "Mini-Projekt 20 Identity-Dokumentation wurde angewendet.\n";
echo "Dokumentation: docs/identity\n";
echo "Marker: " . str_replace($root . DIRECTORY_SEPARATOR, '', $marker) . "\n";
echo "Bitte ausführen:\n";
echo "  php tools\\qa\\check_identity_documentation.php\n";
echo "  php tools\\qa\\run_identity_documentation_checks.php\n";
