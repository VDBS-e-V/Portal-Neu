CREATE TABLE IF NOT EXISTS `cod_seminars` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `seminar_key` VARCHAR(191) NOT NULL,
  `template_id` BIGINT UNSIGNED NULL,
  `school_id` BIGINT UNSIGNED NULL,
  `title` VARCHAR(191) NOT NULL,
  `status` ENUM('draft', 'planned', 'running', 'completed', 'cancelled') NOT NULL DEFAULT 'draft',
  `starts_on` DATE NULL,
  `ends_on` DATE NULL,
  `notes` TEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_cod_seminars_key` (`seminar_key`),
  KEY `idx_cod_seminars_school` (`school_id`),
  KEY `idx_cod_seminars_template` (`template_id`),
  CONSTRAINT `fk_cod_seminars_template` FOREIGN KEY (`template_id`) REFERENCES `cod_seminar_templates` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_cod_seminars_school` FOREIGN KEY (`school_id`) REFERENCES `cod_schools` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
