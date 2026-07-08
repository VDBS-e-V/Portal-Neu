CREATE TABLE IF NOT EXISTS `pt_ticket_attachments` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel des Anhangs.',
  `attachment_uuid` BINARY(16) NOT NULL COMMENT 'Öffentliche, nicht erratbare Referenz für Download-Links. Nicht Teil des Issue-Vorschlags, analog zu ticket_uuid ergänzt.',
  `ticket_message_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf pt_ticket_messages.id; Nachricht, der der Anhang beiliegt.',
  `file_name` VARCHAR(255) NOT NULL COMMENT 'Ursprünglicher Dateiname beim Upload.',
  `storage_path` VARCHAR(255) NOT NULL COMMENT 'Ablagepfad/Schlüssel im Dateispeicher; kein direkter Web-Pfad.',
  `mime_type` VARCHAR(191) NOT NULL COMMENT 'MIME-Type der Datei, wie beim Upload erkannt.',
  `file_size_bytes` INT UNSIGNED NOT NULL COMMENT 'Dateigröße in Bytes.',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt des Uploads.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pt_ticket_attachments_uuid` (`attachment_uuid`),
  KEY `idx_pt_ticket_attachments_message` (`ticket_message_id`),
  CONSTRAINT `fk_pt_ticket_attachments_message`
    FOREIGN KEY (`ticket_message_id`) REFERENCES `pt_ticket_messages` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Datei-Anhänge zu Ticket-Nachrichten.';
