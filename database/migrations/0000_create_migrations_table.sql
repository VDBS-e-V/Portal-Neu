CREATE TABLE IF NOT EXISTS `__migrations` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel der Protokollzeile.',
  `migration` VARCHAR(255) NOT NULL COMMENT 'Name der ausgeführten Migration; muss eindeutig sein.',
  `batch` INT NOT NULL COMMENT 'Nummer des Migrationslaufs; positive ganze Zahl.',
  `ran_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Ausführung; wird automatisch gesetzt.',

  PRIMARY KEY (`id`),
  UNIQUE KEY `uq___migrations_migration` (`migration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Protokolliert ausgeführte Migrationen.';
