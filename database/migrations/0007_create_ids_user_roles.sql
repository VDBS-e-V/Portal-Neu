-- Migration: 0007_create_ids_user_roles.sql
CREATE TABLE IF NOT EXISTS `ids_user_roles` (
  `user_id` BIGINT UNSIGNED NOT NULL,
  `role_id` BIGINT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`,`role_id`),
  CONSTRAINT `fk_ids_user_roles_user` FOREIGN KEY (`user_id`) REFERENCES `ids_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_ids_user_roles_role` FOREIGN KEY (`role_id`) REFERENCES `ids_roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
