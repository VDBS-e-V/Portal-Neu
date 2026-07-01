/* --------------------------------------------------------------------------
   Mini-Projekt 1: Identity-Rechte-System Seed
   Benötigt den Platzhalter <INITIAL_ADMIN_EMAIL>. Er wird durch den Patch in
   patches/bin-console-initial-admin-placeholder.patch aus
   --initial-admin-email oder INITIAL_ADMIN_EMAIL ersetzt.
-------------------------------------------------------------------------- */

SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;
SET @initial_admin_email = CONVERT('<INITIAL_ADMIN_EMAIL>' USING utf8mb4) COLLATE utf8mb4_unicode_ci;
SET @initial_admin_password_hash = '<BCRYPT_HASH>';

INSERT INTO `ids_systems` (`key_name`, `name`, `description`, `is_active`, `is_external`, `sorting`)
VALUES
    ('identity', 'Identity', 'Zentraler Login-, Gruppen- und Permission-Kontext.', 1, 0, 10),
    ('portal', 'Portal', 'Zentrales Vereinsportal.', 1, 0, 20),
    ('bibliocollect', 'BiblioCollect', 'Bibliotheks- und Ausleihsystem.', 1, 1, 30),
    ('methodenmatrix', 'Methodenmatrix', 'Methoden- und Materialsystem.', 1, 1, 40)
ON DUPLICATE KEY UPDATE
    `name` = VALUES(`name`),
    `description` = VALUES(`description`),
    `is_active` = VALUES(`is_active`),
    `is_external` = VALUES(`is_external`),
    `sorting` = VALUES(`sorting`);

SET @system_identity = (SELECT `id` FROM `ids_systems` WHERE `key_name` = 'identity');
SET @system_portal = (SELECT `id` FROM `ids_systems` WHERE `key_name` = 'portal');
SET @system_bibliocollect = (SELECT `id` FROM `ids_systems` WHERE `key_name` = 'bibliocollect');
SET @system_methodenmatrix = (SELECT `id` FROM `ids_systems` WHERE `key_name` = 'methodenmatrix');

INSERT INTO `ids_groups` (`system_id`, `key_name`, `name`, `description`, `sorting`, `is_active`, `is_system`, `is_default`, `is_assignable`)
VALUES
    (@system_identity, 'administrator', 'Administrator', 'Vollzugriff auf Identity-Verwaltung.', 10, 1, 1, 0, 1),
    (@system_identity, 'rechteverwaltung', 'Rechteverwaltung', 'Darf Gruppen, Permissions und Zuweisungen verwalten.', 20, 1, 1, 0, 1),

    (@system_portal, 'administrator', 'Administrator', 'Vollzugriff auf das Portal.', 10, 1, 1, 0, 1),
    (@system_portal, 'verwaltung', 'Verwaltung', 'Verwaltungsfunktionen im Portal.', 20, 1, 0, 0, 1),
    (@system_portal, 'vorstand', 'Vorstand', 'Vorstandsbezogene Portalrechte.', 30, 1, 0, 0, 1),
    (@system_portal, 'teamende', 'Teamende', 'Teamenden-Rechte im Portal.', 40, 1, 0, 0, 1),
    (@system_portal, 'mitglied', 'Mitglied', 'Normale Vereinsmitgliedschaft im Portal.', 50, 1, 0, 0, 1),
    (@system_portal, 'gast', 'Gast', 'Standardgruppe für neue Subjects.', 90, 1, 1, 1, 1),
    (@system_portal, 'developer', 'Developer', 'Technische Entwicklungsrechte im Portal.', 100, 1, 1, 0, 1),

    (@system_bibliocollect, 'administrator', 'Administrator', 'Vollzugriff auf BiblioCollect.', 10, 1, 1, 0, 1),
    (@system_bibliocollect, 'verwaltung', 'Verwaltung', 'Verwaltung in BiblioCollect.', 20, 1, 0, 0, 1),
    (@system_bibliocollect, 'ausleihe', 'Ausleihe', 'Ausleihen und Rückgaben durchführen.', 30, 1, 0, 0, 1),
    (@system_bibliocollect, 'lehrkraft', 'Lehrkraft', 'Lehrkraft-Rechte in BiblioCollect.', 40, 1, 0, 0, 1),
    (@system_bibliocollect, 'schueler', 'Schüler', 'Schüler-Rechte in BiblioCollect.', 50, 1, 0, 0, 1),
    (@system_bibliocollect, 'gast', 'Gast', 'Gastzugang in BiblioCollect.', 90, 1, 1, 0, 1),
    (@system_bibliocollect, 'developer', 'Developer', 'Technische Entwicklungsrechte in BiblioCollect.', 100, 1, 1, 0, 1),

    (@system_methodenmatrix, 'administrator', 'Administrator', 'Vollzugriff auf die Methodenmatrix.', 10, 1, 1, 0, 1),
    (@system_methodenmatrix, 'verwaltung', 'Verwaltung', 'Verwaltung in der Methodenmatrix.', 20, 1, 0, 0, 1),
    (@system_methodenmatrix, 'teamende', 'Teamende', 'Teamenden-Rechte in der Methodenmatrix.', 30, 1, 0, 0, 1),
    (@system_methodenmatrix, 'gast', 'Gast', 'Gastzugang in der Methodenmatrix.', 90, 1, 1, 0, 1),
    (@system_methodenmatrix, 'developer', 'Developer', 'Technische Entwicklungsrechte in der Methodenmatrix.', 100, 1, 1, 0, 1)
