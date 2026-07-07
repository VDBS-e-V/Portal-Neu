SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;
SET @initial_admin_name := CONVERT('admin' USING utf8mb4) COLLATE utf8mb4_unicode_ci;
SET @initial_admin_email := CONVERT('local@admin.com' USING utf8mb4) COLLATE utf8mb4_unicode_ci;
SET @initial_admin_password_hash := '$2y$12$7lsGn79tjk9ZF5nhxi.QlOZiWm6x.7XaZ0BXTOTYT1.CA4GBCLpPy';

SET @initial_admin_person_id := (
    SELECT u.`person_id`
    FROM `ids_users` u
    WHERE CONVERT(u.`email` USING utf8mb4) COLLATE utf8mb4_unicode_ci = @initial_admin_email COLLATE utf8mb4_unicode_ci
    LIMIT 1
);

INSERT INTO `ids_persons` (`person_uuid`, `display_name`, `status`)
SELECT UNHEX(REPLACE(UUID(), '-', '')), @initial_admin_name, 'active'
WHERE @initial_admin_person_id IS NULL;

SET @created_initial_admin_person_id := LAST_INSERT_ID();
SET @initial_admin_person_id := IF(@initial_admin_person_id IS NULL, @created_initial_admin_person_id, @initial_admin_person_id);

INSERT INTO `ids_users` (`person_id`, `user_uuid`, `identity_subject`, `email`, `password_hash`, `status`, `email_verified_at`)
SELECT
    @initial_admin_person_id,
    UNHEX(REPLACE(UUID(), '-', '')),
    NULL,
    @initial_admin_email,
    @initial_admin_password_hash,
    'active',
    CURRENT_TIMESTAMP
WHERE NOT EXISTS (
    SELECT 1
    FROM `ids_users` u
    WHERE CONVERT(u.`email` USING utf8mb4) COLLATE utf8mb4_unicode_ci = @initial_admin_email COLLATE utf8mb4_unicode_ci
);

UPDATE `ids_persons`
SET `display_name` = @initial_admin_name,
    `status` = 'active'
WHERE `id` = @initial_admin_person_id;

UPDATE `ids_users`
SET `password_hash` = @initial_admin_password_hash,
    `status` = 'active',
    `email_verified_at` = COALESCE(`email_verified_at`, CURRENT_TIMESTAMP)
WHERE CONVERT(`email` USING utf8mb4) COLLATE utf8mb4_unicode_ci = @initial_admin_email COLLATE utf8mb4_unicode_ci;

INSERT IGNORE INTO `ids_subjects` (`uuid`, `status`)
SELECT (LOWER(CONCAT(
    SUBSTRING(HEX(p.`person_uuid`), 1, 8), '-',
    SUBSTRING(HEX(p.`person_uuid`), 9, 4), '-',
    SUBSTRING(HEX(p.`person_uuid`), 13, 4), '-',
    SUBSTRING(HEX(p.`person_uuid`), 17, 4), '-',
    SUBSTRING(HEX(p.`person_uuid`), 21, 12)
)) COLLATE utf8mb4_unicode_ci),
'active'
FROM `ids_persons` p
WHERE p.`id` = @initial_admin_person_id
  AND p.`subject_id` IS NULL;

UPDATE `ids_persons` p
JOIN `ids_subjects` s ON s.`uuid` = (LOWER(CONCAT(
    SUBSTRING(HEX(p.`person_uuid`), 1, 8), '-',
    SUBSTRING(HEX(p.`person_uuid`), 9, 4), '-',
    SUBSTRING(HEX(p.`person_uuid`), 13, 4), '-',
    SUBSTRING(HEX(p.`person_uuid`), 17, 4), '-',
    SUBSTRING(HEX(p.`person_uuid`), 21, 12)
)) COLLATE utf8mb4_unicode_ci)
SET p.`subject_id` = s.`id`
WHERE p.`id` = @initial_admin_person_id
  AND p.`subject_id` IS NULL;

SET @initial_admin_subject_id := (
    SELECT p.`subject_id`
    FROM `ids_users` u
    JOIN `ids_persons` p ON p.`id` = u.`person_id`
    WHERE CONVERT(u.`email` USING utf8mb4) COLLATE utf8mb4_unicode_ci = @initial_admin_email COLLATE utf8mb4_unicode_ci
    LIMIT 1
);

INSERT IGNORE INTO `ids_permissions` (`system_id`, `key_name`, `name`, `description`, `category`, `is_active`, `is_system`)
SELECT s.`id`, 'portal.verwaltung.dashboard.view', 'Verwaltungsdashboard anzeigen', 'Darf das Verwaltungs- und Administrationsdashboard öffnen.', 'verwaltung.dashboard', 1, 1
FROM `ids_systems` s
WHERE s.`key_name` = 'portal';

INSERT IGNORE INTO `ids_group_permissions` (`group_id`, `permission_id`)
SELECT g.`id`, p.`id`
FROM `ids_groups` g
JOIN `ids_permissions` p ON p.`system_id` = g.`system_id`
JOIN `ids_systems` s ON s.`id` = g.`system_id`
WHERE s.`is_active` = 1
  AND g.`key_name` = 'administrator'
  AND g.`is_active` = 1
  AND p.`is_active` = 1;

INSERT IGNORE INTO `ids_subject_groups` (`subject_id`, `group_id`, `assigned_at`, `note`)
SELECT @initial_admin_subject_id, g.`id`, CURRENT_TIMESTAMP, CONCAT('Default Admin bekommt alle Admin-Gruppen: ', @initial_admin_email)
FROM `ids_groups` g
JOIN `ids_systems` s ON s.`id` = g.`system_id`
WHERE @initial_admin_subject_id IS NOT NULL
  AND s.`is_active` = 1
  AND g.`is_active` = 1
  AND g.`key_name` = 'administrator';

UPDATE `ids_subjects`
SET `permission_version` = `permission_version` + 1,
    `updated_at` = CURRENT_TIMESTAMP
WHERE `id` = @initial_admin_subject_id;

SELECT @initial_admin_name AS initial_admin_name, @initial_admin_email AS initial_admin_email, @initial_admin_subject_id AS initial_admin_subject_id;

SELECT s.`key_name` AS system_key, g.`key_name` AS group_key
FROM `ids_subject_groups` sg
JOIN `ids_groups` g ON g.`id` = sg.`group_id`
JOIN `ids_systems` s ON s.`id` = g.`system_id`
WHERE sg.`subject_id` = @initial_admin_subject_id
  AND g.`key_name` = 'administrator'
ORDER BY s.`key_name`;

SELECT COUNT(DISTINCT p.`id`) AS effective_permission_count
FROM `ids_subject_groups` sg
JOIN `ids_groups` g ON g.`id` = sg.`group_id`
JOIN `ids_group_permissions` gp ON gp.`group_id` = g.`id`
JOIN `ids_permissions` p ON p.`id` = gp.`permission_id`
WHERE sg.`subject_id` = @initial_admin_subject_id
  AND p.`is_active` = 1;
