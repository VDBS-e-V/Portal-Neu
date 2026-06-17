CREATE TABLE IF NOT EXISTS `ids_person_erasure_requests` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel des DSGVO-Löschvorgangs.',
  `person_id` BIGINT UNSIGNED NOT NULL COMMENT 'Betroffene Person.',
  `status` ENUM('requested', 'approved', 'rejected', 'completed', 'cancelled') NOT NULL DEFAULT 'requested' COMMENT 'Bearbeitungsstatus.',
  `requested_by_user_id` BIGINT UNSIGNED NULL COMMENT 'Login, der den Vorgang erfasst hat.',
  `approved_by_user_id` BIGINT UNSIGNED NULL COMMENT 'Login, der den Vorgang freigegeben hat.',
  `completed_by_user_id` BIGINT UNSIGNED NULL COMMENT 'Login, der die Anonymisierung ausgeführt hat.',
  `reason` TEXT NULL COMMENT 'Grund oder Beschreibung des Löschersuchens.',
  `review_note` TEXT NULL COMMENT 'Interne Prüfnotiz.',
  `requested_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Beantragung.',
  `approved_at` DATETIME NULL COMMENT 'Zeitpunkt der Freigabe.',
  `completed_at` DATETIME NULL COMMENT 'Zeitpunkt der Umsetzung.',
  `rejected_at` DATETIME NULL COMMENT 'Zeitpunkt der Ablehnung.',
  `cancelled_at` DATETIME NULL COMMENT 'Zeitpunkt der Stornierung.',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage.',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung.',

  PRIMARY KEY (`id`),
  KEY `idx_ids_person_erasure_requests_person` (`person_id`),
  KEY `idx_ids_person_erasure_requests_status` (`status`),
  KEY `idx_ids_person_erasure_requests_requested` (`requested_at`),

  CONSTRAINT `fk_ids_person_erasure_requests_person`
    FOREIGN KEY (`person_id`) REFERENCES `ids_persons` (`id`)
    ON DELETE CASCADE,

  CONSTRAINT `fk_ids_person_erasure_requests_requested_by`
    FOREIGN KEY (`requested_by_user_id`) REFERENCES `ids_users` (`id`)
    ON DELETE SET NULL,

  CONSTRAINT `fk_ids_person_erasure_requests_approved_by`
    FOREIGN KEY (`approved_by_user_id`) REFERENCES `ids_users` (`id`)
    ON DELETE SET NULL,

  CONSTRAINT `fk_ids_person_erasure_requests_completed_by`
    FOREIGN KEY (`completed_by_user_id`) REFERENCES `ids_users` (`id`)
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='DSGVO-Lösch- und Anonymisierungsvorgänge für Personen.';
