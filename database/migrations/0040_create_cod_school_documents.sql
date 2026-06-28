CREATE TABLE IF NOT EXISTS `cod_school_documents` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel des Schuldokuments.',
  `school_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf cod_schools.id.',
  `contract_id` BIGINT UNSIGNED NULL COMMENT 'Optionaler Verweis auf cod_school_contracts.id.',
  `uploaded_by_person_id` BIGINT UNSIGNED NULL COMMENT 'Person, die das Dokument hochgeladen hat.',

  `title` VARCHAR(191) NOT NULL COMMENT 'Anzeigename des Dokuments.',
  `document_type` VARCHAR(64) NULL COMMENT 'Dokumenttyp, z. B. contract, invoice, note, other.',

  `original_filename` VARCHAR(255) NOT NULL COMMENT 'Ursprünglicher Dateiname.',
  `storage_path` VARCHAR(500) NOT NULL COMMENT 'Interner Speicherpfad.',
  `mime_type` VARCHAR(191) NULL COMMENT 'MIME-Type der Datei.',
  `file_size` BIGINT UNSIGNED NULL COMMENT 'Dateigröße in Bytes.',
  `file_hash` CHAR(64) NULL COMMENT 'Optionaler SHA-256-Hash der Datei.',

  `visibility` ENUM(
    'internal',
    'restricted'
  ) NOT NULL DEFAULT 'internal' COMMENT 'Interne Sichtbarkeit des Dokuments.',

  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt des Uploads.',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung.',

  PRIMARY KEY (`id`),
  KEY `idx_cod_school_documents_school` (`school_id`),
  KEY `idx_cod_school_documents_contract` (`contract_id`),
  KEY `idx_cod_school_documents_uploaded_by` (`uploaded_by_person_id`),
  KEY `idx_cod_school_documents_type` (`document_type`),
  KEY `idx_cod_school_documents_hash` (`file_hash`),

  CONSTRAINT `fk_cod_school_documents_school`
    FOREIGN KEY (`school_id`)
    REFERENCES `cod_schools` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,

  CONSTRAINT `fk_cod_school_documents_contract`
    FOREIGN KEY (`contract_id`)
    REFERENCES `cod_school_contracts` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,

  CONSTRAINT `fk_cod_school_documents_uploaded_by`
    FOREIGN KEY (`uploaded_by_person_id`)
    REFERENCES `ids_persons` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Dokumente zu Schulen, Partnerschulen und Verträgen.';