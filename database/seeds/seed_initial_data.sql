INSERT INTO `pt_areas`
  (`area_key`, `name`, `description`, `start_path`, `is_active`, `is_external`, `sort_order`)
VALUES
  (
    'verwaltung',
    'Verwaltung',
    'Administrativer Bereich für Personen, Gruppen, Berechtigungen, Datenschutz, Audit, Schulen und Partnerschulen.',
    '/verwaltung',
    1,
    0,
    90
  )
ON DUPLICATE KEY UPDATE
  `name` = VALUES(`name`),
  `description` = VALUES(`description`),
  `start_path` = VALUES(`start_path`),
  `is_active` = VALUES(`is_active`),
  `is_external` = VALUES(`is_external`),
  `sort_order` = VALUES(`sort_order`);

INSERT INTO `pt_menus`
  (`area_id`, `name`, `slug`, `is_default`, `settings`)
SELECT
  a.id,
  'Verwaltung',
  'verwaltung.main',
  1,
  JSON_OBJECT('description', 'Mehrstufiges Header-Aufklappmenue fuer den Verwaltungsbereich')
FROM `pt_areas` a
WHERE a.area_key = 'verwaltung'
ON DUPLICATE KEY UPDATE
  `name` = VALUES(`name`),
  `slug` = VALUES(`slug`),
  `is_default` = VALUES(`is_default`),
  `settings` = VALUES(`settings`);

INSERT INTO `pt_page_groups`
  (`area_id`, `page_group_key`, `name`, `description`, `start_path`, `sort_order`, `is_active`)
SELECT
  a.id,
  x.page_group_key,
  x.name,
  x.description,
  x.start_path,
  x.sort_order,
  1
FROM `pt_areas` a
JOIN (
  SELECT 'personen' AS page_group_key, 'Personen' AS name, 'Personen- und Stammdatenverwaltung' AS description, '/verwaltung/personen' AS start_path, 10 AS sort_order
  UNION ALL SELECT 'gruppen', 'Gruppen', 'Gruppenverwaltung', '/verwaltung/gruppen', 20
  UNION ALL SELECT 'berechtigungen', 'Berechtigungen', 'Berechtigungsmatrix und PageGroup-Zugriffe', '/verwaltung/berechtigungen', 30
  UNION ALL SELECT 'einladungen', 'Einladungen', 'Einladungen und Account-Aktivierung', '/verwaltung/einladungen', 40
  UNION ALL SELECT 'datenschutz', 'DSGVO', 'DSGVO-Loeschungen und Anonymisierung', '/verwaltung/datenschutz', 50
  UNION ALL SELECT 'audit', 'Audit-Log', 'Audit-Log und Nachvollziehbarkeit', '/verwaltung/audit', 60

  UNION ALL SELECT 'schulen', 'Schulen', 'Vereinsinternes Schulverzeichnis', '/verwaltung/schulen', 70
  UNION ALL SELECT 'schulimporte', 'Schulimporte', 'Importe für Schulverzeichnisdaten', '/verwaltung/schulen/importe', 80
  UNION ALL SELECT 'partnerschulen', 'Partnerschulen', 'Arbeitsflächen für Partnerschulen, Seminare, Verträge, Dokumente, Notizen und Aufgaben', '/verwaltung/partnerschulen', 90
  UNION ALL SELECT 'partnerschulen_vertraege', 'Partnerschul-Verträge', 'Verträge und Vertragsdokumente von Partnerschulen', '/verwaltung/partnerschulen/vertraege', 100
  UNION ALL SELECT 'partnerschulen_dokumente', 'Partnerschul-Dokumente', 'Dokumente von Partnerschulen', '/verwaltung/partnerschulen/dokumente', 110
) x
WHERE a.area_key = 'verwaltung'
ON DUPLICATE KEY UPDATE
  `name` = VALUES(`name`),
  `description` = VALUES(`description`),
  `start_path` = VALUES(`start_path`),
  `sort_order` = VALUES(`sort_order`),
  `is_active` = VALUES(`is_active`);

