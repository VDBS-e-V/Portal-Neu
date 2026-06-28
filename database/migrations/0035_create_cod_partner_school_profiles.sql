CREATE TABLE IF NOT EXISTS `cod_partner_school_profiles` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel des Partnerschulprofils.',
  `school_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf cod_schools.id.',

  `partner_status` ENUM(
    'potential',
    'first_contact',
    'seminar_planned',
    'seminar_done',
    'active_partner',
    'contract_pending',
    'contract_active',
    'paused',
    'archived'
  ) NOT NULL DEFAULT 'seminar_done' COMMENT 'Interner Status der Partnerschulbeziehung.',

  `internal_owner_person_id` BIGINT UNSIGNED NULL COMMENT 'Interne hauptverantwortliche Person.',

  `first_seminar_on` DATE NULL COMMENT 'Datum des ersten Seminars an dieser Schule.',
  `last_seminar_on` DATE NULL COMMENT 'Datum des letzten Seminars an dieser Schule.',
  `last_contact_at` DATETIME NULL COMMENT 'Zeitpunkt des letzten Kontakts.',
  `next_followup_at` DATETIME NULL COMMENT 'Zeitpunkt der nächsten Wiedervorlage.',

  `summary` TEXT NULL COMMENT 'Kurze interne Zusammenfassung der Zusammenarbeit.',
  `internal_notes` TEXT NULL COMMENT 'Allgemeine interne Notizen zur Partnerschule.',

  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage des Partnerschulprofils.',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung des Partnerschulprofils.',

  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_cod_partner_school_profiles_school` (`school_id`),
  KEY `idx_cod_partner_school_profiles_status` (`partner_status`),
  KEY `idx_cod_partner_school_profiles_owner` (`internal_owner_person_id`),
  KEY `idx_cod_partner_school_profiles_followup` (`next_followup_at`),
  KEY `idx_cod_partner_school_profiles_last_contact` (`last_contact_at`),

  CONSTRAINT `fk_cod_partner_school_profiles_school`
    FOREIGN KEY (`school_id`)
    REFERENCES `cod_schools` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,

  CONSTRAINT `fk_cod_partner_school_profiles_owner`
    FOREIGN KEY (`internal_owner_person_id`)
    REFERENCES `ids_persons` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Interne Arbeitsfläche und Statusdaten für Partnerschulen.';