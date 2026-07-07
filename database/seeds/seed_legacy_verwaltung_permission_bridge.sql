SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;

SET @portal_system_id := (SELECT id FROM ids_systems WHERE key_name = 'portal' LIMIT 1);

INSERT INTO ids_permissions (
    system_id,
    key_name,
    name,
    description,
    category,
    is_active,
    is_system,
    created_at,
    updated_at
)
SELECT @portal_system_id, 'portal.verwaltung.entity-audit.view', 'Entity-Audit anzeigen', 'Darf alte Entity-Audit-Ansichten der Verwaltung öffnen.', 'Verwaltung', 1, 1, NOW(), NOW()
WHERE @portal_system_id IS NOT NULL
ON DUPLICATE KEY UPDATE name = VALUES(name), description = VALUES(description), category = VALUES(category), is_active = 1, updated_at = NOW();

INSERT INTO ids_permissions (
    system_id,
    key_name,
    name,
    description,
    category,
    is_active,
    is_system,
    created_at,
    updated_at
)
SELECT @portal_system_id, 'portal.verwaltung.datenschutz.view', 'Datenschutz anzeigen', 'Darf Datenschutz-Ansichten der Verwaltung öffnen.', 'Verwaltung', 1, 1, NOW(), NOW()
WHERE @portal_system_id IS NOT NULL
ON DUPLICATE KEY UPDATE name = VALUES(name), description = VALUES(description), category = VALUES(category), is_active = 1, updated_at = NOW();

INSERT INTO ids_permissions (
    system_id,
    key_name,
    name,
    description,
    category,
    is_active,
    is_system,
    created_at,
    updated_at
)
SELECT @portal_system_id, 'portal.verwaltung.einladungen.view', 'Einladungen anzeigen', 'Darf Einladungen in der Verwaltung anzeigen.', 'Verwaltung', 1, 1, NOW(), NOW()
WHERE @portal_system_id IS NOT NULL
ON DUPLICATE KEY UPDATE name = VALUES(name), description = VALUES(description), category = VALUES(category), is_active = 1, updated_at = NOW();

INSERT INTO ids_permissions (
    system_id,
    key_name,
    name,
    description,
    category,
    is_active,
    is_system,
    created_at,
    updated_at
)
SELECT @portal_system_id, 'portal.verwaltung.schulverzeichnis.view', 'Schulverzeichnis anzeigen', 'Darf das Schulverzeichnis in der Verwaltung anzeigen.', 'Verwaltung', 1, 1, NOW(), NOW()
WHERE @portal_system_id IS NOT NULL
ON DUPLICATE KEY UPDATE name = VALUES(name), description = VALUES(description), category = VALUES(category), is_active = 1, updated_at = NOW();

INSERT IGNORE INTO ids_group_permissions (group_id, permission_id, created_at)
SELECT g.id, p.id, NOW()
FROM ids_groups g
JOIN ids_systems s ON s.id = g.system_id
JOIN ids_permissions p ON p.system_id = s.id
WHERE s.key_name = 'portal'
  AND g.key_name = 'administrator'
  AND g.is_active = 1
  AND p.is_active = 1;

UPDATE ids_subjects sub
JOIN ids_subject_groups sg ON sg.subject_id = sub.id
JOIN ids_groups g ON g.id = sg.group_id
JOIN ids_systems s ON s.id = g.system_id
SET sub.permission_version = sub.permission_version + 1,
    sub.updated_at = NOW()
WHERE s.key_name = 'portal'
  AND g.key_name = 'administrator'
  AND g.is_active = 1
  AND (sg.expires_at IS NULL OR sg.expires_at > NOW());
