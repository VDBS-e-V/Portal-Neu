CREATE TABLE IF NOT EXISTS `pt_ticket_subject_edits` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel des Bearbeitungs-Eintrags.',
  `ticket_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf pt_tickets.id; Ticket, dessen Betreff geändert wurde.',
  `previous_subject` VARCHAR(255) NOT NULL COMMENT 'Betreff vor dieser Bearbeitung.',
  `edited_by_person_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf ids_persons.id; Person, die die Bearbeitung vorgenommen hat.',
  `edited_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Bearbeitung.',
  PRIMARY KEY (`id`),
  KEY `idx_pt_ticket_subject_edits_ticket` (`ticket_id`, `edited_at`),
  CONSTRAINT `fk_pt_ticket_subject_edits_ticket`
    FOREIGN KEY (`ticket_id`) REFERENCES `pt_tickets` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_pt_ticket_subject_edits_editor`
    FOREIGN KEY (`edited_by_person_id`) REFERENCES `ids_persons` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Historie geänderter Betreffzeilen; jede Zeile ist der Stand vor einer Bearbeitung. Default-Entscheidung analog pt_ticket_message_edits (eigene Tabelle statt pt_audit_log), da dessen Schema nicht vorlag; ggf. später konsolidieren.';
