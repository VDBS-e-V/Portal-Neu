CREATE TABLE IF NOT EXISTS `cod_seminar_participants` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `seminar_id` BIGINT UNSIGNED NOT NULL,
  `participant_name` VARCHAR(191) NOT NULL,
  `participant_email` VARCHAR(191) NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_cod_seminar_participants_seminar` (`seminar_id`),
  CONSTRAINT `fk_cod_seminar_participants_seminar` FOREIGN KEY (`seminar_id`) REFERENCES `cod_seminars` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
