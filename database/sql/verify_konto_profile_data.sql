SELECT
    u.id AS user_id,
    u.email,
    u.display_name,
    p.id AS person_id,
    n.first_name,
    n.last_name,
    n.preferred_name
FROM ids_users u
JOIN ids_persons p ON p.id = u.person_id
LEFT JOIN ids_person_names n ON n.person_id = p.id
WHERE u.email = COALESCE(NULLIF(@initial_admin_email, ''), 'local@admin.com')
LIMIT 1;

SELECT
    c.contact_type,
    c.label,
    c.value,
    c.is_primary
FROM ids_users u
JOIN ids_persons p ON p.id = u.person_id
JOIN ids_person_contact_details c ON c.person_id = p.id
WHERE u.email = COALESCE(NULLIF(@initial_admin_email, ''), 'local@admin.com')
ORDER BY c.contact_type, c.is_primary DESC, c.id;

SELECT
    a.address_type,
    a.street,
    a.house_number,
    a.postal_code,
    a.city,
    a.country,
    a.is_primary
FROM ids_users u
JOIN ids_persons p ON p.id = u.person_id
JOIN ids_person_addresses a ON a.person_id = p.id
WHERE u.email = COALESCE(NULLIF(@initial_admin_email, ''), 'local@admin.com')
ORDER BY a.is_primary DESC, a.id;
