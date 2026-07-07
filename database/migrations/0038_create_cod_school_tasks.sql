CREATE TABLE IF NOT EXISTS `cod_school_tasks` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel der Schulaufgabe.',
  `school_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf cod_schools.id.',

  `assigned_to_person_id` BIGINT UNSIGNED NULL COMMENT 'Zugewiesene Person.',
  `created_by_person_id` BIGINT UNSIGNED NULL COMMENT 'Person, die die Aufgabe erstellt hat.',

  `title` VARCHAR(191) NOT NULL COMMENT 'Titel der Aufgabe.',
  `description` TEXT NULL COMMENT 'Beschreibung der Aufgabe.',

  `due_at` DATETIME NULL COMMENT 'Fälligkeit oder Wiedervorlage.',
  `completed_at` DATETIME NULL COMMENT 'Zeitpunkt der Erledigung.',

  `status` ENUM(
    'open',
    'in_progress',
    'done',
    'cancelled'
  ) NOT NULL DEFAULT 'open' COMMENT 'Status der Aufgabe.',

  `related_entity_type` VARCHAR(64) NULL COMMENT 'Optionaler Bezugstyp, z. B. seminar oder contract.',
  `related_entity_id` BIGINT UNSIGNED NULL COMMENT 'Optionale ID des Bezugsobjekts.',

  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage der Aufgabe.',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung der Aufgabe.',

  PRIMARY KEY (`id`),
  KEY `idx_cod_school_tasks_school` (`school_id`),
  KEY `idx_cod_school_tasks_assigned_to` (`assigned_to_person_id`),
  KEY `idx_cod_school_tasks_created_by` (`created_by_person_id`),
  KEY `idx_cod_school_tasks_due` (`due_at`),
  KEY `idx_cod_school_tasks_status` (`status`),
  KEY `idx_cod_school_tasks_related` (`related_entity_type`, `related_entity_id`),

  CONSTRAINT `fk_cod_school_tasks_school`
    FOREIGN KEY (`school_id`)
    REFERENCES `cod_schools` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,

  CONSTRAINT `fk_cod_school_tasks_assigned_to`
    FOREIGN KEY (`assigned_to_person_id`)
    REFERENCES `ids_persons` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,

  CONSTRAINT `fk_cod_school_tasks_created_by`
    FOREIGN KEY (`created_by_person_id`)
    REFERENCES `ids_persons` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Aufgaben und Wiedervorlagen zu Schulen und Partnerschulen.';