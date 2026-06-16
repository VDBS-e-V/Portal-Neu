CREATE TABLE IF NOT EXISTS `cod_seminars` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel des Seminars.',
  `seminar_uuid` BINARY(16) NOT NULL COMMENT 'Eindeutige UUID des Seminars als 16-Byte-Binärwert.',
  `template_id` BIGINT UNSIGNED NULL COMMENT 'Optionaler Verweis auf cod_seminar_templates.id.',
  `school_id` BIGINT UNSIGNED NULL COMMENT 'Optionaler Verweis auf cod_schools.id.',
  `title` VARCHAR(191) NOT NULL COMMENT 'Titel des Seminars.',
  `description` TEXT NULL COMMENT 'Optionale Beschreibung des Seminars.',
  `status` ENUM('planned', 'active', 'completed', 'cancelled') NOT NULL DEFAULT 'planned' COMMENT 'Planungs- und Durchführungsstatus des Seminars.',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage des Seminars.',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung des Seminars.',

  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_cod_seminars_uuid` (`seminar_uuid`),
  KEY `idx_cod_seminars_template` (`template_id`),
  KEY `idx_cod_seminars_school` (`school_id`),
  KEY `idx_cod_seminars_status` (`status`),

  CONSTRAINT `fk_cod_seminars_template`
    FOREIGN KEY (`template_id`) REFERENCES `cod_seminar_templates` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,

  CONSTRAINT `fk_cod_seminars_school`
    FOREIGN KEY (`school_id`) REFERENCES `cod_schools` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Konkrete Seminare im Fachbereich COD.';
