-- Entfernt die bisher ungenutzten Alt-Tabellen des Ticket-Systems.
-- Ersetzt durch das vollständige Datenmodell ab Migration 0032.
-- FK-Prüfung kurzzeitig deaktiviert, da die genaue Abhängigkeitsreihenfolge
-- der Alt-Tabellen nicht verifiziert wurde (sie enthalten laut Angabe keine Daten).
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `pt_ticket_comments`;
DROP TABLE IF EXISTS `pt_tickets`;
DROP TABLE IF EXISTS `pt_ticket_types`;

SET FOREIGN_KEY_CHECKS = 1;
