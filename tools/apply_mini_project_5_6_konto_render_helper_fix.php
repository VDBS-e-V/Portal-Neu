<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$source = $root . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Http' . DIRECTORY_SEPARATOR . 'Controller' . DIRECTORY_SEPARATOR . 'UserAccountController.php';
$target = dirname($root) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Http' . DIRECTORY_SEPARATOR . 'Controller' . DIRECTORY_SEPARATOR . 'UserAccountController.php';

if (!is_file($source)) {
    fwrite(STDERR, "Quelldatei nicht gefunden: {$source}\n");
    exit(1);
}

if (!is_file($target)) {
    fwrite(STDERR, "Zieldatei nicht gefunden: {$target}\n");
    exit(1);
}

$backup = $target . '.bak-mini-project-5-6-' . date('Ymd-His');
if (!copy($target, $backup)) {
    fwrite(STDERR, "Backup konnte nicht erstellt werden: {$backup}\n");
    exit(1);
}

if (!copy($source, $target)) {
    fwrite(STDERR, "UserAccountController konnte nicht ersetzt werden.\n");
    exit(1);
}

echo "UserAccountController.php wurde angepasst.\n";
echo "Backup: {$backup}\n";
echo "Bitte ausführen:\n";
echo "  php -l src\\Http\\Controller\\UserAccountController.php\n";
echo "  php tools\\qa\\check_konto_render_helper_fix.php\n";
echo "Danach testen: /konto und /konto/einstellungen\n";
