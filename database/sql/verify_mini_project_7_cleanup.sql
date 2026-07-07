SELECT COUNT(*) AS pt_menu_items_with_page_group_id
FROM pt_menu_items
WHERE page_group_id IS NOT NULL;
