CREATE TABLE IF NOT EXISTS `pt_ticket_message_edits` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel des Bearbeitungs-Eintrags.',
  `message_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf pt_ticket_messages.id; bearbeitete Nachricht (i. d. R. die erste, die Beschreibung).',
  `previous_body` MEDIUMTEXT NOT NULL COMMENT 'Nachrichtentext vor dieser Bearbeitung.',
  `edited_by_person_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf ids_persons.id; Person, die die Bearbeitung vorgenommen hat.',
  `edited_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Bearbeitung.',
  PRIMARY KEY (`id`),
  KEY `idx_pt_ticket_message_edits_message` (`message_id`, `edited_at`),
  CONSTRAINT `fk_pt_ticket_message_edits_message`
    FOREIGN KEY (`message_id`) REFERENCES `pt_ticket_messages` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_pt_ticket_message_edits_editor`
    FOREIGN KEY (`edited_by_person_id`) REFERENCES `ids_persons` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Historie bearbeiteter Nachrichten (insb. der Beschreibung); jede Zeile ist der Stand vor einer Bearbeitung.';
