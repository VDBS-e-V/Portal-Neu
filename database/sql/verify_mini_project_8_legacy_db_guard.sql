SELECT object_type, object_name, replacement, deprecated_at
FROM ids_legacy_deprecations
WHERE object_name IN ('pt_page_groups', 'pt_permission_group_page_group_access', 'pt_menu_items.page_group_id')
ORDER BY object_type, object_name;

SELECT COUNT(*) AS menu_items_with_page_group_id
FROM pt_menu_items
WHERE page_group_id IS NOT NULL;
