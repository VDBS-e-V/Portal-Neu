<?php

declare(strict_types=1);

$file = __DIR__ . '/../src/Security/AuthorizationService.php';
$code = file_get_contents($file);
if ($code === false) {
    fwrite(STDERR, "AuthorizationService.php konnte nicht gelesen werden.\n");
    exit(1);
}

$requiredSnippets = [
    'function requirePageGroupAccess',
    'function canAccessPageGroup',
    'function currentUserCanAccessPageGroup',
    'function currentPersonId',
    "'verwaltung.gruppen' => 'identity.gruppen.view'",
    "'verwaltung.berechtigungen' => 'identity.permissions.view'",
    "'verwaltung.personen' => 'portal.verwaltung.personen.view'",
];

foreach ($requiredSnippets as $snippet) {
    if (!str_contains($code, $snippet)) {
        fwrite(STDERR, "Fehlender Legacy-Bridge-Snippet: {$snippet}\n");
        exit(1);
    }
}

echo "OK: AuthorizationService Legacy-Bridge vorhanden\n";
