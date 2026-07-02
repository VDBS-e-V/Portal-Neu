CREATE TABLE IF NOT EXISTS ids_identity_cleanup_markers (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    marker_key VARCHAR(191) NOT NULL,
    object_type VARCHAR(64) NOT NULL,
    object_name VARCHAR(191) NOT NULL,
    replacement VARCHAR(191) NULL,
    status VARCHAR(32) NOT NULL DEFAULT 'deprecated',
    notes TEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_ids_identity_cleanup_markers_marker_key (marker_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO ids_identity_cleanup_markers (marker_key, object_type, object_name, replacement, status, notes)
VALUES
    ('legacy-table.ids_permission_groups', 'table', 'ids_permission_groups', 'ids_groups', 'deprecated', 'Altes gruppenbasiertes Login-Rechtesystem. Das neue Modell nutzt ids_groups pro System.'),
    ('legacy-table.ids_user_permission_groups', 'table', 'ids_user_permission_groups', 'ids_subject_groups', 'deprecated', 'Alte User-Gruppen-Zuordnung. Das neue Modell nutzt ids_subject_groups mit subject_id.')
ON DUPLICATE KEY UPDATE
    replacement = VALUES(replacement),
    status = VALUES(status),
    notes = VALUES(notes),
    updated_at = CURRENT_TIMESTAMP;
