ALTER TABLE `__migrations`
  COMMENT = 'Protokolliert ausgeführte Migrationen.',
  MODIFY COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel der Protokollzeile.',
  MODIFY COLUMN `migration` VARCHAR(255) NOT NULL COMMENT 'Name der ausgeführten Migration; muss eindeutig sein.',
  MODIFY COLUMN `batch` INT NOT NULL COMMENT 'Nummer des Migrationslaufs; positive ganze Zahl.',
  MODIFY COLUMN `ran_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Ausführung; wird automatisch gesetzt.';

ALTER TABLE `ids_persons`
  COMMENT = 'Fachliche Personenstammdaten; eine Person kann ohne Login existieren.',
  MODIFY COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel der Person.',
  MODIFY COLUMN `person_uuid` BINARY(16) NOT NULL COMMENT 'Eindeutige UUID der Person als 16-Byte-Binärwert.',
  MODIFY COLUMN `display_name` VARCHAR(191) NULL COMMENT 'Optionaler Anzeigename der Person.',
  MODIFY COLUMN `status` ENUM('active', 'disabled', 'erasure_requested', 'erased') NOT NULL DEFAULT 'active' COMMENT 'Personenstatus: aktiv, deaktiviert, Löschung beantragt oder gelöscht/anonymisiert.',
  MODIFY COLUMN `disabled_at` DATETIME NULL COMMENT 'Zeitpunkt der Deaktivierung; NULL wenn nicht deaktiviert.',
  MODIFY COLUMN `erasure_requested_at` DATETIME NULL COMMENT 'Zeitpunkt eines DSGVO-Löschantrags; NULL wenn nicht beantragt.',
  MODIFY COLUMN `erased_at` DATETIME NULL COMMENT 'Zeitpunkt der Löschung oder Anonymisierung; NULL wenn nicht durchgeführt.',
  MODIFY COLUMN `erasure_reason` TEXT NULL COMMENT 'Optionale Begründung oder Referenz zum Löschvorgang.',
  MODIFY COLUMN `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage der Person.',
  MODIFY COLUMN `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung der Person.';

ALTER TABLE `ids_users`
  COMMENT = 'Optionale Login-Konten zu Personen; ein Login gehört genau zu einer Person.',
  MODIFY COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel des Login-Kontos.',
  MODIFY COLUMN `person_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf ids_persons.id; fachliche Person dieses Login-Kontos.',
  MODIFY COLUMN `user_uuid` BINARY(16) NOT NULL COMMENT 'Eindeutige UUID des Login-Kontos als 16-Byte-Binärwert.',
  MODIFY COLUMN `identity_subject` VARCHAR(191) NULL COMMENT 'Optionaler externer Identitäts-Subject; eindeutig, wenn gesetzt.',
  MODIFY COLUMN `email` VARCHAR(191) NOT NULL COMMENT 'E-Mail-Adresse für Login; Pflichtfeld nur für aktive oder eingeladene Login-Konten.',
  MODIFY COLUMN `password_hash` VARCHAR(255) NULL COMMENT 'Optionaler lokaler Passwort-Hash.',
  MODIFY COLUMN `status` ENUM('invited', 'active', 'disabled') NOT NULL DEFAULT 'active' COMMENT 'Login-Status: eingeladen, aktiv oder deaktiviert.',
  MODIFY COLUMN `email_verified_at` DATETIME NULL COMMENT 'Zeitpunkt der E-Mail-Verifizierung.',
  MODIFY COLUMN `last_login_at` DATETIME NULL COMMENT 'Zeitpunkt des letzten erfolgreichen Logins.',
  MODIFY COLUMN `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage des Login-Kontos.',
  MODIFY COLUMN `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung des Login-Kontos.';

