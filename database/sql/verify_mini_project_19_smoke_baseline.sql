-- Mini-Projekt 19: Smoke baseline queries
SELECT 'subjects' AS metric, COUNT(*) AS value FROM ids_subjects
UNION ALL
SELECT 'persons', COUNT(*) FROM ids_persons
UNION ALL
SELECT 'users', COUNT(*) FROM ids_users
UNION ALL
SELECT 'systems', COUNT(*) FROM ids_systems
UNION ALL
SELECT 'groups', COUNT(*) FROM ids_groups
UNION ALL
SELECT 'permissions', COUNT(*) FROM ids_permissions
UNION ALL
SELECT 'subject_groups', COUNT(*) FROM ids_subject_groups
UNION ALL
SELECT 'group_permissions', COUNT(*) FROM ids_group_permissions;