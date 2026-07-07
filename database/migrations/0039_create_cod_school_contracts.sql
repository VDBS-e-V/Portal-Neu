CREATE TABLE IF NOT EXISTS `cod_school_contracts` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel des Schulvertrags.',
  `school_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf cod_schools.id.',

  `title` VARCHAR(191) NOT NULL COMMENT 'Titel oder Bezeichnung des Vertrags.',
  `contract_type` VARCHAR(191) NULL COMMENT 'Vertragsart.',

  `status` ENUM(
    'draft',
    'sent',
    'signed',
    'active',
    'expired',
    'cancelled'
  ) NOT NULL DEFAULT 'draft' COMMENT 'Status des Vertrags.',

  `valid_from` DATE NULL COMMENT 'Beginn der Vertragsgültigkeit.',
  `valid_until` DATE NULL COMMENT 'Ende der Vertragsgültigkeit.',
  `signed_at` DATE NULL COMMENT 'Datum der Unterzeichnung.',

  `notes` TEXT NULL COMMENT 'Interne Notizen zum Vertrag.',

  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage des Vertrags.',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung des Vertrags.',

  PRIMARY KEY (`id`),
  KEY `idx_cod_school_contracts_school` (`school_id`),
  KEY `idx_cod_school_contracts_status` (`status`),
  KEY `idx_cod_school_contracts_valid_until` (`valid_until`),

  CONSTRAINT `fk_cod_school_contracts_school`
    FOREIGN KEY (`school_id`)
    REFERENCES `cod_schools` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Verträge mit Schulen und Partnerschulen.';