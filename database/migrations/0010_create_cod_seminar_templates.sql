CREATE TABLE IF NOT EXISTS `cod_seminar_templates` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel der Seminarvorlage.',
  `template_key` VARCHAR(191) NOT NULL COMMENT 'Eindeutiger technischer Schlüssel der Seminarvorlage.',
  `title` VARCHAR(191) NOT NULL COMMENT 'Titel der Seminarvorlage.',
  `description` TEXT NULL COMMENT 'Optionale Beschreibung der Seminarvorlage.',
  `default_duration_minutes` INT NULL COMMENT 'Optionale Standarddauer in Minuten.',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Aktivstatus der Seminarvorlage.',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage der Seminarvorlage.',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung der Seminarvorlage.',

  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_cod_seminar_templates_key` (`template_key`),
  KEY `idx_cod_seminar_templates_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Vorlagen für Seminare im Fachbereich COD.';
