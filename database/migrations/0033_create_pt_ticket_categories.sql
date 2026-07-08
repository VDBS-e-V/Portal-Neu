CREATE TABLE IF NOT EXISTS `pt_ticket_categories` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel der Kategorie.',
  `area_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf pt_ticket_areas.id; Bereich, dem die Kategorie zugeordnet ist.',
  `category_key` VARCHAR(64) NOT NULL COMMENT 'Technischer, unveränderlicher Schlüssel der Kategorie innerhalb des Bereichs (Muster analog area_key).',
  `name` VARCHAR(191) NOT NULL COMMENT 'Anzeigename der Kategorie innerhalb des Bereichs.',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Ob die Kategorie aktuell zur Ticket-Erstellung angeboten wird.',
  `sort_order` SMALLINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Reihenfolge der Anzeige innerhalb des Bereichs.',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage der Kategorie.',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung der Kategorie.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pt_ticket_categories_area_category_key` (`area_id`, `category_key`),
  KEY `idx_pt_ticket_categories_active` (`is_active`),
  CONSTRAINT `fk_pt_ticket_categories_area`
    FOREIGN KEY (`area_id`) REFERENCES `pt_ticket_areas` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Kategorien innerhalb eines Ticket-Bereichs, denen Folgefragen zugeordnet werden.';
