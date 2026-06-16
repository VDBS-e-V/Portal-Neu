CREATE TABLE IF NOT EXISTS `pt_menus` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel des Menüs.',
  `area_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf pt_areas.id; Bereich, zu dem das Menü gehört.',
  `name` VARCHAR(191) NOT NULL COMMENT 'Anzeigename des Menüs.',
  `slug` VARCHAR(191) NOT NULL COMMENT 'Eindeutiger technischer Menüschlüssel, z. B. verwaltung.main.',
  `is_default` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Kennzeichnet das Standardmenü eines Bereichs.',
  `settings` JSON DEFAULT NULL COMMENT 'Optionale JSON-Einstellungen des Menüs.',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage des Menüs.',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung des Menüs.',
  `deleted_at` DATETIME NULL DEFAULT NULL COMMENT 'Zeitpunkt einer weichen Löschung.',

  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pt_menus_slug` (`slug`),
  KEY `idx_pt_menus_area_id` (`area_id`),

  CONSTRAINT `fk_pt_menus_area`
    FOREIGN KEY (`area_id`) REFERENCES `pt_areas` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Navigationsmenüs pro Portalbereich.';