ALTER TABLE `ids_permission_groups`
  COMMENT = 'Technische Berechtigungsgruppen im Identity-System.',
  MODIFY COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel der Berechtigungsgruppe.',
  MODIFY COLUMN `group_key` VARCHAR(191) NOT NULL COMMENT 'Eindeutiger technischer Schlüssel, meist im Format domain.rolle.',
  MODIFY COLUMN `name` VARCHAR(191) NOT NULL COMMENT 'Anzeigename der Berechtigungsgruppe.',
  MODIFY COLUMN `description` TEXT NULL COMMENT 'Optionale Beschreibung der Berechtigungsgruppe.',
  MODIFY COLUMN `is_system` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Kennzeichnet systemverwaltete Gruppen; Systemgruppen sind nicht löschbar.',
  MODIFY COLUMN `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage der Berechtigungsgruppe.',
  MODIFY COLUMN `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung der Berechtigungsgruppe.';

ALTER TABLE `ids_person_permission_groups`
  COMMENT = 'Verknüpft Personen mit Berechtigungsgruppen.',
  MODIFY COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel der Zuordnung.',
  MODIFY COLUMN `person_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf ids_persons.id; zugeordnete Person.',
  MODIFY COLUMN `permission_group_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf ids_permission_groups.id; zugeordnete Berechtigungsgruppe.',
  MODIFY COLUMN `assigned_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Zuweisung.',
  MODIFY COLUMN `assigned_by_person_id` BIGINT UNSIGNED NULL COMMENT 'Verweist auf ids_persons.id; Person, die die Zuordnung vergeben hat, oder NULL.';

ALTER TABLE `pt_areas`
  COMMENT = 'Fachliche Bereiche des Portals; oberste Ebene von Navigation und Berechtigung.',
  MODIFY COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel des Bereichs.',
  MODIFY COLUMN `area_key` VARCHAR(191) NOT NULL COMMENT 'Eindeutiger technischer Bereichsschlüssel, z. B. portal, verwaltung oder development.',
  MODIFY COLUMN `icon` VARCHAR(100) NULL COMMENT 'Optionales Icon aus dem Icon-Set.',
  MODIFY COLUMN `name` VARCHAR(191) NOT NULL COMMENT 'Anzeigename des Bereichs.',
  MODIFY COLUMN `description` TEXT NULL COMMENT 'Optionale Kurzbeschreibung des Bereichs.',
  MODIFY COLUMN `start_path` VARCHAR(255) NOT NULL DEFAULT '/' COMMENT 'Startpfad des Bereichs; muss mit / beginnen.',
  MODIFY COLUMN `is_active` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Aktivstatus des Bereichs.',
  MODIFY COLUMN `is_external` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Kennzeichnet externe Bereiche.',
  MODIFY COLUMN `sort_order` INT NOT NULL DEFAULT 0 COMMENT 'Sortierreihenfolge.',
  MODIFY COLUMN `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage des Bereichs.',
  MODIFY COLUMN `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung des Bereichs.';

ALTER TABLE `pt_permission_group_area_access`
  COMMENT = 'Grobe Zuordnung von Berechtigungsgruppen zu Portalbereichen.',
  MODIFY COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel der Zuordnung.',
  MODIFY COLUMN `permission_group_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf ids_permission_groups.id; berechtigte Gruppe.',
  MODIFY COLUMN `area_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf pt_areas.id; freigegebener Bereich.',
  MODIFY COLUMN `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Vergabe des Bereichszugriffs.';

