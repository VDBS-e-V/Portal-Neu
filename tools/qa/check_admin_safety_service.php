<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
require_once __DIR__ . '/identity_qa_bootstrap.php';
$autoload = $root . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
if (is_file($autoload)) {
    require_once $autoload;
}

use App\Security\IdentityAdminSafetyService;
$pdo = identity_qa_pdo($root);
$errors = [];
$warnings = [];

foreach (['ids_subjects', 'ids_subject_groups', 'ids_groups', 'ids_systems', 'ids_permissions', 'ids_group_permissions'] as $table) {
    if (!identity_qa_table_exists($pdo, $table)) {
        $errors[] = "Tabelle fehlt: {$table}";
    }
}

foreach ([
    'ids_systems.key_name' => ['ids_systems', 'key_name'],
    'ids_groups.key_name' => ['ids_groups', 'key_name'],
    'ids_permissions.key_name' => ['ids_permissions', 'key_name'],
] as $label => [$table, $column]) {
    if (!identity_qa_column_exists($pdo, $table, $column)) {
        $errors[] = "Spalte fehlt: {$label}";
    }
}

if ($errors !== []) {
    echo 'Admin-Safety-Service-Check fehlgeschlagen:' . PHP_EOL;
    foreach ($errors as $error) {
        echo ' - ' . $error . PHP_EOL;
    }
    exit(1);
}

$service = new IdentityAdminSafetyService($pdo);

$requiredMethods = [
    'assertGroupCanBeDeleted',
    'assertPermissionCanBeDeactivated',
    'assertSubjectGroupCanBeRemoved',
    'assertSubjectGroupsCanBeReplaced',
    'assertSubjectStatusCanBeChanged',
    'assertPermissionCanBeAssignedToGroup',
    'assertGroupPermissionsCanBeReplaced',
];
foreach ($requiredMethods as $method) {
    if (!method_exists($service, $method)) {
        $errors[] = 'IdentityAdminSafetyService-Methode fehlt: ' . $method;
    }
}

function mp16_fetch_one(PDO $pdo, string $sql, array $params = []): ?array
{
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return is_array($row) ? $row : null;
}

function mp16_scalar(PDO $pdo, string $sql, array $params = []): int
{
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return (int)$stmt->fetchColumn();
}

function mp16_expect_throw(callable $callback, string $label, array &$errors): void
{
    try {
        $callback();
        $errors[] = $label . ' wurde nicht blockiert.';
    } catch (Throwable) {
    }
}

function mp16_expect_no_throw(callable $callback, string $label, array &$errors): void
{
    try {
        $callback();
    } catch (Throwable $e) {
        $errors[] = $label . ' wurde unerwartet blockiert: ' . $e->getMessage();
    }
}

