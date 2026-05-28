-- Seed: seed_initial_data.sql
-- Insert default roles, permissions, groups, admin user (replace password hash)
START TRANSACTION;

-- Roles
INSERT INTO `ids_roles` (`name`,`slug`,`description`) VALUES
  ('Administrator','admin','Full access'),
  ('Editor','editor','Edit access'),
  ('User','user','Basic user');

-- Permissions
INSERT INTO `ids_permissions` (`action`,`description`) VALUES
  ('areas.view','View areas'),
  ('areas.edit','Edit areas'),
  ('areas.manage','Manage areas'),
  ('users.manage','Manage users');

-- Map role -> permissions
-- admin -> all permissions
INSERT INTO `ids_role_permissions` (`role_id`,`permission_id`)
  SELECT r.id, p.id FROM `ids_roles` r CROSS JOIN `ids_permissions` p WHERE r.slug='admin';

-- editor -> areas.view + areas.edit
INSERT INTO `ids_role_permissions` (`role_id`,`permission_id`)
  SELECT r.id, p.id FROM `ids_roles` r JOIN `ids_permissions` p ON p.action IN ('areas.view','areas.edit') WHERE r.slug='editor';

-- Groups
INSERT INTO `ids_groups` (`name`,`slug`,`description`) VALUES
  ('Administrators','administrators','Site administrators'),
  ('Editors','editors','Content editors'),
  ('Users','users','End users');

-- Admin user (replace <BCRYPT_HASH> below with a hash generated locally):
-- php -r "echo password_hash('ChangeMe123!', PASSWORD_DEFAULT).PHP_EOL;"
INSERT INTO `ids_users` (`user_uuid`,`username`,`email`,`password_hash`,`first_name`,`last_name`,`is_active`) VALUES
  (UNHEX('<USER_UUID>'),'admin','admin@example.org','<BCRYPT_HASH>','Admin','User',1);

-- Map admin user into Administrators group and admin role
INSERT INTO `ids_user_groups` (`user_id`,`group_id`,`role`)
  SELECT u.id, g.id, 'owner' FROM `ids_users` u JOIN `ids_groups` g WHERE u.username='admin' AND g.slug='administrators';

INSERT INTO `ids_user_roles` (`user_id`,`role_id`)
  SELECT u.id, r.id FROM `ids_users` u JOIN `ids_roles` r WHERE u.username='admin' AND r.slug='admin';

-- Default area
INSERT INTO `pt_areas` (`name`,`slug`,`description`,`is_public`) VALUES
  ('Main','main','Default area',1);

-- Map Administrators group to default area with management permissions
INSERT INTO `pt_areas_groups` (`area_id`,`group_id`,`permissions`)
  SELECT a.id, g.id, JSON_ARRAY('view','edit','manage') FROM `pt_areas` a JOIN `ids_groups` g WHERE a.slug='main' AND g.slug='administrators';

-- Menus: create a default menu for the Main area and example items (max 3 levels)
INSERT INTO `pt_menus` (`area_id`,`name`,`slug`,`is_default`,`created_at`)
  SELECT id,'Main Menu','main-menu',1,NOW() FROM `pt_areas` WHERE slug='main' LIMIT 1;

SET @menu_id = (SELECT id FROM `pt_menus` WHERE slug='main-menu' LIMIT 1);

-- Top-level items
INSERT INTO `pt_menu_items` (`menu_id`,`parent_id`,`title`,`slug`,`url`,`route_name`,`icon`,`order_index`,`level`,`is_active`,`created_at`)
  VALUES (@menu_id,NULL,'Home','home','/','home','icon-home',1,1,1,NOW());
SET @home_id = LAST_INSERT_ID();

INSERT INTO `pt_menu_items` (`menu_id`,`parent_id`,`title`,`slug`,`url`,`route_name`,`icon`,`order_index`,`level`,`is_active`,`created_at`)
  VALUES (@menu_id,NULL,'Dashboard','dashboard','/dashboard','dashboard','icon-dashboard',2,1,1,NOW());
SET @dashboard_id = LAST_INSERT_ID();

INSERT INTO `pt_menu_items` (`menu_id`,`parent_id`,`title`,`slug`,`url`,`route_name`,`icon`,`order_index`,`level`,`is_active`,`created_at`)
  VALUES (@menu_id,NULL,'About','about','/about','about','icon-info',3,1,1,NOW());
SET @about_id = LAST_INSERT_ID();

-- Second-level under Dashboard
INSERT INTO `pt_menu_items` (`menu_id`,`parent_id`,`title`,`slug`,`url`,`route_name`,`icon`,`order_index`,`level`,`is_active`,`created_at`)
  VALUES (@menu_id,@dashboard_id,'Analytics','dashboard-analytics','/dashboard/analytics','dashboard.analytics','icon-analytics',1,2,1,NOW());
SET @analytics_id = LAST_INSERT_ID();

INSERT INTO `pt_menu_items` (`menu_id`,`parent_id`,`title`,`slug`,`url`,`route_name`,`icon`,`order_index`,`level`,`is_active`,`created_at`)
  VALUES (@menu_id,@dashboard_id,'Reports','dashboard-reports','/dashboard/reports','dashboard.reports','icon-reports',2,2,1,NOW());
SET @reports_id = LAST_INSERT_ID();

-- Third-level under Analytics
INSERT INTO `pt_menu_items` (`menu_id`,`parent_id`,`title`,`slug`,`url`,`route_name`,`icon`,`order_index`,`level`,`is_active`,`created_at`)
  VALUES (@menu_id,@analytics_id,'Realtime','dashboard-analytics-realtime','/dashboard/analytics/realtime','dashboard.analytics.realtime','icon-realtime',1,3,1,NOW());


COMMIT;
