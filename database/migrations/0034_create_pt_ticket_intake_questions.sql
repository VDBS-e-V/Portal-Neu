CREATE TABLE IF NOT EXISTS `pt_ticket_intake_questions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel der Folgefrage.',
  `category_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf pt_ticket_categories.id; Kategorie, der die Folgefrage zugeordnet ist.',
  `question_text` VARCHAR(255) NOT NULL COMMENT 'Fragetext, der im Ticket-Formular angezeigt wird.',
  `input_type` ENUM('select', 'text', 'textarea') NOT NULL DEFAULT 'text' COMMENT 'Eingabetyp der Frage: Auswahl, einzeiliger Text oder mehrzeiliger Text.',
  `is_required` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Ob die Frage im Formular verpflichtend beantwortet werden muss.',
  `sort_order` SMALLINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Reihenfolge der Anzeige innerhalb der Kategorie.',
  `depends_on_option_id` BIGINT UNSIGNED NULL COMMENT 'Für spätere bedingte Verzweigung (Phase 2 lt. Issue): Frage erscheint nur, wenn diese Option einer vorherigen Frage gewählt wurde. FK folgt in Migration 0036, da pt_ticket_intake_question_options erst danach angelegt wird.',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage der Folgefrage.',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung der Folgefrage.',
  PRIMARY KEY (`id`),
  KEY `idx_pt_ticket_intake_questions_category` (`category_id`),
  KEY `idx_pt_ticket_intake_questions_depends_on_option` (`depends_on_option_id`),
  CONSTRAINT `fk_pt_ticket_intake_questions_category`
    FOREIGN KEY (`category_id`) REFERENCES `pt_ticket_categories` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Folgefragen, die je nach gewählter Kategorie im Ticket-Formular gestellt werden.';
