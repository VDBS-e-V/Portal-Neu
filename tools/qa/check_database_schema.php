<?php

declare(strict_types=1);

require_once __DIR__ . '/PortalQa.php';

$root = PortalQa::projectRoot();
$failures = [];

try {
    $pdo = PortalQa::pdo($root);
} catch (Throwable $throwable) {
    PortalQa::fail('Datenbankverbindung fehlgeschlagen: ' . $throwable->getMessage());
    exit(1);
}

$requiredTables = [
    'ids_persons',
    'ids_users',
    'ids_person_permission_groups',
    'ids_permission_groups',
    'pt_areas',
    'pt_page_groups',
    'pt_permission_group_page_group_access',
    'pt_audit_log',
];

$optionalProjectTables = [
    'ids_user_invitations',
    'ids_person_erasure_requests',
    'ids_user_password_resets',
    'ids_user_login_events',
];

foreach ($requiredTables as $table) {
    PortalQa::assertTrue(
        PortalQa::tableExists($pdo, $table),
        'Tabelle vorhanden: ' . $table,
        $failures
    );
}

foreach ($optionalProjectTables as $table) {
    if (PortalQa::tableExists($pdo, $table)) {
        PortalQa::ok('Optionale Mini-Projekt-Tabelle vorhanden: ' . $table);
    } else {
        PortalQa::info('Optionale Mini-Projekt-Tabelle fehlt noch: ' . $table);
    }
}

$requiredColumns = [
    'ids_users' => ['id', 'email', 'password_hash', 'status', 'person_id'],
    'ids_persons' => ['id', 'display_name', 'status'],
    'ids_permission_groups' => ['id', 'group_key', 'name'],
    'pt_page_groups' => ['id', 'area_id', 'page_group_key', 'name'],
    'pt_audit_log' => ['id', 'action', 'entity_type', 'entity_id', 'occurred_at'],
];

foreach ($requiredColumns as $table => $columns) {
    foreach ($columns as $column) {
        PortalQa::assertTrue(
            PortalQa::columnExists($pdo, $table, $column),
            'Spalte vorhanden: ' . $table . '.' . $column,
            $failures
        );
    }
}

if (PortalQa::tableExists($pdo, 'pt_menu_items')) {
    if (PortalQa::columnExists($pdo, 'pt_menu_items', 'page_group_id')) {
        PortalQa::ok('Menü-Berechtigungsspalte vorhanden: pt_menu_items.page_group_id');
    } else {
        PortalQa::info('pt_menu_items.page_group_id fehlt. Mini-Projekt 10 noch nicht eingebaut oder Migration fehlt.');
    }
}

exit($failures === [] ? 0 : 1);
