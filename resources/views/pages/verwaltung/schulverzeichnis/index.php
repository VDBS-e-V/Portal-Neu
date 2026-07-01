<?php
/** @var array<int, array<string, mixed>> $schools */
/** @var array<string, mixed> $filters */
$e = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$total = (int) ($total ?? 0);
$page = (int) ($page ?? 1);
$perPage = (int) ($perPage ?? 50);
?>

<section class="page-section">
  <div class="content-header">
    <div>
      <h1>Schulverzeichnis</h1>
      <p>Importgeführtes internes Referenzverzeichnis aller bekannten Schulen.</p>
    </div>
    <div>
      <a class="button" href="/verwaltung/schulverzeichnis/importe">Importe</a>
    </div>
  </div>

  <form method="get" action="/verwaltung/schulverzeichnis" class="form-grid">
    <label>
      Suche
      <input type="search" name="q" value="<?= $e($filters['q'] ?? '') ?>" placeholder="Name, BSN, PLZ, Bezirk, E-Mail">
    </label>

    <label>
      Bundesland
      <input type="text" name="federal_state_code" value="<?= $e($filters['federal_state_code'] ?? '') ?>" placeholder="DE-BE">
    </label>

    <label>
      Bezirk / Kreis
      <input type="text" name="district" value="<?= $e($filters['district'] ?? '') ?>">
    </label>

    <label>
      Schulart
      <input type="text" name="school_type" value="<?= $e($filters['school_type'] ?? '') ?>">
    </label>

    <label>
      Status
      <select name="lifecycle_status">
        <?php foreach (['' => 'Alle', 'active' => 'Aktiv', 'inactive' => 'Inaktiv', 'unknown' => 'Unbekannt', 'merged' => 'Zusammengelegt', 'closed' => 'Geschlossen'] as $value => $label): ?>
          <option value="<?= $e($value) ?>" <?= (string) ($filters['lifecycle_status'] ?? 'active') === $value ? 'selected' : '' ?>><?= $e($label) ?></option>
        <?php endforeach; ?>
      </select>
    </label>

    <label>
      Datenstatus
      <select name="data_status">
        <?php foreach (['' => 'Alle', 'imported' => 'Importiert', 'manually_created' => 'Manuell erstellt', 'manually_verified' => 'Geprüft', 'needs_review' => 'Prüfbedarf'] as $value => $label): ?>
          <option value="<?= $e($value) ?>" <?= (string) ($filters['data_status'] ?? '') === $value ? 'selected' : '' ?>><?= $e($label) ?></option>
        <?php endforeach; ?>
      </select>
    </label>

    <div class="form-actions">
      <button type="submit">Suchen</button>
      <a class="button secondary" href="/verwaltung/schulverzeichnis">Zurücksetzen</a>
    </div>
  </form>

  <p><?= $e($total) ?> Treffer</p>

  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>Bundesland</th>
          <th>Bezirk</th>
          <th>Ortsteil</th>
          <th>PLZ</th>
          <th>Schulart</th>
          <th>Datenstatus</th>
          <th>Korrekturen</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($schools as $school): ?>
          <tr>
            <td>
              <strong><?= $e($school['effective_name'] ?? $school['name'] ?? '') ?></strong><br>
              <small><?= $e($school['school_key'] ?? '') ?><?= !empty($school['bsn']) ? ' · BSN ' . $e($school['bsn']) : '' ?></small>
            </td>
            <td><?= $e($school['federal_state_code'] ?? '') ?></td>
            <td><?= $e($school['district'] ?? '') ?></td>
            <td><?= $e($school['locality'] ?? '') ?></td>
            <td><?= $e($school['postal_code'] ?? '') ?></td>
            <td><?= $e($school['school_type'] ?? '') ?></td>
            <td><?= $e($school['data_status'] ?? '') ?></td>
            <td><?= (int) ($school['override_count'] ?? 0) > 0 ? 'ja' : 'nein' ?></td>
            <td><a href="/verwaltung/schulverzeichnis/<?= $e($school['id'] ?? '') ?>">Öffnen</a></td>
          </tr>
        <?php endforeach; ?>

        <?php if ($schools === []): ?>
          <tr>
            <td colspan="9">Keine Schulen gefunden.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <?php if ($total > $perPage): ?>
    <nav class="pagination">
      <?php if ($page > 1): ?>
        <a href="?<?= http_build_query(array_merge($filters, ['page' => $page - 1])) ?>">Zurück</a>
      <?php endif; ?>
      <span>Seite <?= $e($page) ?></span>
      <?php if ($page * $perPage < $total): ?>
        <a href="?<?= http_build_query(array_merge($filters, ['page' => $page + 1])) ?>">Weiter</a>
      <?php endif; ?>
    </nav>
  <?php endif; ?>
</section>
