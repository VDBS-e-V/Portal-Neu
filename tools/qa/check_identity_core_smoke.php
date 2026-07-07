<?php

declare(strict_types=1);

require_once __DIR__ . '/identity_smoke_bootstrap.php';

$root = mp19_project_root();
$pdo = mp19_pdo($root);
$errors = [];

$requiredTables = [
    'ids_subjects',
    'ids_persons',
    'ids_users',
    'ids_systems',
    'ids_groups',
    'ids_permissions',
    'ids_group_permissions',
    'ids_subject_groups',
    'pt_menu_items',
];

foreach ($requiredTables as $table) {
    if (!mp19_table_exists($pdo, $table)) {
        $errors[] = 'Pflichttabelle fehlt: ' . $table;
    }
}

$removedTables = [
    'pt_page_groups',
    'pt_permission_group_page_group_access',
    'ids_permission_groups',
    'ids_user_permission_groups',
    'ids_person_permission_groups',
];
foreach ($removedTables as $table) {
    if (mp19_table_exists($pdo, $table)) {
        $errors[] = 'Legacy-Tabelle existiert noch: ' . $table;
    }
}

if (mp19_table_exists($pdo, 'pt_menu_items')) {
    if (!mp19_column_exists($pdo, 'pt_menu_items', 'permission_key')) {
        $errors[] = 'pt_menu_items.permission_key fehlt.';
    }
    if (mp19_column_exists($pdo, 'pt_menu_items', 'page_group_id')) {
        $errors[] = 'Legacy-Spalte pt_menu_items.page_group_id existiert noch.';
    }
}

if (mp19_table_exists($pdo, 'ids_persons') && mp19_table_exists($pdo, 'ids_subjects') && mp19_column_exists($pdo, 'ids_persons', 'subject_id')) {
    $count = mp19_count($pdo, 'SELECT COUNT(*) FROM ids_persons p LEFT JOIN ids_subjects s ON s.id = p.subject_id WHERE p.subject_id IS NULL OR s.id IS NULL');
    if ($count > 0) {
        $errors[] = 'Personen ohne gültiges Subject: ' . $count;
    }
}

if (mp19_table_exists($pdo, 'ids_users') && mp19_table_exists($pdo, 'ids_persons') && mp19_column_exists($pdo, 'ids_users', 'person_id')) {
    $count = mp19_count($pdo, 'SELECT COUNT(*) FROM ids_users u LEFT JOIN ids_persons p ON p.id = u.person_id WHERE u.person_id IS NOT NULL AND p.id IS NULL');
    if ($count > 0) {
        $errors[] = 'User mit ungültiger person_id: ' . $count;
    }
}

if (mp19_table_exists($pdo, 'ids_subject_groups')) {
    $count = mp19_count($pdo, 'SELECT COUNT(*) FROM ids_subject_groups sg LEFT JOIN ids_subjects s ON s.id = sg.subject_id LEFT JOIN ids_groups g ON g.id = sg.group_id WHERE s.id IS NULL OR g.id IS NULL');
    if ($count > 0) {
        $errors[] = 'Ungültige Subject-Gruppen-Zuordnungen: ' . $count;
    }
}

if (mp19_table_exists($pdo, 'ids_group_permissions')) {
    $count = mp19_count($pdo, 'SELECT COUNT(*) FROM ids_group_permissions gp LEFT JOIN ids_groups g ON g.id = gp.group_id LEFT JOIN ids_permissions p ON p.id = gp.permission_id WHERE g.id IS NULL OR p.id IS NULL');
    if ($count > 0) {
        $errors[] = 'Ungültige Gruppen-Permission-Zuordnungen: ' . $count;
    }

    if (mp19_column_exists($pdo, 'ids_groups', 'system_id') && mp19_column_exists($pdo, 'ids_permissions', 'system_id')) {
        $count = mp19_count($pdo, 'SELECT COUNT(*) FROM ids_group_permissions gp INNER JOIN ids_groups g ON g.id = gp.group_id INNER JOIN ids_permissions p ON p.id = gp.permission_id WHERE g.system_id <> p.system_id');
        if ($count > 0) {
            $errors[] = 'Systemfremde Gruppen-Permission-Zuordnungen: ' . $count;
        }
    }
}

if (mp19_table_exists($pdo, 'pt_menu_items') && mp19_column_exists($pdo, 'pt_menu_items', 'permission_key') && mp19_table_exists($pdo, 'ids_permissions')) {
    $menuKeys = $pdo->query("SELECT DISTINCT permission_key FROM pt_menu_items WHERE permission_key IS NOT NULL AND TRIM(permission_key) <> ''")->fetchAll(PDO::FETCH_COLUMN);
    $permissionKeys = $pdo->query("SELECT key_name FROM ids_permissions WHERE is_active = 1")->fetchAll(PDO::FETCH_COLUMN);
    $permissionKeySet = array_flip(array_map('strval', $permissionKeys));
    $missing = [];
    foreach ($menuKeys as $key) {
        $key = (string) $key;
        if (!isset($permissionKeySet[$key])) {
            $missing[] = $key;
        }
    }
    $missing = array_values(array_unique($missing));
    if ($missing !== []) {
        $errors[] = 'Menü-Permissions ohne aktive ids_permissions: ' . implode(', ', array_slice($missing, 0, 10)) . (count($missing) > 10 ? ' ...' : '');
    }
}

$env = mp19_load_env($root);
$initialAdminEmail = mp19_env($env, ['INITIAL_ADMIN_EMAIL'], null);
if ($initialAdminEmail !== null && mp19_table_exists($pdo, 'ids_users') && mp19_table_exists($pdo, 'ids_persons') && mp19_table_exists($pdo, 'ids_subject_groups')) {
    $stmt = $pdo->prepare('SELECT s.id AS subject_id FROM ids_users u INNER JOIN ids_persons p ON p.id = u.person_id INNER JOIN ids_subjects s ON s.id = p.subject_id WHERE u.email = :email LIMIT 1');
    $stmt->execute(['email' => $initialAdminEmail]);
    $subjectId = $stmt->fetchColumn();
    if ($subjectId === false) {
        $errors[] = 'Initial-Admin aus .env wurde nicht als Subject gefunden: ' . $initialAdminEmail;
    } else {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM ids_subject_groups sg INNER JOIN ids_groups g ON g.id = sg.group_id INNER JOIN ids_systems sys ON sys.id = g.system_id WHERE sg.subject_id = :subject_id AND g.key_name = 'administrator' AND (sg.expires_at IS NULL OR sg.expires_at > NOW()) AND sys.is_active = 1");
        $stmt->execute(['subject_id' => (int) $subjectId]);
        if ((int) $stmt->fetchColumn() === 0) {
            $errors[] = 'Initial-Admin hat keine aktive Administrator-Zuordnung.';
        }
    }
}

mp19_finish($errors, 'OK: Identity-Core-Smoke-Check bestanden.');