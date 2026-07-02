SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;

UPDATE pt_menu_items mi
LEFT JOIN ids_permissions p ON p.key_name = 'portal.verwaltung.dashboard.view'
SET mi.url = '/administration',
    mi.required_permission_id = COALESCE(mi.required_permission_id, p.id),
    mi.updated_at = NOW()
WHERE mi.url = '/verwaltung'
   OR mi.slug IN ('verwaltung', 'verwaltung-dashboard', 'administration-dashboard');

UPDATE pt_menu_items mi
LEFT JOIN ids_permissions p ON p.key_name = 'identity.gruppen.view'
SET mi.url = '/administration/gruppen',
    mi.required_permission_id = COALESCE(mi.required_permission_id, p.id),
    mi.updated_at = NOW()
WHERE mi.url = '/verwaltung/gruppen'
   OR mi.slug IN ('verwaltung-gruppen', 'gruppen', 'administration-gruppen');

UPDATE pt_menu_items mi
LEFT JOIN ids_permissions p ON p.key_name = 'identity.permissions.view'
SET mi.url = '/administration/permissions',
    mi.required_permission_id = COALESCE(mi.required_permission_id, p.id),
    mi.updated_at = NOW()
WHERE mi.url IN ('/verwaltung/berechtigungen', '/verwaltung/permissions')
   OR mi.slug IN ('verwaltung-berechtigungen', 'berechtigungen', 'permissions', 'administration-permissions');

UPDATE pt_menu_items mi
LEFT JOIN ids_permissions p ON p.key_name = 'identity.systeme.view'
SET mi.url = '/administration/systeme',
    mi.required_permission_id = COALESCE(mi.required_permission_id, p.id),
    mi.updated_at = NOW()
WHERE mi.url = '/verwaltung/systeme'
   OR mi.slug IN ('verwaltung-systeme', 'systeme', 'administration-systeme');

SELECT id, title, slug, url, required_permission_id
FROM pt_menu_items
WHERE url LIKE '/administration%'
   OR url LIKE '/verwaltung%'
ORDER BY menu_id, order_index, id;
