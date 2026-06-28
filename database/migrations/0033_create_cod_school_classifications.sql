CREATE TABLE IF NOT EXISTS `cod_school_classifications` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel der Schulklassifikation.',
  `school_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf cod_schools.id.',

  `school_year` VARCHAR(16) NOT NULL COMMENT 'Schuljahr, z. B. 2025/26.',

  `school_type` VARCHAR(191) NULL COMMENT 'Schulart, z. B. Grundschule, Gymnasium.',
  `school_category` VARCHAR(191) NULL COMMENT 'Schultyp oder Trägerart, z. B. öffentlich oder privat.',
  `operator_name` VARCHAR(255) NULL COMMENT 'Träger der Schule.',

  `source_key` VARCHAR(64) NULL COMMENT 'Datenquelle, z. B. berlin_schulverzeichnis.',
  `source_import_id` BIGINT UNSIGNED NULL COMMENT 'Optionaler Verweis auf cod_school_imports.id.',

  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage der Klassifikation.',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung der Klassifikation.',

  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_cod_school_classifications_school_year_source` (`school_id`, `school_year`, `source_key`),
  KEY `idx_cod_school_classifications_school` (`school_id`),
  KEY `idx_cod_school_classifications_year` (`school_year`),
  KEY `idx_cod_school_classifications_type` (`school_type`),
  KEY `idx_cod_school_classifications_category` (`school_category`),
  KEY `idx_cod_school_classifications_operator` (`operator_name`),
  KEY `idx_cod_school_classifications_import` (`source_import_id`),

  CONSTRAINT `fk_cod_school_classifications_school`
    FOREIGN KEY (`school_id`)
    REFERENCES `cod_schools` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,

  CONSTRAINT `fk_cod_school_classifications_import`
    FOREIGN KEY (`source_import_id`)
    REFERENCES `cod_school_imports` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Schuljahrbezogene Klassifikation von Schulen.';