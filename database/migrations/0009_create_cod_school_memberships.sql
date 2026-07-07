CREATE TABLE IF NOT EXISTS `cod_school_identifiers` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel der Schulkennung.',
  `school_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf cod_schools.id.',

  `source_key` VARCHAR(64) NOT NULL COMMENT 'Datenquelle, z. B. berlin_schulverzeichnis.',
  `identifier_type` VARCHAR(64) NOT NULL COMMENT 'Art der Kennung, z. B. bsn oder amtliche_schulnummer.',
  `identifier_value` VARCHAR(191) NOT NULL COMMENT 'Wert der externen Kennung.',

  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage der Kennung.',

  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_cod_school_identifiers_external` (`source_key`, `identifier_type`, `identifier_value`),
  KEY `idx_cod_school_identifiers_school` (`school_id`),
  KEY `idx_cod_school_identifiers_value` (`identifier_value`),

  CONSTRAINT `fk_cod_school_identifiers_school`
    FOREIGN KEY (`school_id`)
    REFERENCES `cod_schools` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Externe Schulkennungen aus Importquellen und Bundesländern.';