INSERT IGNORE INTO `pt_permission_group_page_group_access`
  (`permission_group_id`, `page_group_id`)
SELECT
  pg.id,
  ppg.id
FROM `ids_permission_groups` pg
JOIN `pt_page_groups` ppg
JOIN `pt_areas` a ON a.id = ppg.area_id
WHERE a.area_key = 'verwaltung'
  AND ppg.page_group_key IN (
    'personen',
    'gruppen',
    'berechtigungen',
    'einladungen',
    'datenschutz',
    'audit',
    'schulen',
    'schulimporte',
    'partnerschulen',
    'partnerschulen_vertraege',
    'partnerschulen_dokumente'
  )
  AND pg.group_key IN (
    'verwaltung.administrator',
    'portal.administrator',
    'portal.system_administrator'
  );

INSERT IGNORE INTO `pt_permission_group_page_group_access`
  (`permission_group_id`, `page_group_id`)
SELECT
  pg.id,
  ppg.id
FROM `ids_permission_groups` pg
JOIN `pt_page_groups` ppg
JOIN `pt_areas` a ON a.id = ppg.area_id
WHERE a.area_key = 'verwaltung'
  AND ppg.page_group_key = 'schulen'
  AND pg.group_key IN (
    'schools.viewer',
    'schools.manager',
    'schools.importer',
    'partner_schools.viewer',
    'partner_schools.manager'
  );

INSERT IGNORE INTO `pt_permission_group_page_group_access`
  (`permission_group_id`, `page_group_id`)
SELECT
  pg.id,
  ppg.id
FROM `ids_permission_groups` pg
JOIN `pt_page_groups` ppg
JOIN `pt_areas` a ON a.id = ppg.area_id
WHERE a.area_key = 'verwaltung'
  AND ppg.page_group_key = 'schulimporte'
  AND pg.group_key IN (
    'schools.importer',
    'schools.manager'
  );

INSERT IGNORE INTO `pt_permission_group_page_group_access`
  (`permission_group_id`, `page_group_id`)
SELECT
  pg.id,
  ppg.id
FROM `ids_permission_groups` pg
JOIN `pt_page_groups` ppg
JOIN `pt_areas` a ON a.id = ppg.area_id
WHERE a.area_key = 'verwaltung'
  AND ppg.page_group_key = 'partnerschulen'
  AND pg.group_key IN (
    'partner_schools.viewer',
    'partner_schools.manager',
    'partner_schools.contracts',
    'partner_schools.documents',
    'partner_schools.notes'
  );

INSERT IGNORE INTO `pt_permission_group_page_group_access`
  (`permission_group_id`, `page_group_id`)
SELECT
  pg.id,
  ppg.id
FROM `ids_permission_groups` pg
JOIN `pt_page_groups` ppg
JOIN `pt_areas` a ON a.id = ppg.area_id
WHERE a.area_key = 'verwaltung'
  AND ppg.page_group_key = 'partnerschulen_vertraege'
  AND pg.group_key IN (
    'partner_schools.manager',
    'partner_schools.contracts'
  );

INSERT IGNORE INTO `pt_permission_group_page_group_access`
  (`permission_group_id`, `page_group_id`)
SELECT
  pg.id,
  ppg.id
FROM `ids_permission_groups` pg
JOIN `pt_page_groups` ppg
JOIN `pt_areas` a ON a.id = ppg.area_id
WHERE a.area_key = 'verwaltung'
  AND ppg.page_group_key = 'partnerschulen_dokumente'
  AND pg.group_key IN (
    'partner_schools.manager',
    'partner_schools.documents'
  );

