CREATE TABLE IF NOT EXISTS `pt_permission_group_area_access` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `permission_group_id` BIGINT UNSIGNED NOT NULL,
  `area_id` BIGINT UNSIGNED NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pt_pg_area_access` (`permission_group_id`, `area_id`),
  KEY `idx_pt_pg_area_access_area` (`area_id`),
  CONSTRAINT `fk_pt_pg_access_group` FOREIGN KEY (`permission_group_id`) REFERENCES `ids_permission_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pt_pg_access_area` FOREIGN KEY (`area_id`) REFERENCES `pt_areas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
