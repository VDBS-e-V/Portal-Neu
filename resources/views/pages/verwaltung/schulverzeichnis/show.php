<?php
/** @var array<string, mixed> $detail */
$e = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$school = $detail['school'] ?? [];
$site = $detail['site'] ?? [];
$effective = $detail['effective'] ?? [];
$contacts = $detail['contacts'] ?? [];
$identifiers = $detail['identifiers'] ?? [];
$classifications = $detail['classifications'] ?? [];
$overrides = $detail['overrides'] ?? [];
$schoolId = (int) ($school['id'] ?? 0);
?>

<section class="page-section">
  <div class="content-header">
    <div>
      <h1><?= $e($effective['name'] ?? $school['name'] ?? 'Schule') ?></h1>
      <p>
        <?= $e($school['school_key'] ?? '') ?>
        · <?= $e($school['federal_state_code'] ?? '') ?>
        · <?= $e($school['lifecycle_status'] ?? '') ?>
        · <?= $e($school['data_status'] ?? '') ?>
      </p>
    </div>
    <div class="button-row">
      <a class="button" href="/verwaltung/schulverzeichnis/<?= $e($schoolId) ?>/edit">Kleine Anpassung</a>
      <form method="post" action="/verwaltung/schulverzeichnis/<?= $e($schoolId) ?>/verify" style="display:inline">
        <button type="submit">Als geprüft markieren</button>
      </form>
      <a class="button secondary" href="/verwaltung/schulverzeichnis">Zur Liste</a>
    </div>
  </div>

  <div class="grid two-columns">
    <section class="card">
      <h2>Übersicht</h2>
      <dl>
        <dt>Name</dt>
        <dd><?= $e($effective['name'] ?? '') ?></dd>

        <dt>Adresse</dt>
        <dd>
          <?= $e($effective['address_line'] ?? '') ?><br>
          <?= $e($effective['postal_city'] ?? '') ?><br>
          <?= $e($site['district'] ?? '') ?><?= !empty($site['locality']) ? ' · ' . $e($site['locality']) : '' ?>
        </dd>

        <dt>Telefon</dt>
        <dd><?= $e($effective['phone'] ?? '') ?></dd>

        <dt>E-Mail</dt>
        <dd><?= $e($effective['email'] ?? '') ?></dd>

        <dt>Website</dt>
        <dd>
          <?php if (!empty($effective['website'])): ?>
            <a href="<?= $e($effective['website']) ?>" target="_blank" rel="noopener"><?= $e($effective['website']) ?></a>
          <?php endif; ?>
        </dd>

        <dt>Koordinaten</dt>
        <dd><?= $e($site['latitude'] ?? '') ?>, <?= $e($site['longitude'] ?? '') ?></dd>
      </dl>
    </section>

    <section class="card">
      <h2>Aktuelle Klassifikation</h2>
      <?php $current = $classifications[0] ?? []; ?>
      <?php if ($current !== []): ?>
        <dl>
          <dt>Schuljahr</dt>
          <dd><?= $e($current['school_year'] ?? '') ?></dd>
          <dt>Schulart</dt>
          <dd><?= $e($current['school_type'] ?? '') ?></dd>
          <dt>Kategorie</dt>
          <dd><?= $e($current['school_category'] ?? '') ?></dd>
          <dt>Träger</dt>
          <dd><?= $e($current['operator_name'] ?? '') ?></dd>
          <dt>Quelle</dt>
          <dd><?= $e($current['source_key'] ?? '') ?></dd>
        </dl>
      <?php else: ?>
        <p>Keine Klassifikation vorhanden.</p>
      <?php endif; ?>
    </section>
  </div>

  <section class="card">
    <h2>Externe Kennungen</h2>
    <table>
      <thead>
        <tr>
          <th>Quelle</th>
          <th>Typ</th>
          <th>Wert</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($identifiers as $identifier): ?>
          <tr>
            <td><?= $e($identifier['source_key'] ?? '') ?></td>
            <td><?= $e($identifier['identifier_type'] ?? '') ?></td>
            <td><?= $e($identifier['identifier_value'] ?? '') ?></td>
          </tr>
        <?php endforeach; ?>
        <?php if ($identifiers === []): ?>
          <tr><td colspan="3">Keine Kennungen vorhanden.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </section>

  <section class="card">
    <h2>Importierte Kontakte</h2>
    <table>
      <thead>
        <tr>
          <th>Typ</th>
          <th>Wert</th>
          <th>Quelle</th>
          <th>Primär</th>
          <th>Geprüft</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($contacts as $contact): ?>
          <tr>
            <td><?= $e($contact['contact_type'] ?? '') ?></td>
            <td><?= $e($contact['value'] ?? '') ?></td>
            <td><?= $e($contact['source_key'] ?? '') ?></td>
            <td><?= !empty($contact['is_primary']) ? 'ja' : 'nein' ?></td>
            <td><?= !empty($contact['is_verified']) ? 'ja' : 'nein' ?></td>
          </tr>
        <?php endforeach; ?>
        <?php if ($contacts === []): ?>
          <tr><td colspan="5">Keine Kontakte vorhanden.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </section>

  <section class="card">
    <h2>Manuelle Korrekturen</h2>
    <table>
      <thead>
        <tr>
          <th>Feld</th>
          <th>Korrektur</th>
          <th>Grund</th>
          <th>Von</th>
          <th>Geändert</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($overrides as $override): ?>
          <tr>
            <td><?= $e($override['field_key'] ?? '') ?></td>
            <td><?= nl2br($e($override['override_value'] ?? '')) ?></td>
            <td><?= nl2br($e($override['reason'] ?? '')) ?></td>
            <td><?= $e($override['created_by_name'] ?? '') ?></td>
            <td><?= $e($override['updated_at'] ?? '') ?></td>
          </tr>
        <?php endforeach; ?>
        <?php if ($overrides === []): ?>
          <tr><td colspan="5">Keine manuellen Korrekturen vorhanden.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </section>
</section>
