<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$mustBeGone = [
    'src/Repository/PermissionGroupRepository.php',
    'src/Repository/PersonPermissionGroupRepository.php',
    'config/services_berechtigungen_block.php',
    'config/services_entity_audit_block.php',
    'config/services_gruppen_block.php',
    'config/services_personen_block.php',
    'config/services_security_block.php',
];

$errors = [];
foreach ($mustBeGone as $relative) {
    $path = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative);
    if (is_file($path)) {
        $errors[] = $relative . ' existiert noch im produktiven Pfad.';
    }
}

$archiveBase = $root . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'archive';
$hasArchive = false;
if (is_dir($archiveBase)) {
    foreach (new DirectoryIterator($archiveBase) as $entry) {
        if ($entry->isDir() && !$entry->isDot() && str_contains($entry->getFilename(), 'legacy-identity-permission-groups')) {
            $hasArchive = true;
            break;
        }
    }
}

if (!$hasArchive) {
    $errors[] = 'Kein Archivordner für legacy-identity-permission-groups gefunden.';
}

if ($errors !== []) {
    echo "Legacy-Identity-PermissionGroup-Archiv-Check fehlgeschlagen:\n";
    foreach ($errors as $error) {
        echo ' - ' . $error . "\n";
    }
    exit(1);
}

echo "OK: Legacy-Identity-PermissionGroup-Repositories und alte Service-Blöcke sind archiviert.\n";