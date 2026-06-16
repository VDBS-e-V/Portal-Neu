CREATE TABLE IF NOT EXISTS `pt_menu_items` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel des Menüeintrags.',
  `menu_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf pt_menus.id; zugehöriges Menü.',
  `page_group_id` BIGINT UNSIGNED NULL COMMENT 'Optionaler Verweis auf pt_page_groups.id; steuert Sichtbarkeit über Area.PageGroup.',
  `parent_id` BIGINT UNSIGNED DEFAULT NULL COMMENT 'Optionaler übergeordneter Menüeintrag.',
  `title` VARCHAR(191) NOT NULL COMMENT 'Anzeigetitel des Menüeintrags.',
  `slug` VARCHAR(191) DEFAULT NULL COMMENT 'Optionaler technischer Slug.',
  `url` VARCHAR(255) DEFAULT NULL COMMENT 'Optionaler Zielpfad.',
  `route_name` VARCHAR(191) DEFAULT NULL COMMENT 'Optionaler Routenname.',
  `icon` VARCHAR(100) DEFAULT NULL COMMENT 'Optionales Icon.',
  `target` VARCHAR(20) DEFAULT NULL COMMENT 'Optionales Link-Ziel.',
  `order_index` INT DEFAULT NULL COMMENT 'Sortierreihenfolge.',
  `level` TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Navigationsebene; aktuell 1 bis 3.',
  `is_active` TINYINT(1) DEFAULT 1 COMMENT 'Aktivstatus des Menüeintrags.',
  `settings` JSON DEFAULT NULL COMMENT 'Optionale JSON-Einstellungen.',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage des Menüeintrags.',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung des Menüeintrags.',
  `deleted_at` DATETIME NULL DEFAULT NULL COMMENT 'Zeitpunkt einer weichen Löschung.',

  PRIMARY KEY (`id`),
  KEY `idx_pt_menu_items_menu_id` (`menu_id`),
  KEY `idx_pt_menu_items_page_group_id` (`page_group_id`),
  KEY `idx_pt_menu_items_parent_id` (`parent_id`),

  CONSTRAINT `fk_pt_menu_items_menu`
    FOREIGN KEY (`menu_id`) REFERENCES `pt_menus` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,

  CONSTRAINT `fk_pt_menu_items_page_group`
    FOREIGN KEY (`page_group_id`) REFERENCES `pt_page_groups` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,

  CONSTRAINT `fk_pt_menu_items_parent`
    FOREIGN KEY (`parent_id`) REFERENCES `pt_menu_items` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,

  CONSTRAINT `chk_pt_menu_items_level` CHECK (`level` BETWEEN 1 AND 3)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Navigationspunkte innerhalb eines Menüs.';
