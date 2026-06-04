ALTER TABLE `__migrations`
  COMMENT = 'Protokolliert ausgeführte Migrationen.',
  MODIFY COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel der Protokollzeile.',
  MODIFY COLUMN `migration` VARCHAR(255) NOT NULL COMMENT 'Name der ausgeführten Migration; muss eindeutig sein.',
  MODIFY COLUMN `batch` INT NOT NULL COMMENT 'Nummer des Migrationslaufs; positive ganze Zahl.',
  MODIFY COLUMN `ran_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Ausführung; wird automatisch gesetzt.';

ALTER TABLE `ids_users`
  COMMENT = 'Benutzerkonten für das Identity-System.',
  MODIFY COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel des Benutzerkontos.',
  MODIFY COLUMN `user_uuid` BINARY(16) NOT NULL COMMENT 'Eindeutige UUID des Benutzerkontos als 16-Byte-Binärwert.',
  MODIFY COLUMN `identity_subject` VARCHAR(191) NULL COMMENT 'Optionaler externer Identitäts-Subject; eindeutig, wenn gesetzt.',
  MODIFY COLUMN `email` VARCHAR(191) NOT NULL COMMENT 'E-Mail-Adresse für Login und Kontakt; muss eindeutig sein.',
  MODIFY COLUMN `display_name` VARCHAR(191) NULL COMMENT 'Anzeigename des Benutzers; optional.',
  MODIFY COLUMN `password_hash` VARCHAR(255) NULL COMMENT 'Passwort-Hash des Benutzers; NULL für externe Konten ohne lokales Passwort.',
  MODIFY COLUMN `status` ENUM('active', 'disabled') NOT NULL DEFAULT 'active' COMMENT 'Kontostatus; active = nutzbar, disabled = gesperrt.',
  MODIFY COLUMN `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage des Benutzerkontos.',
  MODIFY COLUMN `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung des Benutzerkontos.';

ALTER TABLE `ids_permission_groups`
  COMMENT = 'Technische Berechtigungsgruppen im Identity-System.',
  MODIFY COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel der Berechtigungsgruppe.',
  MODIFY COLUMN `group_key` VARCHAR(191) NOT NULL COMMENT 'Eindeutiger technischer Schlüssel, meist im Format domain.rolle.',
  MODIFY COLUMN `name` VARCHAR(191) NOT NULL COMMENT 'Anzeigename der Berechtigungsgruppe.',
  MODIFY COLUMN `description` TEXT NULL COMMENT 'Optionale Beschreibung der Berechtigungsgruppe.',
  MODIFY COLUMN `is_system` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Kennzeichnet systemverwaltete Gruppen; 0 = manuell, 1 = systemseitig.',
  MODIFY COLUMN `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage der Berechtigungsgruppe.',
  MODIFY COLUMN `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung der Berechtigungsgruppe.';

ALTER TABLE `ids_user_permission_groups`
  COMMENT = 'Verknüpft Benutzer mit Berechtigungsgruppen.',
  MODIFY COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel der Zuordnung.',
  MODIFY COLUMN `user_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf ids_users.id; zugeordneter Benutzer.',
  MODIFY COLUMN `permission_group_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf ids_permission_groups.id; zugeordnete Berechtigungsgruppe.',
  MODIFY COLUMN `assigned_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Zuweisung.',
  MODIFY COLUMN `assigned_by_user_id` BIGINT UNSIGNED NULL COMMENT 'Verweist auf ids_users.id; Benutzer, der die Zuordnung vergeben hat, oder NULL.';

ALTER TABLE `pt_areas`
  COMMENT = 'Fachliche Bereiche des Portals; steuern Navigation und Zugriffe.',
  MODIFY COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel des Bereichs.',
  MODIFY COLUMN `area_key` VARCHAR(191) NOT NULL COMMENT 'Eindeutiger technischer Bereichsschlüssel, z. B. portal oder development.',
  MODIFY COLUMN `icon` VARCHAR(100) NULL COMMENT 'Optionales Icon aus dem Icon-Set, z. B. icon-home, icon-shield, icon-library, icon-methods, icon-server oder icon-layout.',
  MODIFY COLUMN `name` VARCHAR(191) NOT NULL COMMENT 'Anzeigename des Bereichs.',
  MODIFY COLUMN `description` TEXT NULL COMMENT 'Optionale Kurzbeschreibung des Bereichs.',
  MODIFY COLUMN `start_path` VARCHAR(255) NOT NULL DEFAULT '/' COMMENT 'Startpfad des Bereichs; muss mit / beginnen, z. B. /, /identity oder /development.',
  MODIFY COLUMN `is_active` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Aktivstatus des Bereichs; 0 = inaktiv, 1 = aktiv.',
  MODIFY COLUMN `is_external` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Kennzeichnet externe Bereiche; 0 = intern, 1 = extern.',
  MODIFY COLUMN `sort_order` INT NOT NULL DEFAULT 0 COMMENT 'Sortierreihenfolge; kleinere Werte werden zuerst angezeigt.',
  MODIFY COLUMN `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage des Bereichs.',
  MODIFY COLUMN `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung des Bereichs.';

