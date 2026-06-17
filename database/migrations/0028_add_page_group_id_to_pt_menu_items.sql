SET @column_exists := (
  SELECT COUNT(*)
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'pt_menu_items'
    AND COLUMN_NAME = 'page_group_id'
);

SET @sql := IF(
  @column_exists = 0,
  'ALTER TABLE `pt_menu_items`
     ADD COLUMN `page_group_id` BIGINT UNSIGNED NULL COMMENT ''Optionale PageGroup-Berechtigung für sichtbarkeitsgesteuerte Menüeinträge.''',
  'SELECT 1'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @index_exists := (
  SELECT COUNT(*)
  FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'pt_menu_items'
    AND INDEX_NAME = 'idx_pt_menu_items_page_group'
);

SET @sql := IF(
  @index_exists = 0,
  'ALTER TABLE `pt_menu_items`
     ADD INDEX `idx_pt_menu_items_page_group` (`page_group_id`)',
  'SELECT 1'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @fk_exists := (
  SELECT COUNT(*)
  FROM information_schema.REFERENTIAL_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE()
    AND CONSTRAINT_NAME = 'fk_pt_menu_items_page_group'
);

SET @sql := IF(
  @fk_exists = 0,
  'ALTER TABLE `pt_menu_items`
     ADD CONSTRAINT `fk_pt_menu_items_page_group`
       FOREIGN KEY (`page_group_id`) REFERENCES `pt_page_groups` (`id`)
       ON DELETE SET NULL',
  'SELECT 1'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
