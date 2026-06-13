INSERT INTO `pt_areas` (`area_key`, `icon`, `name`, `description`, `start_path`, `is_active`, `is_external`, `sort_order`) VALUES
  ('portal', 'icon-home', 'Start', 'Portal Startbereich', '/', 1, 0, 5),
  ('vorstand', 'icon-users', 'Vorstand', 'Vorstandsbereich', '/vorstand', 1, 0, 10),
  ('verwaltung', 'icon-briefcase', 'Verwaltung', 'Verwaltungsbereich', '/verwaltung', 1, 0, 20),
  ('development', 'icon-server', 'Development', 'Entwicklungswerkzeuge', '/development', 1, 0, 30),
  ('teamende', 'icon-team', 'Teamende', 'Bereich fuer Teamende', '/teamende', 1, 0, 40),
  ('schule', 'icon-school', 'Schule', 'Schulbereich', '/schule', 1, 0, 50),
  ('bibliocollect', 'icon-library', 'Bibliocollect', 'Schulbibliothek und Bestand', '/bibliocollect', 1, 0, 60),
  ('webmail', 'icon-mail', 'Webmail', 'Webmail-Zugang', '/webmail', 1, 0, 70),
  ('moodle', 'icon-moodle', 'Moodle', 'Lernplattform', '/moodle', 1, 0, 80),
  ('nextcloud', 'icon-cloud', 'Nextcloud', 'Dateien und Zusammenarbeit', '/nextcloud', 1, 0, 90),
  ('methodenmatrix', 'icon-methods', 'MethodenMatrix', 'Methoden und Materialkatalog', '/methodenmatrix', 1, 0, 100);

INSERT INTO `pt_menus` (`area_id`, `name`, `slug`, `is_default`)
SELECT a.id, CONCAT(a.name, ' Menü'), CONCAT(a.area_key, '.main'), 1
FROM `pt_areas` a;

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
  ('methodenmatrix.administration', 'MethodenMatrix Administration', 'Administration MethodenMatrix', 1);

INSERT INTO `pt_permission_group_area_access` (`permission_group_id`, `area_id`)
SELECT pg.id, a.id
FROM `ids_permission_groups` pg
JOIN `pt_areas` a
  ON (
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

INSERT INTO `ids_users` (`user_uuid`, `identity_subject`, `email`, `display_name`, `password_hash`, `status`)
VALUES (UNHEX('<USER_UUID>'), 'local.admin', 'admin@example.org', 'Admin User', '<BCRYPT_HASH>', 'active');

INSERT INTO `ids_user_permission_groups` (`user_id`, `permission_group_id`)
SELECT u.id, pg.id
FROM `ids_users` u
JOIN `ids_permission_groups` pg ON pg.group_key IN (
  'portal.administrator',
  'identity.administrator',
  'vorstand.administrator',
  'verwaltung.administrator',
  'development.administrator',
  'teamende.administrator',
  'schule.administrator',
  'bibliocollect.administrator',
  'webmail.administrator',
  'moodle.administrator',
  'nextcloud.administrator',
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
) i ON i.menu_slug = m.slug;
