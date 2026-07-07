SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;

UPDATE pt_menu_items
SET permission_key = NULL
WHERE permission_key = '';

UPDATE pt_menu_items mi
JOIN ids_permissions p ON p.key_name = mi.permission_key
SET mi.permission_key = p.key_name
WHERE mi.permission_key IS NOT NULL;