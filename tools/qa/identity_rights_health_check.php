<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
loadEnv($root . DIRECTORY_SEPARATOR . '.env');

$pdo = connectPdo();
$failures = [];

checkRequiredTables($pdo, $failures);
checkRequiredColumns($pdo, $failures);
checkInitialAdmin($pdo, $failures);
checkAdministratorGroups($pdo, $failures);
checkCrossSystemGroupPermissions($pdo, $failures);
checkMenuPermissionReferences($pdo, $failures);
checkSubjectIntegrity($pdo, $failures);

if ($failures !== []) {
    fwrite(STDERR, "Identity-Rechte-Health-Check FEHLGESCHLAGEN:\n");
    foreach ($failures as $failure) {
        fwrite(STDERR, ' - ' . $failure . "\n");
    }
    exit(1);
}

fwrite(STDOUT, "OK: Identity-Rechte-Health-Check bestanden.\n");

function connectPdo(): PDO
{
    $host = getenv('DB_HOST') ?: '127.0.0.1';
    $port = getenv('DB_PORT') ?: '3306';
    $name = getenv('DB_NAME') ?: getenv('DB_DATABASE') ?: 'vdbs';
    $user = getenv('DB_USER') ?: getenv('DB_USERNAME') ?: 'root';
    $pass = getenv('DB_PASS') ?: getenv('DB_PASSWORD') ?: '';
    $charset = getenv('DB_CHARSET') ?: 'utf8mb4';

    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $host, $port, $name, $charset);

    return new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
    ]);
}

/** @param list<string> $failures */
function checkRequiredTables(PDO $pdo, array &$failures): void
{
    $tables = [
        'ids_subjects',
        'ids_systems',
        'ids_groups',
        'ids_permissions',
        'ids_group_permissions',
        'ids_subject_groups',
        'ids_persons',
        'ids_users',
        'pt_menu_items',
    ];

    foreach ($tables as $table) {
        $stmt = $pdo->prepare(
            'SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table'
        );
        $stmt->execute(['table' => $table]);
        if ((int) $stmt->fetchColumn() !== 1) {
            $failures[] = 'Tabelle fehlt: ' . $table;
        }
    }
}

/** @param list<string> $failures */
function checkRequiredColumns(PDO $pdo, array &$failures): void
{
    $columns = [
        ['ids_persons', 'subject_id'],
        ['pt_menu_items', 'required_permission_id'],
        ['ids_subjects', 'permission_version'],
        ['ids_subject_groups', 'expires_at'],
        ['ids_permissions', 'deprecated_at'],
    ];

    foreach ($columns as [$table, $column]) {
        $stmt = $pdo->prepare(
            'SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table AND COLUMN_NAME = :column'
        );
        $stmt->execute(['table' => $table, 'column' => $column]);
        if ((int) $stmt->fetchColumn() !== 1) {
            $failures[] = sprintf('Spalte fehlt: %s.%s', $table, $column);
        }
    }
}

/** @param list<string> $failures */
function checkInitialAdmin(PDO $pdo, array &$failures): void
{
    $email = getenv('INITIAL_ADMIN_EMAIL') ?: 'local@admin.com';

    $stmt = $pdo->prepare(
        'SELECT u.id AS user_id, p.subject_id, s.status AS subject_status
         FROM ids_users u
         JOIN ids_persons p ON p.id = u.person_id
         JOIN ids_subjects s ON s.id = p.subject_id
         WHERE CONVERT(u.email USING utf8mb4) COLLATE utf8mb4_unicode_ci = CONVERT(:email USING utf8mb4) COLLATE utf8mb4_unicode_ci
         LIMIT 1'
    );
    $stmt->execute(['email' => $email]);
    $admin = $stmt->fetch();

    if (!is_array($admin)) {
        $failures[] = 'Initial-Admin nicht gefunden: ' . $email;
        return;
    }

    if ((string) $admin['subject_status'] !== 'active') {
        $failures[] = 'Initial-Admin-Subject ist nicht active: ' . (string) $admin['subject_status'];
    }

    $stmt = $pdo->prepare(
        'SELECT s.key_name AS system_key
         FROM ids_systems s
         JOIN ids_groups g ON g.system_id = s.id AND g.key_name = :group_key AND g.is_active = 1
         JOIN ids_subject_groups sg ON sg.group_id = g.id
         WHERE s.is_active = 1
           AND sg.subject_id = :subject_id
           AND (sg.expires_at IS NULL OR sg.expires_at > CURRENT_TIMESTAMP)'
    );
    $stmt->execute([
        'group_key' => 'administrator',
        'subject_id' => (int) $admin['subject_id'],
    ]);

    $assignedSystems = array_map(static fn (array $row): string => (string) $row['system_key'], $stmt->fetchAll());
    $activeSystems = $pdo->query('SELECT key_name FROM ids_systems WHERE is_active = 1 ORDER BY key_name')->fetchAll();
    foreach ($activeSystems as $system) {
        $systemKey = (string) $system['key_name'];
        if (!in_array($systemKey, $assignedSystems, true)) {
            $failures[] = 'Initial-Admin fehlt administrator-Gruppe für System: ' . $systemKey;
        }
    }
}

