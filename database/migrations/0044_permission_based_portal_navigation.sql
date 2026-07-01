-- Mini-Projekt 2: Portal-Menüs und Route-Schutz auf feine Permissions umstellen.
-- Voraussetzung: Mini-Projekt 1 ist angewendet und ids_permissions existiert.

SET @database_name := DATABASE();

-- 1) pt_menu_items.required_permission_id ergänzen.
SET @has_required_permission_id := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @database_name
      AND TABLE_NAME = 'pt_menu_items'
      AND COLUMN_NAME = 'required_permission_id'
);

SET @sql := IF(
    @has_required_permission_id = 0,
    'ALTER TABLE `pt_menu_items`
        ADD COLUMN `required_permission_id` BIGINT UNSIGNED NULL COMMENT ''Permission, die für die Sichtbarkeit dieses Menüeintrags erforderlich ist.'' AFTER `page_group_id`',
    'SELECT ''pt_menu_items.required_permission_id already exists'''
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 2) Index ergänzen.
SET @has_required_permission_index := (
    SELECT COUNT(*)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = @database_name
      AND TABLE_NAME = 'pt_menu_items'
      AND INDEX_NAME = 'idx_pt_menu_items_required_permission'
);

SET @sql := IF(
    @has_required_permission_index = 0,
    'ALTER TABLE `pt_menu_items`
        ADD KEY `idx_pt_menu_items_required_permission` (`required_permission_id`)',
    'SELECT ''idx_pt_menu_items_required_permission already exists'''
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 3) Foreign Key ergänzen.
SET @has_required_permission_fk := (
    SELECT COUNT(*)
    FROM information_schema.TABLE_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = @database_name
      AND TABLE_NAME = 'pt_menu_items'
      AND CONSTRAINT_NAME = 'fk_pt_menu_items_required_permission'
);

SET @sql := IF(
    @has_required_permission_fk = 0,
    'ALTER TABLE `pt_menu_items`
        ADD CONSTRAINT `fk_pt_menu_items_required_permission`
        FOREIGN KEY (`required_permission_id`) REFERENCES `ids_permissions` (`id`)
        ON DELETE SET NULL
        ON UPDATE CASCADE',
    'SELECT ''fk_pt_menu_items_required_permission already exists'''
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 4) Zusätzliche Portal-Permissions ergänzen, die für vorhandene Portal-/Verwaltungsseiten gebraucht werden.
SET @portal_system_id := (SELECT id FROM ids_systems WHERE key_name = 'portal' LIMIT 1);
SET @identity_system_id := (SELECT id FROM ids_systems WHERE key_name = 'identity' LIMIT 1);

INSERT INTO ids_permissions (system_id, key_name, name, description, category, is_active, is_system)
SELECT @portal_system_id, permission_key, permission_name, permission_description, permission_category, 1, 1
FROM (
    SELECT 'portal.verwaltung.dashboard.view' AS permission_key, 'Verwaltungsdashboard anzeigen' AS permission_name, 'Darf das Verwaltungs-/Administrationsdashboard öffnen.' AS permission_description, 'Verwaltung' AS permission_category
    UNION ALL SELECT 'portal.verwaltung.einladungen.view', 'Einladungen anzeigen', 'Darf Account-Einladungen ansehen.', 'Einladungen'
    UNION ALL SELECT 'portal.verwaltung.einladungen.create', 'Einladungen erstellen', 'Darf Account-Einladungen erstellen.', 'Einladungen'
    UNION ALL SELECT 'portal.verwaltung.einladungen.revoke', 'Einladungen widerrufen', 'Darf Account-Einladungen widerrufen.', 'Einladungen'
    UNION ALL SELECT 'portal.verwaltung.datenschutz.view', 'DSGVO-Vorgänge anzeigen', 'Darf Datenschutz-/Löschvorgänge ansehen.', 'Datenschutz'
    UNION ALL SELECT 'portal.verwaltung.datenschutz.edit', 'DSGVO-Vorgänge bearbeiten', 'Darf Datenschutz-/Löschvorgänge bearbeiten.', 'Datenschutz'
    UNION ALL SELECT 'portal.verwaltung.datenschutz.approve', 'DSGVO-Vorgänge freigeben', 'Darf Datenschutz-/Löschvorgänge freigeben.', 'Datenschutz'
    UNION ALL SELECT 'portal.verwaltung.schulverzeichnis.view', 'Schulverzeichnis anzeigen', 'Darf das Schulverzeichnis ansehen.', 'Schulverzeichnis'
    UNION ALL SELECT 'portal.verwaltung.schulverzeichnis.edit', 'Schulverzeichnis bearbeiten', 'Darf Daten im Schulverzeichnis bearbeiten.', 'Schulverzeichnis'
) AS permissions_to_insert
WHERE @portal_system_id IS NOT NULL
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    description = VALUES(description),
    category = VALUES(category),
    is_active = 1,
    updated_at = CURRENT_TIMESTAMP;

-- 5) Portal-Administrator bekommt alle portal.* Permissions.
INSERT IGNORE INTO ids_group_permissions (group_id, permission_id)
SELECT g.id, p.id
FROM ids_groups g
JOIN ids_systems s ON s.id = g.system_id AND s.key_name = 'portal'
JOIN ids_permissions p ON p.system_id = s.id
WHERE g.key_name = 'administrator'
  AND p.is_active = 1;

-- 6) Portal-Verwaltung bekommt operative Verwaltungsrechte, aber keine generellen Developer-/Systemrechte.
INSERT IGNORE INTO ids_group_permissions (group_id, permission_id)
SELECT g.id, p.id
FROM ids_groups g
JOIN ids_systems s ON s.id = g.system_id AND s.key_name = 'portal'
JOIN ids_permissions p ON p.system_id = s.id
WHERE g.key_name = 'verwaltung'
  AND p.key_name IN (
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

-- 7) Wenn pt_menu_items.page_group_id noch existiert, bleibt die Spalte vorerst als Altbestand erhalten.
--    Die neue Laufzeitlogik ignoriert page_group_id vollständig.
--    Hard Drop der Spalte erst nach Sichtprüfung im Projekt:
--    ALTER TABLE pt_menu_items DROP FOREIGN KEY <alter_fk_name>;
--    ALTER TABLE pt_menu_items DROP COLUMN page_group_id;
