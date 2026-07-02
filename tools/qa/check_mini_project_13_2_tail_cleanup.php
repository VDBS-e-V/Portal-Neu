<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$files = [
    'src/Repository/EntityAuditRepository.php',
    'src/Repository/PersonErasureRepository.php',
];

$errors = [];
foreach ($files as $relative) {
    $path = $root . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relative);
    if (!is_file($path)) {
        $errors[] = $relative . ' fehlt.';
        continue;
    }
    $content = (string) file_get_contents($path);
    if (str_contains($content, 'ids_person_permission_groups')) {
        $errors[] = $relative . ' enthält noch ids_person_permission_groups.';
    }
}

if ($errors !== []) {
    echo "Mini-Projekt-13.2-Tail-Cleanup fehlgeschlagen:\n";
    foreach ($errors as $error) {
        echo ' - ' . $error . "\n";
    }
    exit(1);
}

echo "OK: Mini-Projekt-13.2-Tail-Cleanup ist vollständig.\n";