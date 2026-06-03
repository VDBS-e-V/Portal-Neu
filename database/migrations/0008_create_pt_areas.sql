-- Migration: 0008_create_pt_areas.sql
CREATE TABLE IF NOT EXISTS `pt_areas` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(191) NOT NULL,
  `slug` VARCHAR(191) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `parent_id` BIGINT UNSIGNED DEFAULT NULL,
  `is_public` TINYINT(1) NOT NULL DEFAULT 0,
  `settings` JSON DEFAULT NULL,
  `order_index` INT DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pt_areas_slug` (`slug`),
  KEY `idx_pt_areas_parent_id` (`parent_id`),
  CONSTRAINT `fk_pt_areas_parent` FOREIGN KEY (`parent_id`) REFERENCES `pt_areas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
