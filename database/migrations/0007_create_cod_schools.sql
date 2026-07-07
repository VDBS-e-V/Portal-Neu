CREATE TABLE IF NOT EXISTS `cod_schools` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel der Schule.',
  `school_uuid` BINARY(16) NOT NULL COMMENT 'Eindeutige UUID der Schule als 16-Byte-Binärwert.',
  `school_key` VARCHAR(191) NOT NULL COMMENT 'Stabiler technischer Schlüssel, z. B. de-be:01B01.',

  `name` VARCHAR(191) NOT NULL COMMENT 'Anzeigename der Schule.',
  `slug` VARCHAR(191) NOT NULL COMMENT 'URL- und suchfreundlicher Kurzname.',

  `country_code` CHAR(2) NOT NULL DEFAULT 'DE' COMMENT 'ISO-3166-1-alpha-2-Ländercode.',
  `federal_state_code` VARCHAR(16) NOT NULL COMMENT 'Bundesland-Code, z. B. DE-BE, DE-BB.',

  `lifecycle_status` ENUM(
    'active',
    'inactive',
    'unknown',
    'merged',
    'closed'
  ) NOT NULL DEFAULT 'active' COMMENT 'Lebenszyklusstatus der Schule.',

  `data_status` ENUM(
    'imported',
    'manually_created',
    'manually_verified',
    'needs_review'
  ) NOT NULL DEFAULT 'imported' COMMENT 'Status der Datenpflege.',

  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage der Schule.',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung der Schule.',

  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_cod_schools_uuid` (`school_uuid`),
  UNIQUE KEY `uq_cod_schools_key` (`school_key`),
  KEY `idx_cod_schools_name` (`name`),
  KEY `idx_cod_schools_slug` (`slug`),
  KEY `idx_cod_schools_state` (`federal_state_code`),
  KEY `idx_cod_schools_lifecycle_status` (`lifecycle_status`),
  KEY `idx_cod_schools_data_status` (`data_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Schulstammdaten für das vereinsinterne Schulverzeichnis.';