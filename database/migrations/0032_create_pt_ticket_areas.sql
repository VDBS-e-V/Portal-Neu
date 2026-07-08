CREATE TABLE IF NOT EXISTS `pt_ticket_areas` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel des Ticket-Bereichs.',
  `area_key` VARCHAR(64) NOT NULL COMMENT 'Technischer, unveränderlicher Schlüssel des Bereichs (z. B. "it", "finanzen"). Benannt nach dem Muster area_key/page_group_key/group_key aus dem bestehenden Schema.',
  `name` VARCHAR(191) NOT NULL COMMENT 'Anzeigename des Bereichs für die Ticket-Erstellung und Verwaltung.',
  `page_group_id` BIGINT UNSIGNED NULL COMMENT 'Verweist auf pt_page_groups.id; steuert per pt_permission_group_page_group_access, welche Berechtigungsgruppen diesen Bereich in der Verwaltungsansicht sehen/bearbeiten dürfen. NULL bis die zugehörige PageGroup angelegt ist (Umsetzungsschritt "Area tickets + PageGroups").',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Ob der Bereich aktuell zur Ticket-Erstellung angeboten wird.',
  `sort_order` SMALLINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Reihenfolge der Anzeige im mehrstufigen Ticket-Formular.',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage des Bereichs.',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung des Bereichs.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pt_ticket_areas_area_key` (`area_key`),
  KEY `idx_pt_ticket_areas_active` (`is_active`),
  KEY `idx_pt_ticket_areas_page_group` (`page_group_id`),
  CONSTRAINT `fk_pt_ticket_areas_page_group`
    FOREIGN KEY (`page_group_id`) REFERENCES `pt_page_groups` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Fachbereiche, die Tickets entgegennehmen (z. B. IT, Finanzen, Mitgliederverwaltung).';
