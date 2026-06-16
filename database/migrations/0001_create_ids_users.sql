CREATE TABLE IF NOT EXISTS `ids_persons` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel der Person.',
  `person_uuid` BINARY(16) NOT NULL COMMENT 'Eindeutige UUID der Person als 16-Byte-Binärwert.',
  `display_name` VARCHAR(191) NULL COMMENT 'Optionaler Anzeigename der Person.',
  `status` ENUM('active', 'disabled', 'erasure_requested', 'erased') NOT NULL DEFAULT 'active' COMMENT 'Personenstatus: aktiv, deaktiviert, Löschung beantragt oder gelöscht/anonymisiert.',
  `disabled_at` DATETIME NULL COMMENT 'Zeitpunkt der Deaktivierung; NULL wenn nicht deaktiviert.',
  `erasure_requested_at` DATETIME NULL COMMENT 'Zeitpunkt eines DSGVO-Löschantrags; NULL wenn nicht beantragt.',
  `erased_at` DATETIME NULL COMMENT 'Zeitpunkt der Löschung oder Anonymisierung; NULL wenn nicht durchgeführt.',
  `erasure_reason` TEXT NULL COMMENT 'Optionale Begründung oder Referenz zum Löschvorgang.',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage der Person.',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung der Person.',

  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_ids_persons_uuid` (`person_uuid`),
  KEY `idx_ids_persons_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Fachliche Personenstammdaten; eine Person kann ohne Login existieren.';

CREATE TABLE IF NOT EXISTS `ids_users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel des Login-Kontos.',
  `person_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf ids_persons.id; fachliche Person dieses Login-Kontos.',
  `user_uuid` BINARY(16) NOT NULL COMMENT 'Eindeutige UUID des Login-Kontos als 16-Byte-Binärwert.',
  `identity_subject` VARCHAR(191) NULL COMMENT 'Optionaler externer Identitäts-Subject; eindeutig, wenn gesetzt.',
  `email` VARCHAR(191) NOT NULL COMMENT 'E-Mail-Adresse für Login; Pflichtfeld für Login-Konten.',
  `password_hash` VARCHAR(255) NULL COMMENT 'Optionaler lokaler Passwort-Hash.',
  `status` ENUM('invited', 'active', 'disabled') NOT NULL DEFAULT 'active' COMMENT 'Login-Status: eingeladen, aktiv oder deaktiviert.',
  `email_verified_at` DATETIME NULL COMMENT 'Zeitpunkt der E-Mail-Verifizierung.',
  `last_login_at` DATETIME NULL COMMENT 'Zeitpunkt des letzten erfolgreichen Logins.',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage des Login-Kontos.',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung des Login-Kontos.',

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Optionale Login-Konten zu Personen; ein Login gehört genau zu einer Person.';
