CREATE TABLE IF NOT EXISTS `ids_permission_groups` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel der Berechtigungsgruppe.',
  `group_key` VARCHAR(191) NOT NULL COMMENT 'Eindeutiger technischer Schlüssel, meist im Format domain.rolle.',
  `name` VARCHAR(191) NOT NULL COMMENT 'Anzeigename der Berechtigungsgruppe.',
  `description` TEXT NULL COMMENT 'Optionale Beschreibung der Berechtigungsgruppe.',
  `is_system` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Kennzeichnet systemverwaltete Gruppen; Systemgruppen sind nicht löschbar.',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage der Berechtigungsgruppe.',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung der Berechtigungsgruppe.',

  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_ids_permission_groups_key` (`group_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Technische Berechtigungsgruppen im Identity-System.';
