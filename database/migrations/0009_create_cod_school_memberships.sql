CREATE TABLE IF NOT EXISTS `cod_school_memberships` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `school_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `role_key` VARCHAR(191) NOT NULL,
  `starts_at` DATE NULL,
  `ends_at` DATE NULL,
  `is_primary` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_cod_school_membership_unique` (`school_id`, `user_id`, `role_key`),
  KEY `idx_cod_school_memberships_user` (`user_id`),
  CONSTRAINT `fk_cod_school_memberships_school` FOREIGN KEY (`school_id`) REFERENCES `cod_schools` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cod_school_memberships_user` FOREIGN KEY (`user_id`) REFERENCES `ids_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
