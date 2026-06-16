CREATE TABLE IF NOT EXISTS `cod_school_memberships` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel der Mitgliedschaft.',
  `school_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf cod_schools.id; zugehörige Schule.',
  `person_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf ids_persons.id; zugeordnete Person.',
  `role_key` VARCHAR(191) NOT NULL COMMENT 'Technischer Rollenbezeichner innerhalb der Schule.',
  `starts_at` DATE NULL COMMENT 'Startdatum der Mitgliedschaft.',
  `ends_at` DATE NULL COMMENT 'Enddatum der Mitgliedschaft.',
  `is_primary` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Kennzeichnet die Hauptzuordnung.',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage der Mitgliedschaft.',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung der Mitgliedschaft.',

  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_cod_school_membership_unique` (`school_id`, `person_id`, `role_key`),
  KEY `idx_cod_school_memberships_person` (`person_id`),

  CONSTRAINT `fk_cod_school_memberships_school`
    FOREIGN KEY (`school_id`) REFERENCES `cod_schools` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,

  CONSTRAINT `fk_cod_school_memberships_person`
    FOREIGN KEY (`person_id`) REFERENCES `ids_persons` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Mitgliedschaften von Personen an Schulen.';
