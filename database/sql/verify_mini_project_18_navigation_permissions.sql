SELECT 'pt_menu_items.permission_key exists' AS check_name,
       COUNT(*) AS ok
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'pt_menu_items'
  AND COLUMN_NAME = 'permission_key';

SELECT 'pt_menu_items.page_group_id absent' AS check_name,
       CASE WHEN COUNT(*) = 0 THEN 1 ELSE 0 END AS ok
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'pt_menu_items'
  AND COLUMN_NAME = 'page_group_id';