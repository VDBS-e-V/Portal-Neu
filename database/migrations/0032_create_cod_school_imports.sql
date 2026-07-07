CREATE TABLE IF NOT EXISTS `cod_school_imports` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel des Schulimports.',

  `source_key` VARCHAR(64) NOT NULL COMMENT 'Datenquelle, z. B. berlin_schulverzeichnis.',
  `federal_state_code` VARCHAR(16) NOT NULL COMMENT 'Bundesland-Code des Imports, z. B. DE-BE.',
  `school_year` VARCHAR(16) NULL COMMENT 'Schuljahr des Imports, z. B. 2025/26.',

  `original_filename` VARCHAR(255) NOT NULL COMMENT 'Ursprünglicher Dateiname.',
  `file_hash` CHAR(64) NOT NULL COMMENT 'SHA-256-Hash der importierten Datei.',

  `status` ENUM(
    'uploaded',
    'validated',
    'imported',
    'failed'
  ) NOT NULL DEFAULT 'uploaded' COMMENT 'Status des Importvorgangs.',

  `row_count` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Anzahl der Zeilen in der Importdatei.',
  `imported_count` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Anzahl erfolgreich importierter Zeilen.',
  `warning_count` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Anzahl der Warnungen.',
  `error_count` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Anzahl der Fehler.',

  `metadata_json` JSON NULL COMMENT 'Optionale technische Metadaten zum Import.',
  `created_by_person_id` BIGINT UNSIGNED NULL COMMENT 'Person, die den Import angelegt hat.',

  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage des Imports.',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung des Imports.',

  PRIMARY KEY (`id`),
  KEY `idx_cod_school_imports_source` (`source_key`, `federal_state_code`, `school_year`),
  KEY `idx_cod_school_imports_hash` (`file_hash`),
  KEY `idx_cod_school_imports_status` (`status`),
  KEY `idx_cod_school_imports_created_by` (`created_by_person_id`),

  CONSTRAINT `fk_cod_school_imports_created_by`
    FOREIGN KEY (`created_by_person_id`)
    REFERENCES `ids_persons` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Importläufe für Schulverzeichnisdaten.';