CREATE TABLE IF NOT EXISTS `cod_seminar_sessions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel des Seminartermins.',
  `seminar_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf cod_seminars.id.',
  `school_site_id` BIGINT UNSIGNED NULL COMMENT 'Optionaler Verweis auf den konkreten Schulstandort.',

  `session_number` INT UNSIGNED NOT NULL COMMENT 'Laufende Nummer des Termins innerhalb eines Seminars.',
  `starts_at` DATETIME NOT NULL COMMENT 'Startzeitpunkt des Termins.',
  `ends_at` DATETIME NULL COMMENT 'Endzeitpunkt des Termins.',

  `location` VARCHAR(191) NULL COMMENT 'Freitext-Ort, Raum oder Online-Link.',
  `notes` TEXT NULL COMMENT 'Interne Notizen zum Termin.',

  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage des Termins.',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung des Termins.',

  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_cod_seminar_sessions_unique` (`seminar_id`, `session_number`),
  KEY `idx_cod_seminar_sessions_start` (`starts_at`),
  KEY `idx_cod_seminar_sessions_site` (`school_site_id`),

  CONSTRAINT `fk_cod_seminar_sessions_seminar`
    FOREIGN KEY (`seminar_id`)
    REFERENCES `cod_seminars` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,

  CONSTRAINT `fk_cod_seminar_sessions_site`
    FOREIGN KEY (`school_site_id`)
    REFERENCES `cod_school_sites` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Einzeltermine konkreter Seminare.';