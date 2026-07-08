CREATE TABLE IF NOT EXISTS `pt_ticket_access_tokens` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel des Zugriffstokens.',
  `ticket_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf pt_tickets.id; Ticket, für das der Token Zugriff gewährt.',
  `token_hash` VARCHAR(255) NOT NULL COMMENT 'Gehashter Token/Passwort-Wert; niemals Klartext speichern.',
  `expires_at` DATETIME NULL COMMENT 'Ablaufzeitpunkt; NULL bedeutet kein Ablauf (Standard: Gültigkeit an retention_until des Tickets koppeln).',
  `revoked_at` DATETIME NULL COMMENT 'Zeitpunkt einer manuellen Sperrung, z. B. bei Neuausstellung eines Links.',
  `last_used_at` DATETIME NULL COMMENT 'Zeitpunkt der letzten erfolgreichen Verwendung.',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Ausstellung des Tokens.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pt_ticket_access_tokens_token_hash` (`token_hash`),
  KEY `idx_pt_ticket_access_tokens_ticket` (`ticket_id`),
  CONSTRAINT `fk_pt_ticket_access_tokens_ticket`
    FOREIGN KEY (`ticket_id`) REFERENCES `pt_tickets` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Zugriffstoken, mit denen Ersteller ohne Login auf ihr Ticket zugreifen (eingebettet im Bestätigungsmail-Link). Mehrere Zeilen je Ticket möglich (Neuausstellung), gültig ist die nicht widerrufene/abgelaufene.';
