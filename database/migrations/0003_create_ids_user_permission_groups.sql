CREATE TABLE IF NOT EXISTS `ids_person_permission_groups` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel der Zuordnung.',
  `person_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf ids_persons.id; zugeordnete Person.',
  `permission_group_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf ids_permission_groups.id; zugeordnete Berechtigungsgruppe.',
  `assigned_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Zuweisung.',
  `assigned_by_person_id` BIGINT UNSIGNED NULL COMMENT 'Verweist auf ids_persons.id; Person, die die Zuordnung vergeben hat, oder NULL.',

  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_ids_person_permission_groups_person_group` (`person_id`, `permission_group_id`),
  KEY `idx_ids_ppg_group` (`permission_group_id`),
  KEY `idx_ids_ppg_assigned_by` (`assigned_by_person_id`),

  CONSTRAINT `fk_ids_ppg_person`
    FOREIGN KEY (`person_id`) REFERENCES `ids_persons` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,

  CONSTRAINT `fk_ids_ppg_group`
    FOREIGN KEY (`permission_group_id`) REFERENCES `ids_permission_groups` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,

  CONSTRAINT `fk_ids_ppg_assigned_by`
    FOREIGN KEY (`assigned_by_person_id`) REFERENCES `ids_persons` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Verknüpft Personen mit Berechtigungsgruppen.';
