CREATE TABLE IF NOT EXISTS `cod_school_contacts` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel des Schulkontakts.',
  `school_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf cod_schools.id.',
  `site_id` BIGINT UNSIGNED NULL COMMENT 'Optionaler Verweis auf cod_school_sites.id.',

  `contact_type` ENUM(
    'phone',
    'fax',
    'email',
    'website',
    'other'
  ) NOT NULL COMMENT 'Art des Kontakts.',

  `label` VARCHAR(191) NULL COMMENT 'Optionale Bezeichnung, z. B. Sekretariat.',
  `value` VARCHAR(500) NOT NULL COMMENT 'Kontaktwert, z. B. Telefonnummer, E-Mail oder URL.',

  `source_key` VARCHAR(64) NULL COMMENT 'Quelle des Kontakts, z. B. berlin_schulverzeichnis.',
  `is_primary` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Kennzeichnet den primären Kontakt dieser Art.',
  `is_verified` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Kennzeichnet manuell geprüfte Kontakte.',

  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage des Kontakts.',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung des Kontakts.',

  PRIMARY KEY (`id`),
  KEY `idx_cod_school_contacts_school` (`school_id`),
  KEY `idx_cod_school_contacts_site` (`site_id`),
  KEY `idx_cod_school_contacts_type` (`contact_type`),
  KEY `idx_cod_school_contacts_source` (`source_key`),

  CONSTRAINT `fk_cod_school_contacts_school`
    FOREIGN KEY (`school_id`)
    REFERENCES `cod_schools` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,

  CONSTRAINT `fk_cod_school_contacts_site`
    FOREIGN KEY (`site_id`)
    REFERENCES `cod_school_sites` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Offizielle und importierte Kontaktinformationen von Schulen.';