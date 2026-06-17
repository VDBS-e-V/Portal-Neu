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

foreach ([
    'ids_permission_groups',
    'pt_areas',
    'pt_page_groups',
    'pt_permission_group_page_group_access',
] as $table) {
    PortalQa::assertTrue(
        PortalQa::tableExists($pdo, $table),
        'Berechtigungstabelle vorhanden: ' . $table,
        $failures
    );
}

if ($failures !== []) {
    exit(1);
}

$stmt = $pdo->prepare(
    'SELECT id
     FROM ids_permission_groups
     WHERE group_key = :group_key
     LIMIT 1'
);
$stmt->execute(['group_key' => 'verwaltung.administrator']);
$groupId = (int) ($stmt->fetchColumn() ?: 0);

PortalQa::assertTrue($groupId > 0, 'Gruppe verwaltung.administrator existiert', $failures);

$expectedPageGroups = [
    'personen',
    'gruppen',
    'berechtigungen',
];

$optionalPageGroups = [
    'einladungen',
    'datenschutz',
    'audit',
];

foreach ($expectedPageGroups as $pageGroupKey) {
    $stmt = $pdo->prepare(
        'SELECT pg.id
         FROM pt_page_groups pg
         INNER JOIN pt_areas a ON a.id = pg.area_id
         WHERE a.area_key = :area_key
           AND pg.page_group_key = :page_group_key
         LIMIT 1'
    );
    $stmt->execute([
        'area_key' => 'verwaltung',
        'page_group_key' => $pageGroupKey,
    ]);

    $pageGroupId = (int) ($stmt->fetchColumn() ?: 0);

    PortalQa::assertTrue($pageGroupId > 0, 'PageGroup vorhanden: verwaltung.' . $pageGroupKey, $failures);

    if ($groupId > 0 && $pageGroupId > 0) {
        $stmt = $pdo->prepare(
            'SELECT COUNT(*)
             FROM pt_permission_group_page_group_access
             WHERE permission_group_id = :permission_group_id
               AND page_group_id = :page_group_id'
        );
        $stmt->execute([
            'permission_group_id' => $groupId,
            'page_group_id' => $pageGroupId,
        ]);

        PortalQa::assertTrue(
            (int) $stmt->fetchColumn() > 0,
            'Admin-Gruppe hat Zugriff auf: verwaltung.' . $pageGroupKey,
            $failures
        );
    }
}

foreach ($optionalPageGroups as $pageGroupKey) {
    $stmt = $pdo->prepare(
        'SELECT pg.id
         FROM pt_page_groups pg
         INNER JOIN pt_areas a ON a.id = pg.area_id
         WHERE a.area_key = :area_key
           AND pg.page_group_key = :page_group_key
         LIMIT 1'
    );
    $stmt->execute([
        'area_key' => 'verwaltung',
        'page_group_key' => $pageGroupKey,
    ]);

    $pageGroupId = (int) ($stmt->fetchColumn() ?: 0);

    if ($pageGroupId > 0) {
        PortalQa::ok('Optionale PageGroup vorhanden: verwaltung.' . $pageGroupKey);
    } else {
        PortalQa::info('Optionale PageGroup fehlt noch: verwaltung.' . $pageGroupKey);
    }
}

exit($failures === [] ? 0 : 1);
