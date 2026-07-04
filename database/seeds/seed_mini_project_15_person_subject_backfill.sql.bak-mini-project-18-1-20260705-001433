SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;
DROP TEMPORARY TABLE IF EXISTS tmp_mp15_person_subject_backfill;
CREATE TEMPORARY TABLE tmp_mp15_person_subject_backfill (
    person_id BIGINT UNSIGNED NOT NULL PRIMARY KEY,
    subject_uuid CHAR(36) NOT NULL,
    KEY idx_tmp_mp15_subject_uuid (subject_uuid)
) ENGINE=Memory;
INSERT INTO tmp_mp15_person_subject_backfill (person_id, subject_uuid)
SELECT p.id, UUID()
FROM ids_persons p
WHERE p.subject_id IS NULL;
INSERT INTO ids_subjects (uuid, status, permission_version, created_at, updated_at)
SELECT t.subject_uuid, 'active', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
FROM tmp_mp15_person_subject_backfill t
LEFT JOIN ids_subjects s ON s.uuid = t.subject_uuid
WHERE s.id IS NULL;
UPDATE ids_persons p
INNER JOIN tmp_mp15_person_subject_backfill t ON t.person_id = p.id
INNER JOIN ids_subjects s ON s.uuid = t.subject_uuid
SET p.subject_id = s.id
WHERE p.subject_id IS NULL;
DROP TEMPORARY TABLE IF EXISTS tmp_mp15_person_subject_backfill;