ALTER TABLE `pt_permission_group_area_access`
  COMMENT = 'Zuordnung von Berechtigungsgruppen zu Bereichen.',
  MODIFY COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel der Zuordnung.',
  MODIFY COLUMN `permission_group_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf ids_permission_groups.id; berechtigte Gruppe.',
  MODIFY COLUMN `area_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf pt_areas.id; freigegebener Bereich.',
  MODIFY COLUMN `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Vergabe des Bereichszugriffs.';

ALTER TABLE `pt_ticket_types`
  COMMENT = 'Katalog der Ticketarten.',
  MODIFY COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel der Ticketart.',
  MODIFY COLUMN `type_key` VARCHAR(191) NOT NULL COMMENT 'Eindeutiger technischer Schlüssel der Ticketart, z. B. general oder technical.',
  MODIFY COLUMN `name` VARCHAR(191) NOT NULL COMMENT 'Anzeigename der Ticketart.',
  MODIFY COLUMN `description` TEXT NULL COMMENT 'Optionale Beschreibung der Ticketart.',
  MODIFY COLUMN `is_active` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Aktivstatus; 0 = inaktiv, 1 = aktiv.',
  MODIFY COLUMN `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage der Ticketart.',
  MODIFY COLUMN `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung der Ticketart.';

ALTER TABLE `cod_schools`
  COMMENT = 'Schulen und Schulstammdaten.',
  MODIFY COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel der Schule.',
  MODIFY COLUMN `school_key` VARCHAR(191) NOT NULL COMMENT 'Eindeutiger technischer Schlüssel der Schule, z. B. school_demo.',
  MODIFY COLUMN `name` VARCHAR(191) NOT NULL COMMENT 'Anzeigename der Schule.',
  MODIFY COLUMN `school_code` VARCHAR(64) NULL COMMENT 'Optionaler externer Schulcode; eindeutig, wenn gesetzt.',
  MODIFY COLUMN `street` VARCHAR(191) NULL COMMENT 'Straße der Schule; optional.',
  MODIFY COLUMN `house_number` VARCHAR(32) NULL COMMENT 'Hausnummer der Schule; optional.',
  MODIFY COLUMN `postal_code` VARCHAR(16) NULL COMMENT 'Postleitzahl der Schule; optional.',
  MODIFY COLUMN `city` VARCHAR(191) NULL COMMENT 'Ort der Schule; optional.',
  MODIFY COLUMN `country` VARCHAR(2) NOT NULL DEFAULT 'DE' COMMENT 'ISO-3166-1-Alpha-2-Ländercode; standardmäßig DE.',
  MODIFY COLUMN `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active' COMMENT 'Status der Schule; active = aktiv, inactive = deaktiviert.',
  MODIFY COLUMN `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage der Schule.',
  MODIFY COLUMN `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung der Schule.';