$adminGroup = mp16_fetch_one($pdo, "
    SELECT g.id, g.key_name, g.is_system, s.key_name AS system_key
    FROM ids_groups g
    INNER JOIN ids_systems s ON s.id = g.system_id
    WHERE s.key_name = 'identity' AND g.key_name = 'administrator'
    LIMIT 1
");

if ($adminGroup === null) {
    $errors[] = 'identity.administrator-Gruppe fehlt.';
} else {
    mp16_expect_throw(
        static fn() => $service->assertGroupCanBeDeleted($adminGroup),
        'Löschen der identity.administrator-Gruppe',
        $errors
    );
}

$activeAdminCount = mp16_scalar($pdo, "
    SELECT COUNT(DISTINCT sub.id)
    FROM ids_subjects sub
    INNER JOIN ids_subject_groups sg ON sg.subject_id = sub.id
    INNER JOIN ids_groups g ON g.id = sg.group_id
    INNER JOIN ids_systems s ON s.id = g.system_id
    WHERE sub.status = 'active'
      AND s.key_name = 'identity'
      AND g.key_name = 'administrator'
      AND (sg.expires_at IS NULL OR sg.expires_at > CURRENT_TIMESTAMP)
");

if ($activeAdminCount < 1) {
    $errors[] = 'Es gibt keinen aktiven identity.administrator-Subject.';
} elseif ($adminGroup !== null) {
    $adminSubject = mp16_fetch_one($pdo, "
        SELECT sub.id
        FROM ids_subjects sub
        INNER JOIN ids_subject_groups sg ON sg.subject_id = sub.id
        WHERE sub.status = 'active'
          AND sg.group_id = :group_id
          AND (sg.expires_at IS NULL OR sg.expires_at > CURRENT_TIMESTAMP)
        ORDER BY sub.id
        LIMIT 1
    ", ['group_id' => (int)$adminGroup['id']]);

    if ($adminSubject !== null) {
        if ($activeAdminCount > 1) {
            mp16_expect_no_throw(
                static fn() => $service->assertSubjectGroupCanBeRemoved((int)$adminSubject['id'], (int)$adminGroup['id']),
                'Entfernen eines Identity-Admins bei vorhandenen weiteren Admins',
                $errors
            );
        }

        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare('DELETE FROM ids_subject_groups WHERE group_id = :group_id AND subject_id <> :subject_id');
            $stmt->execute([
                'group_id' => (int)$adminGroup['id'],
                'subject_id' => (int)$adminSubject['id'],
            ]);

            mp16_expect_throw(
                static fn() => $service->assertSubjectGroupCanBeRemoved((int)$adminSubject['id'], (int)$adminGroup['id']),
                'Entfernen des letzten identity.administrator',
                $errors
            );
            mp16_expect_throw(
                static fn() => $service->assertSubjectStatusCanBeChanged((int)$adminSubject['id'], 'disabled'),
                'Deaktivieren des letzten identity.administrator',
                $errors
            );
        } finally {
            $pdo->rollBack();
        }
    }
}

$crossSystem = mp16_fetch_one($pdo, "
    SELECT g.id AS group_id, p.id AS permission_id
    FROM ids_groups g
    INNER JOIN ids_permissions p ON p.system_id <> g.system_id
    LIMIT 1
");
if ($crossSystem === null) {
    $warnings[] = 'Kein Cross-System-Testfall für Gruppen/Permissions gefunden.';
} else {
    mp16_expect_throw(
        static fn() => $service->assertPermissionCanBeAssignedToGroup((int)$crossSystem['group_id'], (int)$crossSystem['permission_id']),
        'systemfremde Permission-Zuweisung',
        $errors
    );
}

$sameSystem = mp16_fetch_one($pdo, "
    SELECT g.id AS group_id, p.id AS permission_id
    FROM ids_groups g
    INNER JOIN ids_permissions p ON p.system_id = g.system_id
    LIMIT 1
");
if ($sameSystem !== null) {
    mp16_expect_no_throw(
        static fn() => $service->assertPermissionCanBeAssignedToGroup((int)$sameSystem['group_id'], (int)$sameSystem['permission_id']),
        'systemeigene Permission-Zuweisung',
        $errors
    );
}

$activeCondition = identity_qa_column_exists($pdo, 'ids_permissions', 'is_active')
    ? '(p.is_active IS NULL OR p.is_active = 1)'
    : '1=1';

$missingIdentityAdminPermissions = mp16_scalar($pdo, "
    SELECT COUNT(*)
    FROM ids_permissions p
    INNER JOIN ids_systems s ON s.id = p.system_id
    INNER JOIN ids_groups g ON g.system_id = s.id AND g.key_name = 'administrator'
    LEFT JOIN ids_group_permissions gp ON gp.group_id = g.id AND gp.permission_id = p.id
    WHERE s.key_name = 'identity'
      AND {$activeCondition}
      AND gp.permission_id IS NULL
");
if ($missingIdentityAdminPermissions > 0) {
    $errors[] = 'identity.administrator deckt nicht alle aktiven Identity-Permissions ab.';
}

foreach ($warnings as $warning) {
    echo 'WARN: ' . $warning . PHP_EOL;
}

if ($errors !== []) {
    echo 'Admin-Safety-Service-Check fehlgeschlagen:' . PHP_EOL;
    foreach ($errors as $error) {
        echo ' - ' . $error . PHP_EOL;
    }
    exit(1);
}

echo 'OK: Admin-Safety-Service blockiert Self-Lockout und systemfremde Permission-Zuweisungen.' . PHP_EOL;