ALTER TABLE `pt_page_groups`
  COMMENT = 'Feingranulare Seitengruppen innerhalb eines Portalbereichs, z. B. Verwaltung.Personen.',
  MODIFY COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel der Seitengruppe.',
  MODIFY COLUMN `area_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf pt_areas.id; übergeordneter Bereich.',
  MODIFY COLUMN `page_group_key` VARCHAR(191) NOT NULL COMMENT 'Technischer Schlüssel der Seitengruppe innerhalb der Area, z. B. personen.',
  MODIFY COLUMN `name` VARCHAR(191) NOT NULL COMMENT 'Anzeigename der Seitengruppe.',
  MODIFY COLUMN `description` TEXT NULL COMMENT 'Optionale Beschreibung der Seitengruppe.',
  MODIFY COLUMN `start_path` VARCHAR(255) NOT NULL COMMENT 'Startpfad der Seitengruppe.',
  MODIFY COLUMN `sort_order` INT NOT NULL DEFAULT 0 COMMENT 'Sortierreihenfolge innerhalb der Area.',
  MODIFY COLUMN `is_active` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Aktivstatus der Seitengruppe.',
  MODIFY COLUMN `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage der Seitengruppe.',
  MODIFY COLUMN `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung der Seitengruppe.';

ALTER TABLE `pt_permission_group_page_group_access`
  COMMENT = 'Feingranulare Zuordnung von Berechtigungsgruppen zu Seitengruppen.',
  MODIFY COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel der Zuordnung.',
  MODIFY COLUMN `permission_group_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf ids_permission_groups.id; berechtigte Gruppe.',
  MODIFY COLUMN `page_group_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf pt_page_groups.id; freigegebene Seitengruppe.',
  MODIFY COLUMN `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Vergabe des Seitengruppenzugriffs.';

ALTER TABLE `cod_school_memberships`
  COMMENT = 'Mitgliedschaften von Personen an Schulen.',
  MODIFY COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel der Mitgliedschaft.',
  MODIFY COLUMN `school_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf cod_schools.id; zugehörige Schule.',
  MODIFY COLUMN `person_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf ids_persons.id; zugeordnete Person.',
  MODIFY COLUMN `role_key` VARCHAR(191) NOT NULL COMMENT 'Technischer Rollenbezeichner innerhalb der Schule.',
  MODIFY COLUMN `starts_at` DATE NULL COMMENT 'Startdatum der Mitgliedschaft.',
  MODIFY COLUMN `ends_at` DATE NULL COMMENT 'Enddatum der Mitgliedschaft.',
  MODIFY COLUMN `is_primary` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Kennzeichnet die Hauptzuordnung.',
  MODIFY COLUMN `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage der Mitgliedschaft.',
  MODIFY COLUMN `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung der Mitgliedschaft.';

ALTER TABLE `cod_seminar_staff`
  COMMENT = 'Personen, die einem Seminar in einer internen Rolle zugeordnet sind.',
  MODIFY COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel der Zuordnung.',
  MODIFY COLUMN `seminar_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf cod_seminars.id; zugehöriges Seminar.',
  MODIFY COLUMN `person_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf ids_persons.id; zugeordnete Person.',
  MODIFY COLUMN `role_key` VARCHAR(191) NOT NULL COMMENT 'Technischer Rollenbezeichner im Seminar.',
  MODIFY COLUMN `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage der Zuordnung.';

ALTER TABLE `cod_seminar_participants`
  COMMENT = 'Teilnehmende eines Seminars; optional mit Personenstammdatensatz verknüpft.',
  MODIFY COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel des Teilnehmenden.',
  MODIFY COLUMN `seminar_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf cod_seminars.id; zugehöriges Seminar.',
  MODIFY COLUMN `person_id` BIGINT UNSIGNED NULL COMMENT 'Optionaler Verweis auf ids_persons.id.',
  MODIFY COLUMN `participant_name` VARCHAR(191) NOT NULL COMMENT 'Name des Teilnehmenden als Snapshot oder Freitext.',
  MODIFY COLUMN `participant_email` VARCHAR(191) NULL COMMENT 'Optionale E-Mail-Adresse des Teilnehmenden als Snapshot oder Freitext.',
  MODIFY COLUMN `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage des Teilnehmenden.';

ALTER TABLE `pt_tickets`
  COMMENT = 'Tickets im Portal.',
  MODIFY COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel des Tickets.',
  MODIFY COLUMN `ticket_number` VARCHAR(32) NOT NULL COMMENT 'Eindeutige Ticketnummer.',
  MODIFY COLUMN `ticket_type_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf pt_ticket_types.id; Ticketart.',
  MODIFY COLUMN `area_id` BIGINT UNSIGNED NULL COMMENT 'Optionaler Portalbereich des Tickets.',
  MODIFY COLUMN `school_id` BIGINT UNSIGNED NULL COMMENT 'Optionale Schule des Tickets.',
  MODIFY COLUMN `seminar_id` BIGINT UNSIGNED NULL COMMENT 'Optionales Seminar des Tickets.',
  MODIFY COLUMN `created_by_person_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf ids_persons.id; erstellende Person.',
  MODIFY COLUMN `assigned_to_person_id` BIGINT UNSIGNED NULL COMMENT 'Verweist auf ids_persons.id; zugewiesene Person.',
  MODIFY COLUMN `subject` VARCHAR(191) NOT NULL COMMENT 'Betreff des Tickets.',
  MODIFY COLUMN `description` MEDIUMTEXT NULL COMMENT 'Beschreibung des Tickets.',
  MODIFY COLUMN `status` ENUM('open', 'in_progress', 'waiting', 'resolved', 'closed') NOT NULL DEFAULT 'open' COMMENT 'Bearbeitungsstatus des Tickets.',
  MODIFY COLUMN `priority` ENUM('low', 'normal', 'high', 'urgent') NOT NULL DEFAULT 'normal' COMMENT 'Priorität des Tickets.',
  MODIFY COLUMN `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage des Tickets.',
  MODIFY COLUMN `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung des Tickets.';

ALTER TABLE `pt_ticket_comments`
  COMMENT = 'Kommentare zu Tickets.',
  MODIFY COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel des Kommentars.',
  MODIFY COLUMN `ticket_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf pt_tickets.id; zugehöriges Ticket.',
  MODIFY COLUMN `author_person_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf ids_persons.id; Autor des Kommentars.',
  MODIFY COLUMN `comment_body` MEDIUMTEXT NOT NULL COMMENT 'Kommentartext.',
  MODIFY COLUMN `is_internal` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Kennzeichnet interne Kommentare.',
  MODIFY COLUMN `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage des Kommentars.';

