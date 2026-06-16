CREATE TABLE IF NOT EXISTS `pt_tickets` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel des Tickets.',
  `ticket_number` VARCHAR(32) NOT NULL COMMENT 'Eindeutige Ticketnummer.',
  `ticket_type_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf pt_ticket_types.id; Ticketart.',
  `area_id` BIGINT UNSIGNED NULL COMMENT 'Optionaler Portalbereich des Tickets.',
  `school_id` BIGINT UNSIGNED NULL COMMENT 'Optionale Schule des Tickets.',
  `seminar_id` BIGINT UNSIGNED NULL COMMENT 'Optionales Seminar des Tickets.',
  `created_by_person_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf ids_persons.id; erstellende Person.',
  `assigned_to_person_id` BIGINT UNSIGNED NULL COMMENT 'Verweist auf ids_persons.id; zugewiesene Person.',
  `subject` VARCHAR(191) NOT NULL COMMENT 'Betreff des Tickets.',
  `description` MEDIUMTEXT NULL COMMENT 'Beschreibung des Tickets.',
  `status` ENUM('open', 'in_progress', 'waiting', 'resolved', 'closed') NOT NULL DEFAULT 'open' COMMENT 'Bearbeitungsstatus des Tickets.',
  `priority` ENUM('low', 'normal', 'high', 'urgent') NOT NULL DEFAULT 'normal' COMMENT 'Priorität des Tickets.',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage des Tickets.',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung des Tickets.',

  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pt_tickets_number` (`ticket_number`),
  KEY `idx_pt_tickets_type` (`ticket_type_id`),
  KEY `idx_pt_tickets_area` (`area_id`),
  KEY `idx_pt_tickets_school` (`school_id`),
  KEY `idx_pt_tickets_seminar` (`seminar_id`),
  KEY `idx_pt_tickets_creator` (`created_by_person_id`),
  KEY `idx_pt_tickets_assignee` (`assigned_to_person_id`),

  CONSTRAINT `fk_pt_tickets_type`
    FOREIGN KEY (`ticket_type_id`) REFERENCES `pt_ticket_types` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,

  CONSTRAINT `fk_pt_tickets_area`
    FOREIGN KEY (`area_id`) REFERENCES `pt_areas` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,

  CONSTRAINT `fk_pt_tickets_school`
    FOREIGN KEY (`school_id`) REFERENCES `cod_schools` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,

  CONSTRAINT `fk_pt_tickets_seminar`
    FOREIGN KEY (`seminar_id`) REFERENCES `cod_seminars` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,

  CONSTRAINT `fk_pt_tickets_created_by`
    FOREIGN KEY (`created_by_person_id`) REFERENCES `ids_persons` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,

  CONSTRAINT `fk_pt_tickets_assigned_to`
    FOREIGN KEY (`assigned_to_person_id`) REFERENCES `ids_persons` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Tickets im Portal.';
