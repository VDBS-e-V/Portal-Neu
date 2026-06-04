CREATE TABLE IF NOT EXISTS `ids_users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_uuid` BINARY(16) NOT NULL,
  `identity_subject` VARCHAR(191) NULL,
  `email` VARCHAR(191) NOT NULL,
  `display_name` VARCHAR(191) NULL,
  `password_hash` VARCHAR(255) NULL,
  `status` ENUM('active', 'disabled') NOT NULL DEFAULT 'active',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_ids_users_uuid` (`user_uuid`),
  UNIQUE KEY `uq_ids_users_email` (`email`),
  UNIQUE KEY `uq_ids_users_subject` (`identity_subject`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
