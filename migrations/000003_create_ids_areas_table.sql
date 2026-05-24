-- migrations/000003_create_ids_areas_table.sql
-- Create central areas table

CREATE TABLE IF NOT EXISTS ids_areas (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT
        COMMENT 'Beschreibung: Interner Primärschlüssel (laufende ID) des Bereichs; Mögliche Werte: BIGINT UNSIGNED, AUTO_INCREMENT',

    uuid CHAR(36) NOT NULL
        COMMENT 'Beschreibung: Öffentliche UUID des Bereichs; Mögliche Werte: CHAR(36)',

    name VARCHAR(300) NOT NULL
        COMMENT 'Beschreibung: Anzeigename des Bereichs; Mögliche Werte: VARCHAR(300)',

    slug VARCHAR(120) NOT NULL
        COMMENT 'Beschreibung: Maschinell lesbarer Slug für URLs; Mögliche Werte: VARCHAR(120)',

    description TEXT NULL
        COMMENT 'Beschreibung: Ausführliche Beschreibung des Bereichs; Mögliche Werte: TEXT',

    icon VARCHAR(500) NULL
        COMMENT 'Beschreibung: Pfad/URL zum Icon; Mögliche Werte: VARCHAR(500)',

    sorting INT NOT NULL DEFAULT 0
        COMMENT 'Beschreibung: Globale Sortierreihenfolge (kleinere Werte werden zuerst angezeigt); Mögliche Werte: INT',

    allowed_user_groups JSON NOT NULL DEFAULT '[]'
        COMMENT 'Beschreibung: JSON-Array mit erlaubten Gruppen/rollen; leeres Array = öffentlich; Mögliche Werte: JSON',

    visibility_header TINYINT(1) NOT NULL DEFAULT 1
        COMMENT 'Beschreibung: Sichtbarkeit im Header (0 = verborgen, 1 = sichtbar); Mögliche Werte: TINYINT(1)',

    visibility_listing TINYINT(1) NOT NULL DEFAULT 1
        COMMENT 'Beschreibung: Sichtbarkeit in Listings (0 = verborgen, 1 = sichtbar); Mögliche Werte: TINYINT(1)',

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        COMMENT 'Beschreibung: Zeitpunkt der Erstellung; Mögliche Werte: DATETIME',

    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        COMMENT 'Beschreibung: Zeitpunkt der letzten Aktualisierung; Mögliche Werte: DATETIME',

    deleted_at DATETIME NULL
        COMMENT 'Beschreibung: Soft-Delete Zeitpunkt; Mögliche Werte: DATETIME oder NULL',

    PRIMARY KEY (id),
    UNIQUE KEY uniq_ids_areas_uuid (uuid),
    UNIQUE KEY uniq_ids_areas_slug (slug),
    KEY idx_ids_areas_sorting (sorting),
    KEY idx_ids_areas_deleted_at (deleted_at)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Beschreibung: Zentrale Areas-Tabelle; Mögliche Werte: InnoDB, utf8mb4';
