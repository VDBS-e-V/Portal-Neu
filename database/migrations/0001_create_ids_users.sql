CREATE TABLE IF NOT EXISTS `ids_persons` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `person_uuid` BINARY(16) NOT NULL,
  `display_name` VARCHAR(191) NULL,
  `status` ENUM('active', 'disabled', 'erasure_requested', 'erased') NOT NULL DEFAULT 'active',
  `disabled_at` DATETIME NULL,
  `erasure_requested_at` DATETIME NULL,
  `erased_at` DATETIME NULL,
  `erasure_reason` TEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_ids_persons_uuid` (`person_uuid`),
  KEY `idx_ids_persons_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `ids_users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `person_id` BIGINT UNSIGNED NOT NULL,
  `user_uuid` BINARY(16) NOT NULL,
  `identity_subject` VARCHAR(191) NULL,
  `email` VARCHAR(191) NOT NULL,
  `password_hash` VARCHAR(255) NULL,
  `status` ENUM('invited', 'active', 'disabled') NOT NULL DEFAULT 'active',
  `email_verified_at` DATETIME NULL,
  `last_login_at` DATETIME NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_ids_users_person_id` (`person_id`),
  UNIQUE KEY `uq_ids_users_uuid` (`user_uuid`),
  UNIQUE KEY `uq_ids_users_email` (`email`),
  UNIQUE KEY `uq_ids_users_subject` (`identity_subject`),
  KEY `idx_ids_users_status` (`status`),

  CONSTRAINT `fk_ids_users_person`
    FOREIGN KEY (`person_id`) REFERENCES `ids_persons` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;