DELETE mi
FROM `pt_menu_items` mi
JOIN `pt_menus` m ON m.id = mi.menu_id
WHERE m.slug = 'verwaltung.main'
  AND mi.slug IN (
    'verwaltung_dashboard',

    'verwaltung_stammdaten',
    'verwaltung_personen',
    'verwaltung_personen_list',
    'verwaltung_personen_create',
    'verwaltung_personen_status',
    'verwaltung_personen_groups',
    'verwaltung_personen_contacts_addresses',
    'verwaltung_gruppen',
    'verwaltung_gruppen_list',
    'verwaltung_gruppen_create',

    'verwaltung_schulverwaltung',
    'verwaltung_schulen',
    'verwaltung_schulen_list',
    'verwaltung_schulen_importe',
    'verwaltung_schulen_importe_list',
    'verwaltung_schulen_importe_upload',

    'verwaltung_partnerschulen',
    'verwaltung_partnerschulen_list',
    'verwaltung_partnerschulen_aufgaben',
    'verwaltung_partnerschulen_kontakte',
    'verwaltung_partnerschulen_seminare',
    'verwaltung_partnerschulen_vertraege',
    'verwaltung_partnerschulen_dokumente',
    'verwaltung_partnerschulen_notizen',

    'verwaltung_zugang',
    'verwaltung_einladungen',
    'verwaltung_einladungen_list',
    'verwaltung_berechtigungen',
    'verwaltung_berechtigungen_matrix',
    'verwaltung_page_groups',

    'verwaltung_datenschutz_sicherheit',
    'verwaltung_datenschutz',
    'verwaltung_datenschutz_open',
    'verwaltung_datenschutz_approved',
    'verwaltung_audit',
    'verwaltung_audit_search',

    'dashboard',
    'personen',
    'personen_create',
    'gruppen',
    'einladungen',
    'berechtigungen',
    'page_groups',
    'datenschutz',
    'audit',

    'blog',
    'schulen',
    'forms',
    'veranstaltungen',
    'tickets',
    'einstellungen'
  );

INSERT INTO `pt_menu_items`
  (`menu_id`, `page_group_id`, `parent_id`, `title`, `slug`, `url`, `route_name`, `icon`, `target`, `order_index`, `level`, `is_active`, `settings`)
SELECT
  m.id,
  NULL,
  NULL,
  x.title,
  x.slug,
  x.url,
  NULL,
  x.icon,
  NULL,
  x.order_index,
  1,
  1,
  JSON_OBJECT('submenu', true)
FROM `pt_menus` m
JOIN (
  SELECT 'Übersicht' AS title, 'verwaltung_dashboard' AS slug, '/verwaltung' AS url, 'dashboard' AS icon, 10 AS order_index
  UNION ALL SELECT 'Stammdaten', 'verwaltung_stammdaten', '/verwaltung/personen', 'persons', 20
  UNION ALL SELECT 'Schulverwaltung', 'verwaltung_schulverwaltung', '/verwaltung/schulen', 'school', 30
  UNION ALL SELECT 'Zugang', 'verwaltung_zugang', '/verwaltung/einladungen', 'account', 40
  UNION ALL SELECT 'Datenschutz & Sicherheit', 'verwaltung_datenschutz_sicherheit', '/verwaltung/datenschutz', 'security', 50
) x
WHERE m.slug = 'verwaltung.main';

INSERT INTO `pt_menu_items`
  (`menu_id`, `page_group_id`, `parent_id`, `title`, `slug`, `url`, `route_name`, `icon`, `target`, `order_index`, `level`, `is_active`, `settings`)
SELECT
  m.id,
  ppg.id,
  parent.id,
  x.title,
  x.slug,
  x.url,
  NULL,
  x.icon,
  NULL,
  x.order_index,
  2,
  1,
  JSON_OBJECT('submenu', true)
