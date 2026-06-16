CREATE TABLE IF NOT EXISTS `ids_person_names` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `person_id` BIGINT UNSIGNED NOT NULL,
  `salutation` VARCHAR(64) NULL,
  `title` VARCHAR(64) NULL,
  `first_name` VARCHAR(191) NULL,
  `middle_name` VARCHAR(191) NULL,
  `last_name` VARCHAR(191) NULL,
  `preferred_name` VARCHAR(191) NULL,
  `pronouns` VARCHAR(64) NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_ids_person_names_person_id` (`person_id`),

  CONSTRAINT `fk_ids_person_names_person`
    FOREIGN KEY (`person_id`) REFERENCES `ids_persons` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Namensbestandteile einer Person.';

CREATE TABLE IF NOT EXISTS `ids_person_contact_details` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `person_id` BIGINT UNSIGNED NOT NULL,
  `contact_type` ENUM('email', 'phone', 'mobile', 'website', 'other') NOT NULL,
  `label` VARCHAR(64) NULL,
  `value` VARCHAR(191) NOT NULL,
  `is_primary` TINYINT(1) NOT NULL DEFAULT 0,
  `is_verified` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

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
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `person_id` BIGINT UNSIGNED NOT NULL,
  `address_type` ENUM('private', 'work', 'billing', 'shipping', 'other') NOT NULL DEFAULT 'private',
  `recipient_name` VARCHAR(191) NULL,
  `organization` VARCHAR(191) NULL,
  `street` VARCHAR(191) NULL,
  `house_number` VARCHAR(32) NULL,
  `address_addition` VARCHAR(191) NULL,
  `postal_code` VARCHAR(16) NULL,
  `city` VARCHAR(191) NULL,
  `state` VARCHAR(191) NULL,
  `country` VARCHAR(2) NOT NULL DEFAULT 'DE',
  `is_primary` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

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
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `language` VARCHAR(16) NOT NULL DEFAULT 'de',
  `timezone` VARCHAR(64) NOT NULL DEFAULT 'Europe/Berlin',
  `email_notifications` TINYINT(1) NOT NULL DEFAULT 1,
  `profile_visibility` ENUM('private', 'members', 'public') NOT NULL DEFAULT 'private',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_ids_user_account_settings_user_id` (`user_id`),

  CONSTRAINT `fk_ids_user_account_settings_user`
    FOREIGN KEY (`user_id`) REFERENCES `ids_users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Accountbezogene Einstellungen eines Login-Kontos.';