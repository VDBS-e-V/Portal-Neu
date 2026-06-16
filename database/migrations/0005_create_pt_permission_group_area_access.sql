CREATE TABLE IF NOT EXISTS `pt_permission_group_area_access` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel der Zuordnung.',
  `permission_group_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf ids_permission_groups.id; berechtigte Gruppe.',
  `area_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf pt_areas.id; freigegebener Bereich.',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Vergabe des Bereichszugriffs.',

  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pt_pg_area_access` (`permission_group_id`, `area_id`),
  KEY `idx_pt_pg_area_access_area` (`area_id`),

  CONSTRAINT `fk_pt_pg_access_group`
    FOREIGN KEY (`permission_group_id`) REFERENCES `ids_permission_groups` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,

  CONSTRAINT `fk_pt_pg_access_area`
    FOREIGN KEY (`area_id`) REFERENCES `pt_areas` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Grobe Zuordnung von Berechtigungsgruppen zu Portalbereichen.';

CREATE TABLE IF NOT EXISTS `pt_page_groups` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel der Seitengruppe.',
  `area_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf pt_areas.id; übergeordneter Bereich.',
  `page_group_key` VARCHAR(191) NOT NULL COMMENT 'Technischer Schlüssel der Seitengruppe innerhalb der Area, z. B. personen.',
  `name` VARCHAR(191) NOT NULL COMMENT 'Anzeigename der Seitengruppe.',
  `description` TEXT NULL COMMENT 'Optionale Beschreibung der Seitengruppe.',
  `start_path` VARCHAR(255) NOT NULL COMMENT 'Startpfad der Seitengruppe.',
  `sort_order` INT NOT NULL DEFAULT 0 COMMENT 'Sortierreihenfolge innerhalb der Area.',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Aktivstatus der Seitengruppe.',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage der Seitengruppe.',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung der Seitengruppe.',

  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pt_page_groups_area_key` (`area_id`, `page_group_key`),
  KEY `idx_pt_page_groups_area` (`area_id`),
  KEY `idx_pt_page_groups_active_sort` (`is_active`, `sort_order`),

  CONSTRAINT `fk_pt_page_groups_area`
    FOREIGN KEY (`area_id`) REFERENCES `pt_areas` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Feingranulare Seitengruppen innerhalb eines Portalbereichs, z. B. Verwaltung.Personen.';

CREATE TABLE IF NOT EXISTS `pt_permission_group_page_group_access` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel der Zuordnung.',
  `permission_group_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf ids_permission_groups.id; berechtigte Gruppe.',
  `page_group_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf pt_page_groups.id; freigegebene Seitengruppe.',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Vergabe des Seitengruppenzugriffs.',

  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pt_pg_page_group_access` (`permission_group_id`, `page_group_id`),
  KEY `idx_pt_pg_page_group_access_page_group` (`page_group_id`),

  CONSTRAINT `fk_pt_pg_page_group_access_group`
    FOREIGN KEY (`permission_group_id`) REFERENCES `ids_permission_groups` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,

  CONSTRAINT `fk_pt_pg_page_group_access_page_group`
    FOREIGN KEY (`page_group_id`) REFERENCES `pt_page_groups` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Feingranulare Zuordnung von Berechtigungsgruppen zu Seitengruppen.';
