SELECT 'active_identity_admin_subjects' AS check_name, COUNT(DISTINCT sub.id) AS count_value
FROM ids_subjects sub
INNER JOIN ids_subject_groups sg ON sg.subject_id = sub.id
INNER JOIN ids_groups g ON g.id = sg.group_id
INNER JOIN ids_systems s ON s.id = g.system_id
WHERE sub.status = 'active'
  AND s.system_key = 'identity'
  AND g.group_key = 'administrator'
  AND (sg.expires_at IS NULL OR sg.expires_at > CURRENT_TIMESTAMP);

SELECT 'cross_system_group_permissions' AS check_name, COUNT(*) AS count_value
FROM ids_group_permissions gp
INNER JOIN ids_groups g ON g.id = gp.group_id
INNER JOIN ids_permissions p ON p.id = gp.permission_id
WHERE g.system_id <> p.system_id;

SELECT 'identity_admin_missing_active_identity_permissions' AS check_name, COUNT(*) AS count_value
FROM ids_permissions p
INNER JOIN ids_systems s ON s.id = p.system_id
LEFT JOIN ids_groups g ON g.system_id = s.id AND g.group_key = 'administrator'
LEFT JOIN ids_group_permissions gp ON gp.group_id = g.id AND gp.permission_id = p.id
WHERE s.system_key = 'identity'
  AND (p.is_active IS NULL OR p.is_active = 1)
  AND gp.permission_id IS NULL;
