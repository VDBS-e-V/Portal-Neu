CREATE TABLE IF NOT EXISTS `cod_school_import_rows` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel der Importzeile.',
  `import_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf cod_school_imports.id.',

  `row_number` INT UNSIGNED NOT NULL COMMENT 'Zeilennummer innerhalb der Importdatei.',
  `external_identifier` VARCHAR(191) NULL COMMENT 'Externe Kennung aus der Importzeile, z. B. BSN.',

  `raw_payload_json` JSON NOT NULL COMMENT 'Originaldaten der Zeile als JSON.',
  `normalized_hash` CHAR(64) NULL COMMENT 'SHA-256-Hash der normalisierten Zeilendaten.',

  `validation_status` ENUM(
    'pending',
    'valid',
    'warning',
    'error',
    'imported'
  ) NOT NULL DEFAULT 'pending' COMMENT 'Validierungs- und Importstatus der Zeile.',

  `school_id` BIGINT UNSIGNED NULL COMMENT 'Optionaler Verweis auf die importierte oder aktualisierte Schule.',
  `message_json` JSON NULL COMMENT 'Warnungen, Fehler oder Hinweise zur Zeile.',

  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage der Importzeile.',

  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_cod_school_import_rows_import_row` (`import_id`, `row_number`),
  KEY `idx_cod_school_import_rows_identifier` (`external_identifier`),
  KEY `idx_cod_school_import_rows_status` (`validation_status`),
  KEY `idx_cod_school_import_rows_school` (`school_id`),

  CONSTRAINT `fk_cod_school_import_rows_import`
    FOREIGN KEY (`import_id`)
    REFERENCES `cod_school_imports` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,

  CONSTRAINT `fk_cod_school_import_rows_school`
    FOREIGN KEY (`school_id`)
    REFERENCES `cod_schools` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Einzelzeilen und Prüfprotokoll von Schulimporten.';