CREATE TABLE IF NOT EXISTS `pt_tickets` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel des Tickets.',
  `ticket_uuid` BINARY(16) NOT NULL COMMENT 'Öffentliche, nicht erratbare Referenz für /tickets/{id} und Mail-Links. Nicht Teil des Issue-Vorschlags, sondern analog zu person_uuid/user_uuid ergänzt, damit keine fortlaufende ID nach außen sichtbar wird.',
  `area_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf pt_ticket_areas.id; Arbeitsbereich, dem das Ticket zugeordnet ist.',
  `category_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf pt_ticket_categories.id; Kategorie, die beim Erstellen gewählt wurde.',
  `subject` VARCHAR(255) NOT NULL COMMENT 'Betreff des Tickets, vom Ersteller vergeben, nachträglich bearbeitbar.',
  `status` ENUM('open', 'assigned', 'done') NOT NULL DEFAULT 'open' COMMENT 'Ticket-Status: offen, zugewiesen oder erledigt. Wechselt automatisch auf assigned bei erster Zuweisung.',
  `priority` ENUM('low', 'normal', 'high', 'urgent') NOT NULL DEFAULT 'normal' COMMENT 'Priorität, ausschließlich von der Verwaltung gesetzt. Anzeige in der UI auf Deutsch (niedrig/normal/hoch/dringend), interner Wert auf Englisch analog zu ids_persons.status.',
  `created_by_person_id` BIGINT UNSIGNED NULL COMMENT 'Verweist auf ids_persons.id, falls beim Erstellen eine Person ermittelt/angelegt werden konnte. NULL bleibt möglich, da ids_persons aktuell kein E-Mail-Feld besitzt und die "Person anhand E-Mail finden/anlegen"-Logik aus Abschnitt 1 des Issues noch nicht abschließend geklärt ist (siehe Hinweis unten).',
  `contact_email` VARCHAR(191) NOT NULL COMMENT 'Kontakt-E-Mail für dieses Ticket; kann von der Login-Mail abweichen. Primärer Anknüpfungspunkt für Benachrichtigungen und den Einmalpasswort-Flow.',
  `consented_at` DATETIME NOT NULL COMMENT 'Zeitpunkt der DSGVO-Einwilligung beim Absenden des Erstellen-Formulars.',
  `retention_until` DATETIME NOT NULL COMMENT 'Errechnet aus consented_at + 72 Monate; Grundlage für den Anonymisierungs-Cronjob.',
  `last_activity_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Aktivität (Nachricht, Statuswechsel, Zuweisung); von der Anwendung gepflegt, für Sortierung in Verwaltungsansicht.',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Ticket-Erstellung.',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung des Tickets.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pt_tickets_uuid` (`ticket_uuid`),
  KEY `idx_pt_tickets_area_status` (`area_id`, `status`),
  KEY `idx_pt_tickets_category` (`category_id`),
  KEY `idx_pt_tickets_created_by_person` (`created_by_person_id`),
  KEY `idx_pt_tickets_contact_email` (`contact_email`),
  KEY `idx_pt_tickets_retention_until` (`retention_until`),
  KEY `idx_pt_tickets_last_activity` (`last_activity_at`),
  CONSTRAINT `fk_pt_tickets_area`
    FOREIGN KEY (`area_id`) REFERENCES `pt_ticket_areas` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_pt_tickets_category`
    FOREIGN KEY (`category_id`) REFERENCES `pt_ticket_categories` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_pt_tickets_created_by_person`
    FOREIGN KEY (`created_by_person_id`) REFERENCES `ids_persons` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Ticket-Kern: Anliegen von angemeldeten oder anonymen Erstellern, zugeordnet zu Bereich und Kategorie.';
