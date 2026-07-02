SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Mini-Projekt 7: Menüeinträge dürfen nicht mehr am alten PageGroup-System hängen.
-- Die Menüeinträge bleiben erhalten; nur die alte page_group_id-Verknüpfung wird entfernt.
UPDATE pt_menu_items
SET page_group_id = NULL
WHERE page_group_id IS NOT NULL;
