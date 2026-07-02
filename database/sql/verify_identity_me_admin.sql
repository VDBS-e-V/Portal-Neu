SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;

SET @admin_email := CONVERT(COALESCE(@admin_email, 'local@admin.com') USING utf8mb4) COLLATE utf8mb4_unicode_ci;

SELECT
    u.email,
    s.uuid AS subject_uuid,
    s.status AS subject_status,
    s.permission_version
FROM ids_users u
JOIN ids_persons p ON p.id = u.person_id
JOIN ids_subjects s ON s.id = p.subject_id
WHERE CONVERT(u.email USING utf8mb4) COLLATE utf8mb4_unicode_ci = @admin_email;

SELECT
    sys.key_name AS system_key,
    g.key_name AS group_key,
    COUNT(DISTINCT perm.id) AS permission_count
FROM ids_users u
JOIN ids_persons p ON p.id = u.person_id
JOIN ids_subject_groups sg ON sg.subject_id = p.subject_id
JOIN ids_groups g ON g.id = sg.group_id
JOIN ids_systems sys ON sys.id = g.system_id
LEFT JOIN ids_group_permissions gp ON gp.group_id = g.id
LEFT JOIN ids_permissions perm ON perm.id = gp.permission_id AND perm.is_active = 1
WHERE CONVERT(u.email USING utf8mb4) COLLATE utf8mb4_unicode_ci = @admin_email
GROUP BY sys.key_name, g.key_name
ORDER BY sys.key_name, g.key_name;
