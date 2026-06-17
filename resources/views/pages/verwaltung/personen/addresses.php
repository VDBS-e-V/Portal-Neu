<?php
$e = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

$person = $person ?? [];
$addresses = $addresses ?? [];
$personId = (int) ($person['id'] ?? 0);
$message = (string) ($message ?? '');
?>

<section class="content-section">
    <header class="content-header">
        <div>
            <h1><?= $e($pageTitle ?? 'Adressen') ?></h1>
            <p>Mehrere Adressen pro Person verwalten.</p>
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
        <h2>Adresse hinzufügen</h2>

        <form method="post" action="/verwaltung/personen/<?= $personId ?>/adressen" class="stack-form">
            <label>
                Typ
                <select name="address_type">
                    <option value="private">Privat</option>
                    <option value="work">Arbeit</option>
                    <option value="billing">Rechnung</option>
                    <option value="shipping">Versand</option>
                    <option value="other">Sonstiges</option>
                </select>
            </label>

            <label>
                Empfängername
                <input type="text" name="recipient_name">
            </label>

            <label>
                Organisation
                <input type="text" name="organization">
            </label>

            <label>
                Straße
                <input type="text" name="street">
            </label>

            <label>
                Hausnummer
                <input type="text" name="house_number">
            </label>

            <label>
                Zusatz
                <input type="text" name="address_addition">
            </label>

            <label>
                PLZ
                <input type="text" name="postal_code">
            </label>

            <label>
                Ort
                <input type="text" name="city">
            </label>

            <label>
                Bundesland / Region
                <input type="text" name="state">
            </label>

            <label>
                Land
                <input type="text" name="country" value="DE" maxlength="2">
            </label>

            <label>
                <input type="checkbox" name="is_primary" value="1">
                Primär
            </label>

            <button type="submit">Adresse speichern</button>
        </form>
    </section>

    <section class="card">
        <h2>Vorhandene Adressen</h2>

        <table class="data-table">
            <thead>
            <tr>
                <th>Typ</th>
                <th>Empfänger</th>
                <th>Adresse</th>
                <th>Ort</th>
                <th>Primär</th>
                <th>Aktion</th>
            </tr>
            </thead>
            <tbody>
            <?php if ($addresses === []): ?>
                <tr>
                    <td colspan="6">Keine Adressen vorhanden.</td>
                </tr>
            <?php endif; ?>

            <?php foreach ($addresses as $address): ?>
                <?php $addressId = (int) ($address['id'] ?? 0); ?>
                <tr>
                    <td><?= $e($address['address_type'] ?? '') ?></td>
                    <td>
                        <?= $e($address['recipient_name'] ?? '') ?>
                        <?php if (!empty($address['organization'])): ?>
                            <br><?= $e($address['organization']) ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?= $e(trim((string) ($address['street'] ?? '') . ' ' . (string) ($address['house_number'] ?? ''))) ?>
                        <?php if (!empty($address['address_addition'])): ?>
                            <br><?= $e($address['address_addition']) ?>
                        <?php endif; ?>
                    </td>
                    <td><?= $e(trim((string) ($address['postal_code'] ?? '') . ' ' . (string) ($address['city'] ?? ''))) ?></td>
                    <td><?= !empty($address['is_primary']) ? 'ja' : 'nein' ?></td>
                    <td>
                        <form method="post" action="/verwaltung/personen/<?= $personId ?>/adressen/<?= $addressId ?>/delete">
                            <button type="submit">Löschen</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</section>