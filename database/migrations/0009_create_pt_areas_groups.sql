-- Migration: 0009_create_pt_areas_groups.sql
CREATE TABLE IF NOT EXISTS `pt_areas_groups` (
  `area_id` BIGINT UNSIGNED NOT NULL,
  `group_id` BIGINT UNSIGNED NOT NULL,
  `permissions` JSON DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`area_id`,`group_id`),
  KEY `idx_pt_areas_groups_group_id` (`group_id`),
  CONSTRAINT `fk_pt_areas_groups_area` FOREIGN KEY (`area_id`) REFERENCES `pt_areas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_pt_areas_groups_group` FOREIGN KEY (`group_id`) REFERENCES `ids_groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
