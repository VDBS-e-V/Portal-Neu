CREATE TABLE IF NOT EXISTS `cod_seminar_participants` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel des Teilnehmenden.',
  `seminar_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf cod_seminars.id; zugehöriges Seminar.',
  `person_id` BIGINT UNSIGNED NULL COMMENT 'Optionaler Verweis auf ids_persons.id.',
  `participant_name` VARCHAR(191) NOT NULL COMMENT 'Name des Teilnehmenden als Snapshot oder Freitext.',
  `participant_email` VARCHAR(191) NULL COMMENT 'Optionale E-Mail-Adresse des Teilnehmenden als Snapshot oder Freitext.',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage des Teilnehmenden.',

  PRIMARY KEY (`id`),
  KEY `idx_cod_seminar_participants_seminar` (`seminar_id`),
  KEY `idx_cod_seminar_participants_person` (`person_id`),

  CONSTRAINT `fk_cod_seminar_participants_seminar`
    FOREIGN KEY (`seminar_id`) REFERENCES `cod_seminars` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,

  CONSTRAINT `fk_cod_seminar_participants_person`
    FOREIGN KEY (`person_id`) REFERENCES `ids_persons` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Teilnehmende eines Seminars; optional mit Personenstammdatensatz verknüpft.';
