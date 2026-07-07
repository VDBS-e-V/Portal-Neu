<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);

function mp15_load_env(string $root): array
{
    $env = [];
    $file = $root . DIRECTORY_SEPARATOR . '.env';
    if (!is_file($file)) {
        return $env;
    }
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return $env;
    }
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        if ((str_starts_with($value, '"') && str_ends_with($value, '"')) || (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
            $value = substr($value, 1, -1);
        }
        $env[$key] = $value;
    }
    return $env;
}

function mp15_pdo(string $root): PDO
{
    $env = mp15_load_env($root);
    $host = $env['DB_HOST'] ?? '127.0.0.1';
    $port = $env['DB_PORT'] ?? '3306';
    $db = $env['DB_NAME'] ?? $env['DB_DATABASE'] ?? 'vdbs_sys';
    $user = $env['DB_USER'] ?? $env['DB_USERNAME'] ?? 'root';
    $pass = $env['DB_PASS'] ?? $env['DB_PASSWORD'] ?? '';
    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $db);

    try {
        return new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    } catch (PDOException $e) {
        if ($host !== 'localhost') {
            $dsn = sprintf('mysql:host=localhost;port=%s;dbname=%s;charset=utf8mb4', $port, $db);
            return new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        }
        throw $e;
    }
}

function mp15_scalar(PDO $pdo, string $sql, array $params = []): int
{
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return (int) $stmt->fetchColumn();
}

function mp15_table_exists(PDO $pdo, string $table): bool
{
    return mp15_scalar($pdo, 'SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = :table', ['table' => $table]) > 0;
}

function mp15_column_exists(PDO $pdo, string $table, string $column): bool
{
    return mp15_scalar($pdo, 'SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = :table AND column_name = :column', ['table' => $table, 'column' => $column]) > 0;
}

$pdo = mp15_pdo($root);
$errors = [];

foreach (['ids_subjects', 'ids_persons', 'ids_users', 'ids_groups', 'ids_subject_groups', 'ids_permissions', 'ids_group_permissions'] as $table) {
    if (!mp15_table_exists($pdo, $table)) {
        $errors[] = 'Pflicht-Tabelle fehlt: ' . $table;
    }
}

foreach ([
    ['ids_persons', 'subject_id'],
    ['ids_users', 'person_id'],
    ['ids_subject_groups', 'subject_id'],
    ['ids_subject_groups', 'group_id'],
    ['ids_groups', 'system_id'],
    ['ids_permissions', 'system_id'],
] as [$table, $column]) {
    if (!mp15_column_exists($pdo, $table, $column)) {
        $errors[] = 'Pflicht-Spalte fehlt: ' . $table . '.' . $column;
    }
}

foreach (['ids_permission_groups', 'ids_user_permission_groups', 'ids_person_permission_groups'] as $table) {
    if (mp15_table_exists($pdo, $table)) {
        $errors[] = 'Legacy-Identity-Gruppentabelle existiert noch: ' . $table;
    }
}

if ($errors === []) {
    $personsWithoutSubject = mp15_scalar($pdo, 'SELECT COUNT(*) FROM ids_persons p LEFT JOIN ids_subjects s ON s.id = p.subject_id WHERE p.subject_id IS NULL OR s.id IS NULL');
    if ($personsWithoutSubject > 0) {
        $errors[] = 'Personen ohne gültiges Subject: ' . $personsWithoutSubject . ' (php bin\\console seed ausführen, danach erneut prüfen)';
    }

    $usersWithMissingPerson = mp15_scalar($pdo, 'SELECT COUNT(*) FROM ids_users u LEFT JOIN ids_persons p ON p.id = u.person_id WHERE u.person_id IS NOT NULL AND p.id IS NULL');
    if ($usersWithMissingPerson > 0) {
        $errors[] = 'User mit fehlender Person-Referenz: ' . $usersWithMissingPerson;
    }

    $subjectGroupRefs = mp15_scalar($pdo, 'SELECT COUNT(*) FROM ids_subject_groups sg LEFT JOIN ids_subjects s ON s.id = sg.subject_id LEFT JOIN ids_groups g ON g.id = sg.group_id WHERE s.id IS NULL OR g.id IS NULL');
    if ($subjectGroupRefs > 0) {
        $errors[] = 'Subject-Gruppen mit fehlenden Referenzen: ' . $subjectGroupRefs;
    }

    $duplicateSubjects = mp15_scalar($pdo, 'SELECT COUNT(*) FROM (SELECT subject_id FROM ids_persons WHERE subject_id IS NOT NULL GROUP BY subject_id HAVING COUNT(*) > 1) x');
    if ($duplicateSubjects > 0) {
        $errors[] = 'Mehrere Personen teilen sich dasselbe Subject: ' . $duplicateSubjects;
    }

    $crossSystemPermissionAssignments = mp15_scalar($pdo, 'SELECT COUNT(*) FROM ids_group_permissions gp INNER JOIN ids_groups g ON g.id = gp.group_id INNER JOIN ids_permissions p ON p.id = gp.permission_id WHERE g.system_id <> p.system_id');
    if ($crossSystemPermissionAssignments > 0) {
        $errors[] = 'Gruppen haben systemfremde Permissions: ' . $crossSystemPermissionAssignments;
    }
}

if ($errors !== []) {
    echo 'Personen-/Subject-Integritätscheck fehlgeschlagen:' . PHP_EOL;
    foreach ($errors as $error) {
        echo ' - ' . $error . PHP_EOL;
    }
    exit(1);
}

echo 'OK: Personen, User, Subjects und Gruppen-Zuordnungen sind im neuen Identity-Modell konsistent.' . PHP_EOL;