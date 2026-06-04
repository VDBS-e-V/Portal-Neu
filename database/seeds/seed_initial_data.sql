INSERT INTO `pt_areas` (`area_key`, `icon`, `name`, `description`, `start_path`, `is_active`, `is_external`, `sort_order`) VALUES
  ('portal', 'icon-home', 'Portal', 'Portal Startbereich', '/', 1, 0, 10),
  ('identity', 'icon-shield', 'Identity Service', 'Identity Verwaltung', '/identity', 1, 0, 20),
  ('bibliocollect', 'icon-library', 'Bibliocollect', 'Schulbibliothek und Bestand', '/bibliocollect', 1, 0, 30),
  ('methodenmatrix', 'icon-methods', 'MethodenMatrix', 'Methoden und Materialkatalog', '/methodenmatrix', 1, 0, 40),
  ('development', 'icon-server', 'Development', 'Entwicklungswerkzeuge', '/development', 1, 0, 90),
  ('styleguide', 'icon-layout', 'Style Guide', 'UI Komponenten und Patterns', '/styleguide', 1, 0, 100);

INSERT INTO `pt_menus` (`area_id`, `name`, `slug`, `is_default`)
SELECT a.id, CONCAT(a.name, ' Menü'), CONCAT(a.area_key, '.main'), 1
FROM `pt_areas` a;

INSERT INTO `ids_permission_groups` (`group_key`, `name`, `description`, `is_system`) VALUES
  ('portal.administrator', 'Portal Administrator', 'Voller Zugriff auf das Portal', 1),
  ('portal.system_administrator', 'Portal System Administrator', 'Systemweite technische Verwaltung im Portal', 1),
  ('portal.developer', 'Portal Developer', 'Entwicklung und technische Tools im Portal', 1),
  ('portal.vorstand', 'Portal Vorstand', 'Vorstandsrolle im Portal', 1),
  ('portal.geschaeftsfuehrung', 'Portal Geschaeftsfuehrung', 'Geschaeftsfuehrung im Portal', 1),
  ('portal.geschaeftsstelle', 'Portal Geschaeftsstelle', 'Geschaeftsstelle im Portal', 1),
  ('portal.teamende', 'Portal Teamende', 'Teamrolle im Portal', 1),
  ('portal.mitglied', 'Portal Mitglied', 'Mitgliedsrolle im Portal', 1),
  ('identity.administrator', 'Identity Administrator', 'Identity Verwaltung', 1),
  ('bibliocollect.administrator', 'Bibliocollect Administrator', 'Voller Zugriff auf Bibliocollect', 1),
  ('bibliocollect.developer', 'Bibliocollect Developer', 'Entwicklung in Bibliocollect', 1),
  ('bibliocollect.verwaltung', 'Bibliocollect Verwaltung', 'Verwaltungsrolle in Bibliocollect', 1),
  ('bibliocollect.mitarbeiter', 'Bibliocollect Mitarbeiter', 'Mitarbeitendenrolle in Bibliocollect', 1),
  ('methodenmatrix.gast', 'MethodenMatrix Gast', 'Gastzugang MethodenMatrix', 1),
  ('methodenmatrix.teamende', 'MethodenMatrix Teamende', 'Teamrolle MethodenMatrix', 1),
  ('methodenmatrix.verwaltung', 'MethodenMatrix Verwaltung', 'Verwaltungsrolle MethodenMatrix', 1),
  ('methodenmatrix.administration', 'MethodenMatrix Administration', 'Administration MethodenMatrix', 1);

INSERT INTO `pt_permission_group_area_access` (`permission_group_id`, `area_id`)
SELECT pg.id, a.id
FROM `ids_permission_groups` pg
JOIN `pt_areas` a
  ON (
    (pg.group_key LIKE 'portal.%' AND a.area_key = 'portal')
    OR (pg.group_key LIKE 'identity.%' AND a.area_key = 'identity')
    OR (pg.group_key LIKE 'bibliocollect.%' AND a.area_key = 'bibliocollect')
    OR (pg.group_key LIKE 'methodenmatrix.%' AND a.area_key = 'methodenmatrix')
  );

INSERT INTO `ids_users` (`user_uuid`, `identity_subject`, `email`, `display_name`, `password_hash`, `status`)
VALUES (UNHEX('<USER_UUID>'), 'local.admin', 'admin@example.org', 'Admin User', '<BCRYPT_HASH>', 'active');

INSERT INTO `ids_user_permission_groups` (`user_id`, `permission_group_id`)
SELECT u.id, pg.id
FROM `ids_users` u
JOIN `ids_permission_groups` pg ON pg.group_key IN (
  'portal.administrator',
  'identity.administrator',
  'bibliocollect.administrator',
  'methodenmatrix.administration'
)
WHERE u.email = 'admin@example.org';

INSERT INTO `pt_ticket_types` (`type_key`, `name`, `description`, `is_active`) VALUES
  ('general', 'Allgemein', 'Allgemeine Anfrage', 1),
  ('seminar_request', 'Seminaranfrage', 'Anfrage zu Seminaren', 1),
  ('technical', 'Technisch', 'Technischer Support', 1),
  ('account', 'Account', 'Anfragen zu Nutzerkonten', 1);

INSERT INTO `cod_schools` (`school_key`, `name`, `school_code`, `street`, `house_number`, `postal_code`, `city`, `country`, `status`) VALUES
  ('school_demo', 'Demo Schule', 'DEMO-001', 'Musterstrasse', '1', '12345', 'Berlin', 'DE', 'active');

INSERT INTO `cod_seminar_templates` (`template_key`, `title`, `description`, `default_duration_minutes`, `is_active`) VALUES
  ('template_intro', 'Einfuehrungsseminar', 'Baseline Seminarvorlage', 180, 1);

INSERT INTO `pt_menu_items` (`menu_id`, `parent_id`, `title`, `slug`, `url`, `route_name`, `target`, `order_index`, `level`, `is_active`)
SELECT m.id, NULL, 'Start', 'start', a.start_path, NULL, NULL, 10, 1, 1
FROM `pt_menus` m
JOIN `pt_areas` a ON a.id = m.area_id;

INSERT INTO `pt_menu_items` (`menu_id`, `parent_id`, `title`, `slug`, `url`, `route_name`, `target`, `order_index`, `level`, `is_active`)
SELECT m.id, NULL, 'Bereiche', 'areas', '/development/web-control/areas', NULL, NULL, 20, 1, 1
FROM `pt_menus` m
WHERE m.slug = 'portal.main';

INSERT INTO `pt_menu_items` (`menu_id`, `parent_id`, `title`, `slug`, `url`, `route_name`, `target`, `order_index`, `level`, `is_active`)
SELECT m.id, NULL, 'Menüs', 'menus', '/development/web-control/menus', NULL, NULL, 30, 1, 1
FROM `pt_menus` m
WHERE m.slug = 'portal.main';

INSERT INTO `pt_menu_items` (`menu_id`, `parent_id`, `title`, `slug`, `url`, `route_name`, `target`, `order_index`, `level`, `is_active`)
SELECT m.id, NULL, 'Style Guide', 'styleguide', '/styleguide', NULL, NULL, 40, 1, 1
FROM `pt_menus` m
WHERE m.slug = 'portal.main';
