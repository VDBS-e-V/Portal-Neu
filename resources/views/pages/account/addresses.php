<?php
$e = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

$addresses = $addresses ?? [];
$errors = $errors ?? [];
$message = (string) ($message ?? '');
$csrfToken = (string) ($csrfToken ?? '');
$deleteTokens = $deleteTokens ?? [];
?>

<section class="content-section">
    <header class="content-header">
        <div>
            <h1>Meine Adressen</h1>
            <p>Eigene Adressen pflegen.</p>
        </div>

        <p>
            <a class="button" href="/konto/profil">Zurück zum Profil</a>
        </p>
    </header>

    <nav class="tabs">
        <a href="/konto/profil">Profil</a>
        <a href="/konto/kontakte">Kontakte</a>
        <a href="/konto/adressen">Adressen</a>
        <a href="/konto/passwort">Passwort</a>
        <a href="/konto/sicherheit">Sicherheit</a>
    </nav>

    <?php if ($message !== ''): ?>
        <div class="notice notice-success"><?= $e($message) ?></div>
    <?php endif; ?>

    <?php if ($errors !== []): ?>
        <div class="notice notice-error">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= $e($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <section class="card">
        <h2>Adresse hinzufügen</h2>

        <form method="post" action="/konto/adressen" class="stack-form">
            <input type="hidden" name="_csrf_token" value="<?= $e($csrfToken) ?>">

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

            <label>Empfängername <input type="text" name="recipient_name"></label>
            <label>Organisation <input type="text" name="organization"></label>
            <label>Straße <input type="text" name="street"></label>
            <label>Hausnummer <input type="text" name="house_number"></label>
            <label>Zusatz <input type="text" name="address_addition"></label>
            <label>PLZ <input type="text" name="postal_code"></label>
            <label>Ort <input type="text" name="city"></label>
            <label>Bundesland / Region <input type="text" name="state"></label>
            <label>Land <input type="text" name="country" value="DE" maxlength="2"></label>
            <label><input type="checkbox" name="is_primary" value="1"> Primär</label>

            <button type="submit">Adresse speichern</button>
        </form>
    </section>

    <section class="card">
        <h2>Adressen</h2>

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
                <tr><td colspan="6">Keine Adressen vorhanden.</td></tr>
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
                        <form method="post" action="/konto/adressen/<?= $addressId ?>/delete">
                            <input type="hidden" name="_csrf_token" value="<?= $e($deleteTokens[$addressId] ?? '') ?>">
                            <button type="submit">Löschen</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</section>
