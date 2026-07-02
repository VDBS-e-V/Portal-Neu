<?php

declare(strict_types=1);

require_once __DIR__ . '/identity_qa_bootstrap.php';

$root = identity_qa_project_root();
$env = identity_qa_load_env($root);
$pdo = identity_qa_pdo($root);

$adminEmail = identity_qa_env($env, ['INITIAL_ADMIN_EMAIL'], 'local@admin.com');
$errors = [];
$warnings = [];

if (!identity_qa_table_exists($pdo, 'ids_users') || !identity_qa_table_exists($pdo, 'ids_subjects')) {
    echo "Initial-Admin-Check übersprungen: ids_users oder ids_subjects fehlt.\n";
    exit(1);
}

$subjectId = null;
$userRow = null;

try {
    $stmt = $pdo->prepare('SELECT * FROM ids_users WHERE email = :email LIMIT 1');
    $stmt->execute(['email' => $adminEmail]);
    $userRow = $stmt->fetch();
} catch (Throwable $e) {
    $errors[] = 'Admin-User konnte nicht geladen werden: ' . $e->getMessage();
}

if (!$userRow) {
    $errors[] = "Initial-Admin nicht in ids_users gefunden: {$adminEmail}";
} else {
    if (array_key_exists('subject_id', $userRow) && $userRow['subject_id'] !== null && (string)$userRow['subject_id'] !== '') {
        $subjectId = (int)$userRow['subject_id'];
    } elseif (array_key_exists('person_id', $userRow) && identity_qa_table_exists($pdo, 'ids_persons') && identity_qa_column_exists($pdo, 'ids_persons', 'subject_id')) {
        $stmt = $pdo->prepare('SELECT subject_id FROM ids_persons WHERE id = :person_id LIMIT 1');
        $stmt->execute(['person_id' => $userRow['person_id']]);
        $value = $stmt->fetchColumn();
        if ($value !== false && $value !== null) {
            $subjectId = (int)$value;
        }
    }

    if ($subjectId === null) {
        $errors[] = "Initial-Admin {$adminEmail} hat kein auflösbares subject_id.";
    }
}

if ($errors !== []) {
    echo "Initial-Admin-Autorisierungscheck fehlgeschlagen:\n";
    foreach ($errors as $error) {
        echo " - {$error}\n";
    }
    exit(1);
}

$activeSystemCondition = identity_qa_active_condition($pdo, 's', 'ids_systems');
$stmt = $pdo->query("SELECT * FROM ids_systems s WHERE {$activeSystemCondition} ORDER BY id");
$systems = $stmt->fetchAll();

if ($systems === []) {
    $errors[] = 'Keine aktiven Systeme in ids_systems gefunden.';
}

$groupLabelColumns = ['slug', 'code', 'name', 'group_key', 'label', 'title'];
$permissionLabelColumns = ['permission_key', 'key', 'code', 'name', 'slug'];

foreach ($systems as $system) {
    $systemId = (int)$system['id'];
    $systemLabel = identity_qa_row_label($system, ['slug', 'code', 'name', 'system_key', 'label']);

    $stmt = $pdo->prepare('SELECT * FROM ids_groups WHERE system_id = :system_id ORDER BY id');
    $stmt->execute(['system_id' => $systemId]);
    $groups = $stmt->fetchAll();

    $adminGroups = [];
    foreach ($groups as $group) {
        $haystack = strtolower(implode(' ', array_map(static fn($v): string => is_scalar($v) ? (string)$v : '', $group)));
        foreach ($groupLabelColumns as $column) {
            if (isset($group[$column]) && strtolower((string)$group[$column]) === 'administrator') {
                $adminGroups[] = $group;
                continue 2;
            }
        }
        if (str_contains($haystack, 'administrator')) {
            $adminGroups[] = $group;
        }
    }

    if ($adminGroups === []) {
        $errors[] = "Keine Administrator-Gruppe für System {$systemLabel} gefunden.";
        continue;
    }

    $adminGroupIds = array_map(static fn(array $row): int => (int)$row['id'], $adminGroups);
    $placeholders = implode(',', array_fill(0, count($adminGroupIds), '?'));

    $params = [$subjectId, ...$adminGroupIds];
    $membershipSql = 'SELECT COUNT(*) FROM ids_subject_groups WHERE subject_id = ? AND group_id IN (' . $placeholders . ')';
    if (identity_qa_column_exists($pdo, 'ids_subject_groups', 'expires_at')) {
        $membershipSql .= ' AND (expires_at IS NULL OR expires_at >= NOW())';
    }
    $stmt = $pdo->prepare($membershipSql);
    $stmt->execute($params);
    $membershipCount = (int)$stmt->fetchColumn();

    if ($membershipCount === 0) {
        $errors[] = "Initial-Admin {$adminEmail} ist in keiner aktiven Administrator-Gruppe für System {$systemLabel}.";
    }

    $activePermissionCondition = identity_qa_active_condition($pdo, 'p', 'ids_permissions');
    $stmt = $pdo->prepare("SELECT * FROM ids_permissions p WHERE p.system_id = :system_id AND {$activePermissionCondition}");
    $stmt->execute(['system_id' => $systemId]);
    $permissions = $stmt->fetchAll();

    if ($permissions === []) {
        $warnings[] = "Keine aktiven Permissions für System {$systemLabel} gefunden.";
        continue;
    }

    $permissionIds = array_map(static fn(array $row): int => (int)$row['id'], $permissions);
    $permissionPlaceholders = implode(',', array_fill(0, count($permissionIds), '?'));
    $groupPlaceholders = implode(',', array_fill(0, count($adminGroupIds), '?'));

    $stmt = $pdo->prepare(
        'SELECT DISTINCT permission_id FROM ids_group_permissions '
        . 'WHERE group_id IN (' . $groupPlaceholders . ') AND permission_id IN (' . $permissionPlaceholders . ')'
    );
    $stmt->execute([...$adminGroupIds, ...$permissionIds]);
    $assigned = array_map('intval', array_column($stmt->fetchAll(), 'permission_id'));
    $missing = array_values(array_diff($permissionIds, $assigned));

    if ($missing !== []) {
        $labelsById = [];
        foreach ($permissions as $permission) {
            $labelsById[(int)$permission['id']] = identity_qa_row_label($permission, $permissionLabelColumns);
        }
        $shown = array_slice(array_map(static fn(int $id): string => $labelsById[$id] ?? ('#' . $id), $missing), 0, 10);
        $errors[] = "Administrator-Gruppe für {$systemLabel} enthält nicht alle aktiven Permissions. Fehlend: " . implode(', ', $shown) . (count($missing) > 10 ? ' ...' : '');
    }
}

if ($errors !== []) {
    echo "Initial-Admin-Autorisierungscheck fehlgeschlagen:\n";
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
    echo "OK: Initial-Admin-Autorisierung bestanden, mit Warnungen:\n";
    foreach ($warnings as $warning) {
        echo " - {$warning}\n";
    }
    exit(0);
}

echo "OK: Initial-Admin hat aktive Administrator-Zuordnung und Administrator-Gruppen decken aktive System-Permissions ab.\n";
