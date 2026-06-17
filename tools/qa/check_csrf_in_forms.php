<?php

declare(strict_types=1);

require_once __DIR__ . '/PortalQa.php';

$root = PortalQa::projectRoot();
$failures = [];

$viewFiles = PortalQa::readProjectFiles($root, ['resources/views'], ['php']);

foreach ($viewFiles as $file) {
    $content = (string) file_get_contents($file);

    if (!str_contains($content, '<form')) {
        continue;
    }

    if (!preg_match_all('/<form\b[^>]*method=["\']?post["\']?[^>]*>/i', $content, $matches)) {
        continue;
    }

    $hasCsrf = str_contains($content, '_csrf_token')
        || str_contains($content, 'csrfToken')
        || str_contains($content, 'csrfTokens')
        || str_contains($content, 'csrfField');

    PortalQa::assertTrue(
        $hasCsrf,
        'POST-Formular hat CSRF-Hinweis: ' . str_replace($root . '/', '', $file),
        $failures
    );
}

if ($failures === []) {
    PortalQa::ok('Alle gefundenen POST-Formulare enthalten CSRF-Hinweise.');
}

exit($failures === [] ? 0 : 1);
