CREATE TABLE IF NOT EXISTS `ids_permissions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel der Einzelberechtigung.',
  `permission_key` VARCHAR(191) NOT NULL COMMENT 'Stabiler technischer Berechtigungsschlüssel, z. B. school_directory.import.',
  `name` VARCHAR(191) NOT NULL COMMENT 'Anzeigename der Berechtigung.',
  `description` TEXT NULL COMMENT 'Beschreibung der Berechtigung.',
  `is_system` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Systemberechtigungen werden durch Seeds gepflegt.',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage.',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung.',

  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_ids_permissions_key` (`permission_key`),
  KEY `idx_ids_permissions_system` (`is_system`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Granulare Einzelberechtigungen des Portals.';

CREATE TABLE IF NOT EXISTS `ids_permission_group_permissions` (
  `permission_group_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf ids_permission_groups.id.',
  `permission_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf ids_permissions.id.',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Zuordnung.',

  PRIMARY KEY (`permission_group_id`, `permission_id`),
  KEY `idx_permission_group_permissions_permission` (`permission_id`),

  CONSTRAINT `fk_permission_group_permissions_group`
    FOREIGN KEY (`permission_group_id`)
    REFERENCES `ids_permission_groups` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,

  CONSTRAINT `fk_permission_group_permissions_permission`
    FOREIGN KEY (`permission_id`)
    REFERENCES `ids_permissions` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Zuordnung von Personengruppen zu granularen Berechtigungen.';
