SELECT key_name, is_active
FROM ids_permissions
WHERE key_name IN (
    'portal.verwaltung.dashboard.view',
    'portal.verwaltung.personen.view',
    'portal.verwaltung.audit.view',
    'portal.verwaltung.entity-audit.view',
    'portal.verwaltung.datenschutz.view',
    'portal.verwaltung.einladungen.view',
    'portal.verwaltung.schulverzeichnis.view'
)
ORDER BY key_name;

SELECT sys.key_name AS system_key, g.key_name AS group_key, COUNT(p.id) AS permission_count
FROM ids_groups g
JOIN ids_systems sys ON sys.id = g.system_id
LEFT JOIN ids_group_permissions gp ON gp.group_id = g.id
LEFT JOIN ids_permissions p ON p.id = gp.permission_id AND p.is_active = 1
WHERE sys.key_name = 'portal'
  AND g.key_name = 'administrator'
GROUP BY sys.key_name, g.key_name;
