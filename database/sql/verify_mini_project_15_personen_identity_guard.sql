SELECT 'persons_without_subject' AS check_name, COUNT(*) AS count_value
FROM ids_persons p
LEFT JOIN ids_subjects s ON s.id = p.subject_id
WHERE p.subject_id IS NULL OR s.id IS NULL;
SELECT 'users_with_missing_person' AS check_name, COUNT(*) AS count_value
FROM ids_users u
LEFT JOIN ids_persons p ON p.id = u.person_id
WHERE u.person_id IS NOT NULL AND p.id IS NULL;
SELECT 'subject_groups_with_missing_refs' AS check_name, COUNT(*) AS count_value
FROM ids_subject_groups sg
LEFT JOIN ids_subjects s ON s.id = sg.subject_id
LEFT JOIN ids_groups g ON g.id = sg.group_id
WHERE s.id IS NULL OR g.id IS NULL;
SELECT 'legacy_identity_group_tables' AS check_name, COUNT(*) AS count_value
FROM information_schema.tables
WHERE table_schema = DATABASE()
  AND table_name IN ('ids_permission_groups', 'ids_user_permission_groups', 'ids_person_permission_groups');