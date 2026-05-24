-- migrations/000005_create_ids_menus_table.sql
-- Create menus table associated with areas

CREATE TABLE IF NOT EXISTS ids_menus (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT
        COMMENT 'Beschreibung: Interner Primärschlüssel (laufende ID des Menüs); Mögliche Werte: BIGINT UNSIGNED, AUTO_INCREMENT',

    uuid CHAR(36) NOT NULL
        COMMENT 'Beschreibung: Öffentliche UUID des Menüs; Mögliche Werte: CHAR(36)',

    area_id BIGINT UNSIGNED NOT NULL
        COMMENT 'Beschreibung: Fremdschlüssel zur zugehörigen Area (ids_areas.id); Mögliche Werte: BIGINT UNSIGNED',

    name VARCHAR(300) NOT NULL
        COMMENT 'Beschreibung: Anzeigename des Menüs; Mögliche Werte: VARCHAR(300)',

    slug VARCHAR(120) NOT NULL
        COMMENT 'Beschreibung: Maschinell lesbarer Slug für das Menü; Mögliche Werte: VARCHAR(120)',

    description TEXT NULL
        COMMENT 'Beschreibung: Ausführliche Beschreibung des Menüs; Mögliche Werte: TEXT',

    href VARCHAR(500) NULL
        COMMENT 'Beschreibung: Optionaler Basis-Link des Menüs; Mögliche Werte: VARCHAR(500)',

    website_paths JSON NOT NULL DEFAULT '[]'
        COMMENT 'Beschreibung: JSON-Array mit Pfaden/URLs; Mögliche Werte: JSON',

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

    menu_type VARCHAR(32) NOT NULL DEFAULT 'header'
        COMMENT 'Beschreibung: Typ des Menüs (z.B. header, footer); Mögliche Werte: VARCHAR(32)',

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        COMMENT 'Beschreibung: Zeitpunkt der Erstellung; Mögliche Werte: DATETIME',

    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        COMMENT 'Beschreibung: Zeitpunkt der letzten Aktualisierung; Mögliche Werte: DATETIME',

    deleted_at DATETIME NULL
        COMMENT 'Beschreibung: Soft-Delete Zeitpunkt; Mögliche Werte: DATETIME oder NULL',

    PRIMARY KEY (id),
    UNIQUE KEY uniq_ids_menus_uuid (uuid),
    UNIQUE KEY uniq_ids_menus_slug (slug),
    KEY idx_ids_menus_area_id (area_id),
    KEY idx_ids_menus_sorting (sorting),
    KEY idx_ids_menus_deleted_at (deleted_at),
    CONSTRAINT fk_ids_menus_area FOREIGN KEY (area_id) REFERENCES ids_areas(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Beschreibung: Zentrale Menüs-Tabelle; Mögliche Werte: InnoDB, utf8mb4';
