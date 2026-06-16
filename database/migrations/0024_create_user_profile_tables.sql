CREATE TABLE IF NOT EXISTS `ids_person_names` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel des Namensdatensatzes.',
  `person_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf ids_persons.id; zugehörige Person.',
  `salutation` VARCHAR(64) NULL COMMENT 'Optionale Anrede.',
  `title` VARCHAR(64) NULL COMMENT 'Optionaler akademischer oder fachlicher Titel.',
  `first_name` VARCHAR(191) NULL COMMENT 'Vorname der Person.',
  `middle_name` VARCHAR(191) NULL COMMENT 'Optionale weitere Vornamen.',
  `last_name` VARCHAR(191) NULL COMMENT 'Nachname der Person.',
  `preferred_name` VARCHAR(191) NULL COMMENT 'Bevorzugter Anzeigename oder Rufname.',
  `pronouns` VARCHAR(64) NULL COMMENT 'Optionale Pronomenangabe.',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage des Namensdatensatzes.',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung des Namensdatensatzes.',

  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_ids_person_names_person_id` (`person_id`),

  CONSTRAINT `fk_ids_person_names_person`
    FOREIGN KEY (`person_id`) REFERENCES `ids_persons` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Namensbestandteile einer Person.';

CREATE TABLE IF NOT EXISTS `ids_person_contact_details` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel der Kontaktmöglichkeit.',
  `person_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf ids_persons.id; zugehörige Person.',
  `contact_type` ENUM('email', 'phone', 'mobile', 'website', 'other') NOT NULL COMMENT 'Art der Kontaktmöglichkeit.',
  `label` VARCHAR(64) NULL COMMENT 'Optionale frei wählbare Bezeichnung, z. B. privat oder dienstlich.',
  `value` VARCHAR(191) NOT NULL COMMENT 'Kontaktwert, z. B. E-Mail-Adresse, Telefonnummer oder URL.',
  `is_primary` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Kennzeichnet bevorzugte Kontaktmöglichkeit innerhalb des Typs.',
  `is_verified` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Kennzeichnet, ob der Kontaktwert verifiziert wurde.',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage der Kontaktmöglichkeit.',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung der Kontaktmöglichkeit.',

  PRIMARY KEY (`id`),
  KEY `idx_ids_person_contact_details_person_id` (`person_id`),
  KEY `idx_ids_person_contact_details_type` (`contact_type`),
  KEY `idx_ids_person_contact_details_primary` (`person_id`, `contact_type`, `is_primary`),

  CONSTRAINT `fk_ids_person_contact_details_person`
    FOREIGN KEY (`person_id`) REFERENCES `ids_persons` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Mehrere Kontaktmöglichkeiten einer Person.';

CREATE TABLE IF NOT EXISTS `ids_person_addresses` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel der Adresse.',
  `person_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf ids_persons.id; zugehörige Person.',
  `address_type` ENUM('private', 'work', 'billing', 'shipping', 'other') NOT NULL DEFAULT 'private' COMMENT 'Art der Adresse.',
  `recipient_name` VARCHAR(191) NULL COMMENT 'Optionaler abweichender Empfängername.',
  `organization` VARCHAR(191) NULL COMMENT 'Optionale Organisation oder Firma.',
  `street` VARCHAR(191) NULL COMMENT 'Straßenname.',
  `house_number` VARCHAR(32) NULL COMMENT 'Hausnummer.',
  `address_addition` VARCHAR(191) NULL COMMENT 'Optionale Adresszusätze.',
  `postal_code` VARCHAR(16) NULL COMMENT 'Postleitzahl.',
  `city` VARCHAR(191) NULL COMMENT 'Ort.',
  `state` VARCHAR(191) NULL COMMENT 'Bundesland, Region oder Staat.',
  `country` VARCHAR(2) NOT NULL DEFAULT 'DE' COMMENT 'ISO-3166-1-alpha-2-Ländercode.',
  `is_primary` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Kennzeichnet bevorzugte Adresse innerhalb des Typs.',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage der Adresse.',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung der Adresse.',

  PRIMARY KEY (`id`),
  KEY `idx_ids_person_addresses_person_id` (`person_id`),
  KEY `idx_ids_person_addresses_type` (`address_type`),
  KEY `idx_ids_person_addresses_primary` (`person_id`, `address_type`, `is_primary`),

  CONSTRAINT `fk_ids_person_addresses_person`
    FOREIGN KEY (`person_id`) REFERENCES `ids_persons` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Mehrere Adressen einer Person.';

CREATE TABLE IF NOT EXISTS `ids_user_account_settings` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel der Account-Einstellungen.',
  `user_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf ids_users.id; zugehöriges Login-Konto.',
  `language` VARCHAR(16) NOT NULL DEFAULT 'de' COMMENT 'Bevorzugte Sprache des Login-Kontos.',
  `timezone` VARCHAR(64) NOT NULL DEFAULT 'Europe/Berlin' COMMENT 'Bevorzugte Zeitzone des Login-Kontos.',
  `email_notifications` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Steuert, ob E-Mail-Benachrichtigungen aktiv sind.',
  `profile_visibility` ENUM('private', 'members', 'public') NOT NULL DEFAULT 'private' COMMENT 'Sichtbarkeit des Profils im Portal.',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage der Account-Einstellungen.',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung der Account-Einstellungen.',

  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_ids_user_account_settings_user_id` (`user_id`),

  CONSTRAINT `fk_ids_user_account_settings_user`
    FOREIGN KEY (`user_id`) REFERENCES `ids_users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Accountbezogene Einstellungen eines Login-Kontos.';
