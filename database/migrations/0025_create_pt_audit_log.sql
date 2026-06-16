CREATE TABLE IF NOT EXISTS `pt_audit_log` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel des Audit-Eintrags.',
  `occurred_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt, zu dem die protokollierte Aktion fachlich stattgefunden hat.',
  `actor_person_id` BIGINT UNSIGNED NULL COMMENT 'Optionaler Verweis auf ids_persons.id; handelnde Person.',
  `actor_user_id` BIGINT UNSIGNED NULL COMMENT 'Optionaler Verweis auf ids_users.id; handelndes Login-Konto.',
  `action` VARCHAR(191) NOT NULL COMMENT 'Technischer Aktionsschlüssel, z. B. person.updated oder permission.page_group.granted.',
  `entity_type` VARCHAR(191) NOT NULL COMMENT 'Technischer Entitätstyp, auf den sich die Aktion bezieht.',
  `entity_id` BIGINT UNSIGNED NULL COMMENT 'Optionale numerische ID der betroffenen Entität.',
  `entity_uuid` BINARY(16) NULL COMMENT 'Optionale UUID der betroffenen Entität als 16-Byte-Binärwert.',
  `entity_label` VARCHAR(191) NULL COMMENT 'Optionale lesbare Kurzbezeichnung der betroffenen Entität.',
  `request_id` VARCHAR(191) NULL COMMENT 'Optionale Request-ID zur Korrelation mehrerer Audit-Einträge.',
  `request_method` VARCHAR(16) NULL COMMENT 'Optionales HTTP-Verb der auslösenden Anfrage.',
  `request_uri` VARCHAR(512) NULL COMMENT 'Optionale URI der auslösenden Anfrage.',
  `ip_address` VARCHAR(45) NULL COMMENT 'Optionale IP-Adresse der auslösenden Anfrage.',
  `user_agent` VARCHAR(512) NULL COMMENT 'Optionaler User-Agent der auslösenden Anfrage.',
  `old_values` JSON NULL COMMENT 'Optionale JSON-Struktur mit vorherigen, bewusst minimierten Werten.',
  `new_values` JSON NULL COMMENT 'Optionale JSON-Struktur mit neuen, bewusst minimierten Werten.',
  `metadata` JSON NULL COMMENT 'Optionale zusätzliche technische oder fachliche Metadaten.',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Speicherung des Audit-Eintrags.',

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
