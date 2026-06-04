CREATE TABLE IF NOT EXISTS `cod_seminar_sessions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `seminar_id` BIGINT UNSIGNED NOT NULL,
  `session_number` INT UNSIGNED NOT NULL,
  `starts_at` DATETIME NOT NULL,
  `ends_at` DATETIME NULL,
  `location` VARCHAR(191) NULL,
  `notes` TEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_cod_seminar_sessions_unique` (`seminar_id`, `session_number`),
  KEY `idx_cod_seminar_sessions_start` (`starts_at`),
  CONSTRAINT `fk_cod_seminar_sessions_seminar` FOREIGN KEY (`seminar_id`) REFERENCES `cod_seminars` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
