-- Migration: 0012_create_pt_menus.sql
-- Creates menus linked to areas
CREATE TABLE IF NOT EXISTS `pt_menus` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `area_id` BIGINT UNSIGNED NOT NULL,
  `name` VARCHAR(191) NOT NULL,
  `slug` VARCHAR(191) NOT NULL,
  `is_default` TINYINT(1) NOT NULL DEFAULT 0,
  `settings` JSON DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pt_menus_slug` (`slug`),
  KEY `idx_pt_menus_area_id` (`area_id`),
  CONSTRAINT `fk_pt_menus_area` FOREIGN KEY (`area_id`) REFERENCES `pt_areas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