ON DUPLICATE KEY UPDATE
    `name` = VALUES(`name`),
    `description` = VALUES(`description`),
    `sorting` = VALUES(`sorting`),
    `is_active` = VALUES(`is_active`),
    `is_system` = VALUES(`is_system`),
    `is_default` = VALUES(`is_default`),
    `is_assignable` = VALUES(`is_assignable`);

INSERT INTO `ids_permissions` (`system_id`, `key_name`, `name`, `description`, `category`, `is_active`, `is_system`)
VALUES
    (@system_identity, 'identity.systeme.view', 'Systeme anzeigen', 'Identity-Systeme anzeigen.', 'systeme', 1, 1),
    (@system_identity, 'identity.systeme.create', 'Systeme erstellen', 'Identity-Systeme erstellen.', 'systeme', 1, 1),
    (@system_identity, 'identity.systeme.edit', 'Systeme bearbeiten', 'Identity-Systeme bearbeiten.', 'systeme', 1, 1),
    (@system_identity, 'identity.systeme.delete', 'Systeme löschen', 'Identity-Systeme löschen oder deaktivieren.', 'systeme', 1, 1),

    (@system_identity, 'identity.permissions.view', 'Permissions anzeigen', 'Permissions anzeigen.', 'permissions', 1, 1),
    (@system_identity, 'identity.permissions.create', 'Permissions erstellen', 'Permissions erstellen.', 'permissions', 1, 1),
    (@system_identity, 'identity.permissions.edit', 'Permissions bearbeiten', 'Permissions bearbeiten oder deaktivieren.', 'permissions', 1, 1),
    (@system_identity, 'identity.permissions.delete', 'Permissions löschen', 'Permissions entfernen, falls erlaubt.', 'permissions', 1, 1),

    (@system_identity, 'identity.gruppen.view', 'Gruppen anzeigen', 'Gruppen anzeigen.', 'gruppen', 1, 1),
    (@system_identity, 'identity.gruppen.create', 'Gruppen erstellen', 'Gruppen erstellen.', 'gruppen', 1, 1),
    (@system_identity, 'identity.gruppen.edit', 'Gruppen bearbeiten', 'Gruppen bearbeiten.', 'gruppen', 1, 1),
    (@system_identity, 'identity.gruppen.delete', 'Gruppen löschen', 'Gruppen löschen oder deaktivieren.', 'gruppen', 1, 1),
    (@system_identity, 'identity.gruppen.permissions.manage', 'Gruppen-Permissions verwalten', 'Permissions einer Gruppe zuweisen oder entfernen.', 'gruppen', 1, 1),

    (@system_identity, 'identity.subjects.view', 'Subjects anzeigen', 'Subjects anzeigen.', 'subjects', 1, 1),
    (@system_identity, 'identity.subjects.groups.view', 'Subject-Gruppen anzeigen', 'Gruppen eines Subjects anzeigen.', 'subjects', 1, 1),
    (@system_identity, 'identity.subjects.groups.assign', 'Subject-Gruppen zuweisen', 'Gruppen an Subjects vergeben.', 'subjects', 1, 1),
    (@system_identity, 'identity.subjects.groups.remove', 'Subject-Gruppen entfernen', 'Gruppen von Subjects entfernen.', 'subjects', 1, 1),

    (@system_portal, 'portal.dashboard.view', 'Dashboard anzeigen', 'Portal-Dashboard anzeigen.', 'dashboard', 1, 1),
    (@system_portal, 'portal.verwaltung.personen.view', 'Personen anzeigen', 'Personenverwaltung anzeigen.', 'verwaltung.personen', 1, 1),
    (@system_portal, 'portal.verwaltung.personen.create', 'Personen erstellen', 'Personen erstellen.', 'verwaltung.personen', 1, 1),
    (@system_portal, 'portal.verwaltung.personen.edit', 'Personen bearbeiten', 'Personen bearbeiten.', 'verwaltung.personen', 1, 1),
    (@system_portal, 'portal.verwaltung.personen.delete', 'Personen löschen', 'Personen löschen oder deaktivieren.', 'verwaltung.personen', 1, 1),
    (@system_portal, 'portal.verwaltung.personen.export', 'Personen exportieren', 'Personendaten exportieren.', 'verwaltung.personen', 1, 1),
    (@system_portal, 'portal.verwaltung.audit.view', 'Audit anzeigen', 'Audit-Log anzeigen.', 'verwaltung.audit', 1, 1),

    (@system_bibliocollect, 'bibliocollect.medien.view', 'Medien anzeigen', 'Medien anzeigen.', 'medien', 1, 1),
    (@system_bibliocollect, 'bibliocollect.medien.create', 'Medien erstellen', 'Medien erstellen.', 'medien', 1, 1),
    (@system_bibliocollect, 'bibliocollect.medien.edit', 'Medien bearbeiten', 'Medien bearbeiten.', 'medien', 1, 1),
    (@system_bibliocollect, 'bibliocollect.medien.delete', 'Medien löschen', 'Medien löschen.', 'medien', 1, 1),
    (@system_bibliocollect, 'bibliocollect.ausleihe.create', 'Ausleihe erstellen', 'Ausleihe durchführen.', 'ausleihe', 1, 1),
    (@system_bibliocollect, 'bibliocollect.ausleihe.return', 'Rückgabe durchführen', 'Medienrückgabe durchführen.', 'ausleihe', 1, 1),

    (@system_methodenmatrix, 'methodenmatrix.material.view', 'Material anzeigen', 'Materialien anzeigen.', 'material', 1, 1),
    (@system_methodenmatrix, 'methodenmatrix.material.create', 'Material erstellen', 'Materialien erstellen.', 'material', 1, 1),
    (@system_methodenmatrix, 'methodenmatrix.material.edit', 'Material bearbeiten', 'Materialien bearbeiten.', 'material', 1, 1),
    (@system_methodenmatrix, 'methodenmatrix.material.delete', 'Material löschen', 'Materialien löschen.', 'material', 1, 1)
