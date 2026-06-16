CREATE TABLE IF NOT EXISTS `pt_ticket_comments` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel des Kommentars.',
  `ticket_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf pt_tickets.id; zugehöriges Ticket.',
  `author_person_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf ids_persons.id; Autor des Kommentars.',
  `comment_body` MEDIUMTEXT NOT NULL COMMENT 'Kommentartext.',
  `is_internal` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Kennzeichnet interne Kommentare.',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage des Kommentars.',

  PRIMARY KEY (`id`),
  KEY `idx_pt_ticket_comments_ticket` (`ticket_id`),
  KEY `idx_pt_ticket_comments_author` (`author_person_id`),

  CONSTRAINT `fk_pt_ticket_comments_ticket`
    FOREIGN KEY (`ticket_id`) REFERENCES `pt_tickets` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,

  CONSTRAINT `fk_pt_ticket_comments_author`
    FOREIGN KEY (`author_person_id`) REFERENCES `ids_persons` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Kommentare zu Tickets.';
