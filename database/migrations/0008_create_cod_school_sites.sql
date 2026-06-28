CREATE TABLE IF NOT EXISTS `cod_school_sites` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel des Schulstandorts.',
  `school_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf cod_schools.id.',

  `site_key` VARCHAR(191) NOT NULL DEFAULT 'primary' COMMENT 'Stabiler Schlüssel des Standorts innerhalb der Schule.',
  `name` VARCHAR(191) NULL COMMENT 'Optionale Bezeichnung des Standorts.',

  `street` VARCHAR(191) NULL COMMENT 'Straße des Standorts.',
  `house_number` VARCHAR(64) NULL COMMENT 'Hausnummer des Standorts.',
  `postal_code` VARCHAR(16) NULL COMMENT 'Postleitzahl des Standorts.',
  `city` VARCHAR(191) NULL COMMENT 'Ort des Standorts.',

  `district` VARCHAR(191) NULL COMMENT 'Bezirk, Kreis oder vergleichbare Verwaltungseinheit.',
  `locality` VARCHAR(191) NULL COMMENT 'Ortsteil oder lokale Untergliederung.',

  `latitude` DECIMAL(10, 7) NULL COMMENT 'Breitengrad in WGS84.',
  `longitude` DECIMAL(10, 7) NULL COMMENT 'Längengrad in WGS84.',

  `is_primary` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Kennzeichnet den Hauptstandort der Schule.',

  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage des Standorts.',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung des Standorts.',

  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_cod_school_sites_school_site_key` (`school_id`, `site_key`),
  KEY `idx_cod_school_sites_school` (`school_id`),
  KEY `idx_cod_school_sites_postal_city` (`postal_code`, `city`),
  KEY `idx_cod_school_sites_district_locality` (`district`, `locality`),
  KEY `idx_cod_school_sites_geo` (`latitude`, `longitude`),

  CONSTRAINT `fk_cod_school_sites_school`
    FOREIGN KEY (`school_id`)
    REFERENCES `cod_schools` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Standorte, Adressen und Geokoordinaten von Schulen.';