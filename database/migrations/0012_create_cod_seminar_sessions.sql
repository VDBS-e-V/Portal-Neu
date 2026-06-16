CREATE TABLE IF NOT EXISTS `cod_seminar_sessions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel des Seminartermins.',
  `seminar_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf cod_seminars.id; zugehöriges Seminar.',
  `title` VARCHAR(191) NULL COMMENT 'Optionaler Titel des einzelnen Termins.',
  `starts_at` DATETIME NOT NULL COMMENT 'Startzeitpunkt des Termins.',
  `ends_at` DATETIME NULL COMMENT 'Endzeitpunkt des Termins.',
  `location` VARCHAR(191) NULL COMMENT 'Optionaler Ort des Termins.',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage des Termins.',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung des Termins.',

  PRIMARY KEY (`id`),
  KEY `idx_cod_seminar_sessions_seminar` (`seminar_id`),
  KEY `idx_cod_seminar_sessions_starts_at` (`starts_at`),

  CONSTRAINT `fk_cod_seminar_sessions_seminar`
    FOREIGN KEY (`seminar_id`) REFERENCES `cod_seminars` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Einzelne Termine eines Seminars.';
