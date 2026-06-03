-- Migration: 0006_create_ids_user_groups.sql
CREATE TABLE IF NOT EXISTS `ids_user_groups` (
  `user_id` BIGINT UNSIGNED NOT NULL,
  `group_id` BIGINT UNSIGNED NOT NULL,
  `role` VARCHAR(100) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`,`group_id`),
  KEY `idx_ids_user_groups_group_id` (`group_id`),
  CONSTRAINT `fk_ids_user_groups_user` FOREIGN KEY (`user_id`) REFERENCES `ids_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_ids_user_groups_group` FOREIGN KEY (`group_id`) REFERENCES `ids_groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
