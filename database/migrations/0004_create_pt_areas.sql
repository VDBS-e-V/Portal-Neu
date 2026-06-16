CREATE TABLE IF NOT EXISTS `pt_areas` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel des Bereichs.',
  `area_key` VARCHAR(191) NOT NULL COMMENT 'Eindeutiger technischer Bereichsschlüssel, z. B. portal, verwaltung oder development.',
  `name` VARCHAR(191) NOT NULL COMMENT 'Anzeigename des Bereichs.',
  `description` TEXT NULL COMMENT 'Optionale Kurzbeschreibung des Bereichs.',
  `start_path` VARCHAR(255) NOT NULL DEFAULT '/' COMMENT 'Startpfad des Bereichs; muss mit / beginnen.',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Aktivstatus des Bereichs.',
  `is_external` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Kennzeichnet externe Bereiche.',
  `sort_order` INT NOT NULL DEFAULT 0 COMMENT 'Sortierreihenfolge.',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage des Bereichs.',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung des Bereichs.',

  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pt_areas_key` (`area_key`),
  KEY `idx_pt_areas_active_sort` (`is_active`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Fachliche Bereiche des Portals; oberste Ebene von Navigation und Berechtigung.';
