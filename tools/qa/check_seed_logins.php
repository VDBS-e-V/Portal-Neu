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

if (!PortalQa::tableExists($pdo, 'ids_users')) {
    PortalQa::fail('Tabelle ids_users fehlt.');
    exit(1);
}

$expectedUsers = [
    'admin@example.org',
    'verwaltung.admin@example.org',
    'demo.user@example.org',
];

foreach ($expectedUsers as $email) {
    $stmt = $pdo->prepare(
        'SELECT id, email, status, person_id
         FROM ids_users
         WHERE email = :email
         LIMIT 1'
    );
    $stmt->execute(['email' => $email]);

    $user = $stmt->fetch() ?: [];

    PortalQa::assertTrue($user !== [], 'Seed-Login vorhanden: ' . $email, $failures);

    if ($user !== []) {
        PortalQa::info(sprintf(
            '%s => id=%d status=%s person_id=%d',
            $email,
            (int) $user['id'],
            (string) $user['status'],
            (int) ($user['person_id'] ?? 0)
        ));
    }
}

if (PortalQa::tableExists($pdo, 'ids_permission_groups')) {
    $stmt = $pdo->prepare(
        'SELECT id
         FROM ids_permission_groups
         WHERE group_key = :group_key
         LIMIT 1'
    );
    $stmt->execute(['group_key' => 'verwaltung.administrator']);

    PortalQa::assertTrue(
        (int) ($stmt->fetchColumn() ?: 0) > 0,
        'Initiale Verwaltungsgruppe vorhanden: verwaltung.administrator',
        $failures
    );
}

exit($failures === [] ? 0 : 1);
