SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Mini-Projekt 18.1:
-- Der alte Backfill-Seed wurde durch QA-gestützte Integritätsprüfungen ersetzt,
-- weil gemischte Alt-Kollationen in Bestandsinstallationen den Seed-Lauf abbrechen konnten.
-- Fehlende Person-Subject-Zuordnungen werden durch tools/qa/check_person_subject_integrity.php erkannt.
SELECT 1 AS mini_project_15_person_subject_backfill_retired;