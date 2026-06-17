CREATE TABLE IF NOT EXISTS `ids_user_password_resets` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel des Passwort-Reset-Tokens.',
  `user_id` BIGINT UNSIGNED NOT NULL COMMENT 'Login-Konto, für das der Reset gilt.',
  `token_hash` CHAR(64) NOT NULL COMMENT 'SHA-256 Hash des Reset-Tokens. Das Roh-Token wird nicht gespeichert.',
  `email` VARCHAR(191) NOT NULL COMMENT 'E-Mail-Adresse zum Zeitpunkt der Anforderung.',
  `status` ENUM('pending', 'used', 'revoked', 'expired') NOT NULL DEFAULT 'pending' COMMENT 'Status des Reset-Tokens.',
  `expires_at` DATETIME NOT NULL COMMENT 'Ablaufzeitpunkt des Tokens.',
  `used_at` DATETIME NULL COMMENT 'Zeitpunkt der Nutzung.',
  `revoked_at` DATETIME NULL COMMENT 'Zeitpunkt des Widerrufs.',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anforderung.',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung.',

  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_ids_user_password_resets_token_hash` (`token_hash`),
  KEY `idx_ids_user_password_resets_user` (`user_id`),
  KEY `idx_ids_user_password_resets_status` (`status`),
  KEY `idx_ids_user_password_resets_expires` (`expires_at`),

  CONSTRAINT `fk_ids_user_password_resets_user`
    FOREIGN KEY (`user_id`) REFERENCES `ids_users` (`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Tokens für Passwort-vergessen- und Passwort-zurücksetzen-Flows.';
