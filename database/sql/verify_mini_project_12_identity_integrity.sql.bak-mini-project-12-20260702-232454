SELECT 'groups_without_system' AS check_name, COUNT(*) AS count_value
FROM ids_groups g
LEFT JOIN ids_systems s ON s.id = g.system_id
WHERE s.id IS NULL;

SELECT 'permissions_without_system' AS check_name, COUNT(*) AS count_value
FROM ids_permissions p
LEFT JOIN ids_systems s ON s.id = p.system_id
WHERE s.id IS NULL;

SELECT 'group_permissions_missing_refs' AS check_name, COUNT(*) AS count_value
FROM ids_group_permissions gp
LEFT JOIN ids_groups g ON g.id = gp.group_id
LEFT JOIN ids_permissions p ON p.id = gp.permission_id
WHERE g.id IS NULL OR p.id IS NULL;

SELECT 'group_permission_system_mismatch' AS check_name, COUNT(*) AS count_value
FROM ids_group_permissions gp
INNER JOIN ids_groups g ON g.id = gp.group_id
INNER JOIN ids_permissions p ON p.id = gp.permission_id
WHERE g.system_id <> p.system_id;

SELECT 'subject_groups_missing_refs' AS check_name, COUNT(*) AS count_value
FROM ids_subject_groups sg
LEFT JOIN ids_subjects s ON s.id = sg.subject_id
LEFT JOIN ids_groups g ON g.id = sg.group_id
WHERE s.id IS NULL OR g.id IS NULL;

SELECT *
FROM ids_identity_cleanup_markers
WHERE marker_key IN ('legacy-table.ids_permission_groups', 'legacy-table.ids_user_permission_groups');
