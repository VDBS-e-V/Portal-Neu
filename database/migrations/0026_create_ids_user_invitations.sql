CREATE TABLE IF NOT EXISTS `ids_user_invitations` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel der Einladung.',
  `user_id` BIGINT UNSIGNED NOT NULL COMMENT 'Login-Konto, für das die Einladung gilt.',
  `token_hash` CHAR(64) NOT NULL COMMENT 'SHA-256 Hash des Einladungstokens. Das Roh-Token wird nicht gespeichert.',
  `email` VARCHAR(191) NOT NULL COMMENT 'E-Mail-Adresse zum Zeitpunkt der Einladung.',
  `status` ENUM('pending', 'accepted', 'revoked', 'expired') NOT NULL DEFAULT 'pending' COMMENT 'Status der Einladung.',
  `expires_at` DATETIME NOT NULL COMMENT 'Ablaufzeitpunkt der Einladung.',
  `accepted_at` DATETIME NULL COMMENT 'Zeitpunkt der Annahme.',
  `revoked_at` DATETIME NULL COMMENT 'Zeitpunkt des Widerrufs.',
  `created_by_user_id` BIGINT UNSIGNED NULL COMMENT 'Login, der die Einladung erstellt hat.',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage.',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung.',

  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_ids_user_invitations_token_hash` (`token_hash`),
  KEY `idx_ids_user_invitations_user` (`user_id`),
  KEY `idx_ids_user_invitations_status` (`status`),
  KEY `idx_ids_user_invitations_expires` (`expires_at`),

  CONSTRAINT `fk_ids_user_invitations_user`
    FOREIGN KEY (`user_id`) REFERENCES `ids_users` (`id`)
    ON DELETE CASCADE,

  CONSTRAINT `fk_ids_user_invitations_created_by`
    FOREIGN KEY (`created_by_user_id`) REFERENCES `ids_users` (`id`)
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Einladungen zur Aktivierung von Login-Konten.';