ALTER TABLE `cod_school_sites`
  COMMENT = 'Standorte einer Schule.',
  MODIFY COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel des Standorts.',
  MODIFY COLUMN `school_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf cod_schools.id; zugehörige Schule.',
  MODIFY COLUMN `site_key` VARCHAR(191) NOT NULL COMMENT 'Eindeutiger technischer Schlüssel des Standorts innerhalb der Schule.',
  MODIFY COLUMN `name` VARCHAR(191) NOT NULL COMMENT 'Anzeigename des Standorts.',
  MODIFY COLUMN `street` VARCHAR(191) NULL COMMENT 'Straße des Standorts; optional.',
  MODIFY COLUMN `house_number` VARCHAR(32) NULL COMMENT 'Hausnummer des Standorts; optional.',
  MODIFY COLUMN `postal_code` VARCHAR(16) NULL COMMENT 'Postleitzahl des Standorts; optional.',
  MODIFY COLUMN `city` VARCHAR(191) NULL COMMENT 'Ort des Standorts; optional.',
  MODIFY COLUMN `country` VARCHAR(2) NOT NULL DEFAULT 'DE' COMMENT 'ISO-3166-1-Alpha-2-Ländercode des Standorts; standardmäßig DE.',
  MODIFY COLUMN `is_primary` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Kennzeichnet den Hauptstandort der Schule; 0 = nein, 1 = ja.',
  MODIFY COLUMN `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage des Standorts.',
  MODIFY COLUMN `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung des Standorts.';

ALTER TABLE `cod_school_memberships`
  COMMENT = 'Mitgliedschaften von Benutzern an Schulen.',
  MODIFY COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel der Mitgliedschaft.',
  MODIFY COLUMN `school_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf cod_schools.id; zugehörige Schule.',
  MODIFY COLUMN `user_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf ids_users.id; zugeordneter Benutzer.',
  MODIFY COLUMN `role_key` VARCHAR(191) NOT NULL COMMENT 'Technischer Rollenbezeichner innerhalb der Schule; frei wählbarer Schlüssel wie z. B. teacher oder admin.',
  MODIFY COLUMN `starts_at` DATE NULL COMMENT 'Startdatum der Mitgliedschaft; NULL bedeutet ohne festes Startdatum.',
  MODIFY COLUMN `ends_at` DATE NULL COMMENT 'Enddatum der Mitgliedschaft; NULL bedeutet unbefristet.',
  MODIFY COLUMN `is_primary` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Kennzeichnet die Hauptzuordnung; 0 = nein, 1 = ja.',
  MODIFY COLUMN `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage der Mitgliedschaft.',
  MODIFY COLUMN `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung der Mitgliedschaft.';

ALTER TABLE `cod_seminar_templates`
  COMMENT = 'Vorlagen für Seminare.',
  MODIFY COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel der Seminarvorlage.',
  MODIFY COLUMN `template_key` VARCHAR(191) NOT NULL COMMENT 'Eindeutiger technischer Schlüssel der Vorlage, z. B. template_intro.',
  MODIFY COLUMN `title` VARCHAR(191) NOT NULL COMMENT 'Titel der Vorlage.',
  MODIFY COLUMN `description` TEXT NULL COMMENT 'Optionale Beschreibung der Vorlage.',
  MODIFY COLUMN `default_duration_minutes` INT UNSIGNED NULL COMMENT 'Standarddauer in Minuten; positive ganze Zahl oder NULL.',
  MODIFY COLUMN `is_active` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Aktivstatus; 0 = inaktiv, 1 = aktiv.',
  MODIFY COLUMN `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage der Seminarvorlage.',
  MODIFY COLUMN `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung der Seminarvorlage.';

ALTER TABLE `cod_seminars`
  COMMENT = 'Einzelne Seminartermine.',
  MODIFY COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel des Seminars.',
  MODIFY COLUMN `seminar_key` VARCHAR(191) NOT NULL COMMENT 'Eindeutiger technischer Schlüssel des Seminars.',
  MODIFY COLUMN `template_id` BIGINT UNSIGNED NULL COMMENT 'Verweist auf cod_seminar_templates.id; optionale Seminarvorlage.',
  MODIFY COLUMN `school_id` BIGINT UNSIGNED NULL COMMENT 'Verweist auf cod_schools.id; optionale zugeordnete Schule.',
  MODIFY COLUMN `title` VARCHAR(191) NOT NULL COMMENT 'Titel des Seminars.',
  MODIFY COLUMN `status` ENUM('draft', 'planned', 'running', 'completed', 'cancelled') NOT NULL DEFAULT 'draft' COMMENT 'Status des Seminars; draft, planned, running, completed oder cancelled.',
  MODIFY COLUMN `starts_on` DATE NULL COMMENT 'Startdatum des Seminars; NULL wenn noch nicht geplant.',
  MODIFY COLUMN `ends_on` DATE NULL COMMENT 'Enddatum des Seminars; NULL wenn noch nicht geplant.',
  MODIFY COLUMN `notes` TEXT NULL COMMENT 'Freie Notizen zum Seminar.',
  MODIFY COLUMN `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage des Seminars.',
  MODIFY COLUMN `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung des Seminars.';

ALTER TABLE `cod_seminar_sessions`
  COMMENT = 'Terminabschnitte eines Seminars.',
  MODIFY COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel der Session.',
  MODIFY COLUMN `seminar_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf cod_seminars.id; zugehöriges Seminar.',
  MODIFY COLUMN `session_number` INT UNSIGNED NOT NULL COMMENT 'Laufende Nummer der Session innerhalb des Seminars; beginnt bei 1.',
  MODIFY COLUMN `starts_at` DATETIME NOT NULL COMMENT 'Startzeitpunkt der Session.',
  MODIFY COLUMN `ends_at` DATETIME NULL COMMENT 'Endzeitpunkt der Session; NULL wenn offen.',
  MODIFY COLUMN `location` VARCHAR(191) NULL COMMENT 'Ort der Session; optional.',
  MODIFY COLUMN `notes` TEXT NULL COMMENT 'Freie Notizen zur Session.',
  MODIFY COLUMN `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage der Session.',
  MODIFY COLUMN `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung der Session.';

