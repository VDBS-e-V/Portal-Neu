
CREATE TABLE IF NOT EXISTS ids_menu_items (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT
        COMMENT 'Beschreibung: Interner Primärschlüssel (laufende ID des Menüeintrags); Mögliche Werte: BIGINT UNSIGNED, AUTO_INCREMENT',

    uuid CHAR(36) NOT NULL
        COMMENT 'Beschreibung: Öffentliche UUID des Menüeintrags; Mögliche Werte: CHAR(36)',

    menu_id BIGINT UNSIGNED NOT NULL
        COMMENT 'Beschreibung: Fremdschlüssel auf ids_menus.id; Mögliche Werte: BIGINT UNSIGNED',

    parent_id BIGINT UNSIGNED NULL
        COMMENT 'Beschreibung: Parent-ID für verschachtelte Einträge; NULL = root; Mögliche Werte: BIGINT UNSIGNED oder NULL',

    title VARCHAR(300) NOT NULL
        COMMENT 'Beschreibung: Anzeigename des Eintrags; Mögliche Werte: VARCHAR(300)',

    slug VARCHAR(120) NULL
        COMMENT 'Beschreibung: Optionale slug/identifier; Mögliche Werte: VARCHAR(120)',

    href VARCHAR(500) NULL
        COMMENT 'Beschreibung: Ziel-URL oder interner Pfad; Mögliche Werte: VARCHAR(500)',

    attributes JSON NOT NULL DEFAULT '{}'
        COMMENT 'Beschreibung: Freie Attribute/Metadaten als JSON; Mögliche Werte: JSON',

    sorting INT NOT NULL DEFAULT 0
        COMMENT 'Beschreibung: Sortierreihenfolge innerhalb derselben Eltern; Mögliche Werte: INT',

    visible TINYINT(1) NOT NULL DEFAULT 1
        COMMENT 'Beschreibung: Sichtbarkeit (0 = verborgen, 1 = sichtbar); Mögliche Werte: TINYINT(1)',

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        COMMENT 'Beschreibung: Zeitpunkt der Erstellung; Mögliche Werte: DATETIME',

    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        COMMENT 'Beschreibung: Zeitpunkt der letzten Aktualisierung; Mögliche Werte: DATETIME',

    deleted_at DATETIME NULL
        COMMENT 'Beschreibung: Soft-Delete Zeitpunkt; Mögliche Werte: DATETIME oder NULL',

    PRIMARY KEY (id),
    UNIQUE KEY uniq_ids_menu_items_uuid (uuid),
    KEY idx_ids_menu_items_menu_id (menu_id),
    KEY idx_ids_menu_items_parent_id (parent_id),
    KEY idx_ids_menu_items_sorting (sorting),
    KEY idx_ids_menu_items_deleted_at (deleted_at),
    CONSTRAINT fk_ids_menu_items_menu FOREIGN KEY (menu_id) REFERENCES ids_menus(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_ids_menu_items_parent FOREIGN KEY (parent_id) REFERENCES ids_menu_items(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Beschreibung: Normalisierte Menüeinträge (verschachtelbar); Mögliche Werte: InnoDB, utf8mb4';
