SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;

SET @admin_email := CONVERT('jan.ole.schmiedecke@student.hu-berlin.de' USING utf8mb4) COLLATE utf8mb4_unicode_ci;

SET @portal_system_id := (
    SELECT id
    FROM ids_systems
    WHERE key_name = 'portal'
    LIMIT 1
);

SET @identity_system_id := (
    SELECT id
    FROM ids_systems
    WHERE key_name = 'identity'
    LIMIT 1
);

INSERT INTO ids_permissions (
    system_id,
    key_name,
    name,
    description,
    category,
    is_active,
    is_system,
    deprecated_at,
    deprecated_reason,
    created_at,
    updated_at
)
SELECT
    @portal_system_id,
    'portal.verwaltung.dashboard.view',
    'Verwaltungsdashboard anzeigen',
    'Darf das Verwaltungs- und Administrationsdashboard öffnen.',
    'Verwaltung',
    1,
    1,
    NULL,
    NULL,
    NOW(),
    NOW()
WHERE @portal_system_id IS NOT NULL
ON DUPLICATE KEY UPDATE
    system_id = VALUES(system_id),
    name = VALUES(name),
    description = VALUES(description),
    category = VALUES(category),
    is_active = 1,
    is_system = 1,
    deprecated_at = NULL,
    deprecated_reason = NULL,
    updated_at = NOW();

INSERT IGNORE INTO ids_group_permissions (group_id, permission_id, created_at)
SELECT
    g.id,
    p.id,
    NOW()
FROM ids_groups g
JOIN ids_systems s ON s.id = g.system_id
JOIN ids_permissions p ON p.system_id = s.id
WHERE s.key_name = 'portal'
  AND g.key_name = 'administrator'
  AND p.key_name = 'portal.verwaltung.dashboard.view'
  AND g.is_active = 1
  AND p.is_active = 1;

INSERT IGNORE INTO ids_group_permissions (group_id, permission_id, created_at)
SELECT
    g.id,
    p.id,
    NOW()
FROM ids_groups g
JOIN ids_systems s ON s.id = g.system_id
JOIN ids_permissions p ON p.system_id = s.id
WHERE s.key_name = 'portal'
  AND g.key_name = 'administrator'
  AND p.key_name LIKE 'portal.%'
  AND g.is_active = 1
  AND p.is_active = 1;

SET @configured_admin_subject_id := (
    SELECT person.subject_id
    FROM ids_users u
    JOIN ids_persons person ON person.id = u.person_id
    WHERE CONVERT(u.email USING utf8mb4) COLLATE utf8mb4_unicode_ci = @admin_email
    LIMIT 1
);

INSERT IGNORE INTO ids_subject_groups (
    subject_id,
    group_id,
    assigned_by_subject_id,
    assigned_at,
    expires_at,
    note,
    created_at,
    updated_at
)
SELECT
    @configured_admin_subject_id,
    g.id,
    NULL,
    NOW(),
    NULL,
    'Admin dashboard access repair: configured admin gets portal.administrator',
    NOW(),
    NOW()
FROM ids_groups g
JOIN ids_systems s ON s.id = g.system_id
WHERE @configured_admin_subject_id IS NOT NULL
  AND s.key_name = 'portal'
  AND g.key_name = 'administrator';

INSERT IGNORE INTO ids_subject_groups (
    subject_id,
    group_id,
    assigned_by_subject_id,
    assigned_at,
    expires_at,
    note,
    created_at,
    updated_at
)
SELECT DISTINCT
    sg_identity.subject_id,
    portal_admin_group.id,
    NULL,
    NOW(),
    NULL,
    'Admin dashboard access repair: identity.administrator also gets portal.administrator',
    NOW(),
    NOW()
FROM ids_subject_groups sg_identity
JOIN ids_groups identity_group ON identity_group.id = sg_identity.group_id
JOIN ids_systems identity_system ON identity_system.id = identity_group.system_id
JOIN ids_systems portal_system ON portal_system.key_name = 'portal'
JOIN ids_groups portal_admin_group
    ON portal_admin_group.system_id = portal_system.id
   AND portal_admin_group.key_name = 'administrator'
WHERE identity_system.key_name = 'identity'
  AND identity_group.key_name = 'administrator'
  AND (sg_identity.expires_at IS NULL OR sg_identity.expires_at > NOW())
  AND identity_group.is_active = 1
  AND portal_admin_group.is_active = 1;

UPDATE ids_subjects s
SET s.permission_version = s.permission_version + 1,
    s.updated_at = NOW()
WHERE s.id IN (
    SELECT DISTINCT subject_id
    FROM ids_subject_groups
);

SELECT
    'admin_subject' AS check_name,
    @configured_admin_subject_id AS value;

SELECT
    u.email,
    sys.key_name AS system_key,
    g.key_name AS group_key
FROM ids_users u
JOIN ids_persons person ON person.id = u.person_id
JOIN ids_subject_groups sg ON sg.subject_id = person.subject_id
JOIN ids_groups g ON g.id = sg.group_id
JOIN ids_systems sys ON sys.id = g.system_id
WHERE CONVERT(u.email USING utf8mb4) COLLATE utf8mb4_unicode_ci = @admin_email
ORDER BY sys.key_name, g.key_name;

SELECT
    u.email,
    perm.key_name AS granted_permission
FROM ids_users u
JOIN ids_persons person ON person.id = u.person_id
JOIN ids_subject_groups sg ON sg.subject_id = person.subject_id
JOIN ids_groups g ON g.id = sg.group_id
JOIN ids_group_permissions gp ON gp.group_id = g.id
JOIN ids_permissions perm ON perm.id = gp.permission_id
WHERE CONVERT(u.email USING utf8mb4) COLLATE utf8mb4_unicode_ci = @admin_email
  AND perm.key_name = 'portal.verwaltung.dashboard.view'
  AND perm.is_active = 1
LIMIT 1;
