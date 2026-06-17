<?php
$e = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

$person = $person ?? [];
$contacts = $contacts ?? [];
$personId = (int) ($person['id'] ?? 0);
$message = (string) ($message ?? '');
?>

<section class="content-section">
    <header class="content-header">
        <div>
            <h1><?= $e($pageTitle ?? 'Kontakte') ?></h1>
            <p>Mehrere Kontaktmöglichkeiten pro Person verwalten.</p>
        </div>

        <p>
            <a class="button" href="/verwaltung/personen/<?= $personId ?>">Zurück zur Person</a>
        </p>
    </header>

    <?php if ($message !== ''): ?>
        <div class="notice notice-success">
            <?= $e($message) ?>
        </div>
    <?php endif; ?>

    <nav class="tabs">
        <a href="/verwaltung/personen/<?= $personId ?>">Übersicht</a>
        <a href="/verwaltung/personen/<?= $personId ?>/kontakte">Kontakte</a>
        <a href="/verwaltung/personen/<?= $personId ?>/adressen">Adressen</a>
        <a href="/verwaltung/personen/<?= $personId ?>/gruppen">Gruppen</a>
    </nav>

    <section class="card">
        <h2>Kontakt hinzufügen</h2>

        <form method="post" action="/verwaltung/personen/<?= $personId ?>/kontakte" class="stack-form">
            <label>
                Typ
                <select name="contact_type">
                    <option value="email">E-Mail</option>
                    <option value="phone">Telefon</option>
                    <option value="mobile">Mobil</option>
                    <option value="website">Website</option>
                    <option value="other">Sonstiges</option>
                </select>
            </label>

            <label>
                Label
                <input type="text" name="label" placeholder="privat, dienstlich, Notfall ...">
            </label>

            <label>
                Wert
                <input type="text" name="value" required>
            </label>

            <label>
                <input type="checkbox" name="is_primary" value="1">
                Primär
            </label>

            <label>
                <input type="checkbox" name="is_verified" value="1">
                Verifiziert
            </label>

            <button type="submit">Kontakt speichern</button>
        </form>
    </section>

    <section class="card">
        <h2>Vorhandene Kontakte</h2>

        <table class="data-table">
            <thead>
            <tr>
                <th>Typ</th>
                <th>Label</th>
                <th>Wert</th>
                <th>Primär</th>
                <th>Verifiziert</th>
                <th>Aktion</th>
            </tr>
            </thead>
            <tbody>
            <?php if ($contacts === []): ?>
                <tr>
                    <td colspan="6">Keine Kontakte vorhanden.</td>
                </tr>
            <?php endif; ?>

            <?php foreach ($contacts as $contact): ?>
                <?php $contactId = (int) ($contact['id'] ?? 0); ?>
                <tr>
                    <td><?= $e($contact['contact_type'] ?? '') ?></td>
                    <td><?= $e($contact['label'] ?? '') ?></td>
                    <td><?= $e($contact['value'] ?? '') ?></td>
                    <td><?= !empty($contact['is_primary']) ? 'ja' : 'nein' ?></td>
                    <td><?= !empty($contact['is_verified']) ? 'ja' : 'nein' ?></td>
                    <td>
                        <form method="post" action="/verwaltung/personen/<?= $personId ?>/kontakte/<?= $contactId ?>/delete">
                            <button type="submit">Löschen</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</section>