ON DUPLICATE KEY UPDATE
    `name` = VALUES(`name`),
    `description` = VALUES(`description`),
    `category` = VALUES(`category`),
    `is_active` = VALUES(`is_active`),
    `is_system` = VALUES(`is_system`);

/* Admin- und Developer-Gruppen bekommen alle Permissions ihres Systems. */
INSERT IGNORE INTO `ids_group_permissions` (`group_id`, `permission_id`)
SELECT g.`id`, p.`id`
FROM `ids_groups` g
JOIN `ids_permissions` p ON p.`system_id` = g.`system_id`
WHERE g.`key_name` IN ('administrator', 'developer')
  AND g.`is_active` = 1
  AND p.`is_active` = 1;

/* Identity-Rechteverwaltung bekommt alle Verwaltungsrechte, aber keine System-Löschung als Sonderrecht. */
INSERT IGNORE INTO `ids_group_permissions` (`group_id`, `permission_id`)
SELECT g.`id`, p.`id`
FROM `ids_groups` g
JOIN `ids_systems` s ON s.`id` = g.`system_id`
JOIN `ids_permissions` p ON p.`system_id` = s.`id`
WHERE s.`key_name` = 'identity'
  AND g.`key_name` = 'rechteverwaltung'
  AND p.`key_name` IN (
      'identity.systeme.view',
      'identity.permissions.view', 'identity.permissions.create', 'identity.permissions.edit',
      'identity.gruppen.view', 'identity.gruppen.create', 'identity.gruppen.edit', 'identity.gruppen.permissions.manage',
      'identity.subjects.view', 'identity.subjects.groups.view', 'identity.subjects.groups.assign', 'identity.subjects.groups.remove'
  );

/* Reduzierte Startrechte für operative Gruppen. */
INSERT IGNORE INTO `ids_group_permissions` (`group_id`, `permission_id`)
SELECT g.`id`, p.`id`
FROM `ids_groups` g
JOIN `ids_systems` s ON s.`id` = g.`system_id`
JOIN `ids_permissions` p ON p.`system_id` = s.`id`
WHERE s.`key_name` = 'portal'
  AND g.`key_name` IN ('gast', 'mitglied', 'teamende', 'vorstand', 'verwaltung')
  AND p.`key_name` = 'portal.dashboard.view';

