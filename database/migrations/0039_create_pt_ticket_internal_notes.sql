CREATE TABLE IF NOT EXISTS `pt_ticket_internal_notes` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel der internen Notiz.',
  `ticket_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf pt_tickets.id; Ticket, zu dem die Notiz gehört.',
  `author_person_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf ids_persons.id; verfassende:r Sachbearbeiter:in.',
  `body` MEDIUMTEXT NOT NULL COMMENT 'Notiztext; erscheint nie in der Ersteller-Ansicht oder in Mails an den Ersteller.',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt des Verfassens der Notiz.',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung der Notiz.',
  PRIMARY KEY (`id`),
  KEY `idx_pt_ticket_internal_notes_ticket_created` (`ticket_id`, `created_at`),
  KEY `idx_pt_ticket_internal_notes_author` (`author_person_id`),
  CONSTRAINT `fk_pt_ticket_internal_notes_ticket`
    FOREIGN KEY (`ticket_id`) REFERENCES `pt_tickets` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_pt_ticket_internal_notes_author`
    FOREIGN KEY (`author_person_id`) REFERENCES `ids_persons` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Interner Notizkanal je Ticket, ausschließlich für die Verwaltung sichtbar, getrennt vom Chat.';
