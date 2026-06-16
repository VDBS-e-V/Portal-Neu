CREATE TABLE IF NOT EXISTS `pt_audit_log` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `occurred_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actor_person_id` BIGINT UNSIGNED NULL,
  `actor_user_id` BIGINT UNSIGNED NULL,
  `action` VARCHAR(191) NOT NULL,
  `entity_type` VARCHAR(191) NOT NULL,
  `entity_id` BIGINT UNSIGNED NULL,
  `entity_uuid` BINARY(16) NULL,
  `entity_label` VARCHAR(191) NULL,
  `request_id` VARCHAR(191) NULL,
  `request_method` VARCHAR(16) NULL,
  `request_uri` VARCHAR(512) NULL,
  `ip_address` VARCHAR(45) NULL,
  `user_agent` VARCHAR(512) NULL,
  `old_values` JSON NULL,
  `new_values` JSON NULL,
  `metadata` JSON NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (`id`),
  KEY `idx_pt_audit_log_occurred_at` (`occurred_at`),
  KEY `idx_pt_audit_log_actor_person` (`actor_person_id`),
  KEY `idx_pt_audit_log_actor_user` (`actor_user_id`),
  KEY `idx_pt_audit_log_action` (`action`),
  KEY `idx_pt_audit_log_entity` (`entity_type`, `entity_id`),
  KEY `idx_pt_audit_log_request_id` (`request_id`),

  CONSTRAINT `fk_pt_audit_log_actor_person`
    FOREIGN KEY (`actor_person_id`) REFERENCES `ids_persons` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,

  CONSTRAINT `fk_pt_audit_log_actor_user`
    FOREIGN KEY (`actor_user_id`) REFERENCES `ids_users` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Umfangreicher Audit-Log für administrative und sicherheitsrelevante Änderungen.';