FROM `pt_menus` m
JOIN (
  SELECT 'verwaltung_dashboard' AS parent_slug, 'personen' AS page_group_key, 'Dashboard' AS title, 'dashboard' AS slug, '/verwaltung' AS url, 'dashboard' AS icon, 10 AS order_index

  UNION ALL SELECT 'verwaltung_stammdaten', 'personen', 'Personen', 'verwaltung_personen', '/verwaltung/personen', 'persons', 10
  UNION ALL SELECT 'verwaltung_stammdaten', 'gruppen', 'Gruppen', 'verwaltung_gruppen', '/verwaltung/gruppen', 'groups', 20

  UNION ALL SELECT 'verwaltung_schulverwaltung', 'schulen', 'Schulen', 'verwaltung_schulen', '/verwaltung/schulen', 'school', 10
  UNION ALL SELECT 'verwaltung_schulverwaltung', 'schulimporte', 'Schulimporte', 'verwaltung_schulen_importe', '/verwaltung/schulen/importe', 'upload', 20
  UNION ALL SELECT 'verwaltung_schulverwaltung', 'partnerschulen', 'Partnerschulen', 'verwaltung_partnerschulen', '/verwaltung/partnerschulen', 'handshake', 30

  UNION ALL SELECT 'verwaltung_zugang', 'einladungen', 'Einladungen', 'verwaltung_einladungen', '/verwaltung/einladungen', 'mail', 10
  UNION ALL SELECT 'verwaltung_zugang', 'berechtigungen', 'Berechtigungen', 'verwaltung_berechtigungen', '/verwaltung/berechtigungen', 'lock', 20

  UNION ALL SELECT 'verwaltung_datenschutz_sicherheit', 'datenschutz', 'DSGVO-Löschung', 'verwaltung_datenschutz', '/verwaltung/datenschutz', 'shield', 10
  UNION ALL SELECT 'verwaltung_datenschutz_sicherheit', 'audit', 'Audit-Log', 'verwaltung_audit', '/verwaltung/audit', 'audit', 20
) x
JOIN `pt_menu_items` parent ON parent.menu_id = m.id AND parent.slug = x.parent_slug
LEFT JOIN `pt_areas` a ON a.area_key = 'verwaltung'
LEFT JOIN `pt_page_groups` ppg ON ppg.area_id = a.id AND ppg.page_group_key = x.page_group_key
WHERE m.slug = 'verwaltung.main';

INSERT INTO `pt_menu_items`
  (`menu_id`, `page_group_id`, `parent_id`, `title`, `slug`, `url`, `route_name`, `icon`, `target`, `order_index`, `level`, `is_active`, `settings`)
SELECT
  m.id,
  ppg.id,
  parent.id,
  x.title,
  x.slug,
  x.url,
  NULL,
  x.icon,
  NULL,
  x.order_index,
  3,
  1,
  JSON_OBJECT('submenu', false)
