CREATE TABLE IF NOT EXISTS `ids_user_permission_groups` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `permission_group_id` BIGINT UNSIGNED NOT NULL,
  `assigned_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `assigned_by_user_id` BIGINT UNSIGNED NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_ids_user_permission_groups_user_group` (`user_id`, `permission_group_id`),
  KEY `idx_ids_upg_group` (`permission_group_id`),
  KEY `idx_ids_upg_assigned_by` (`assigned_by_user_id`),
  CONSTRAINT `fk_ids_upg_user` FOREIGN KEY (`user_id`) REFERENCES `ids_users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ids_upg_group` FOREIGN KEY (`permission_group_id`) REFERENCES `ids_permission_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ids_upg_assigned_by` FOREIGN KEY (`assigned_by_user_id`) REFERENCES `ids_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