INSERT IGNORE INTO `ids_group_permissions` (`group_id`, `permission_id`)
SELECT g.`id`, p.`id`
FROM `ids_groups` g
JOIN `ids_systems` s ON s.`id` = g.`system_id`
JOIN `ids_permissions` p ON p.`system_id` = s.`id`
WHERE s.`key_name` = 'portal'
  AND g.`key_name` IN ('verwaltung', 'vorstand')
  AND p.`key_name` IN (
      'portal.verwaltung.personen.view',
      'portal.verwaltung.personen.create',
      'portal.verwaltung.personen.edit',
      'portal.verwaltung.personen.export',
      'portal.verwaltung.audit.view'
  );

INSERT IGNORE INTO `ids_group_permissions` (`group_id`, `permission_id`)
SELECT g.`id`, p.`id`
FROM `ids_groups` g
JOIN `ids_systems` s ON s.`id` = g.`system_id`
JOIN `ids_permissions` p ON p.`system_id` = s.`id`
WHERE s.`key_name` = 'bibliocollect'
  AND g.`key_name` IN ('gast', 'schueler', 'lehrkraft', 'ausleihe', 'verwaltung')
  AND p.`key_name` = 'bibliocollect.medien.view';

INSERT IGNORE INTO `ids_group_permissions` (`group_id`, `permission_id`)
SELECT g.`id`, p.`id`
FROM `ids_groups` g
JOIN `ids_systems` s ON s.`id` = g.`system_id`
JOIN `ids_permissions` p ON p.`system_id` = s.`id`
WHERE s.`key_name` = 'bibliocollect'
  AND g.`key_name` IN ('ausleihe', 'verwaltung')
  AND p.`key_name` IN ('bibliocollect.ausleihe.create', 'bibliocollect.ausleihe.return');

INSERT IGNORE INTO `ids_group_permissions` (`group_id`, `permission_id`)
SELECT g.`id`, p.`id`
FROM `ids_groups` g
JOIN `ids_systems` s ON s.`id` = g.`system_id`
JOIN `ids_permissions` p ON p.`system_id` = s.`id`
WHERE s.`key_name` = 'methodenmatrix'
  AND g.`key_name` IN ('gast', 'teamende', 'verwaltung')
  AND p.`key_name` = 'methodenmatrix.material.view';

INSERT IGNORE INTO `ids_group_permissions` (`group_id`, `permission_id`)
SELECT g.`id`, p.`id`
FROM `ids_groups` g
JOIN `ids_systems` s ON s.`id` = g.`system_id`
JOIN `ids_permissions` p ON p.`system_id` = s.`id`
WHERE s.`key_name` = 'methodenmatrix'
  AND g.`key_name` IN ('teamende', 'verwaltung')
  AND p.`key_name` IN ('methodenmatrix.material.create', 'methodenmatrix.material.edit');

/* Bootstrap-Admin sicherstellen. Falls das konfigurierte Konto noch nicht existiert, wird es angelegt. */
SET @initial_admin_person_id = (
    SELECT u.`person_id`
    FROM `ids_users` u
    WHERE CONVERT(u.`email` USING utf8mb4) COLLATE utf8mb4_unicode_ci = @initial_admin_email COLLATE utf8mb4_unicode_ci
    LIMIT 1
);

INSERT INTO `ids_persons` (`person_uuid`, `display_name`, `status`)
SELECT UNHEX(REPLACE(UUID(), '-', '')), @initial_admin_email, 'active'
WHERE @initial_admin_person_id IS NULL;

SET @created_initial_admin_person_id = LAST_INSERT_ID();

SET @initial_admin_person_id = IF(
    @initial_admin_person_id IS NULL,
    @created_initial_admin_person_id,
    @initial_admin_person_id
);

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
SET `status` = 'active',
    `display_name` = COALESCE(NULLIF(`display_name`, ''), @initial_admin_email)
WHERE `id` = @initial_admin_person_id;

UPDATE `ids_users`
SET `status` = 'active',
    `password_hash` = @initial_admin_password_hash,
    `email_verified_at` = COALESCE(`email_verified_at`, CURRENT_TIMESTAMP)
WHERE CONVERT(`email` USING utf8mb4) COLLATE utf8mb4_unicode_ci = @initial_admin_email COLLATE utf8mb4_unicode_ci;

