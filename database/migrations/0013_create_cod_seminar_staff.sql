CREATE TABLE IF NOT EXISTS `cod_seminar_staff` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `seminar_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `role_key` VARCHAR(191) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_cod_seminar_staff_unique` (`seminar_id`, `user_id`, `role_key`),
  KEY `idx_cod_seminar_staff_user` (`user_id`),
  CONSTRAINT `fk_cod_seminar_staff_seminar` FOREIGN KEY (`seminar_id`) REFERENCES `cod_seminars` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cod_seminar_staff_user` FOREIGN KEY (`user_id`) REFERENCES `ids_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
