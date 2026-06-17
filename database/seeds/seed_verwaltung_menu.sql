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
  UNION ALL SELECT 'datenschutz', 'DSGVO', 'DSGVO-Löschungen und Anonymisierung', '/verwaltung/datenschutz', 50
  UNION ALL SELECT 'audit', 'Audit-Log', 'Audit-Log und Nachvollziehbarkeit', '/verwaltung/audit', 60
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
    'audit'
  )
  AND pg.group_key IN (
    'verwaltung.administrator',
    'portal.administrator',
    'portal.system_administrator'
  );

INSERT INTO `pt_menu_items`
  (`menu_id`, `parent_id`, `title`, `slug`, `url`, `route_name`, `target`, `order_index`, `level`, `is_active`)
SELECT
  m.id,
  NULL,
  i.title,
  i.slug,
  i.url,
  NULL,
  NULL,
  i.order_index,
  1,
  1
FROM `pt_menus` m
JOIN (
  SELECT 'verwaltung.main' AS menu_slug, 'Übersicht' AS title, 'dashboard' AS slug, '/verwaltung' AS url, 5 AS order_index
  UNION ALL SELECT 'verwaltung.main', 'Personen', 'personen', '/verwaltung/personen', 10
  UNION ALL SELECT 'verwaltung.main', 'Einladungen', 'einladungen', '/verwaltung/einladungen', 15
  UNION ALL SELECT 'verwaltung.main', 'DSGVO', 'datenschutz', '/verwaltung/datenschutz', 20
  UNION ALL SELECT 'verwaltung.main', 'Gruppen', 'gruppen', '/verwaltung/gruppen', 30
  UNION ALL SELECT 'verwaltung.main', 'Berechtigungen', 'berechtigungen', '/verwaltung/berechtigungen', 40
  UNION ALL SELECT 'verwaltung.main', 'Audit-Log', 'audit', '/verwaltung/audit', 50
) i ON i.menu_slug = m.slug
ON DUPLICATE KEY UPDATE
  `title` = VALUES(`title`),
  `url` = VALUES(`url`),
  `order_index` = VALUES(`order_index`),
  `is_active` = VALUES(`is_active`);

INSERT INTO `pt_menu_items`
  (`menu_id`, `parent_id`, `title`, `slug`, `url`, `route_name`, `target`, `order_index`, `level`, `is_active`)
SELECT
  m.id,
  NULL,
  'Verwaltung',
  'verwaltung',
  '/verwaltung',
  NULL,
  NULL,
  90,
  1,
  1
FROM `pt_menus` m
WHERE m.slug = 'portal.main'
ON DUPLICATE KEY UPDATE
  `title` = VALUES(`title`),
  `url` = VALUES(`url`),
  `order_index` = VALUES(`order_index`),
  `is_active` = VALUES(`is_active`);

SET @has_pt_menu_items_page_group_id := (
  SELECT COUNT(*)
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'pt_menu_items'
    AND COLUMN_NAME = 'page_group_id'
);

SET @sql := IF(
  @has_pt_menu_items_page_group_id > 0,
  'UPDATE `pt_menu_items` mi
     JOIN `pt_menus` m ON m.id = mi.menu_id
     JOIN `pt_areas` menu_area ON menu_area.id = m.area_id
     JOIN `pt_areas` verwaltung_area ON verwaltung_area.area_key = ''verwaltung''
     JOIN `pt_page_groups` ppg
       ON ppg.area_id = verwaltung_area.id
      AND ppg.page_group_key = CASE
          WHEN mi.slug = ''dashboard'' THEN ''personen''
          WHEN mi.slug = ''verwaltung'' THEN ''personen''
          ELSE mi.slug
      END
   SET mi.page_group_id = ppg.id
   WHERE mi.slug IN (
     ''dashboard'',
     ''verwaltung'',
     ''personen'',
     ''einladungen'',
     ''datenschutz'',
     ''gruppen'',
     ''berechtigungen'',
     ''audit''
   )',
  'SELECT 1'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
