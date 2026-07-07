CREATE TABLE IF NOT EXISTS `cod_school_notes` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel der Schulnotiz.',
  `school_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf cod_schools.id.',
  `author_person_id` BIGINT UNSIGNED NULL COMMENT 'Person, die die Notiz erstellt hat.',

  `note_type` ENUM(
    'general',
    'contact',
    'seminar',
    'contract',
    'followup'
  ) NOT NULL DEFAULT 'general' COMMENT 'Art der Notiz.',

  `body` TEXT NOT NULL COMMENT 'Inhalt der Notiz.',

  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage der Notiz.',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung der Notiz.',

  PRIMARY KEY (`id`),
  KEY `idx_cod_school_notes_school` (`school_id`),
  KEY `idx_cod_school_notes_author` (`author_person_id`),
  KEY `idx_cod_school_notes_type` (`note_type`),
  KEY `idx_cod_school_notes_created` (`created_at`),

  CONSTRAINT `fk_cod_school_notes_school`
    FOREIGN KEY (`school_id`)
    REFERENCES `cod_schools` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,

  CONSTRAINT `fk_cod_school_notes_author`
    FOREIGN KEY (`author_person_id`)
    REFERENCES `ids_persons` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Interne Notizen zu Schulen und Partnerschulen.';