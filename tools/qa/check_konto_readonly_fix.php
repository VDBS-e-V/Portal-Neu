<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$file = $root . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Http' . DIRECTORY_SEPARATOR . 'Controller' . DIRECTORY_SEPARATOR . 'UserAccountController.php';

if (!is_file($file)) {
    fwrite(STDERR, "UserAccountController.php nicht gefunden.\n");
    exit(1);
}

$content = file_get_contents($file);
if ($content === false) {
    fwrite(STDERR, "UserAccountController.php konnte nicht gelesen werden.\n");
    exit(1);
}

$errors = [];

if (str_contains($content, 'unset($this->')) {
    $errors[] = 'Controller enthält noch unset($this->...). Das verursacht Fehler bei readonly Properties.';
}

foreach (['/user', '/user/settings', '/konto/profil', '/konto/kontakte', '/konto/adressen', '/konto/sicherheit'] as $legacyUrl) {
    if (str_contains($content, $legacyUrl)) {
        $errors[] = "Controller enthält noch alte Konto-URL: {$legacyUrl}";
    }
}

if (!str_contains($content, "return \$this->redirect('/konto")) {
    $errors[] = 'Controller enthält keine kanonischen /konto-Redirects.';
}

if ($errors !== []) {
    foreach ($errors as $error) {
        echo "FEHLER: {$error}\n";
    }
    exit(1);
}

echo "OK: Konto-Controller nutzt keine readonly-unset-Pattern und keine alten Konto-Web-URLs.\n";
