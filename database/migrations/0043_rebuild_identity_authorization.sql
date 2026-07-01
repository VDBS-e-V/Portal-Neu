SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `ids_subject_groups`;
DROP TABLE IF EXISTS `ids_group_permissions`;
DROP TABLE IF EXISTS `ids_permission_group_permissions`;
DROP TABLE IF EXISTS `pt_permission_group_page_group_access`;
DROP TABLE IF EXISTS `pt_permission_group_area_access`;
DROP TABLE IF EXISTS `ids_person_permission_groups`;
DROP TABLE IF EXISTS `ids_user_permission_groups`;
ALTER TABLE `pt_menu_items` DROP FOREIGN KEY `fk_pt_menu_items_page_group`;
DROP TABLE IF EXISTS `pt_page_groups`;
DROP TABLE IF EXISTS `ids_permissions`;
DROP TABLE IF EXISTS `ids_groups`;
DROP TABLE IF EXISTS `ids_systems`;
DROP TABLE IF EXISTS `ids_subjects`;
DROP TABLE IF EXISTS `ids_permission_groups`;
SET FOREIGN_KEY_CHECKS = 1;
CREATE TABLE `ids_subjects` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel der stabilen digitalen Identität.',
    `uuid` CHAR(36) NOT NULL COMMENT 'Stabile externe Subject-UUID.',
    `status` ENUM('active', 'disabled', 'deleted', 'merged') NOT NULL DEFAULT 'active' COMMENT 'Subject-Status.',
    `permission_version` INT UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Wird erhöht, wenn sich effektive Rechte ändern.',
    `merged_into_subject_id` BIGINT UNSIGNED NULL COMMENT 'Ziel-Subject, wenn dieses Subject zusammengeführt wurde.',
    `merged_at` DATETIME NULL COMMENT 'Zeitpunkt der Zusammenführung.',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_ids_subjects_uuid` (`uuid`),
    KEY `idx_ids_subjects_status` (`status`),
    KEY `idx_ids_subjects_merged_into` (`merged_into_subject_id`),
    CONSTRAINT `fk_ids_subjects_merged_into`
        FOREIGN KEY (`merged_into_subject_id`) REFERENCES `ids_subjects` (`id`)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Stabile digitale Identitäten, an denen Gruppen und Rechte hängen.';
ALTER TABLE `ids_persons`
    ADD COLUMN `subject_id` BIGINT UNSIGNED NULL COMMENT 'Verweist auf ids_subjects.id; stabile digitale Identität dieser Person.' AFTER `id`;
ALTER TABLE `ids_persons`
    ADD UNIQUE KEY `uq_ids_persons_subject_id` (`subject_id`),
    ADD CONSTRAINT `fk_ids_persons_subject`
        FOREIGN KEY (`subject_id`) REFERENCES `ids_subjects` (`id`)
        ON DELETE RESTRICT
        ON UPDATE CASCADE;
CREATE TABLE `ids_systems` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel des Systems.',
    `key_name` VARCHAR(100) NOT NULL COMMENT 'Stabiler technischer System-Key, z. B. portal.',
    `name` VARCHAR(255) NOT NULL COMMENT 'Anzeigename.',
    `description` TEXT NULL COMMENT 'Beschreibung.',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'System ist aktiv nutzbar.',
    `is_external` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Extern angebundenes System.',
    `sorting` INT NOT NULL DEFAULT 100 COMMENT 'Sortierung in Oberflächen.',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_ids_systems_key_name` (`key_name`),
    KEY `idx_ids_systems_active` (`is_active`),
    KEY `idx_ids_systems_sorting` (`sorting`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Zentrale Systeme der Identity-Struktur.';
CREATE TABLE `ids_groups` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel der Gruppe.',
    `system_id` BIGINT UNSIGNED NOT NULL COMMENT 'System, zu dem diese Gruppe gehört.',
    `key_name` VARCHAR(100) NOT NULL COMMENT 'Stabiler technischer Gruppen-Key innerhalb des Systems.',
    `name` VARCHAR(255) NOT NULL COMMENT 'Anzeigename.',
    `description` TEXT NULL COMMENT 'Beschreibung.',
    `sorting` INT NOT NULL DEFAULT 100 COMMENT 'Sortierung in Oberflächen.',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Inaktive Gruppen zählen nicht für Rechte.',
    `is_system` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Systemgruppen sind vor hartem Löschen geschützt.',
    `is_default` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Wird neuen Subjects automatisch zugewiesen.',
    `is_assignable` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Darf manuell vergeben werden.',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_ids_groups_system_key` (`system_id`, `key_name`),
    KEY `idx_ids_groups_system_active` (`system_id`, `is_active`),
    KEY `idx_ids_groups_default` (`is_default`),
    CONSTRAINT `fk_ids_groups_system`
        FOREIGN KEY (`system_id`) REFERENCES `ids_systems` (`id`)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Unabhängige Gruppen pro System.';
CREATE TABLE `ids_permissions` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel der Permission.',
    `system_id` BIGINT UNSIGNED NOT NULL COMMENT 'System, zu dem die Permission gehört.',
    `key_name` VARCHAR(255) NOT NULL COMMENT 'Global eindeutiger Permission-Key, z. B. portal.verwaltung.personen.view.',
    `name` VARCHAR(255) NOT NULL COMMENT 'Anzeigename.',
    `description` TEXT NULL COMMENT 'Beschreibung.',
    `category` VARCHAR(100) NULL COMMENT 'UI-Kategorie.',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Inaktive Permissions zählen nicht.',
    `is_system` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Systempermissions werden durch Seeds/Code gepflegt.',
    `deprecated_at` DATETIME NULL COMMENT 'Zeitpunkt der fachlichen Abkündigung.',
    `deprecated_reason` TEXT NULL COMMENT 'Grund oder Ersatzpermission.',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_ids_permissions_key_name` (`key_name`),
    KEY `idx_ids_permissions_system_active` (`system_id`, `is_active`),
    KEY `idx_ids_permissions_category` (`category`),
    CONSTRAINT `fk_ids_permissions_system`
        FOREIGN KEY (`system_id`) REFERENCES `ids_systems` (`id`)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Granulare technische Permissions.';
CREATE TABLE `ids_group_permissions` (
    `group_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf ids_groups.id.',
    `permission_id` BIGINT UNSIGNED NOT NULL COMMENT 'Verweist auf ids_permissions.id.',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`group_id`, `permission_id`),
    KEY `idx_ids_group_permissions_permission` (`permission_id`),
    CONSTRAINT `fk_ids_group_permissions_group`
        FOREIGN KEY (`group_id`) REFERENCES `ids_groups` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `fk_ids_group_permissions_permission`
        FOREIGN KEY (`permission_id`) REFERENCES `ids_permissions` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Zuordnung von Gruppen zu Permissions.';
