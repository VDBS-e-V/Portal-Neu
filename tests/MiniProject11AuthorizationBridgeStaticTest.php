<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$file = $root . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Security' . DIRECTORY_SEPARATOR . 'AuthorizationService.php';

if (!is_file($file)) {
    fwrite(STDERR, "AuthorizationService.php fehlt.\n");
    exit(1);
}

$content = file_get_contents($file);
if ($content === false) {
    fwrite(STDERR, "AuthorizationService.php konnte nicht gelesen werden.\n");
    exit(1);
}

$forbidden = [
    'function requirePageGroupAccess',
    'function canAccessPageGroup',
    'function currentUserCanAccessPageGroup',
];

$hits = [];
foreach ($forbidden as $fragment) {
    if (str_contains($content, $fragment)) {
        $hits[] = $fragment;
    }
}

if ($hits !== []) {
    echo "MiniProject11AuthorizationBridgeStaticTest fehlgeschlagen:\n";
    foreach ($hits as $hit) {
        echo ' - ' . $hit . PHP_EOL;
    }
    exit(1);
}

echo "OK: MiniProject11AuthorizationBridgeStaticTest bestanden.\n";
