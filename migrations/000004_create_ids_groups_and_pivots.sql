-- migrations/000004_create_ids_groups_and_pivots.sql
-- Create groups and pivot tables for normalized permissions

-- Groups table
CREATE TABLE IF NOT EXISTS ids_groups (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT
        COMMENT 'Beschreibung: Interne ID der Gruppe; Mögliche Werte: BIGINT UNSIGNED, AUTO_INCREMENT',

    uuid CHAR(36) NOT NULL
        COMMENT 'Beschreibung: Öffentliche UUID der Gruppe; Mögliche Werte: CHAR(36)',

    name VARCHAR(120) NOT NULL
        COMMENT 'Beschreibung: Anzeigename der Gruppe; Mögliche Werte: VARCHAR(120)',

    slug VARCHAR(120) NOT NULL
        COMMENT 'Beschreibung: Maschinell lesbarer Slug der Gruppe; Mögliche Werte: VARCHAR(120)',

    description TEXT NULL
        COMMENT 'Beschreibung: Beschreibung der Gruppe; Mögliche Werte: TEXT',

    is_active TINYINT(1) NOT NULL DEFAULT 1
        COMMENT 'Beschreibung: Aktiv-Flag der Gruppe (0/1); Mögliche Werte: TINYINT(1)',

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        COMMENT 'Beschreibung: Erstellungszeitpunkt der Gruppe; Mögliche Werte: DATETIME',

    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        COMMENT 'Beschreibung: Aktualisierungszeitpunkt der Gruppe; Mögliche Werte: DATETIME',

    PRIMARY KEY (id),
    UNIQUE KEY uniq_ids_groups_uuid (uuid),
    UNIQUE KEY uniq_ids_groups_slug (slug),
    KEY idx_ids_groups_is_active (is_active)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Beschreibung: Gruppen für Berechtigungen; Mögliche Werte: InnoDB, utf8mb4';

-- Pivot: group ↔ user
CREATE TABLE IF NOT EXISTS ids_group_user (
    group_id BIGINT UNSIGNED NOT NULL
        COMMENT 'FK -> ids_groups.id',

    user_id BIGINT UNSIGNED NOT NULL
        COMMENT 'FK -> ids_users.id',

    role VARCHAR(50) NULL
        COMMENT 'Optionale Rolle innerhalb der Gruppe (z.B. owner, manager)',

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        COMMENT 'Zeitpunkt der Aufnahme in die Gruppe',

    PRIMARY KEY (group_id, user_id),
    KEY idx_ids_group_user_user_id (user_id),
    CONSTRAINT fk_ids_group_user_group FOREIGN KEY (group_id) REFERENCES ids_groups(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_ids_group_user_user FOREIGN KEY (user_id) REFERENCES ids_users(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Pivot: Zuordnung Gruppen -> User';

-- Pivot: area ↔ group
CREATE TABLE IF NOT EXISTS ids_area_group (
    area_id BIGINT UNSIGNED NOT NULL
        COMMENT 'FK -> ids_areas.id',

    group_id BIGINT UNSIGNED NOT NULL
        COMMENT 'FK -> ids_groups.id',

    access_level VARCHAR(30) NOT NULL DEFAULT 'view'
        COMMENT 'Beschreibung: Zugriffsebene z.B. view/edit',

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        COMMENT 'Zeitpunkt der Zuordnung',

    PRIMARY KEY (area_id, group_id),
    KEY idx_ids_area_group_group_id (group_id),
    CONSTRAINT fk_ids_area_group_area FOREIGN KEY (area_id) REFERENCES ids_areas(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_ids_area_group_group FOREIGN KEY (group_id) REFERENCES ids_groups(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Pivot: Zuordnung Areas -> Gruppen';

-- Optional: per-user area ordering (kept for future use)
CREATE TABLE IF NOT EXISTS ids_area_user_order (
    user_id BIGINT UNSIGNED NOT NULL
        COMMENT 'FK -> ids_users.id',

    area_id BIGINT UNSIGNED NOT NULL
        COMMENT 'FK -> ids_areas.id',

    position INT NOT NULL
        COMMENT 'Position für user-spezifische Reihenfolge (kleiner = früher angezeigt)',

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        COMMENT 'Zeitpunkt der Aufzeichnung',

    PRIMARY KEY (user_id, area_id),
    KEY idx_ids_area_user_order_user (user_id, position),
    CONSTRAINT fk_ids_area_user_order_user FOREIGN KEY (user_id) REFERENCES ids_users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_ids_area_user_order_area FOREIGN KEY (area_id) REFERENCES ids_areas(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Reihenfolge-Tabelle für Nutzer-spezifische Area-Order';
