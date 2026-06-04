CREATE TABLE IF NOT EXISTS `pt_areas` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `area_key` VARCHAR(191) NOT NULL,
  `name` VARCHAR(191) NOT NULL,
  `description` TEXT NULL,
  `start_path` VARCHAR(255) NOT NULL DEFAULT '/',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `is_external` TINYINT(1) NOT NULL DEFAULT 0,
  `sort_order` INT NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pt_areas_key` (`area_key`),
  KEY `idx_pt_areas_active_order` (`is_active`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