INSERT IGNORE INTO `ids_person_contact_details` (`person_id`, `contact_type`, `label`, `value`, `is_primary`, `is_verified`)
SELECT @initial_admin_person_id, 'email', 'Login', @initial_admin_email, 1, 1
WHERE EXISTS (
    SELECT 1
    FROM information_schema.TABLES
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'ids_person_contact_details'
);

INSERT IGNORE INTO `ids_user_account_settings` (`user_id`, `language`, `timezone`, `email_notifications`, `profile_visibility`)
SELECT u.`id`, 'de', 'Europe/Berlin', 1, 'private'
FROM `ids_users` u
WHERE CONVERT(u.`email` USING utf8mb4) COLLATE utf8mb4_unicode_ci = @initial_admin_email COLLATE utf8mb4_unicode_ci
  AND EXISTS (
      SELECT 1
      FROM information_schema.TABLES
      WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = 'ids_user_account_settings'
  );

/* Für alle Personen Subjects erzeugen. Als Start-UUID wird die Personen-UUID verwendet. */
INSERT IGNORE INTO `ids_subjects` (`uuid`, `status`)
SELECT (LOWER(CONCAT(
    SUBSTRING(HEX(p.`person_uuid`), 1, 8), '-',
    SUBSTRING(HEX(p.`person_uuid`), 9, 4), '-',
    SUBSTRING(HEX(p.`person_uuid`), 13, 4), '-',
    SUBSTRING(HEX(p.`person_uuid`), 17, 4), '-',
    SUBSTRING(HEX(p.`person_uuid`), 21, 12)
)) COLLATE utf8mb4_unicode_ci),
CASE
    WHEN p.`status` = 'disabled' THEN 'disabled'
    WHEN p.`status` IN ('erased', 'erasure_requested') THEN 'deleted'
    ELSE 'active'
END
FROM `ids_persons` p
WHERE p.`subject_id` IS NULL;

UPDATE `ids_persons` p
JOIN `ids_subjects` s ON s.`uuid` = (LOWER(CONCAT(
    SUBSTRING(HEX(p.`person_uuid`), 1, 8), '-',
    SUBSTRING(HEX(p.`person_uuid`), 9, 4), '-',
    SUBSTRING(HEX(p.`person_uuid`), 13, 4), '-',
    SUBSTRING(HEX(p.`person_uuid`), 17, 4), '-',
    SUBSTRING(HEX(p.`person_uuid`), 21, 12)
)) COLLATE utf8mb4_unicode_ci)
SET p.`subject_id` = s.`id`
WHERE p.`subject_id` IS NULL;

SET @initial_admin_subject_id = (
    SELECT p.`subject_id`
    FROM `ids_users` u
    JOIN `ids_persons` p ON p.`id` = u.`person_id`
    WHERE CONVERT(u.`email` USING utf8mb4) COLLATE utf8mb4_unicode_ci = @initial_admin_email COLLATE utf8mb4_unicode_ci
    LIMIT 1
);

/* Default-Gruppen zuweisen. */
INSERT IGNORE INTO `ids_subject_groups` (`subject_id`, `group_id`, `assigned_at`, `note`)
SELECT p.`subject_id`, g.`id`, CURRENT_TIMESTAMP, 'Automatisch über ids_groups.is_default zugewiesen.'
FROM `ids_persons` p
JOIN `ids_groups` g ON g.`is_default` = 1 AND g.`is_active` = 1 AND g.`is_assignable` = 1
JOIN `ids_subjects` s ON s.`id` = p.`subject_id` AND s.`status` = 'active'
WHERE p.`subject_id` IS NOT NULL;

/* Initialer Admin: identity.administrator + portal.administrator. */
INSERT IGNORE INTO `ids_subject_groups` (`subject_id`, `group_id`, `assigned_at`, `note`)
SELECT @initial_admin_subject_id, g.`id`, CURRENT_TIMESTAMP, CONCAT('Initialer Admin-Seed für ', @initial_admin_email)
FROM `ids_groups` g
JOIN `ids_systems` s ON s.`id` = g.`system_id`
WHERE @initial_admin_subject_id IS NOT NULL
  AND (
      (s.`key_name` = 'identity' AND g.`key_name` = 'administrator')
      OR
      (s.`key_name` = 'portal' AND g.`key_name` = 'administrator')
  );

UPDATE `ids_subjects`
SET `permission_version` = `permission_version` + 1
WHERE `id` = @initial_admin_subject_id;