/** @param list<string> $failures */
function checkAdministratorGroups(PDO $pdo, array &$failures): void
{
    $stmt = $pdo->query(
        'SELECT s.key_name AS system_key, g.id AS group_id
         FROM ids_systems s
         JOIN ids_groups g ON g.system_id = s.id
         WHERE s.is_active = 1 AND g.is_active = 1 AND g.key_name = \'administrator\''
    );

    foreach ($stmt->fetchAll() as $row) {
        $systemKey = (string) $row['system_key'];
        $groupId = (int) $row['group_id'];
        $missing = $pdo->prepare(
            'SELECT COUNT(*)
             FROM ids_permissions p
             JOIN ids_systems s ON s.id = p.system_id
             LEFT JOIN ids_group_permissions gp ON gp.permission_id = p.id AND gp.group_id = :group_id
             WHERE s.key_name = :system_key
               AND p.is_active = 1
               AND gp.permission_id IS NULL'
        );
        $missing->execute(['group_id' => $groupId, 'system_key' => $systemKey]);
        $count = (int) $missing->fetchColumn();
        if ($count > 0) {
            $failures[] = sprintf('administrator-Gruppe von %s hat nicht alle Permissions. Fehlend: %d', $systemKey, $count);
        }
    }
}

/** @param list<string> $failures */
function checkCrossSystemGroupPermissions(PDO $pdo, array &$failures): void
{
    $stmt = $pdo->query(
        'SELECT COUNT(*)
         FROM ids_group_permissions gp
         JOIN ids_groups g ON g.id = gp.group_id
         JOIN ids_permissions p ON p.id = gp.permission_id
         WHERE g.system_id <> p.system_id'
    );

    $count = (int) $stmt->fetchColumn();
    if ($count > 0) {
        $failures[] = 'Systemfremde Gruppen-Permissions gefunden: ' . $count;
    }
}

/** @param list<string> $failures */
function checkMenuPermissionReferences(PDO $pdo, array &$failures): void
{
    $stmt = $pdo->query(
        'SELECT COUNT(*)
         FROM pt_menu_items mi
         LEFT JOIN ids_permissions p ON p.id = mi.required_permission_id
         WHERE mi.required_permission_id IS NOT NULL
           AND p.id IS NULL'
    );

    $count = (int) $stmt->fetchColumn();
    if ($count > 0) {
        $failures[] = 'Menüeinträge mit ungültiger required_permission_id: ' . $count;
    }
}

/** @param list<string> $failures */
function checkSubjectIntegrity(PDO $pdo, array &$failures): void
{
    $checks = [
        'Personen ohne subject_id' => 'SELECT COUNT(*) FROM ids_persons WHERE subject_id IS NULL',
        'User ohne Person' => 'SELECT COUNT(*) FROM ids_users u LEFT JOIN ids_persons p ON p.id = u.person_id WHERE p.id IS NULL',
        'Subject-Gruppen ohne Subject' => 'SELECT COUNT(*) FROM ids_subject_groups sg LEFT JOIN ids_subjects s ON s.id = sg.subject_id WHERE s.id IS NULL',
        'Subject-Gruppen ohne Gruppe' => 'SELECT COUNT(*) FROM ids_subject_groups sg LEFT JOIN ids_groups g ON g.id = sg.group_id WHERE g.id IS NULL',
    ];

    foreach ($checks as $label => $sql) {
        $count = (int) $pdo->query($sql)->fetchColumn();
        if ($count > 0) {
            $failures[] = $label . ': ' . $count;
        }
    }
}

function loadEnv(string $file): void
{
    if (!is_file($file)) {
        return;
    }

    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return;
    }

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }

        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        $value = trim($value, "\"'");

        if ($key !== '' && getenv($key) === false) {
            putenv($key . '=' . $value);
            $_ENV[$key] = $value;
        }
    }
}
