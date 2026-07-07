<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$seedDir = $root . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'seeds';

$legacyTables = [
    'ids_permission_groups',
    'ids_user_permission_groups',
    'ids_person_permission_groups',
];

$allowedFiles = [
    'seed_mini_project_12_identity_cleanup_markers.sql',
    'seed_mini_project_13_deprecate_legacy_identity_permission_groups.sql',
];

$errors = [];

if (is_dir($seedDir)) {
    $files = glob($seedDir . DIRECTORY_SEPARATOR . '*.sql') ?: [];
    foreach ($files as $file) {
        $base = basename($file);
        $content = file_get_contents($file);
        if ($content === false) {
            continue;
        }
        $normalized = preg_replace('/--.*$/m', '', $content) ?? $content;

        foreach ($legacyTables as $table) {
            $quoted = preg_quote($table, '/');
            $structuralPattern = '/\b(INSERT\s+INTO|REPLACE\s+INTO|UPDATE|DELETE\s+FROM|FROM|JOIN|ALTER\s+TABLE|CREATE\s+TABLE)\s+`?' . $quoted . '`?\b/i';
            if (preg_match($structuralPattern, $normalized)) {
                if (in_array($base, $allowedFiles, true) && !preg_match('/\b(INSERT\s+INTO|REPLACE\s+INTO|UPDATE|DELETE\s+FROM|FROM|JOIN)\s+`?' . $quoted . '`?\b/i', $normalized)) {
                    continue;
                }
                $errors[] = $base . ': struktureller Seed-Verweis auf ' . $table;
            }
        }
    }
}

if ($errors !== []) {
    echo "Aktive Seed-Dateien schreiben/lesen noch Legacy-Identity-Gruppentabellen:\n";
    foreach ($errors as $error) {
        echo " - {$error}\n";
    }
    exit(1);
}

echo "OK: Aktive Seeds schreiben/lesen keine Legacy-Identity-Gruppentabellen mehr.\n";
