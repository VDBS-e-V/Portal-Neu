CREATE TABLE IF NOT EXISTS `pt_ticket_types` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel der Ticketart.',
  `type_key` VARCHAR(191) NOT NULL COMMENT 'Eindeutiger technischer Schlüssel der Ticketart.',
  `name` VARCHAR(191) NOT NULL COMMENT 'Anzeigename der Ticketart.',
  `description` TEXT NULL COMMENT 'Optionale Beschreibung der Ticketart.',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Aktivstatus der Ticketart.',
  `sort_order` INT NOT NULL DEFAULT 0 COMMENT 'Sortierreihenfolge der Ticketart.',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage der Ticketart.',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung der Ticketart.',

  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pt_ticket_types_key` (`type_key`),
  KEY `idx_pt_ticket_types_active_sort` (`is_active`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Konfigurierbare Ticketarten im Portal.';
