<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$file = $root . '/src/Repository/UserRepository.php';

if (!is_file($file)) {
    fwrite(STDERR, "UserRepository.php nicht gefunden.\n");
    exit(1);
}

$code = file_get_contents($file);
if ($code === false) {
    fwrite(STDERR, "UserRepository.php konnte nicht gelesen werden.\n");
    exit(1);
}

$forbidden = [
    'ids_user_names',
    'ids_user_contact_details',
    'ids_user_addresses',
];

$failed = false;
foreach ($forbidden as $needle) {
    if (str_contains($code, $needle)) {
        fwrite(STDERR, "FEHLER: Alte Tabelle noch vorhanden: {$needle}\n");
        $failed = true;
    }
}

$required = [
    'ids_person_names',
    'ids_person_contact_details',
    'ids_person_addresses',
];

foreach ($required as $needle) {
    if (!str_contains($code, $needle)) {
        fwrite(STDERR, "FEHLER: Erwartete Personentabelle fehlt im Repository: {$needle}\n");
        $failed = true;
    }
}

if ($failed) {
    exit(1);
}

echo "OK: UserRepository nutzt ids_person_* Profil-Tabellen.\n";
