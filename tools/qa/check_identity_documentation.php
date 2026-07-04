<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);

$requiredFiles = [
    'docs/identity/README_IDENTITY_RECHTESYSTEM.md' => ['ids_subjects', 'ids_persons', 'ids_users', 'ids_groups', 'ids_permissions', 'pt_menu_items.permission_key', '/identity/me'],
    'docs/identity/BERECHTIGUNGSKONZEPT.md' => ['<system>.<bereich>.<ressource>.<aktion>', 'ids_group_permissions', 'ids_subject_groups'],
    'docs/identity/ADMIN_HANDBUCH.md' => ['/administration', 'Self-Lockout', 'identity.administrator'],
    'docs/identity/BETRIEB_BACKUP_RESTORE.md' => ['mysqldump', 'run_identity_smoke_suite.php', 'Browser-Smoke-Test'],
    'docs/identity/QA_CHECKLISTE.md' => ['run_identity_smoke_suite.php', 'run_identity_navigation_permission_checks.php', 'Mini-Projekt-19-Smoke-Test-Suite'],
    'docs/identity/MIGRATIONEN_MINI_PROJEKTE_1_20.md' => ['1 bis 5', '6 bis 11', '12 bis 14', '15 bis 18', '19 bis 20'],
    'docs/identity/API_IDENTITY_ME.md' => ['/identity/me', 'cache_ttl_seconds', 'permission_version'],
    'docs/identity/SMOKE_TESTS.md' => ['run_identity_smoke_suite.php', 'check_identity_core_smoke.php', 'check_route_smoke_matrix.php'],
];

$errors = [];

foreach ($requiredFiles as $relative => $needles) {
    $path = $root . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relative);
    if (!is_file($path)) {
        $errors[] = "Datei fehlt: {$relative}";
        continue;
    }

    $content = file_get_contents($path);
    if ($content === false || trim($content) === '') {
        $errors[] = "Datei ist leer oder nicht lesbar: {$relative}";
        continue;
    }

    foreach ($needles as $needle) {
        if (!str_contains($content, $needle)) {
            $errors[] = "{$relative}: erwarteter Inhalt fehlt: {$needle}";
        }
    }
}

if ($errors !== []) {
    echo "Identity-Dokumentationscheck fehlgeschlagen:\n";
    foreach ($errors as $error) {
        echo " - {$error}\n";
    }
    exit(1);
}

echo "OK: Identity-Dokumentation ist vollständig vorhanden.\n";
