CREATE TABLE IF NOT EXISTS `ids_person_access_otps` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel des Einmalpassworts.',
  `email` VARCHAR(191) NOT NULL COMMENT 'E-Mail-Adresse, an die der Code gesendet wurde. Bewusst e-mail-basiert statt an ids_persons.id gebunden, da eine anonyme Anfrage nicht zwingend einer Person zugeordnet ist (siehe Hinweis zu pt_tickets.created_by_person_id).',
  `code_hash` VARCHAR(255) NOT NULL COMMENT 'Gehashter Einmalcode; niemals Klartext speichern.',
  `expires_at` DATETIME NOT NULL COMMENT 'Ablaufzeitpunkt des Codes (kurzlebig, z. B. 15 Minuten).',
  `attempts` SMALLINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Anzahl fehlgeschlagener Eingabeversuche, für Rate-Limiting/Sperre.',
  `consumed_at` DATETIME NULL COMMENT 'Zeitpunkt erfolgreicher Verwendung; verhindert Wiederverwendung desselben Codes.',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anforderung des Codes.',
  PRIMARY KEY (`id`),
  KEY `idx_ids_person_access_otps_email` (`email`),
  KEY `idx_ids_person_access_otps_expires` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Einmalpasswörter für den E-Mail+OTP-Zugriffsflow auf /tickets/meine.';
