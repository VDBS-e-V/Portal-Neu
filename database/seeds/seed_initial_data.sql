INSERT INTO `pt_areas` (`area_key`, `icon`, `name`, `description`, `start_path`, `is_active`, `is_external`, `sort_order`) VALUES
('portal', 'icon-home', 'Start', 'Portal Startbereich', '/', 1, 0, 5),
('vorstand', 'icon-users', 'Vorstand', 'Vorstandsbereich', '/vorstand', 1, 0, 10),
('verwaltung', 'icon-briefcase', 'Verwaltung', 'Verwaltungsbereich', '/verwaltung', 1, 0, 20),
('development', 'icon-server', 'Development', 'Entwicklungswerkzeuge', '/development', 1, 0, 30),
('teamende', 'icon-team', 'Teamende', 'Bereich fuer Teamende', '/teamende', 1, 0, 40),
('schule', 'icon-school', 'Schulbereich', 'Schulbereich', '/schule', 1, 0, 50),
('bibliocollect', 'icon-library', 'Bibliocollect', 'Schulbibliothek und Bestand', '/bibliocollect', 1, 0, 60),
('webmail', 'icon-mail', 'Webmail', 'Webmail-Zugang', '/webmail', 1, 0, 70),
('moodle', 'icon-moodle', 'Moodle', 'Lernplattform', '/moodle', 1, 0, 80),
('nextcloud', 'icon-cloud', 'Nextcloud', 'Dateien und Zusammenarbeit', '/nextcloud', 1, 0, 90),
('methodenmatrix', 'icon-methods', 'MethodenMatrix', 'Methoden und Materialkatalog', '/methodenmatrix', 1, 0, 100)
ON DUPLICATE KEY UPDATE
`icon` = VALUES(`icon`),
`name` = VALUES(`name`),
`description` = VALUES(`description`),
`start_path` = VALUES(`start_path`),
`is_active` = VALUES(`is_active`),
`is_external` = VALUES(`is_external`),
`sort_order` = VALUES(`sort_order`);

SET @has_pt_menus_is_default := (
  SELECT COUNT(*)
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'pt_menus'
    AND COLUMN_NAME = 'is_default'
);

