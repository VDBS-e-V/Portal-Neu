-- Migration: 0005_create_ids_role_permissions.sql
CREATE TABLE IF NOT EXISTS `ids_role_permissions` (
  `role_id` BIGINT UNSIGNED NOT NULL,
  `permission_id` BIGINT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`role_id`,`permission_id`),
  CONSTRAINT `fk_ids_role_permissions_role` FOREIGN KEY (`role_id`) REFERENCES `ids_roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_ids_role_permissions_permission` FOREIGN KEY (`permission_id`) REFERENCES `ids_permissions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
