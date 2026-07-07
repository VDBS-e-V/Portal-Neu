-- Erwartung nach Mini-Projekt 13.1:
-- 1) Der Seed seed_mini_project_13_deprecate_legacy_identity_permission_groups.sql nutzt keine Spalte `note` mehr.
-- 2) Produktiver Code nutzt ids_person_permission_groups nicht mehr.
-- 3) Alte ids_permission_groups-/ids_user_permission_groups-Logik ist aus produktivem Code entfernt.

SELECT 'mini_project_13_1_verify' AS check_name, 'run PHP QA tools' AS instruction;
