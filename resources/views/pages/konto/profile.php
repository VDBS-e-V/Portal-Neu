<?php
$e = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$contactValue = static function (array $contacts, string $type): string {
    foreach ($contacts as $contact) {
        if (($contact['contact_type'] ?? '') === $type) {
            return (string) ($contact['value'] ?? '');
        }
    }
    return '';
};
$name = is_array($name ?? null) ? $name : [];
$contacts = is_array($contacts ?? null) ? $contacts : [];
$address = is_array($address ?? null) ? $address : [];
$user = is_array($user ?? null) ? $user : [];
$profileUrl = (string) ($canonicalProfileUrl ?? '/konto');
$settingsUrl = (string) ($canonicalSettingsUrl ?? '/konto/einstellungen');
?>

<section class="page-section konto-profile-page">
    <div class="container">
        <header class="page-header">
            <p class="text-muted">Konto</p>
            <h1>Mein Konto</h1>
            <p>Verwalten Sie hier Ihre persönlichen Daten, Kontaktdaten und Adresse.</p>
            <nav class="button-row" aria-label="Kontonavigation">
                <a class="button button-secondary" href="<?= $e($settingsUrl) ?>">Kontoeinstellungen</a>
                <a class="button button-secondary" href="/identity/me">Identity-Daten anzeigen</a>
            </nav>
        </header>

        <?php if (!empty($saved)): ?>
            <div class="alert alert-success">Ihr Profil wurde gespeichert.</div>
        <?php endif; ?>

        <form method="post" action="<?= $e($profileUrl) ?>" class="form-card">
            <input type="hidden" name="_csrf" value="<?= $e($csrfToken ?? '') ?>">

            <section class="card">
                <h2>Name</h2>
                <div class="form-grid">
                    <label>
                        <span>Anrede</span>
                        <input type="text" name="salutation" value="<?= $e($name['salutation'] ?? '') ?>">
                    </label>
                    <label>
                        <span>Titel</span>
                        <input type="text" name="title" value="<?= $e($name['title'] ?? '') ?>">
                    </label>
                    <label>
                        <span>Vorname</span>
                        <input type="text" name="first_name" value="<?= $e($name['first_name'] ?? '') ?>">
                    </label>
                    <label>
                        <span>Weitere Vornamen</span>
                        <input type="text" name="middle_name" value="<?= $e($name['middle_name'] ?? '') ?>">
                    </label>
                    <label>
                        <span>Nachname</span>
                        <input type="text" name="last_name" value="<?= $e($name['last_name'] ?? '') ?>">
                    </label>
                    <label>
                        <span>Anzeigename</span>
                        <input type="text" name="preferred_name" value="<?= $e($name['preferred_name'] ?? $user['display_name'] ?? '') ?>">
                    </label>
                    <label>
                        <span>Pronomen</span>
                        <input type="text" name="pronouns" value="<?= $e($name['pronouns'] ?? '') ?>">
                    </label>
                </div>
            </section>

            <section class="card">
                <h2>Kontakt</h2>
                <div class="form-grid">
                    <label>
                        <span>Login-E-Mail</span>
                        <input type="email" value="<?= $e($user['email'] ?? '') ?>" readonly>
                    </label>
                    <label>
                        <span>Telefon</span>
                        <input type="text" name="phone" value="<?= $e($contactValue($contacts, 'phone')) ?>">
                    </label>
                    <label>
                        <span>Mobil</span>
                        <input type="text" name="mobile" value="<?= $e($contactValue($contacts, 'mobile')) ?>">
                    </label>
                    <label>
                        <span>Website</span>
                        <input type="url" name="website" value="<?= $e($contactValue($contacts, 'website')) ?>">
                    </label>
                </div>
            </section>

            <section class="card">
                <h2>Adresse</h2>
                <div class="form-grid">
                    <label>
                        <span>Adresstyp</span>
                        <select name="address_type">
                            <?php foreach (['private' => 'Privat', 'work' => 'Arbeit', 'billing' => 'Rechnung', 'shipping' => 'Lieferung', 'other' => 'Sonstige'] as $value => $label): ?>
                                <option value="<?= $e($value) ?>" <?= (($address['address_type'] ?? 'private') === $value) ? 'selected' : '' ?>><?= $e($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label>
                        <span>Empfängername</span>
                        <input type="text" name="recipient_name" value="<?= $e($address['recipient_name'] ?? '') ?>">
                    </label>
                    <label>
                        <span>Organisation</span>
                        <input type="text" name="organization" value="<?= $e($address['organization'] ?? '') ?>">
                    </label>
                    <label>
                        <span>Straße</span>
                        <input type="text" name="street" value="<?= $e($address['street'] ?? '') ?>">
                    </label>
                    <label>
                        <span>Hausnummer</span>
                        <input type="text" name="house_number" value="<?= $e($address['house_number'] ?? '') ?>">
                    </label>
                    <label>
                        <span>Adresszusatz</span>
                        <input type="text" name="address_addition" value="<?= $e($address['address_addition'] ?? '') ?>">
                    </label>
                    <label>
                        <span>Postleitzahl</span>
                        <input type="text" name="postal_code" value="<?= $e($address['postal_code'] ?? '') ?>">
                    </label>
                    <label>
                        <span>Ort</span>
                        <input type="text" name="city" value="<?= $e($address['city'] ?? '') ?>">
                    </label>
                    <label>
                        <span>Bundesland / Region</span>
                        <input type="text" name="state" value="<?= $e($address['state'] ?? '') ?>">
                    </label>
                    <label>
                        <span>Land</span>
                        <input type="text" name="country" maxlength="2" value="<?= $e($address['country'] ?? 'DE') ?>">
                    </label>
                </div>
            </section>

            <div class="button-row">
                <button type="submit" class="button button-primary">Profil speichern</button>
                <a class="button button-secondary" href="<?= $e($settingsUrl) ?>">Zu den Einstellungen</a>
            </div>
        </form>
    </div>
</section>