FROM `pt_menus` m
JOIN (
  SELECT 'verwaltung_personen' AS parent_slug, 'personen' AS page_group_key, 'Personenübersicht' AS title, 'verwaltung_personen_list' AS slug, '/verwaltung/personen' AS url, 'list' AS icon, 10 AS order_index
  UNION ALL SELECT 'verwaltung_personen', 'personen', 'Person anlegen', 'verwaltung_personen_create', '/verwaltung/personen/create', 'add', 20
  UNION ALL SELECT 'verwaltung_personen', 'personen', 'Gruppenzuweisungen', 'verwaltung_personen_groups', '/verwaltung/personen', 'groups', 30
  UNION ALL SELECT 'verwaltung_personen', 'personen', 'Kontakte & Adressen', 'verwaltung_personen_contacts_addresses', '/verwaltung/personen', 'address-book', 40

  UNION ALL SELECT 'verwaltung_gruppen', 'gruppen', 'Gruppenübersicht', 'verwaltung_gruppen_list', '/verwaltung/gruppen', 'list', 10
  UNION ALL SELECT 'verwaltung_gruppen', 'gruppen', 'Gruppe anlegen', 'verwaltung_gruppen_create', '/verwaltung/gruppen/create', 'add', 20

  UNION ALL SELECT 'verwaltung_schulen', 'schulen', 'Schulübersicht', 'verwaltung_schulen_list', '/verwaltung/schulen', 'list', 10
  UNION ALL SELECT 'verwaltung_schulen', 'schulimporte', 'Importe', 'verwaltung_schulen_importe_list', '/verwaltung/schulen/importe', 'upload', 20
  UNION ALL SELECT 'verwaltung_schulen_importe', 'schulimporte', 'Import hochladen', 'verwaltung_schulen_importe_upload', '/verwaltung/schulen/importe/upload', 'add', 10

  UNION ALL SELECT 'verwaltung_partnerschulen', 'partnerschulen', 'Partnerschulübersicht', 'verwaltung_partnerschulen_list', '/verwaltung/partnerschulen', 'list', 10
  UNION ALL SELECT 'verwaltung_partnerschulen', 'partnerschulen', 'Aufgaben', 'verwaltung_partnerschulen_aufgaben', '/verwaltung/partnerschulen/aufgaben', 'tasks', 20
  UNION ALL SELECT 'verwaltung_partnerschulen', 'partnerschulen', 'Kontakte', 'verwaltung_partnerschulen_kontakte', '/verwaltung/partnerschulen/kontakte', 'address-book', 30
  UNION ALL SELECT 'verwaltung_partnerschulen', 'partnerschulen', 'Seminare', 'verwaltung_partnerschulen_seminare', '/verwaltung/partnerschulen/seminare', 'seminar', 40
  UNION ALL SELECT 'verwaltung_partnerschulen', 'partnerschulen_vertraege', 'Verträge', 'verwaltung_partnerschulen_vertraege', '/verwaltung/partnerschulen/vertraege', 'contract', 50
  UNION ALL SELECT 'verwaltung_partnerschulen', 'partnerschulen_dokumente', 'Dokumente', 'verwaltung_partnerschulen_dokumente', '/verwaltung/partnerschulen/dokumente', 'documents', 60
  UNION ALL SELECT 'verwaltung_partnerschulen', 'partnerschulen', 'Notizen', 'verwaltung_partnerschulen_notizen', '/verwaltung/partnerschulen/notizen', 'note', 70

  UNION ALL SELECT 'verwaltung_einladungen', 'einladungen', 'Alle Einladungen', 'verwaltung_einladungen_list', '/verwaltung/einladungen', 'list', 10

  UNION ALL SELECT 'verwaltung_berechtigungen', 'berechtigungen', 'Berechtigungsmatrix', 'verwaltung_berechtigungen_matrix', '/verwaltung/berechtigungen', 'matrix', 10
  UNION ALL SELECT 'verwaltung_berechtigungen', 'berechtigungen', 'PageGroups', 'verwaltung_page_groups', '/verwaltung/berechtigungen/page-groups', 'pages', 20

  UNION ALL SELECT 'verwaltung_datenschutz', 'datenschutz', 'Offene Vorgänge', 'verwaltung_datenschutz_open', '/verwaltung/datenschutz?status=requested', 'warning', 10
  UNION ALL SELECT 'verwaltung_datenschutz', 'datenschutz', 'Freigegebene Vorgänge', 'verwaltung_datenschutz_approved', '/verwaltung/datenschutz?status=approved', 'check', 20

  UNION ALL SELECT 'verwaltung_audit', 'audit', 'Audit-Suche', 'verwaltung_audit_search', '/verwaltung/audit', 'search', 10
) x
JOIN `pt_menu_items` parent ON parent.menu_id = m.id AND parent.slug = x.parent_slug
LEFT JOIN `pt_areas` a ON a.area_key = 'verwaltung'
LEFT JOIN `pt_page_groups` ppg ON ppg.area_id = a.id AND ppg.page_group_key = x.page_group_key
WHERE m.slug = 'verwaltung.main';

INSERT INTO `pt_menu_items`
  (`menu_id`, `parent_id`, `title`, `slug`, `url`, `route_name`, `icon`, `target`, `order_index`, `level`, `is_active`, `settings`)
SELECT
  m.id,
  NULL,
  'Verwaltung',
  'verwaltung',
  '/verwaltung',
  NULL,
  'admin',
  NULL,
  90,
  1,
  1,
  JSON_OBJECT('area', 'verwaltung')
FROM `pt_menus` m
WHERE m.slug = 'portal.main'
  AND NOT EXISTS (
    SELECT 1
    FROM `pt_menu_items` existing
    WHERE existing.menu_id = m.id
      AND existing.slug = 'verwaltung'
  );