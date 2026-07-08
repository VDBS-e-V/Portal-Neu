CREATE TABLE IF NOT EXISTS `pt_ticket_assignments` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel der Zuweisung.',
  `ticket_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf pt_tickets.id; zugewiesenes Ticket.',
  `assignee_person_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf ids_persons.id; zugewiesene:r Bearbeiter:in.',
  `assigned_by_person_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf ids_persons.id; Person, die die Zuweisung vorgenommen hat.',
  `assigned_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Zuweisung.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pt_ticket_assignments_ticket_assignee` (`ticket_id`, `assignee_person_id`),
  KEY `idx_pt_ticket_assignments_assignee` (`assignee_person_id`),
  CONSTRAINT `fk_pt_ticket_assignments_ticket`
    FOREIGN KEY (`ticket_id`) REFERENCES `pt_tickets` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_pt_ticket_assignments_assignee`
    FOREIGN KEY (`assignee_person_id`) REFERENCES `ids_persons` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_pt_ticket_assignments_assigned_by`
    FOREIGN KEY (`assigned_by_person_id`) REFERENCES `ids_persons` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Zuweisung von Tickets an Bearbeiter:innen; n:m, damit Mehrfachzuweisung möglich ist. Erste Zuweisung setzt pt_tickets.status auf assigned (Anwendungslogik).';
