CREATE TABLE IF NOT EXISTS `pt_notification_outbox` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel des Benachrichtigungs-Eintrags.',
  `ticket_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf pt_tickets.id; Ticket, auf das sich die Benachrichtigung bezieht.',
  `person_id` BIGINT UNSIGNED NULL COMMENT 'Verweist auf ids_persons.id, falls Empfänger ein Konto hat (z. B. Sachbearbeiter:in). NULL bei anonymen Empfängern ohne Konto.',
  `recipient_email` VARCHAR(191) NULL COMMENT 'Empfänger-E-Mail, falls kein person_id vorliegt (z. B. anonymer Ersteller). Anwendungsseitig gilt: genau eines von person_id/recipient_email ist gesetzt.',
  `event_type` ENUM('ticket_created', 'new_message', 'status_changed', 'priority_changed', 'subject_changed', 'assignment_changed', 'internal_note_added') NOT NULL COMMENT 'Auslösender Ereignistyp, siehe Abschnitt 6 des Issues (Sofort-Benachrichtigungen an allen Auslösepunkten).',
  `sent_at` DATETIME NULL COMMENT 'Zeitpunkt des Versands; NULL = noch ausstehend (Sofortversand oder Teil des nächsten Tages-Digests).',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt des auslösenden Ereignisses.',
  PRIMARY KEY (`id`),
  KEY `idx_pt_notification_outbox_ticket` (`ticket_id`),
  KEY `idx_pt_notification_outbox_person` (`person_id`),
  KEY `idx_pt_notification_outbox_sent` (`sent_at`),
  CONSTRAINT `fk_pt_notification_outbox_ticket`
    FOREIGN KEY (`ticket_id`) REFERENCES `pt_tickets` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_pt_notification_outbox_person`
    FOREIGN KEY (`person_id`) REFERENCES `ids_persons` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Ausgangskorb für Benachrichtigungen; wird vom Sofortversand direkt geleert und vom Tages-Digest-Cronjob gesammelt verarbeitet.';
