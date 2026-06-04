CREATE TABLE IF NOT EXISTS `cod_school_sites` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `school_id` BIGINT UNSIGNED NOT NULL,
  `site_key` VARCHAR(191) NOT NULL,
  `name` VARCHAR(191) NOT NULL,
  `street` VARCHAR(191) NULL,
  `house_number` VARCHAR(32) NULL,
  `postal_code` VARCHAR(16) NULL,
  `city` VARCHAR(191) NULL,
  `country` VARCHAR(2) NOT NULL DEFAULT 'DE',
  `is_primary` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_cod_school_sites_school_key` (`school_id`, `site_key`),
  KEY `idx_cod_school_sites_school` (`school_id`),
  CONSTRAINT `fk_cod_school_sites_school` FOREIGN KEY (`school_id`) REFERENCES `cod_schools` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