CREATE TABLE `ids_subject_groups` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Technischer Primärschlüssel der Gruppenzuweisung.',
    `subject_id` BIGINT UNSIGNED NOT NULL COMMENT 'Subject, das die Gruppe erhält.',
    `group_id` BIGINT UNSIGNED NOT NULL COMMENT 'Zugewiesene Gruppe.',
    `assigned_by_subject_id` BIGINT UNSIGNED NULL COMMENT 'Subject, das die Zuweisung vorgenommen hat.',
    `assigned_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Zuweisung.',
    `expires_at` DATETIME NULL COMMENT 'Optionaler Ablaufzeitpunkt.',
    `note` TEXT NULL COMMENT 'Optionale Notiz.',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_ids_subject_groups_subject_group` (`subject_id`, `group_id`),
    KEY `idx_ids_subject_groups_group` (`group_id`),
    KEY `idx_ids_subject_groups_expires` (`expires_at`),
    KEY `idx_ids_subject_groups_assigned_by` (`assigned_by_subject_id`),
    CONSTRAINT `fk_ids_subject_groups_subject`
        FOREIGN KEY (`subject_id`) REFERENCES `ids_subjects` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `fk_ids_subject_groups_group`
        FOREIGN KEY (`group_id`) REFERENCES `ids_groups` (`id`)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,
    CONSTRAINT `fk_ids_subject_groups_assigned_by`
        FOREIGN KEY (`assigned_by_subject_id`) REFERENCES `ids_subjects` (`id`)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Gruppenzuweisungen an Subjects, optional zeitlich begrenzt.';
