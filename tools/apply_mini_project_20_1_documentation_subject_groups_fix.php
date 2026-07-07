<?php
/**
 * Mini-Projekt 20.1: Dokumentationsfix ids_subject_groups
 *
 * Ergänzt docs/identity/BERECHTIGUNGSKONZEPT.md um die zentrale Tabelle
 * ids_subject_groups, falls der Begriff dort noch fehlt.
 */

declare(strict_types=1);

$projectRoot = dirname(__DIR__);
$docPath = $projectRoot . DIRECTORY_SEPARATOR . 'docs' . DIRECTORY_SEPARATOR . 'identity' . DIRECTORY_SEPARATOR . 'BERECHTIGUNGSKONZEPT.md';
$markerDir = $projectRoot . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'mini-project-markers';
$timestamp = date('Ymd-His');

function mp20_1_write_file(string $path, string $content): void
{
    $dir = dirname($path);
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
    file_put_contents($path, $content);
}

if (!is_file($docPath)) {
    fwrite(STDERR, "FEHLER: docs/identity/BERECHTIGUNGSKONZEPT.md wurde nicht gefunden. Bitte zuerst Mini-Projekt 20 ausführen.\n");
    exit(1);
}

$content = file_get_contents($docPath);
if ($content === false) {
    fwrite(STDERR, "FEHLER: Dokument konnte nicht gelesen werden: {$docPath}\n");
    exit(1);
}

if (strpos($content, 'ids_subject_groups') !== false) {
    echo "OK: docs/identity/BERECHTIGUNGSKONZEPT.md enthält bereits ids_subject_groups.\n";
} else {
    $backupPath = $docPath . '.bak-mini-project-20-1-' . $timestamp;
    copy($docPath, $backupPath);

    $section = <<<'MD'

## Subject-Gruppen-Zuordnung

Die Tabelle `ids_subject_groups` ist die zentrale Zuordnung zwischen digitalen Identitäten und Gruppen.
Sie verbindet `ids_subjects` mit `ids_groups` und ersetzt die alten benutzer- oder personenbezogenen Legacy-Gruppentabellen.

Regeln:

- Berechtigungen werden nicht direkt an Personen oder User vergeben, sondern über Gruppen.
- Eine Person besitzt über `ids_persons.subject_id` genau die digitale Identität, deren Gruppenmitgliedschaften gelten.
- Logins in `ids_users` verweisen auf Personen; die effektiven Rechte ergeben sich daraus über `ids_subjects` und `ids_subject_groups`.
- Gruppenmitgliedschaften können über `ids_subject_groups.expires_at` zeitlich begrenzt werden.
- Der produktive Berechtigungspfad lautet: `ids_subjects` → `ids_subject_groups` → `ids_groups` → `ids_group_permissions` → `ids_permissions`.
- Alte Tabellen wie `ids_permission_groups`, `ids_user_permission_groups` und `ids_person_permission_groups` werden nicht mehr produktiv verwendet.

MD;

    $content = rtrim($content) . $section;
    file_put_contents($docPath, $content);

    echo "Backup: " . $backupPath . "\n";
    echo "Aktualisiert: docs/identity/BERECHTIGUNGSKONZEPT.md\n";
}

if (!is_dir($markerDir)) {
    mkdir($markerDir, 0775, true);
}
$markerPath = $markerDir . DIRECTORY_SEPARATOR . 'mini-project-20-1-documentation-subject-groups-' . $timestamp . '.txt';
mp20_1_write_file($markerPath, "Mini-Projekt 20.1 Dokumentationsfix angewendet: " . date('c') . PHP_EOL);

echo "\nMini-Projekt 20.1 Dokumentationsfix wurde angewendet.\n";
echo "Marker: " . $markerPath . "\n";
echo "Bitte ausführen:\n";
echo "  php tools\\qa\\check_identity_documentation.php\n";
echo "  php tools\\qa\\run_identity_documentation_checks.php\n";
