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
if (str_contains($content, '->renderPage($request')) {
    $errors[] = 'Controller ruft noch $this->renderPage($request, ...) auf.';
}
if (!str_contains($content, 'private function renderKontoPage(')) {
    $errors[] = 'renderKontoPage()-Helper fehlt.';
}
if (!str_contains($content, '$this->renderer->renderPage(')) {
    $errors[] = 'Renderer::renderPage() wird nicht über den lokalen Helper genutzt.';
}
if (str_contains($content, 'unset($this->')) {
    $errors[] = 'Es gibt noch unset($this->...) auf Properties.';
}

if ($errors !== []) {
    foreach ($errors as $error) {
        echo "FAIL: {$error}\n";
    }
    exit(1);
}

echo "OK: Konto-Controller nutzt lokalen renderKontoPage()-Helper.\n";
