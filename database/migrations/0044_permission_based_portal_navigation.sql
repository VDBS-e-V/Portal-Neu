ALTER TABLE `pt_menu_items`
    ADD COLUMN `required_permission_id` BIGINT UNSIGNED NULL COMMENT 'Permission, die fuer die Sichtbarkeit dieses Menueeintrags erforderlich ist.' AFTER `page_group_id`;
ALTER TABLE `pt_menu_items`
    ADD KEY `idx_pt_menu_items_required_permission` (`required_permission_id`);
ALTER TABLE `pt_menu_items`
    ADD CONSTRAINT `fk_pt_menu_items_required_permission`
        FOREIGN KEY (`required_permission_id`) REFERENCES `ids_permissions` (`id`)
        ON DELETE SET NULL
        ON UPDATE CASCADE;
SET @portal_system_id := (SELECT `id` FROM `ids_systems` WHERE `key_name` = 'portal' LIMIT 1);
INSERT INTO `ids_permissions` (`system_id`, `key_name`, `name`, `description`, `category`, `is_active`, `is_system`)
SELECT @portal_system_id, `permission_key`, `permission_name`, `permission_description`, `permission_category`, 1, 1
FROM (
    SELECT 'portal.verwaltung.dashboard.view' AS `permission_key`, 'Verwaltungsdashboard anzeigen' AS `permission_name`, 'Darf das Verwaltungs-/Administrationsdashboard oeffnen.' AS `permission_description`, 'Verwaltung' AS `permission_category`
    UNION ALL SELECT 'portal.verwaltung.einladungen.view', 'Einladungen anzeigen', 'Darf Account-Einladungen ansehen.', 'Einladungen'
    UNION ALL SELECT 'portal.verwaltung.einladungen.create', 'Einladungen erstellen', 'Darf Account-Einladungen erstellen.', 'Einladungen'
    UNION ALL SELECT 'portal.verwaltung.einladungen.revoke', 'Einladungen widerrufen', 'Darf Account-Einladungen widerrufen.', 'Einladungen'
    UNION ALL SELECT 'portal.verwaltung.datenschutz.view', 'DSGVO-Vorgaenge anzeigen', 'Darf Datenschutz-/Loeschvorgaenge ansehen.', 'Datenschutz'
    UNION ALL SELECT 'portal.verwaltung.datenschutz.edit', 'DSGVO-Vorgaenge bearbeiten', 'Darf Datenschutz-/Loeschvorgaenge bearbeiten.', 'Datenschutz'
    UNION ALL SELECT 'portal.verwaltung.datenschutz.approve', 'DSGVO-Vorgaenge freigeben', 'Darf Datenschutz-/Loeschvorgaenge freigeben.', 'Datenschutz'
    UNION ALL SELECT 'portal.verwaltung.schulverzeichnis.view', 'Schulverzeichnis anzeigen', 'Darf das Schulverzeichnis ansehen.', 'Schulverzeichnis'
    UNION ALL SELECT 'portal.verwaltung.schulverzeichnis.edit', 'Schulverzeichnis bearbeiten', 'Darf Daten im Schulverzeichnis bearbeiten.', 'Schulverzeichnis'
) AS `permissions_to_insert`
WHERE @portal_system_id IS NOT NULL
ON DUPLICATE KEY UPDATE
    `name` = VALUES(`name`),
    `description` = VALUES(`description`),
    `category` = VALUES(`category`),
    `is_active` = 1,
    `updated_at` = CURRENT_TIMESTAMP;
INSERT IGNORE INTO `ids_group_permissions` (`group_id`, `permission_id`)
SELECT `g`.`id`, `p`.`id`
FROM `ids_groups` `g`
JOIN `ids_systems` `s` ON `s`.`id` = `g`.`system_id` AND `s`.`key_name` = 'portal'
JOIN `ids_permissions` `p` ON `p`.`system_id` = `s`.`id`
WHERE `g`.`key_name` = 'administrator'
  AND `p`.`is_active` = 1;
INSERT IGNORE INTO `ids_group_permissions` (`group_id`, `permission_id`)
SELECT `g`.`id`, `p`.`id`
FROM `ids_groups` `g`
JOIN `ids_systems` `s` ON `s`.`id` = `g`.`system_id` AND `s`.`key_name` = 'portal'
JOIN `ids_permissions` `p` ON `p`.`system_id` = `s`.`id`
WHERE `g`.`key_name` = 'verwaltung'
  AND `p`.`key_name` IN (
      'portal.verwaltung.dashboard.view',
      'portal.verwaltung.personen.view',
      'portal.verwaltung.personen.create',
      'portal.verwaltung.personen.edit',
      'portal.verwaltung.personen.export',
      'portal.verwaltung.einladungen.view',
      'portal.verwaltung.einladungen.create',
      'portal.verwaltung.einladungen.revoke',
      'portal.verwaltung.datenschutz.view',
      'portal.verwaltung.datenschutz.edit',
      'portal.verwaltung.schulverzeichnis.view',
      'portal.verwaltung.schulverzeichnis.edit',
      'portal.verwaltung.audit.view'
  );
