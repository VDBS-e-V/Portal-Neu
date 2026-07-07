SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;

SELECT 'admin_groups' AS check_name, u.email, s.key_name AS system_key, g.key_name AS group_key
FROM ids_users u
JOIN ids_persons p ON p.id = u.person_id
JOIN ids_subject_groups sg ON sg.subject_id = p.subject_id
JOIN ids_groups g ON g.id = sg.group_id
JOIN ids_systems s ON s.id = g.system_id
WHERE u.email = COALESCE(@initial_admin_email, 'local@admin.com')
ORDER BY s.key_name, g.key_name;

SELECT 'cross_system_group_permissions' AS check_name, COUNT(*) AS issue_count
FROM ids_group_permissions gp
JOIN ids_groups g ON g.id = gp.group_id
JOIN ids_permissions p ON p.id = gp.permission_id
WHERE g.system_id <> p.system_id;

SELECT 'missing_admin_permissions' AS check_name, s.key_name AS system_key, COUNT(p.id) AS missing_count
FROM ids_systems s
JOIN ids_groups g ON g.system_id = s.id AND g.key_name = 'administrator'
JOIN ids_permissions p ON p.system_id = s.id AND p.is_active = 1
LEFT JOIN ids_group_permissions gp ON gp.group_id = g.id AND gp.permission_id = p.id
WHERE s.is_active = 1
  AND gp.permission_id IS NULL
GROUP BY s.key_name;

SELECT 'menu_permission_orphans' AS check_name, COUNT(*) AS issue_count
FROM pt_menu_items mi
LEFT JOIN ids_permissions p ON p.id = mi.required_permission_id
WHERE mi.required_permission_id IS NOT NULL
  AND p.id IS NULL;
