<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$source = __DIR__ . '/../src/Repository/UserRepository.php';
$target = $root . '/src/Repository/UserRepository.php';

if (!is_file($source)) {
    fwrite(STDERR, "Quelle fehlt: {$source}\n");
    exit(1);
}

if (!is_file($target)) {
    fwrite(STDERR, "Ziel fehlt: {$target}\n");
    exit(1);
}

$backup = $target . '.bak-mini-project-5-2-' . date('Ymd-His');
if (!copy($target, $backup)) {
    fwrite(STDERR, "Backup konnte nicht erstellt werden: {$backup}\n");
    exit(1);
}

if (!copy($source, $target)) {
    fwrite(STDERR, "UserRepository.php konnte nicht ersetzt werden. Backup: {$backup}\n");
    exit(1);
}

echo "UserRepository.php wurde auf ids_person_* Tabellen umgestellt.\n";
echo "Backup: {$backup}\n";
echo "Bitte ausführen:\n";
echo "  php -l src\\Repository\\UserRepository.php\n";
echo "  php tools\\qa\\check_user_profile_repository_tables.php\n";
echo "Danach testen: /konto und /konto/einstellungen\n";