ALTER TABLE `cod_seminar_staff`
  COMMENT = 'Personalzuordnung zu Seminaren.',
  MODIFY COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel der Personalzuordnung.',
  MODIFY COLUMN `seminar_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf cod_seminars.id; zugehöriges Seminar.',
  MODIFY COLUMN `user_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf ids_users.id; zugeordneter Benutzer.',
  MODIFY COLUMN `role_key` VARCHAR(191) NOT NULL COMMENT 'Technischer Rollenbezeichner im Seminar; frei wählbarer Schlüssel wie z. B. lead oder assistant.',
  MODIFY COLUMN `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage der Personalzuordnung.';

ALTER TABLE `cod_seminar_participants`
  COMMENT = 'Teilnehmende an Seminaren.',
  MODIFY COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel der Teilnahme.',
  MODIFY COLUMN `seminar_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf cod_seminars.id; zugehöriges Seminar.',
  MODIFY COLUMN `participant_name` VARCHAR(191) NOT NULL COMMENT 'Name der teilnehmenden Person.',
  MODIFY COLUMN `participant_email` VARCHAR(191) NULL COMMENT 'E-Mail-Adresse der teilnehmenden Person; optional.',
  MODIFY COLUMN `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage der Teilnahmedaten.';

ALTER TABLE `pt_tickets`
  COMMENT = 'Support- und Fachtickets.',
  MODIFY COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel des Tickets.',
  MODIFY COLUMN `ticket_number` VARCHAR(32) NOT NULL COMMENT 'Eindeutige Ticketnummer für externe Referenz und Suche.',
  MODIFY COLUMN `ticket_type_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf pt_ticket_types.id; Art des Tickets.',
  MODIFY COLUMN `area_id` BIGINT UNSIGNED NULL COMMENT 'Verweist auf pt_areas.id; fachlich zugeordneter Bereich oder NULL.',
  MODIFY COLUMN `school_id` BIGINT UNSIGNED NULL COMMENT 'Verweist auf cod_schools.id; optionale betroffene Schule.',
  MODIFY COLUMN `seminar_id` BIGINT UNSIGNED NULL COMMENT 'Verweist auf cod_seminars.id; optional betroffener Seminartermin.',
  MODIFY COLUMN `created_by_user_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf ids_users.id; Benutzer, der das Ticket angelegt hat.',
  MODIFY COLUMN `assigned_to_user_id` BIGINT UNSIGNED NULL COMMENT 'Verweist auf ids_users.id; aktuell zuständiger Bearbeiter oder NULL.',
  MODIFY COLUMN `subject` VARCHAR(191) NOT NULL COMMENT 'Kurzer Betreff des Tickets.',
  MODIFY COLUMN `description` MEDIUMTEXT NULL COMMENT 'Ausführliche Beschreibung des Anliegens.',
  MODIFY COLUMN `status` ENUM('open', 'in_progress', 'waiting', 'resolved', 'closed') NOT NULL DEFAULT 'open' COMMENT 'Bearbeitungsstatus; open, in_progress, waiting, resolved oder closed.',
  MODIFY COLUMN `priority` ENUM('low', 'normal', 'high', 'urgent') NOT NULL DEFAULT 'normal' COMMENT 'Priorität des Tickets; low, normal, high oder urgent.',
  MODIFY COLUMN `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage des Tickets.',
  MODIFY COLUMN `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung des Tickets.';

ALTER TABLE `pt_ticket_comments`
  COMMENT = 'Kommentare und Verlaufseinträge zu Tickets.',
  MODIFY COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel des Kommentars.',
  MODIFY COLUMN `ticket_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf pt_tickets.id; zugehöriges Ticket.',
  MODIFY COLUMN `author_user_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf ids_users.id; Verfasser des Kommentars.',
  MODIFY COLUMN `comment_body` MEDIUMTEXT NOT NULL COMMENT 'Inhalt des Kommentars; freier Text.',
  MODIFY COLUMN `is_internal` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Kennzeichnet interne Notizen; 0 = sichtbar für externe Sicht, 1 = intern.',
  MODIFY COLUMN `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Erstellung des Kommentars.';

