SELECT 'ids_permission_groups' AS object_name,
       CASE WHEN COUNT(*) = 0 THEN 'dropped' ELSE 'exists' END AS status
FROM information_schema.tables
WHERE table_schema = DATABASE()
  AND table_name = 'ids_permission_groups'
UNION ALL
SELECT 'ids_user_permission_groups' AS object_name,
       CASE WHEN COUNT(*) = 0 THEN 'dropped' ELSE 'exists' END AS status
FROM information_schema.tables
WHERE table_schema = DATABASE()
  AND table_name = 'ids_user_permission_groups'
UNION ALL
SELECT 'ids_person_permission_groups' AS object_name,
       CASE WHEN COUNT(*) = 0 THEN 'dropped' ELSE 'exists' END AS status
FROM information_schema.tables
WHERE table_schema = DATABASE()
  AND table_name = 'ids_person_permission_groups';
