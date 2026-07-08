CREATE TABLE IF NOT EXISTS `pt_ticket_intake_question_options` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel der Auswahloption.',
  `question_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf pt_ticket_intake_questions.id; Frage, zu der die Option gehört.',
  `label` VARCHAR(191) NOT NULL COMMENT 'Anzeigetext der Auswahloption im Ticket-Formular.',
  `sort_order` SMALLINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Reihenfolge der Anzeige innerhalb der Frage.',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage der Option.',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung der Option.',
  PRIMARY KEY (`id`),
  KEY `idx_pt_ticket_intake_question_options_question` (`question_id`),
  CONSTRAINT `fk_pt_ticket_intake_question_options_question`
    FOREIGN KEY (`question_id`) REFERENCES `pt_ticket_intake_questions` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Auswahloptionen für Folgefragen vom Eingabetyp "select".';