ALTER TABLE `pt_menus`
  COMMENT = 'Navigationseinträge pro Area.',
  MODIFY COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel des Menüs.',
  MODIFY COLUMN `area_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf pt_areas.id; jede Area hat genau ein Menü.',
  MODIFY COLUMN `name` VARCHAR(191) NOT NULL COMMENT 'Anzeigename des Menüs.',
  MODIFY COLUMN `slug` VARCHAR(191) NOT NULL COMMENT 'Eindeutiger technischer Schlüssel des Menüs, z. B. portal.main.',
  MODIFY COLUMN `is_default` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Kennzeichnet das Standardmenü der Area; 0 = nein, 1 = ja.',
  MODIFY COLUMN `settings` JSON DEFAULT NULL COMMENT 'Freies JSON für Menüeinstellungen; NULL oder gültiges JSON-Objekt/Array.',
  MODIFY COLUMN `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage des Menüs.',
  MODIFY COLUMN `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung des Menüs.',
  MODIFY COLUMN `deleted_at` DATETIME NULL DEFAULT NULL COMMENT 'Zeitpunkt der Soft-Deletion; NULL = aktiv.';

ALTER TABLE `pt_menu_items`
  COMMENT = 'Einträge eines Menüs.',
  MODIFY COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel des Menüeintrags.',
  MODIFY COLUMN `menu_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf pt_menus.id; zugehöriges Menü.',
  MODIFY COLUMN `parent_id` BIGINT UNSIGNED DEFAULT NULL COMMENT 'Verweist auf pt_menu_items.id; übergeordneter Menüpunkt oder NULL.',
  MODIFY COLUMN `title` VARCHAR(191) NOT NULL COMMENT 'Anzeigename des Menüeintrags.',
  MODIFY COLUMN `slug` VARCHAR(191) DEFAULT NULL COMMENT 'Optionale technische Kurzkennung des Menüeintrags.',
  MODIFY COLUMN `url` VARCHAR(255) DEFAULT NULL COMMENT 'Ziel-URL des Menüeintrags; NULL, wenn die Route genutzt wird.',
  MODIFY COLUMN `route_name` VARCHAR(191) DEFAULT NULL COMMENT 'Optionaler Routenname für die Zielauflösung.',
  MODIFY COLUMN `icon` VARCHAR(100) DEFAULT NULL COMMENT 'Derzeit unbenutztes Icon-Feld für Menüeinträge; muss NULL bleiben.',
  MODIFY COLUMN `target` VARCHAR(20) DEFAULT NULL COMMENT 'Linkziel; NULL oder Werte wie _self, _blank, _parent oder _top.',
  MODIFY COLUMN `order_index` INT DEFAULT NULL COMMENT 'Sortierreihenfolge innerhalb des Menüs; kleinere Werte zuerst.',
  MODIFY COLUMN `level` TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Verschachtelungsebene des Menüeintrags; erlaubt sind Werte von 1 bis 3.',
  MODIFY COLUMN `is_active` TINYINT(1) DEFAULT 1 COMMENT 'Aktivstatus des Menüeintrags; 0 = inaktiv, 1 = aktiv.',
  MODIFY COLUMN `settings` JSON DEFAULT NULL COMMENT 'Freies JSON für Eintragsoptionen; NULL oder gültiges JSON-Objekt/Array.',
  MODIFY COLUMN `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage des Menüeintrags.',
  MODIFY COLUMN `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung des Menüeintrags.',
  MODIFY COLUMN `deleted_at` DATETIME NULL DEFAULT NULL COMMENT 'Zeitpunkt der Soft-Deletion; NULL = aktiv.';
