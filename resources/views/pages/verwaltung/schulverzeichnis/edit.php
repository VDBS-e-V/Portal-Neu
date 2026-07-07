<?php
/** @var array<string, mixed> $detail */
/** @var array<int, string> $errors */
$e = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$school = $detail['school'] ?? [];
$effective = $detail['effective'] ?? [];
$overrides = $detail['overrides'] ?? [];
$schoolId = (int) ($school['id'] ?? 0);

$getOverride = static function (string $key) use ($overrides): string {
    return (string) (($overrides[$key]['override_value'] ?? '') ?: '');
};
?>

<section class="page-section">
  <div class="content-header">
    <div>
      <h1>Kleine Anpassung</h1>
      <p><?= $e($effective['name'] ?? $school['name'] ?? '') ?></p>
    </div>
    <div>
      <a class="button secondary" href="/verwaltung/schulverzeichnis/<?= $e($schoolId) ?>">Abbrechen</a>
    </div>
  </div>

  <?php if ($errors !== []): ?>
    <div class="alert alert-error">
      <ul>
        <?php foreach ($errors as $error): ?>
          <li><?= $e($error) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form method="post" action="/verwaltung/schulverzeichnis/<?= $e($schoolId) ?>/edit" class="form-stack">
    <fieldset>
      <legend>Status</legend>

      <label>
        Lebenszyklusstatus
        <select name="lifecycle_status">
          <?php foreach (['active' => 'Aktiv', 'inactive' => 'Inaktiv', 'unknown' => 'Unbekannt', 'merged' => 'Zusammengelegt', 'closed' => 'Geschlossen'] as $value => $label): ?>
            <option value="<?= $e($value) ?>" <?= (string) ($school['lifecycle_status'] ?? '') === $value ? 'selected' : '' ?>><?= $e($label) ?></option>
          <?php endforeach; ?>
        </select>
      </label>

      <label>
        Datenstatus
        <select name="data_status">
          <?php foreach (['imported' => 'Importiert', 'manually_created' => 'Manuell erstellt', 'manually_verified' => 'Geprüft', 'needs_review' => 'Prüfbedarf'] as $value => $label): ?>
            <option value="<?= $e($value) ?>" <?= (string) ($school['data_status'] ?? '') === $value ? 'selected' : '' ?>><?= $e($label) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
    </fieldset>

    <fieldset>
      <legend>Manuelle Overrides</legend>
      <p>Leere Felder entfernen vorhandene manuelle Korrekturen. Die Importdaten bleiben unverändert.</p>

      <label>
        Anzeigename-Korrektur
        <input type="text" name="display_name" value="<?= $e($getOverride('display_name')) ?>" placeholder="<?= $e($school['name'] ?? '') ?>">
      </label>

      <label>
        Offizielle E-Mail-Korrektur
        <input type="email" name="primary_email" value="<?= $e($getOverride('primary_email')) ?>" placeholder="<?= $e($effective['email'] ?? '') ?>">
      </label>

      <label>
        Offizielle Telefon-Korrektur
        <input type="text" name="primary_phone" value="<?= $e($getOverride('primary_phone')) ?>" placeholder="<?= $e($effective['phone'] ?? '') ?>">
      </label>

      <label>
        Website-Korrektur
        <input type="url" name="primary_website" value="<?= $e($getOverride('primary_website')) ?>" placeholder="<?= $e($effective['website'] ?? '') ?>">
      </label>

      <label>
        Adressnotiz
        <textarea name="address_note" rows="3"><?= $e($getOverride('address_note')) ?></textarea>
      </label>

      <label>
        Datenqualitätsnotiz
        <textarea name="data_quality_note" rows="4"><?= $e($getOverride('data_quality_note')) ?></textarea>
      </label>

      <label>
        Grund der Änderung
        <textarea name="reason" rows="3"></textarea>
      </label>
    </fieldset>

    <div class="form-actions">
      <button type="submit">Speichern</button>
      <a class="button secondary" href="/verwaltung/schulverzeichnis/<?= $e($schoolId) ?>">Abbrechen</a>
    </div>
  </form>
</section>
