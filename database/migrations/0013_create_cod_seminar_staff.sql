CREATE TABLE IF NOT EXISTS `cod_seminar_staff` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel der Zuordnung.',
  `seminar_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf cod_seminars.id; zugehöriges Seminar.',
  `person_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf ids_persons.id; zugeordnete Person.',
  `role_key` VARCHAR(191) NOT NULL COMMENT 'Technischer Rollenbezeichner im Seminar.',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage der Zuordnung.',

  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_cod_seminar_staff_unique` (`seminar_id`, `person_id`, `role_key`),
  KEY `idx_cod_seminar_staff_person` (`person_id`),

  CONSTRAINT `fk_cod_seminar_staff_seminar`
    FOREIGN KEY (`seminar_id`) REFERENCES `cod_seminars` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,

  CONSTRAINT `fk_cod_seminar_staff_person`
    FOREIGN KEY (`person_id`) REFERENCES `ids_persons` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Personen, die einem Seminar in einer internen Rolle zugeordnet sind.';
