CREATE TABLE IF NOT EXISTS `pt_notification_preferences` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel der Präferenz.',
  `person_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf ids_persons.id; eine Präferenz-Zeile je Person.',
  `frequency` ENUM('instant', 'daily', 'none') NOT NULL DEFAULT 'instant' COMMENT 'Sofort, gesammelt als Tages-Digest oder keine Benachrichtigungen. Nur relevant für Personen mit Konto; anonyme Ersteller erhalten immer sofortige Mails an ihre contact_email.',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage der Präferenz.',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung, z. B. über /konto/einstellungen.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pt_notification_preferences_person` (`person_id`),
  CONSTRAINT `fk_pt_notification_preferences_person`
    FOREIGN KEY (`person_id`) REFERENCES `ids_persons` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Benachrichtigungspräferenz je Person, gepflegt unter /konto/einstellungen.';
