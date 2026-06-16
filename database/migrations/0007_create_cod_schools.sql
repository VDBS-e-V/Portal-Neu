CREATE TABLE IF NOT EXISTS `cod_schools` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel der Schule.',
  `school_key` VARCHAR(191) NOT NULL COMMENT 'Eindeutiger technischer Schlüssel der Schule.',
  `name` VARCHAR(191) NOT NULL COMMENT 'Anzeigename der Schule.',
  `school_code` VARCHAR(64) NULL COMMENT 'Optionale Schulnummer oder externer Schulcode.',
  `street` VARCHAR(191) NULL COMMENT 'Straße der Schule.',
  `house_number` VARCHAR(32) NULL COMMENT 'Hausnummer der Schule.',
  `postal_code` VARCHAR(16) NULL COMMENT 'Postleitzahl der Schule.',
  `city` VARCHAR(191) NULL COMMENT 'Ort der Schule.',
  `country` VARCHAR(2) NOT NULL DEFAULT 'DE' COMMENT 'ISO-3166-1-alpha-2 Ländercode.',
  `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active' COMMENT 'Status der Schule.',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage der Schule.',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung der Schule.',

  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_cod_schools_key` (`school_key`),
  UNIQUE KEY `uq_cod_schools_code` (`school_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Schulen beziehungsweise Organisationseinheiten im Portal.';