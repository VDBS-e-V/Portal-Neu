CREATE TABLE IF NOT EXISTS ids_legacy_deprecations (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    object_type VARCHAR(64) NOT NULL,
    object_name VARCHAR(191) NOT NULL,
    replacement VARCHAR(191) NULL,
    deprecated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    notes TEXT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_ids_legacy_deprecations_object (object_type, object_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO ids_legacy_deprecations (object_type, object_name, replacement, notes)
VALUES
    ('table', 'pt_page_groups', 'ids_permissions + route permission mapping', 'Deprecated durch Identity-Rechtesystem. Tabelle bleibt vorerst nur als Legacy-Datenbestand erhalten.'),
    ('table', 'pt_permission_group_page_group_access', 'ids_group_permissions', 'Deprecated durch feine Permissions je System. Keine produktive Laufzeitverwendung mehr zulassen.'),
    ('column', 'pt_menu_items.page_group_id', 'pt_menu_items.required_permission_id', 'Deprecated. Menü-Zugriff wird über Permissions gefiltert.')
ON DUPLICATE KEY UPDATE
    replacement = VALUES(replacement),
    notes = VALUES(notes);
