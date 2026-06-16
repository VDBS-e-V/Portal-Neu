CREATE TABLE IF NOT EXISTS `cod_school_sites` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel des Schulstandorts.',
  `school_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf cod_schools.id; zugehörige Schule.',
  `name` VARCHAR(191) NOT NULL COMMENT 'Name oder Bezeichnung des Standorts.',
  `street` VARCHAR(191) NULL COMMENT 'Straße des Standorts.',
  `house_number` VARCHAR(32) NULL COMMENT 'Hausnummer des Standorts.',
  `postal_code` VARCHAR(16) NULL COMMENT 'Postleitzahl des Standorts.',
  `city` VARCHAR(191) NULL COMMENT 'Ort des Standorts.',
  `country` VARCHAR(2) NOT NULL DEFAULT 'DE' COMMENT 'ISO-3166-1-alpha-2-Ländercode des Standorts.',
  `is_primary` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Kennzeichnet den Hauptstandort der Schule.',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage des Standorts.',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung des Standorts.',

  PRIMARY KEY (`id`),
  KEY `idx_cod_school_sites_school` (`school_id`),

  CONSTRAINT `fk_cod_school_sites_school`
    FOREIGN KEY (`school_id`) REFERENCES `cod_schools` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Standorte und Adressen von Schulen.';
