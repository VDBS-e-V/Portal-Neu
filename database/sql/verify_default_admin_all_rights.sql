SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;
SET @initial_admin_email = CONVERT('local@admin.com' USING utf8mb4) COLLATE utf8mb4_unicode_ci;

SELECT
    u.email,
    p.id AS person_id,
    p.subject_id,
    s.status AS subject_status,
    s.permission_version
FROM ids_users u
JOIN ids_persons p ON p.id = u.person_id
JOIN ids_subjects s ON s.id = p.subject_id
WHERE CONVERT(u.email USING utf8mb4) COLLATE utf8mb4_unicode_ci = @initial_admin_email;

SELECT
    sys.key_name AS system_key,
    g.key_name AS group_key,
    g.name AS group_name
FROM ids_users u
JOIN ids_persons p ON p.id = u.person_id
JOIN ids_subject_groups sg ON sg.subject_id = p.subject_id
JOIN ids_groups g ON g.id = sg.group_id
JOIN ids_systems sys ON sys.id = g.system_id
WHERE CONVERT(u.email USING utf8mb4) COLLATE utf8mb4_unicode_ci = @initial_admin_email
ORDER BY sys.key_name, g.key_name;

SELECT
    COUNT(DISTINCT perm.id) AS effective_permission_count
FROM ids_users u
JOIN ids_persons person ON person.id = u.person_id
JOIN ids_subject_groups sg ON sg.subject_id = person.subject_id
JOIN ids_groups g ON g.id = sg.group_id
JOIN ids_group_permissions gp ON gp.group_id = g.id
JOIN ids_permissions perm ON perm.id = gp.permission_id
WHERE CONVERT(u.email USING utf8mb4) COLLATE utf8mb4_unicode_ci = @initial_admin_email
  AND perm.is_active = 1
  AND g.is_active = 1
  AND (sg.expires_at IS NULL OR sg.expires_at > NOW());

SELECT
    perm.key_name AS dashboard_permission
FROM ids_users u
JOIN ids_persons person ON person.id = u.person_id
JOIN ids_subject_groups sg ON sg.subject_id = person.subject_id
JOIN ids_groups g ON g.id = sg.group_id
JOIN ids_group_permissions gp ON gp.group_id = g.id
JOIN ids_permissions perm ON perm.id = gp.permission_id
WHERE CONVERT(u.email USING utf8mb4) COLLATE utf8mb4_unicode_ci = @initial_admin_email
  AND perm.key_name = 'portal.verwaltung.dashboard.view'
  AND perm.is_active = 1
LIMIT 1;
