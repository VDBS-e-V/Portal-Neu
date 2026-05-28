-- Migration: 0001_create_ids_users.sql
-- Creates the core users table
CREATE TABLE IF NOT EXISTS `ids_users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_uuid` BINARY(16) DEFAULT NULL,
  `username` VARCHAR(100) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `password_hash` VARCHAR(255) DEFAULT NULL,
  `first_name` VARCHAR(191) DEFAULT NULL,
  `last_name` VARCHAR(191) DEFAULT NULL,
  `avatar_path` VARCHAR(255) DEFAULT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `last_login` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_ids_users_email` (`email`),
  UNIQUE KEY `uq_ids_users_username` (`username`),
  KEY `idx_ids_users_user_uuid` (`user_uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
