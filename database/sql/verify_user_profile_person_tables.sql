SELECT 'ids_person_names' AS table_name, COUNT(*) AS rows_count FROM ids_person_names
UNION ALL
SELECT 'ids_person_contact_details' AS table_name, COUNT(*) AS rows_count FROM ids_person_contact_details
UNION ALL
SELECT 'ids_person_addresses' AS table_name, COUNT(*) AS rows_count FROM ids_person_addresses
UNION ALL
SELECT 'ids_user_account_settings' AS table_name, COUNT(*) AS rows_count FROM ids_user_account_settings;

SELECT
    u.id AS user_id,
    u.email,
    u.person_id,
    pn.preferred_name,
    pn.first_name,
    pn.last_name
FROM ids_users u
LEFT JOIN ids_person_names pn ON pn.person_id = u.person_id
ORDER BY u.id
LIMIT 25;
