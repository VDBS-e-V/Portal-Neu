SET @verwaltung_area_id := (SELECT `id` FROM `pt_areas` WHERE `area_key` = 'verwaltung' LIMIT 1);
INSERT INTO `pt_menus` (`area_id`, `name`, `slug`, `is_default`, `settings`)
SELECT @verwaltung_area_id, 'Administration', 'administration', 0, NULL
WHERE @verwaltung_area_id IS NOT NULL
ON DUPLICATE KEY UPDATE
    `name` = VALUES(`name`),
    `updated_at` = CURRENT_TIMESTAMP;
SET @administration_menu_id := (SELECT `id` FROM `pt_menus` WHERE `slug` = 'administration' LIMIT 1);
DELETE FROM `pt_menu_items`
WHERE `menu_id` = @administration_menu_id
  AND `slug` IN (
      'administration-dashboard',
      'administration-personen-gruppen',
      'administration-gruppen',
      'administration-permissions',
      'administration-systeme'
  );
INSERT INTO `pt_menu_items` (`menu_id`, `page_group_id`, `parent_id`, `title`, `slug`, `url`, `route_name`, `icon`, `target`, `order_index`, `level`, `is_active`, `settings`, `required_permission_id`)
SELECT @administration_menu_id, NULL, NULL, item_title, item_slug, item_url, NULL, item_icon, '_self', item_order, 1, 1, NULL, p.`id`
FROM (
    SELECT 'Administration' AS item_title, 'administration-dashboard' AS item_slug, '/administration' AS item_url, 'settings' AS item_icon, 10 AS item_order, 'portal.verwaltung.dashboard.view' AS permission_key
    UNION ALL SELECT 'Personen-Gruppen', 'administration-personen-gruppen', '/administration/personen', 'users', 20, 'identity.subjects.groups.view'
    UNION ALL SELECT 'Gruppen', 'administration-gruppen', '/administration/gruppen', 'shield', 30, 'identity.gruppen.view'
    UNION ALL SELECT 'Permissions', 'administration-permissions', '/administration/permissions', 'key', 40, 'identity.permissions.view'
    UNION ALL SELECT 'Systeme', 'administration-systeme', '/administration/systeme', 'server', 50, 'identity.systeme.view'
) items
JOIN `ids_permissions` p ON p.`key_name` = items.permission_key
WHERE @administration_menu_id IS NOT NULL;
UPDATE `pt_menu_items` mi
LEFT JOIN `ids_permissions` p ON p.`key_name` = CASE
    WHEN mi.`url` IN ('/verwaltung', '/administration') THEN 'portal.verwaltung.dashboard.view'
    WHEN mi.`url` IN ('/verwaltung/personen', '/administration/personen') THEN 'identity.subjects.groups.view'
    WHEN mi.`url` IN ('/verwaltung/gruppen', '/administration/gruppen') THEN 'identity.gruppen.view'
    WHEN mi.`url` IN ('/verwaltung/berechtigungen', '/administration/permissions') THEN 'identity.permissions.view'
    WHEN mi.`url` IN ('/administration/systeme') THEN 'identity.systeme.view'
    ELSE NULL
END
SET mi.`required_permission_id` = p.`id`
WHERE p.`id` IS NOT NULL;
