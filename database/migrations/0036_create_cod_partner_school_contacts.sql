CREATE TABLE IF NOT EXISTS `cod_partner_school_contacts` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel des internen Partnerschulkontakts.',
  `school_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf cod_schools.id.',

  `name` VARCHAR(191) NOT NULL COMMENT 'Name der Kontaktperson.',
  `role_label` VARCHAR(191) NULL COMMENT 'Rolle oder Funktion an der Schule.',

  `email` VARCHAR(191) NULL COMMENT 'E-Mail-Adresse der Kontaktperson.',
  `phone` VARCHAR(64) NULL COMMENT 'Telefonnummer der Kontaktperson.',

  `notes` TEXT NULL COMMENT 'Interne Notizen zur Kontaktperson.',
  `is_primary` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Kennzeichnet die Hauptkontaktperson.',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Kennzeichnet, ob der Kontakt aktuell aktiv ist.',

  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage des Kontakts.',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung des Kontakts.',

  PRIMARY KEY (`id`),
  KEY `idx_cod_partner_school_contacts_school` (`school_id`),
  KEY `idx_cod_partner_school_contacts_email` (`email`),
  KEY `idx_cod_partner_school_contacts_active` (`is_active`),

  CONSTRAINT `fk_cod_partner_school_contacts_school`
    FOREIGN KEY (`school_id`)
    REFERENCES `cod_schools` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Interne Ansprechpartnerinnen und Ansprechpartner an Partnerschulen.';