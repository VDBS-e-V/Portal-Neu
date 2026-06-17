SELECT 'ids_persons' AS table_name, COUNT(*) AS rows_count FROM ids_persons
UNION ALL
SELECT 'ids_users', COUNT(*) FROM ids_users
UNION ALL
SELECT 'ids_permission_groups', COUNT(*) FROM ids_permission_groups
UNION ALL
SELECT 'pt_page_groups', COUNT(*) FROM pt_page_groups
UNION ALL
SELECT 'pt_audit_log', COUNT(*) FROM pt_audit_log;

SELECT
  u.id,
  u.email,
  u.status,
  u.person_id,
  p.display_name
FROM ids_users u
LEFT JOIN ids_persons p ON p.id = u.person_id
ORDER BY u.id;

SELECT
  pg.group_key,
  a.area_key,
  ppg.page_group_key
FROM ids_permission_groups pg
JOIN pt_permission_group_page_group_access access ON access.permission_group_id = pg.id
JOIN pt_page_groups ppg ON ppg.id = access.page_group_id
JOIN pt_areas a ON a.id = ppg.area_id
WHERE pg.group_key = 'verwaltung.administrator'
ORDER BY a.area_key, ppg.page_group_key;

SELECT
  action,
  entity_type,
  COUNT(*) AS count_actions
FROM pt_audit_log
GROUP BY action, entity_type
ORDER BY count_actions DESC, action ASC;
