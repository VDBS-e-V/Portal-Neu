<?php
$e = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

$person = $person ?? [];
$contacts = $contacts ?? [];
$addresses = $addresses ?? [];
$groups = $groups ?? [];
$personId = (int) ($person['id'] ?? 0);
$message = (string) ($message ?? '');
?>

<section class="content-section">
    <header class="content-header">
        <div>
            <h1><?= $e($pageTitle ?? 'Person') ?></h1>
            <p>Personenstammdaten, Kontakte, Adressen, Login und Gruppen.</p>
        </div>

        <p>
            <a class="button" href="/verwaltung/personen">Zurück</a>
            <a class="button button-primary" href="/verwaltung/personen/<?= $personId ?>/edit">Bearbeiten</a>
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
        <h2>Stammdaten</h2>

        <dl>
            <dt>ID</dt>
            <dd><?= $personId ?></dd>

            <dt>Status</dt>
            <dd><?= $e($person['status'] ?? '') ?></dd>

            <dt>Anzeigename</dt>
            <dd><?= $e($person['display_name'] ?? '') ?></dd>

            <dt>Anrede / Titel</dt>
            <dd><?= $e(trim((string) ($person['salutation'] ?? '') . ' ' . (string) ($person['title'] ?? ''))) ?></dd>

            <dt>Vorname</dt>
            <dd><?= $e($person['first_name'] ?? '') ?></dd>

            <dt>Nachname</dt>
            <dd><?= $e($person['last_name'] ?? '') ?></dd>

            <dt>Bevorzugter Name</dt>
            <dd><?= $e($person['preferred_name'] ?? '') ?></dd>

            <dt>Pronomen</dt>
            <dd><?= $e($person['pronouns'] ?? '') ?></dd>
        </dl>
    </section>

    <section class="card">
        <h2>Login</h2>

        <dl>
            <dt>Login-ID</dt>
            <dd><?= $e($person['user_id'] ?? '') ?></dd>

            <dt>Login-E-Mail</dt>
            <dd><?= $e($person['login_email'] ?? '') ?></dd>

            <dt>Login-Status</dt>
            <dd><?= $e($person['login_status'] ?? '') ?></dd>

            <dt>Letzter Login</dt>
            <dd><?= $e($person['last_login_at'] ?? '') ?></dd>
        </dl>
    </section>

    <section class="card">
        <h2>Kontakte</h2>

        <?php if ($contacts === []): ?>
            <p>Keine Kontakte erfasst.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($contacts as $contact): ?>
                    <li>
                        <?= $e($contact['contact_type'] ?? '') ?>:
                        <?= $e($contact['value'] ?? '') ?>
                        <?= !empty($contact['is_primary']) ? ' · primär' : '' ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>

    <section class="card">
        <h2>Adressen</h2>

        <?php if ($addresses === []): ?>
            <p>Keine Adressen erfasst.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($addresses as $address): ?>
                    <li>
                        <?= $e($address['address_type'] ?? '') ?>:
                        <?= $e(trim((string) ($address['street'] ?? '') . ' ' . (string) ($address['house_number'] ?? ''))) ?>,
                        <?= $e(trim((string) ($address['postal_code'] ?? '') . ' ' . (string) ($address['city'] ?? ''))) ?>
                        <?= !empty($address['is_primary']) ? ' · primär' : '' ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>

    <section class="card">
        <h2>Gruppen</h2>

        <?php if ($groups === []): ?>
            <p>Keine Gruppen zugewiesen.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($groups as $group): ?>
                    <li><?= $e($group['group_key'] ?? '') ?> — <?= $e($group['name'] ?? '') ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>

    <section class="card">
        <h2>Status ändern</h2>

        <form method="post" action="/verwaltung/personen/<?= $personId ?>/status">
            <label>
                Status
                <select name="status">
                    <?php foreach (['active' => 'Aktiv', 'disabled' => 'Deaktiviert', 'erasure_requested' => 'Löschung beantragt', 'erased' => 'Gelöscht/anonymisiert'] as $value => $label): ?>
                        <option value="<?= $e($value) ?>" <?= (string) ($person['status'] ?? '') === $value ? 'selected' : '' ?>>
                            <?= $e($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <button type="submit">Status speichern</button>
        </form>
    </section>
</section>