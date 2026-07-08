CREATE TABLE IF NOT EXISTS `pt_ticket_messages` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel der Nachricht.',
  `ticket_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf pt_tickets.id; Ticket, zu dem die Nachricht gehört.',
  `author_person_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf ids_persons.id; Verfasser der Nachricht (Ersteller oder Sachbearbeiter:in).',
  `author_role` ENUM('requester', 'staff') NOT NULL COMMENT 'Rolle des Verfassers zum Zeitpunkt der Nachricht: Ersteller oder Verwaltung.',
  `body` MEDIUMTEXT NOT NULL COMMENT 'Nachrichtentext. Die erste Nachricht eines Tickets ist inhaltlich die Beschreibung aus dem Erstellen-Formular.',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt des Verfassens der Nachricht.',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Bearbeitung; relevant v. a. für die editierbare erste Nachricht (siehe pt_ticket_message_edits).',
  PRIMARY KEY (`id`),
  KEY `idx_pt_ticket_messages_ticket_created` (`ticket_id`, `created_at`),
  KEY `idx_pt_ticket_messages_author` (`author_person_id`),
  CONSTRAINT `fk_pt_ticket_messages_ticket`
    FOREIGN KEY (`ticket_id`) REFERENCES `pt_tickets` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_pt_ticket_messages_author`
    FOREIGN KEY (`author_person_id`) REFERENCES `ids_persons` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Chat-Nachrichten eines Tickets, sichtbar für Ersteller und Verwaltung gleichermaßen.';
