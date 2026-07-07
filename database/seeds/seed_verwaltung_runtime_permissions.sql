SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;

SET @portal_system_id := (SELECT id FROM ids_systems WHERE key_name = 'portal' LIMIT 1);

INSERT INTO ids_permissions (system_id, key_name, name, description, category, is_active, is_system, created_at, updated_at)
SELECT @portal_system_id, 'portal.verwaltung.dashboard.view', 'Verwaltungsdashboard anzeigen', 'Darf das Verwaltungsdashboard öffnen.', 'Verwaltung', 1, 1, NOW(), NOW()
WHERE @portal_system_id IS NOT NULL
ON DUPLICATE KEY UPDATE name = VALUES(name), description = VALUES(description), category = VALUES(category), is_active = 1, updated_at = NOW();

INSERT INTO ids_permissions (system_id, key_name, name, description, category, is_active, is_system, created_at, updated_at)
SELECT @portal_system_id, 'portal.verwaltung.personen.view', 'Personen anzeigen', 'Darf Personen in der Verwaltung anzeigen.', 'Verwaltung', 1, 1, NOW(), NOW()
WHERE @portal_system_id IS NOT NULL
ON DUPLICATE KEY UPDATE name = VALUES(name), description = VALUES(description), category = VALUES(category), is_active = 1, updated_at = NOW();

INSERT INTO ids_permissions (system_id, key_name, name, description, category, is_active, is_system, created_at, updated_at)
SELECT @portal_system_id, 'portal.verwaltung.audit.view', 'Audit-Log anzeigen', 'Darf Audit-Log-Einträge anzeigen.', 'Verwaltung', 1, 1, NOW(), NOW()
WHERE @portal_system_id IS NOT NULL
ON DUPLICATE KEY UPDATE name = VALUES(name), description = VALUES(description), category = VALUES(category), is_active = 1, updated_at = NOW();

INSERT INTO ids_permissions (system_id, key_name, name, description, category, is_active, is_system, created_at, updated_at)
SELECT @portal_system_id, 'portal.verwaltung.entity-audit.view', 'Objekt-Audit anzeigen', 'Darf objektbezogene Audit-Einträge anzeigen.', 'Verwaltung', 1, 1, NOW(), NOW()
WHERE @portal_system_id IS NOT NULL
ON DUPLICATE KEY UPDATE name = VALUES(name), description = VALUES(description), category = VALUES(category), is_active = 1, updated_at = NOW();

INSERT INTO ids_permissions (system_id, key_name, name, description, category, is_active, is_system, created_at, updated_at)
SELECT @portal_system_id, 'portal.verwaltung.datenschutz.view', 'Datenschutz anzeigen', 'Darf Datenschutz- und Löschanfragen anzeigen.', 'Verwaltung', 1, 1, NOW(), NOW()
WHERE @portal_system_id IS NOT NULL
ON DUPLICATE KEY UPDATE name = VALUES(name), description = VALUES(description), category = VALUES(category), is_active = 1, updated_at = NOW();

INSERT INTO ids_permissions (system_id, key_name, name, description, category, is_active, is_system, created_at, updated_at)
SELECT @portal_system_id, 'portal.verwaltung.einladungen.view', 'Einladungen anzeigen', 'Darf Einladungen in der Verwaltung anzeigen.', 'Verwaltung', 1, 1, NOW(), NOW()
WHERE @portal_system_id IS NOT NULL
ON DUPLICATE KEY UPDATE name = VALUES(name), description = VALUES(description), category = VALUES(category), is_active = 1, updated_at = NOW();

INSERT INTO ids_permissions (system_id, key_name, name, description, category, is_active, is_system, created_at, updated_at)
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
  AND p.is_active = 1;

UPDATE ids_subjects s
JOIN ids_persons person ON person.subject_id = s.id
JOIN ids_subject_groups sg ON sg.subject_id = s.id
JOIN ids_groups g ON g.id = sg.group_id
JOIN ids_systems sys ON sys.id = g.system_id
SET s.permission_version = s.permission_version + 1,
    s.updated_at = NOW()
WHERE sys.key_name = 'portal'
  AND g.key_name = 'administrator';