SET @sql := IF(
  @has_pt_menus_is_default > 0,
  'INSERT INTO `pt_menus` (`area_id`, `name`, `slug`, `is_default`) SELECT a.id, CONCAT(a.name, '' Menü''), CONCAT(a.area_key, ''.main''), 1 FROM `pt_areas` a ON DUPLICATE KEY UPDATE `name` = VALUES(`name`), `is_default` = VALUES(`is_default`)',
  'INSERT INTO `pt_menus` (`area_id`, `name`, `slug`) SELECT a.id, CONCAT(a.name, '' Menü''), CONCAT(a.area_key, ''.main'') FROM `pt_areas` a ON DUPLICATE KEY UPDATE `name` = VALUES(`name`)'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

INSERT INTO `ids_permission_groups` (`group_key`, `name`, `description`, `is_system`) VALUES
('portal.administrator', 'Portal Administrator', 'Voller Zugriff auf alle Bereiche', 1),
('portal.system_administrator', 'Portal System Administrator', 'Systemweite technische Verwaltung im Portal', 1),
('portal.developer', 'Portal Developer', 'Entwicklung und technische Tools im Portal', 1),
('portal.vorstand', 'Portal Vorstand', 'Vorstandsrolle im Portal', 1),
('portal.geschaeftsfuehrung', 'Portal Geschaeftsfuehrung', 'Geschaeftsfuehrung im Portal', 1),
('portal.geschaeftsstelle', 'Portal Geschaeftsstelle', 'Geschaeftsstelle im Portal', 1),
('portal.teamende', 'Portal Teamende', 'Teamrolle im Portal', 1),
('portal.mitglied', 'Portal Mitglied', 'Mitgliedsrolle im Portal', 1),
('identity.administrator', 'Identity Administrator', 'Identity Verwaltung', 1),
('vorstand.administrator', 'Vorstand Administrator', 'Voller Zugriff auf den Vorstandsbereich', 1),
('verwaltung.administrator', 'Verwaltung Administrator', 'Voller Zugriff auf den Verwaltungsbereich', 1),
('development.administrator', 'Development Administrator', 'Voller Zugriff auf Development', 1),
('teamende.administrator', 'Teamende Administrator', 'Voller Zugriff auf den Bereich Teamende', 1),
('schule.administrator', 'Schule Administrator', 'Voller Zugriff auf den Schulbereich', 1),
('webmail.administrator', 'Webmail Administrator', 'Voller Zugriff auf Webmail', 1),
('moodle.administrator', 'Moodle Administrator', 'Voller Zugriff auf Moodle', 1),
('nextcloud.administrator', 'Nextcloud Administrator', 'Voller Zugriff auf Nextcloud', 1),
('bibliocollect.administrator', 'Bibliocollect Administrator', 'Voller Zugriff auf Bibliocollect', 1),
('bibliocollect.developer', 'Bibliocollect Developer', 'Entwicklung in Bibliocollect', 1),
('bibliocollect.verwaltung', 'Bibliocollect Verwaltung', 'Verwaltungsrolle in Bibliocollect', 1),
('bibliocollect.mitarbeiter', 'Bibliocollect Mitarbeiter', 'Mitarbeitendenrolle in Bibliocollect', 1),
('methodenmatrix.gast', 'MethodenMatrix Gast', 'Gastzugang MethodenMatrix', 1),
('methodenmatrix.teamende', 'MethodenMatrix Teamende', 'Teamrolle MethodenMatrix', 1),
('methodenmatrix.verwaltung', 'MethodenMatrix Verwaltung', 'Verwaltungsrolle MethodenMatrix', 1),
('methodenmatrix.administration', 'MethodenMatrix Administration', 'Administration MethodenMatrix', 1)
ON DUPLICATE KEY UPDATE
`name` = VALUES(`name`),
`description` = VALUES(`description`),
`is_system` = VALUES(`is_system`);

INSERT IGNORE INTO `pt_permission_group_area_access` (`permission_group_id`, `area_id`)
SELECT pg.id, a.id
FROM `ids_permission_groups` pg
JOIN `pt_areas` a ON (
  pg.group_key IN ('portal.administrator', 'portal.system_administrator')
  OR (pg.group_key LIKE 'portal.%' AND a.area_key = 'portal')
  OR (pg.group_key = 'portal.vorstand' AND a.area_key = 'vorstand')
  OR (pg.group_key IN ('portal.geschaeftsfuehrung', 'portal.geschaeftsstelle') AND a.area_key = 'verwaltung')
  OR (pg.group_key = 'portal.developer' AND a.area_key = 'development')
  OR (pg.group_key = 'portal.teamende' AND a.area_key = 'teamende')
  OR (pg.group_key = 'portal.mitglied' AND a.area_key IN ('schule', 'webmail', 'moodle', 'nextcloud'))
  OR (pg.group_key LIKE 'vorstand.%' AND a.area_key = 'vorstand')
  OR (pg.group_key LIKE 'verwaltung.%' AND a.area_key = 'verwaltung')
  OR (pg.group_key LIKE 'development.%' AND a.area_key = 'development')
  OR (pg.group_key LIKE 'teamende.%' AND a.area_key = 'teamende')
  OR (pg.group_key LIKE 'schule.%' AND a.area_key = 'schule')
  OR (pg.group_key LIKE 'webmail.%' AND a.area_key = 'webmail')
  OR (pg.group_key LIKE 'moodle.%' AND a.area_key = 'moodle')
  OR (pg.group_key LIKE 'nextcloud.%' AND a.area_key = 'nextcloud')
  OR (pg.group_key LIKE 'bibliocollect.%' AND a.area_key = 'bibliocollect')
  OR (pg.group_key LIKE 'methodenmatrix.%' AND a.area_key = 'methodenmatrix')
);

SET @has_ids_persons := (
  SELECT COUNT(*)
  FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'ids_persons'
);

SET @sql := IF(
  @has_ids_persons > 0,
  'INSERT INTO `ids_persons` (`person_uuid`, `display_name`, `status`) VALUES (UNHEX(''00000000000000000000000000000001''), ''System Administrator'', ''active''), (UNHEX(''00000000000000000000000000000002''), ''Verwaltung Admin'', ''active''), (UNHEX(''00000000000000000000000000000003''), ''Demo Nutzer'', ''active''), (UNHEX(''00000000000000000000000000000004''), ''Eingeladene Person'', ''active''), (UNHEX(''00000000000000000000000000000005''), ''Person ohne Login'', ''active'') ON DUPLICATE KEY UPDATE `display_name` = VALUES(`display_name`), `status` = VALUES(`status`)',
  'SELECT 1'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @has_ids_users_person_id := (
  SELECT COUNT(*)
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'ids_users'
    AND COLUMN_NAME = 'person_id'
);

SET @has_invited_status := (
  SELECT COUNT(*)
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'ids_users'
    AND COLUMN_NAME = 'status'
    AND COLUMN_TYPE LIKE '%invited%'
);

SET @sql := IF(
  @has_ids_users_person_id > 0,
  'INSERT INTO `ids_users` (`person_id`, `user_uuid`, `identity_subject`, `email`, `password_hash`, `status`) SELECT p.id, UNHEX(''10000000000000000000000000000001''), ''local.admin'', ''admin@example.org'', ''$2y$12$kL0lbLlmnWsF5Cdem4s7CepJVllAYj903mhMzd6MqA8nJrSLuHrXW'', ''active'' FROM `ids_persons` p WHERE p.person_uuid = UNHEX(''00000000000000000000000000000001'') ON DUPLICATE KEY UPDATE `person_id` = VALUES(`person_id`), `password_hash` = VALUES(`password_hash`), `status` = VALUES(`status`)',
  'INSERT INTO `ids_users` (`user_uuid`, `identity_subject`, `email`, `display_name`, `password_hash`, `status`) VALUES (UNHEX(''10000000000000000000000000000001''), ''local.admin'', ''admin@example.org'', ''System Administrator'', ''$2y$12$kL0lbLlmnWsF5Cdem4s7CepJVllAYj903mhMzd6MqA8nJrSLuHrXW'', ''active'') ON DUPLICATE KEY UPDATE `display_name` = VALUES(`display_name`), `password_hash` = VALUES(`password_hash`), `status` = VALUES(`status`)'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql := IF(
  @has_ids_users_person_id > 0,
  'INSERT INTO `ids_users` (`person_id`, `user_uuid`, `identity_subject`, `email`, `password_hash`, `status`) SELECT p.id, UNHEX(''10000000000000000000000000000002''), ''local.verwaltung_admin'', ''verwaltung.admin@example.org'', ''$2y$12$Gx0KkLQU5icF2ffA7YB2n.U2Imukv8qEsekCWFNGg7TSXQNZIJZaq'', ''active'' FROM `ids_persons` p WHERE p.person_uuid = UNHEX(''00000000000000000000000000000002'') ON DUPLICATE KEY UPDATE `person_id` = VALUES(`person_id`), `password_hash` = VALUES(`password_hash`), `status` = VALUES(`status`)',
  'INSERT INTO `ids_users` (`user_uuid`, `identity_subject`, `email`, `display_name`, `password_hash`, `status`) VALUES (UNHEX(''10000000000000000000000000000002''), ''local.verwaltung_admin'', ''verwaltung.admin@example.org'', ''Verwaltung Admin'', ''$2y$12$Gx0KkLQU5icF2ffA7YB2n.U2Imukv8qEsekCWFNGg7TSXQNZIJZaq'', ''active'') ON DUPLICATE KEY UPDATE `display_name` = VALUES(`display_name`), `password_hash` = VALUES(`password_hash`), `status` = VALUES(`status`)'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql := IF(
  @has_ids_users_person_id > 0,
  'INSERT INTO `ids_users` (`person_id`, `user_uuid`, `identity_subject`, `email`, `password_hash`, `status`) SELECT p.id, UNHEX(''10000000000000000000000000000003''), ''local.demo_user'', ''demo.user@example.org'', ''$2y$12$Gx0KkLQU5icF2ffA7YB2n.U2Imukv8qEsekCWFNGg7TSXQNZIJZaq'', ''active'' FROM `ids_persons` p WHERE p.person_uuid = UNHEX(''00000000000000000000000000000003'') ON DUPLICATE KEY UPDATE `person_id` = VALUES(`person_id`), `password_hash` = VALUES(`password_hash`), `status` = VALUES(`status`)',
  'INSERT INTO `ids_users` (`user_uuid`, `identity_subject`, `email`, `display_name`, `password_hash`, `status`) VALUES (UNHEX(''10000000000000000000000000000003''), ''local.demo_user'', ''demo.user@example.org'', ''Demo Nutzer'', ''$2y$12$Gx0KkLQU5icF2ffA7YB2n.U2Imukv8qEsekCWFNGg7TSXQNZIJZaq'', ''active'') ON DUPLICATE KEY UPDATE `display_name` = VALUES(`display_name`), `password_hash` = VALUES(`password_hash`), `status` = VALUES(`status`)'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @invited_status_value := IF(@has_invited_status > 0, 'invited', 'disabled');

SET @sql := IF(
  @has_ids_users_person_id > 0,
  CONCAT('INSERT INTO `ids_users` (`person_id`, `user_uuid`, `identity_subject`, `email`, `password_hash`, `status`) SELECT p.id, UNHEX(''10000000000000000000000000000004''), ''local.invited'', ''invited@example.org'', NULL, ''', @invited_status_value, ''' FROM `ids_persons` p WHERE p.person_uuid = UNHEX(''00000000000000000000000000000004'') ON DUPLICATE KEY UPDATE `person_id` = VALUES(`person_id`), `password_hash` = VALUES(`password_hash`), `status` = VALUES(`status`)'),
  CONCAT('INSERT INTO `ids_users` (`user_uuid`, `identity_subject`, `email`, `display_name`, `password_hash`, `status`) VALUES (UNHEX(''10000000000000000000000000000004''), ''local.invited'', ''invited@example.org'', ''Eingeladene Person'', NULL, ''', @invited_status_value, ''') ON DUPLICATE KEY UPDATE `display_name` = VALUES(`display_name`), `password_hash` = VALUES(`password_hash`), `status` = VALUES(`status`)')
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @has_ids_person_permission_groups := (
  SELECT COUNT(*)
  FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'ids_person_permission_groups'
);

SET @sql := IF(
  @has_ids_person_permission_groups > 0,
  'INSERT IGNORE INTO `ids_person_permission_groups` (`person_id`, `permission_group_id`, `assigned_by_person_id`) SELECT p.id, pg.id, adminp.id FROM `ids_persons` p JOIN `ids_permission_groups` pg JOIN `ids_persons` adminp ON adminp.person_uuid = UNHEX(''00000000000000000000000000000001'') WHERE p.person_uuid = UNHEX(''00000000000000000000000000000001'') AND pg.group_key IN (''portal.administrator'', ''portal.system_administrator'', ''identity.administrator'', ''vorstand.administrator'', ''verwaltung.administrator'', ''development.administrator'', ''teamende.administrator'', ''schule.administrator'', ''bibliocollect.administrator'', ''webmail.administrator'', ''moodle.administrator'', ''nextcloud.administrator'', ''methodenmatrix.administration'')',
  'INSERT IGNORE INTO `ids_user_permission_groups` (`user_id`, `permission_group_id`, `assigned_by_user_id`) SELECT u.id, pg.id, u.id FROM `ids_users` u JOIN `ids_permission_groups` pg WHERE u.email = ''admin@example.org'' AND pg.group_key IN (''portal.administrator'', ''portal.system_administrator'', ''identity.administrator'', ''vorstand.administrator'', ''verwaltung.administrator'', ''development.administrator'', ''teamende.administrator'', ''schule.administrator'', ''bibliocollect.administrator'', ''webmail.administrator'', ''moodle.administrator'', ''nextcloud.administrator'', ''methodenmatrix.administration'')'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql := IF(
  @has_ids_person_permission_groups > 0,
  'INSERT IGNORE INTO `ids_person_permission_groups` (`person_id`, `permission_group_id`, `assigned_by_person_id`) SELECT p.id, pg.id, adminp.id FROM `ids_persons` p JOIN `ids_permission_groups` pg JOIN `ids_persons` adminp ON adminp.person_uuid = UNHEX(''00000000000000000000000000000001'') WHERE p.person_uuid = UNHEX(''00000000000000000000000000000002'') AND pg.group_key = ''verwaltung.administrator''',
  'INSERT IGNORE INTO `ids_user_permission_groups` (`user_id`, `permission_group_id`, `assigned_by_user_id`) SELECT u.id, pg.id, adminu.id FROM `ids_users` u JOIN `ids_permission_groups` pg JOIN `ids_users` adminu ON adminu.email = ''admin@example.org'' WHERE u.email = ''verwaltung.admin@example.org'' AND pg.group_key = ''verwaltung.administrator'''
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @has_ids_person_names := (
  SELECT COUNT(*)
  FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'ids_person_names'
);

SET @sql := IF(
  @has_ids_person_names > 0,
  'INSERT INTO `ids_person_names` (`person_id`, `first_name`, `last_name`, `preferred_name`) SELECT p.id, x.first_name, x.last_name, x.preferred_name FROM `ids_persons` p JOIN (SELECT UNHEX(''00000000000000000000000000000001'') AS person_uuid, ''System'' AS first_name, ''Administrator'' AS last_name, ''Admin'' AS preferred_name UNION ALL SELECT UNHEX(''00000000000000000000000000000002''), ''Verwaltung'', ''Admin'', ''Verwaltung Admin'' UNION ALL SELECT UNHEX(''00000000000000000000000000000003''), ''Demo'', ''Nutzer'', ''Demo'' UNION ALL SELECT UNHEX(''00000000000000000000000000000004''), ''Eingeladene'', ''Person'', ''Einladung'' UNION ALL SELECT UNHEX(''00000000000000000000000000000005''), ''Person'', ''Ohne Login'', ''Ohne Login'') x ON x.person_uuid = p.person_uuid ON DUPLICATE KEY UPDATE `first_name` = VALUES(`first_name`), `last_name` = VALUES(`last_name`), `preferred_name` = VALUES(`preferred_name`)',
  'INSERT INTO `ids_user_names` (`user_id`, `first_name`, `last_name`, `preferred_name`) SELECT u.id, x.first_name, x.last_name, x.preferred_name FROM `ids_users` u JOIN (SELECT ''admin@example.org'' AS email, ''System'' AS first_name, ''Administrator'' AS last_name, ''Admin'' AS preferred_name UNION ALL SELECT ''verwaltung.admin@example.org'', ''Verwaltung'', ''Admin'', ''Verwaltung Admin'' UNION ALL SELECT ''demo.user@example.org'', ''Demo'', ''Nutzer'', ''Demo'' UNION ALL SELECT ''invited@example.org'', ''Eingeladene'', ''Person'', ''Einladung'') x ON x.email = u.email ON DUPLICATE KEY UPDATE `first_name` = VALUES(`first_name`), `last_name` = VALUES(`last_name`), `preferred_name` = VALUES(`preferred_name`)'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @has_ids_person_contact_details := (
  SELECT COUNT(*)
  FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'ids_person_contact_details'
);

SET @sql := IF(
  @has_ids_person_contact_details > 0,
  'INSERT IGNORE INTO `ids_person_contact_details` (`person_id`, `contact_type`, `label`, `value`, `is_primary`, `is_verified`) SELECT p.id, ''email'', ''Login'', u.email, 1, 1 FROM `ids_persons` p JOIN `ids_users` u ON u.person_id = p.id WHERE u.email IN (''admin@example.org'', ''verwaltung.admin@example.org'', ''demo.user@example.org'', ''invited@example.org'')',
  'INSERT IGNORE INTO `ids_user_contact_details` (`user_id`, `contact_type`, `label`, `value`, `is_primary`, `is_verified`) SELECT u.id, ''email'', ''Login'', u.email, 1, 1 FROM `ids_users` u WHERE u.email IN (''admin@example.org'', ''verwaltung.admin@example.org'', ''demo.user@example.org'', ''invited@example.org'')'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @has_ids_person_addresses := (
  SELECT COUNT(*)
  FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'ids_person_addresses'
);

SET @sql := IF(
  @has_ids_person_addresses > 0,
  'INSERT IGNORE INTO `ids_person_addresses` (`person_id`, `address_type`, `recipient_name`, `street`, `house_number`, `postal_code`, `city`, `country`, `is_primary`) SELECT p.id, ''work'', p.display_name, ''Musterstrasse'', ''1'', ''12345'', ''Berlin'', ''DE'', 1 FROM `ids_persons` p WHERE p.person_uuid IN (UNHEX(''00000000000000000000000000000001''), UNHEX(''00000000000000000000000000000002''), UNHEX(''00000000000000000000000000000003''))',
  'INSERT IGNORE INTO `ids_user_addresses` (`user_id`, `address_type`, `recipient_name`, `street`, `house_number`, `postal_code`, `city`, `country`, `is_primary`) SELECT u.id, ''work'', u.display_name, ''Musterstrasse'', ''1'', ''12345'', ''Berlin'', ''DE'', 1 FROM `ids_users` u WHERE u.email IN (''admin@example.org'', ''verwaltung.admin@example.org'', ''demo.user@example.org'')'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

INSERT INTO `ids_user_account_settings` (`user_id`, `language`, `timezone`, `email_notifications`, `profile_visibility`)
SELECT u.id, 'de', 'Europe/Berlin', 1, 'private'
FROM `ids_users` u
WHERE u.email IN ('admin@example.org', 'verwaltung.admin@example.org', 'demo.user@example.org', 'invited@example.org')
ON DUPLICATE KEY UPDATE
`language` = VALUES(`language`),
`timezone` = VALUES(`timezone`),
`email_notifications` = VALUES(`email_notifications`),
`profile_visibility` = VALUES(`profile_visibility`);

SET @has_pt_page_groups := (
  SELECT COUNT(*)
  FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'pt_page_groups'
);

SET @sql := IF(
  @has_pt_page_groups > 0,
  'INSERT INTO `pt_page_groups` (`area_id`, `page_group_key`, `name`, `description`, `start_path`, `sort_order`, `is_active`) SELECT a.id, x.page_group_key, x.name, x.description, x.start_path, x.sort_order, 1 FROM `pt_areas` a JOIN (SELECT ''personen'' AS page_group_key, ''Personen'' AS name, ''Personen- und Stammdatenverwaltung'' AS description, ''/verwaltung/personen'' AS start_path, 10 AS sort_order UNION ALL SELECT ''gruppen'', ''Gruppen'', ''Gruppenverwaltung'', ''/verwaltung/gruppen'', 20 UNION ALL SELECT ''berechtigungen'', ''Berechtigungen'', ''Berechtigungsmatrix und PageGroup-Zugriffe'', ''/verwaltung/berechtigungen'', 30) x WHERE a.area_key = ''verwaltung'' ON DUPLICATE KEY UPDATE `name` = VALUES(`name`), `description` = VALUES(`description`), `start_path` = VALUES(`start_path`), `sort_order` = VALUES(`sort_order`), `is_active` = VALUES(`is_active`)',
  'SELECT 1'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @has_pg_page_group_access := (
  SELECT COUNT(*)
  FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'pt_permission_group_page_group_access'
);

SET @sql := IF(
  @has_pg_page_group_access > 0,
  'INSERT IGNORE INTO `pt_permission_group_page_group_access` (`permission_group_id`, `page_group_id`) SELECT pg.id, ppg.id FROM `ids_permission_groups` pg JOIN `pt_page_groups` ppg JOIN `pt_areas` a ON a.id = ppg.area_id WHERE a.area_key = ''verwaltung'' AND ppg.page_group_key IN (''personen'', ''gruppen'', ''berechtigungen'') AND pg.group_key IN (''verwaltung.administrator'', ''portal.administrator'', ''portal.system_administrator'')',
  'SELECT 1'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Ersetzt den frueheren pt_ticket_types-Seed (flache Liste ohne Bereichs-Ebene).
-- Die alten 4 Typen werden 1:1 als Kategorien unter einem einzigen Bereich "Allgemein"
-- abgebildet, da die tatsaechliche Fachbereichsstruktur (Umsetzungsschritt "Area tickets
-- + PageGroups") noch nicht feststeht. category_key uebernimmt bewusst die alten
-- type_key-Werte, damit bestehende Referenzen/Reports nachvollziehbar bleiben.
INSERT INTO `pt_ticket_areas`
  (`area_key`, `name`, `is_active`, `sort_order`)
VALUES
  ('allgemein', 'Allgemein', 1, 10)
ON DUPLICATE KEY UPDATE
`name` = VALUES(`name`),
`is_active` = VALUES(`is_active`),
`sort_order` = VALUES(`sort_order`);

INSERT INTO `pt_ticket_categories`
  (`area_id`, `category_key`, `name`, `is_active`, `sort_order`)
SELECT
  a.id,
  x.category_key,
  x.name,
  1,
  x.sort_order
FROM `pt_ticket_areas` a
JOIN (
  SELECT 'general' AS category_key, 'Allgemeine Anfrage' AS name, 10 AS sort_order
  UNION ALL SELECT 'seminar_request', 'Seminaranfrage', 20
  UNION ALL SELECT 'technical', 'Technischer Support', 30
  UNION ALL SELECT 'account', 'Anfragen zu Nutzerkonten', 40
) x
WHERE a.area_key = 'allgemein'
ON DUPLICATE KEY UPDATE
`name` = VALUES(`name`),
`is_active` = VALUES(`is_active`),
`sort_order` = VALUES(`sort_order`);

SET @has_cod_schools_school_key := (
  SELECT COUNT(*)
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'cod_schools'
    AND COLUMN_NAME = 'school_key'
);

SET @sql := IF(
  @has_cod_schools_school_key > 0,
  'INSERT INTO `cod_schools` (`school_key`, `name`, `school_code`, `street`, `house_number`, `postal_code`, `city`, `country`, `status`) VALUES (''school_demo'', ''Demo Schule'', ''DEMO-001'', ''Musterstrasse'', ''1'', ''12345'', ''Berlin'', ''DE'', ''active'') ON DUPLICATE KEY UPDATE `name` = VALUES(`name`), `school_code` = VALUES(`school_code`), `street` = VALUES(`street`), `house_number` = VALUES(`house_number`), `postal_code` = VALUES(`postal_code`), `city` = VALUES(`city`), `country` = VALUES(`country`), `status` = VALUES(`status`)',
  'INSERT INTO `cod_schools` (`name`, `school_code`, `street`, `house_number`, `postal_code`, `city`, `country`, `status`) VALUES (''Demo Schule'', ''DEMO-001'', ''Musterstrasse'', ''1'', ''12345'', ''Berlin'', ''DE'', ''active'') ON DUPLICATE KEY UPDATE `name` = VALUES(`name`), `street` = VALUES(`street`), `house_number` = VALUES(`house_number`), `postal_code` = VALUES(`postal_code`), `city` = VALUES(`city`), `country` = VALUES(`country`), `status` = VALUES(`status`)'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

INSERT INTO `cod_seminar_templates` (`template_key`, `title`, `description`, `default_duration_minutes`, `is_active`) VALUES
('template_intro', 'Einfuehrungsseminar', 'Baseline Seminarvorlage', 180, 1)
ON DUPLICATE KEY UPDATE
`title` = VALUES(`title`),
`description` = VALUES(`description`),
`default_duration_minutes` = VALUES(`default_duration_minutes`),
`is_active` = VALUES(`is_active`);

INSERT INTO `pt_menu_items` (`menu_id`, `parent_id`, `title`, `slug`, `url`, `route_name`, `target`, `order_index`, `level`, `is_active`)
SELECT m.id, NULL, i.title, i.slug, i.url, NULL, NULL, i.order_index, 1, 1
FROM `pt_menus` m
JOIN (
  SELECT 'portal.main' AS menu_slug, 'Über das Portal' AS title, 'ueber-das-portal' AS slug, '/ueber-das-portal' AS url, 10 AS order_index
  UNION ALL SELECT 'portal.main', 'Zugang zum Portal', 'zugang-zum-portal', '/zugang-zum-portal', 20
  UNION ALL SELECT 'portal.main', 'FAQ', 'faq', '/faq', 30
  UNION ALL SELECT 'portal.main', 'Kontakt', 'kontakt', '/kontakt', 40
  UNION ALL SELECT 'vorstand.main', 'Mitglieder', 'mitglieder', '/vorstand/mitglieder', 10
  UNION ALL SELECT 'vorstand.main', 'Sitzungen', 'sitzungen', '/vorstand/sitzungen', 20
  UNION ALL SELECT 'vorstand.main', 'Beschlüsse', 'beschluesse', '/vorstand/beschluesse', 30
  UNION ALL SELECT 'verwaltung.main', 'Blog', 'blog', '/verwaltung/blog', 10
  UNION ALL SELECT 'verwaltung.main', 'Personen', 'personen', '/verwaltung/personen', 20
  UNION ALL SELECT 'verwaltung.main', 'Gruppen', 'gruppen', '/verwaltung/gruppen', 25
  UNION ALL SELECT 'verwaltung.main', 'Berechtigungen', 'berechtigungen', '/verwaltung/berechtigungen', 26
  UNION ALL SELECT 'verwaltung.main', 'Schulen', 'schulen', '/verwaltung/schulen', 30
  UNION ALL SELECT 'verwaltung.main', 'Forms', 'forms', '/verwaltung/forms', 40
  UNION ALL SELECT 'verwaltung.main', 'Veranstaltungen', 'veranstaltungen', '/verwaltung/veranstaltungen', 50
  UNION ALL SELECT 'verwaltung.main', 'Tickets', 'tickets', '/verwaltung/tickets', 60
  UNION ALL SELECT 'verwaltung.main', 'Einstellungen', 'einstellungen', '/verwaltung/einstellungen', 70
  UNION ALL SELECT 'development.main', 'Web-Control', 'web-control', '/development/web-control', 10
  UNION ALL SELECT 'development.main', 'WCL', 'wcl', '/development/wcl', 20
  UNION ALL SELECT 'development.main', 'Tickets', 'tickets', '/development/tickets', 30
  UNION ALL SELECT 'development.main', 'Einstellungen', 'einstellungen', '/development/einstellungen', 40
  UNION ALL SELECT 'development.main', 'Personen', 'personen', '/development/personen', 50
  UNION ALL SELECT 'development.main', 'Help', 'help', '/development/help', 60
  UNION ALL SELECT 'teamende.main', 'Seminare', 'seminare', '/teamende/seminare', 10
) i ON i.menu_slug = m.slug
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
  @has_pt_menu_items_page_group_id > 0 AND @has_pt_page_groups > 0,
  'UPDATE `pt_menu_items` mi JOIN `pt_menus` m ON m.id = mi.menu_id JOIN `pt_areas` a ON a.id = m.area_id JOIN `pt_page_groups` ppg ON ppg.area_id = a.id AND ppg.page_group_key = mi.slug SET mi.page_group_id = ppg.id WHERE a.area_key = ''verwaltung'' AND mi.slug IN (''personen'', ''gruppen'', ''berechtigungen'')',
  'SELECT 1'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @has_pt_audit_log := (
  SELECT COUNT(*)
  FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'pt_audit_log'
);

SET @sql := IF(
  @has_pt_audit_log > 0,
  'INSERT INTO `pt_audit_log` (`occurred_at`, `actor_user_id`, `action`, `entity_type`, `entity_label`, `metadata`) SELECT NOW(), u.id, ''seed.initial_data'', ''database.seed'', ''seed_initial_data.sql'', JSON_OBJECT(''admin'', ''admin@example.org'', ''verwaltung_admin'', ''verwaltung.admin@example.org'', ''demo_user'', ''demo.user@example.org'') FROM `ids_users` u WHERE u.email = ''admin@example.org'' LIMIT 1',
  'SELECT 1'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;