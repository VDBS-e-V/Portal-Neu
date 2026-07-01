CREATE TABLE IF NOT EXISTS `cod_school_overrides` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel der manuellen Schulkorrektur.',
  `school_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf cod_schools.id.',

  `field_key` VARCHAR(64) NOT NULL COMMENT 'Korrigiertes Feld, z. B. display_name, primary_email oder data_quality_note.',
  `override_value` TEXT NULL COMMENT 'Manuell gesetzter effektiver Wert.',
  `reason` TEXT NULL COMMENT 'Begründung oder interne Notiz zur Korrektur.',

  `created_by_person_id` BIGINT UNSIGNED NULL COMMENT 'Person, die die Korrektur angelegt hat.',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage.',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung.',

  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_cod_school_overrides_school_field` (`school_id`, `field_key`),
  KEY `idx_cod_school_overrides_school` (`school_id`),
  KEY `idx_cod_school_overrides_field` (`field_key`),
  KEY `idx_cod_school_overrides_created_by` (`created_by_person_id`),

  CONSTRAINT `fk_cod_school_overrides_school`
    FOREIGN KEY (`school_id`)
    REFERENCES `cod_schools` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,

  CONSTRAINT `fk_cod_school_overrides_created_by`
    FOREIGN KEY (`created_by_person_id`)
    REFERENCES `ids_persons` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Manuelle Overrides für das importgeführte Schulverzeichnis.';
