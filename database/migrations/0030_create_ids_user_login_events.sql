CREATE TABLE IF NOT EXISTS `ids_user_login_events` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel des Login-Ereignisses.',
  `user_id` BIGINT UNSIGNED NULL COMMENT 'Login-Konto, falls bekannt.',
  `email` VARCHAR(191) NULL COMMENT 'Verwendete E-Mail-Adresse.',
  `event_type` ENUM('login_success', 'login_failed', 'logout', 'password_changed', 'password_reset') NOT NULL COMMENT 'Art des Ereignisses.',
  `ip_address` VARCHAR(45) NULL COMMENT 'IP-Adresse des Requests.',
  `user_agent` TEXT NULL COMMENT 'User-Agent des Clients.',
  `request_uri` VARCHAR(500) NULL COMMENT 'Request-URI.',
  `metadata` JSON NULL COMMENT 'Optionale Metadaten.',
  `occurred_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt des Ereignisses.',

  PRIMARY KEY (`id`),
  KEY `idx_ids_user_login_events_user` (`user_id`),
  KEY `idx_ids_user_login_events_email` (`email`),
  KEY `idx_ids_user_login_events_event_type` (`event_type`),
  KEY `idx_ids_user_login_events_occurred_at` (`occurred_at`),

  CONSTRAINT `fk_ids_user_login_events_user`
    FOREIGN KEY (`user_id`) REFERENCES `ids_users` (`id`)
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Login-, Logout- und sicherheitsrelevante Account-Ereignisse.';
