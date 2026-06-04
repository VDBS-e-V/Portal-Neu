CREATE TABLE IF NOT EXISTS `cod_schools` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `school_key` VARCHAR(191) NOT NULL,
  `name` VARCHAR(191) NOT NULL,
  `school_code` VARCHAR(64) NULL,
  `street` VARCHAR(191) NULL,
  `house_number` VARCHAR(32) NULL,
  `postal_code` VARCHAR(16) NULL,
  `city` VARCHAR(191) NULL,
  `country` VARCHAR(2) NOT NULL DEFAULT 'DE',
  `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_cod_schools_key` (`school_key`),
  UNIQUE KEY `uq_cod_schools_code` (`school_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
