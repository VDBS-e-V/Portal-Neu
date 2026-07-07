UPDATE `pt_menu_items` `mi`
JOIN `ids_permissions` `p` ON `p`.`key_name` = 'portal.verwaltung.dashboard.view'
SET `mi`.`required_permission_id` = `p`.`id`
WHERE `mi`.`url` IN ('/verwaltung', '/administration')
   OR `mi`.`slug` IN ('verwaltung', 'administration', 'dashboard');
UPDATE `pt_menu_items` `mi`
JOIN `ids_permissions` `p` ON `p`.`key_name` = 'portal.verwaltung.personen.view'
SET `mi`.`required_permission_id` = `p`.`id`
WHERE `mi`.`url` LIKE '/verwaltung/personen%'
   OR `mi`.`url` LIKE '/administration/personen%'
   OR `mi`.`slug` IN ('personen', 'people');
UPDATE `pt_menu_items` `mi`
JOIN `ids_permissions` `p` ON `p`.`key_name` = 'identity.gruppen.view'
SET `mi`.`required_permission_id` = `p`.`id`
WHERE `mi`.`url` LIKE '/verwaltung/gruppen%'
   OR `mi`.`url` LIKE '/administration/gruppen%'
   OR `mi`.`slug` IN ('gruppen', 'groups');
UPDATE `pt_menu_items` `mi`
JOIN `ids_permissions` `p` ON `p`.`key_name` = 'identity.permissions.view'
SET `mi`.`required_permission_id` = `p`.`id`
WHERE `mi`.`url` LIKE '/verwaltung/berechtigungen%'
   OR `mi`.`url` LIKE '/verwaltung/permissions%'
   OR `mi`.`url` LIKE '/administration/permissions%'
   OR `mi`.`slug` IN ('berechtigungen', 'permissions');
UPDATE `pt_menu_items` `mi`
JOIN `ids_permissions` `p` ON `p`.`key_name` = 'portal.verwaltung.einladungen.view'
SET `mi`.`required_permission_id` = `p`.`id`
WHERE `mi`.`url` LIKE '/verwaltung/einladungen%'
   OR `mi`.`url` LIKE '/administration/einladungen%'
   OR `mi`.`slug` IN ('einladungen', 'invitations');
UPDATE `pt_menu_items` `mi`
JOIN `ids_permissions` `p` ON `p`.`key_name` = 'portal.verwaltung.datenschutz.view'
SET `mi`.`required_permission_id` = `p`.`id`
WHERE `mi`.`url` LIKE '/verwaltung/datenschutz%'
   OR `mi`.`url` LIKE '/administration/datenschutz%'
   OR `mi`.`slug` IN ('datenschutz', 'privacy', 'dsgvo');
UPDATE `pt_menu_items` `mi`
JOIN `ids_permissions` `p` ON `p`.`key_name` = 'portal.verwaltung.audit.view'
SET `mi`.`required_permission_id` = `p`.`id`
WHERE `mi`.`url` LIKE '/verwaltung/audit%'
   OR `mi`.`url` LIKE '/administration/audit%'
   OR `mi`.`slug` IN ('audit', 'audit-log');
UPDATE `pt_menu_items` `mi`
JOIN `ids_permissions` `p` ON `p`.`key_name` = 'portal.verwaltung.schulverzeichnis.view'
SET `mi`.`required_permission_id` = `p`.`id`
WHERE `mi`.`url` LIKE '/verwaltung/schulverzeichnis%'
   OR `mi`.`url` LIKE '/administration/schulverzeichnis%'
   OR `mi`.`slug` IN ('schulverzeichnis', 'schools');
