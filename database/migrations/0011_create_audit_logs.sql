-- Migration: 0011_create_audit_logs.sql
CREATE TABLE IF NOT EXISTS `__audit_logs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `entity_type` VARCHAR(100) NOT NULL,
  `entity_id` BIGINT UNSIGNED NOT NULL,
  `action` VARCHAR(100) NOT NULL,
  `performed_by` BIGINT UNSIGNED DEFAULT NULL,
  `meta` JSON DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx__audit_logs_entity` (`entity_type`,`entity_id`),
  CONSTRAINT `fk___audit_logs_performed_by` FOREIGN KEY (`performed_by`) REFERENCES `ids_users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