ALTER TABLE `pt_menu_items`
  COMMENT = 'Navigationspunkte innerhalb eines Menüs.',
  MODIFY COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel des Menüeintrags.',
  MODIFY COLUMN `menu_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf pt_menus.id; zugehöriges Menü.',
  MODIFY COLUMN `page_group_id` BIGINT UNSIGNED NULL COMMENT 'Optionaler Verweis auf pt_page_groups.id; steuert Sichtbarkeit über Area.PageGroup.',
  MODIFY COLUMN `parent_id` BIGINT UNSIGNED DEFAULT NULL COMMENT 'Optionaler übergeordneter Menüeintrag.',
  MODIFY COLUMN `title` VARCHAR(191) NOT NULL COMMENT 'Anzeigetitel des Menüeintrags.',
  MODIFY COLUMN `slug` VARCHAR(191) DEFAULT NULL COMMENT 'Optionaler technischer Slug.',
  MODIFY COLUMN `url` VARCHAR(255) DEFAULT NULL COMMENT 'Optionaler Zielpfad.',
  MODIFY COLUMN `route_name` VARCHAR(191) DEFAULT NULL COMMENT 'Optionaler Routenname.',
  MODIFY COLUMN `icon` VARCHAR(100) DEFAULT NULL COMMENT 'Optionales Icon.',
  MODIFY COLUMN `target` VARCHAR(20) DEFAULT NULL COMMENT 'Optionales Link-Ziel.',
  MODIFY COLUMN `order_index` INT DEFAULT NULL COMMENT 'Sortierreihenfolge.',
  MODIFY COLUMN `level` TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Navigationsebene; aktuell 1 bis 3.',
  MODIFY COLUMN `is_active` TINYINT(1) DEFAULT 1 COMMENT 'Aktivstatus des Menüeintrags.',
  MODIFY COLUMN `settings` JSON DEFAULT NULL COMMENT 'Optionale JSON-Einstellungen.',
  MODIFY COLUMN `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage des Menüeintrags.',
  MODIFY COLUMN `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung des Menüeintrags.',
  MODIFY COLUMN `deleted_at` DATETIME NULL DEFAULT NULL COMMENT 'Zeitpunkt einer weichen Löschung.';