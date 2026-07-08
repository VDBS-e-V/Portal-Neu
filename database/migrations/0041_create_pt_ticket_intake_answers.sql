CREATE TABLE IF NOT EXISTS `pt_ticket_intake_answers` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel der Antwort.',
  `ticket_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf pt_tickets.id; Ticket, zu dem die Antwort gehört.',
  `question_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf pt_ticket_intake_questions.id; beantwortete Folgefrage.',
  `selected_option_id` BIGINT UNSIGNED NULL COMMENT 'Verweist auf pt_ticket_intake_question_options.id; gesetzt, wenn input_type = select.',
  `answer_text` MEDIUMTEXT NULL COMMENT 'Freitextantwort; gesetzt, wenn input_type = text oder textarea.',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Beantwortung beim Erstellen des Tickets.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pt_ticket_intake_answers_ticket_question` (`ticket_id`, `question_id`),
  KEY `idx_pt_ticket_intake_answers_question` (`question_id`),
  KEY `idx_pt_ticket_intake_answers_selected_option` (`selected_option_id`),
  CONSTRAINT `fk_pt_ticket_intake_answers_ticket`
    FOREIGN KEY (`ticket_id`) REFERENCES `pt_tickets` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_pt_ticket_intake_answers_question`
    FOREIGN KEY (`question_id`) REFERENCES `pt_ticket_intake_questions` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_pt_ticket_intake_answers_selected_option`
    FOREIGN KEY (`selected_option_id`) REFERENCES `pt_ticket_intake_question_options` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Antworten auf Folgefragen, die beim Erstellen eines Tickets gegeben wurden.';
