<?php

declare(strict_types=1);

require_once __DIR__ . '/identity_qa_bootstrap.php';

$root = identity_qa_project_root();
$pdo = identity_qa_pdo($root);

$errors = [];
$warnings = [];

$requiredTables = [
    'ids_subjects',
    'ids_systems',
    'ids_groups',
    'ids_permissions',
    'ids_group_permissions',
    'ids_subject_groups',
    'ids_users',
];

foreach ($requiredTables as $table) {
    if (!identity_qa_table_exists($pdo, $table)) {
        $errors[] = "Tabelle fehlt: {$table}";
    }
}

if ($errors !== []) {
    echo "Identity-Modell-Integritätscheck fehlgeschlagen:\n";
    foreach ($errors as $error) {
        echo " - {$error}\n";
    }
    exit(1);
}

$checks = [
    'ids_groups ohne existierendes ids_systems' => "SELECT COUNT(*) FROM ids_groups g LEFT JOIN ids_systems s ON s.id = g.system_id WHERE s.id IS NULL",
    'ids_permissions ohne existierendes ids_systems' => "SELECT COUNT(*) FROM ids_permissions p LEFT JOIN ids_systems s ON s.id = p.system_id WHERE s.id IS NULL",
    'ids_group_permissions ohne Gruppe oder Permission' => "SELECT COUNT(*) FROM ids_group_permissions gp LEFT JOIN ids_groups g ON g.id = gp.group_id LEFT JOIN ids_permissions p ON p.id = gp.permission_id WHERE g.id IS NULL OR p.id IS NULL",
    'ids_subject_groups ohne Subject oder Gruppe' => "SELECT COUNT(*) FROM ids_subject_groups sg LEFT JOIN ids_subjects s ON s.id = sg.subject_id LEFT JOIN ids_groups g ON g.id = sg.group_id WHERE s.id IS NULL OR g.id IS NULL",
];

foreach ($checks as $label => $sql) {
    try {
        $count = identity_qa_scalar($pdo, $sql);
        if ($count > 0) {
            $errors[] = "{$label}: {$count}";
        }
    } catch (Throwable $e) {
        $errors[] = "{$label}: Query fehlgeschlagen ({$e->getMessage()})";
    }
}

try {
    $stmt = $pdo->query(
        'SELECT gp.group_id, gp.permission_id, g.system_id AS group_system_id, p.system_id AS permission_system_id '
        . 'FROM ids_group_permissions gp '
        . 'INNER JOIN ids_groups g ON g.id = gp.group_id '
        . 'INNER JOIN ids_permissions p ON p.id = gp.permission_id '
        . 'WHERE g.system_id <> p.system_id '
        . 'LIMIT 20'
    );
    $mismatches = $stmt->fetchAll();
    if ($mismatches !== []) {
        foreach ($mismatches as $row) {
            $errors[] = sprintf(
                'System-Mismatch in ids_group_permissions: group_id=%s system=%s, permission_id=%s system=%s',
                $row['group_id'],
                $row['group_system_id'],
                $row['permission_id'],
                $row['permission_system_id']
            );
        }
    }
} catch (Throwable $e) {
    $errors[] = 'System-Mismatch-Prüfung fehlgeschlagen: ' . $e->getMessage();
}

if (identity_qa_column_exists($pdo, 'ids_subject_groups', 'expires_at')) {
    try {
        $expired = identity_qa_scalar($pdo, "SELECT COUNT(*) FROM ids_subject_groups WHERE expires_at IS NOT NULL AND expires_at < NOW()");
        if ($expired > 0) {
            $warnings[] = "Abgelaufene Subject-Gruppen-Mitgliedschaften vorhanden: {$expired}";
        }
    } catch (Throwable $e) {
        $warnings[] = 'Ablauf-Prüfung fehlgeschlagen: ' . $e->getMessage();
    }
}

if (identity_qa_column_exists($pdo, 'ids_groups', 'is_default')) {
    try {
        $defaults = identity_qa_scalar($pdo, "SELECT COUNT(*) FROM ids_groups WHERE is_default = 1");
        if ($defaults === 0) {
            $warnings[] = 'Keine Default-Gruppe in ids_groups markiert.';
        }
    } catch (Throwable $e) {
        $warnings[] = 'Default-Gruppen-Prüfung fehlgeschlagen: ' . $e->getMessage();
    }
}

if ($errors !== []) {
    echo "Identity-Modell-Integritätscheck fehlgeschlagen:\n";
    foreach ($errors as $error) {
        echo " - {$error}\n";
    }
    if ($warnings !== []) {
        echo "\nWarnungen:\n";
        foreach ($warnings as $warning) {
            echo " - {$warning}\n";
        }
    }
    exit(1);
}

if ($warnings !== []) {
    echo "OK: Identity-Modell-Integrität bestanden, mit Warnungen:\n";
    foreach ($warnings as $warning) {
        echo " - {$warning}\n";
    }
    exit(0);
}

echo "OK: Identity-Modell-Integrität bestanden.\n";
