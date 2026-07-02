<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$archiveBase = $root . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'archive';

if (!is_dir($archiveBase)) {
    fwrite(STDERR, "Kein Archiv-Verzeichnis gefunden: {$archiveBase}\n");
    exit(1);
}

$archives = glob($archiveBase . DIRECTORY_SEPARATOR . 'mini-project-7-legacy-pagegroup-*', GLOB_ONLYDIR) ?: [];
if ($archives === []) {
    fwrite(STDERR, "Kein Mini-Projekt-7-Archiv gefunden.\n");
    exit(1);
}

rsort($archives, SORT_STRING);
$archive = $argv[1] ?? $archives[0];
if (!str_starts_with($archive, $archiveBase)) {
    $archive = $archiveBase . DIRECTORY_SEPARATOR . $archive;
}

$manifestFile = $archive . DIRECTORY_SEPARATOR . 'manifest.json';
if (!is_file($manifestFile)) {
    fwrite(STDERR, "Manifest fehlt: {$manifestFile}\n");
    exit(1);
}

$manifest = json_decode((string) file_get_contents($manifestFile), true);
if (!is_array($manifest) || !isset($manifest['files']) || !is_array($manifest['files'])) {
    fwrite(STDERR, "Manifest ist ungültig: {$manifestFile}\n");
    exit(1);
}

foreach ($manifest['files'] as $entry) {
    if (!is_array($entry) || !isset($entry['original'], $entry['archived_to'])) {
        continue;
    }

    $source = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, (string) $entry['archived_to']);
    $target = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, (string) $entry['original']);

    if (!is_file($source)) {
        echo "Fehlt im Archiv, übersprungen: {$source}\n";
        continue;
    }

    if (is_file($target)) {
        echo "Ziel existiert bereits, übersprungen: {$target}\n";
        continue;
    }

    $dir = dirname($target);
    if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
        throw new RuntimeException('Ziel-Verzeichnis konnte nicht erstellt werden: ' . $dir);
    }

    if (!rename($source, $target)) {
        throw new RuntimeException('Restore fehlgeschlagen: ' . $target);
    }

    echo "Wiederhergestellt: {$entry['original']}\n";
}

echo "Restore abgeschlossen. Bitte danach config/routes.php bei Bedarf aus Backup zurücksetzen.\n";
