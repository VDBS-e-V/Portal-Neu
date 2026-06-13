<?php
$user = is_array($user ?? null) ? $user : [];
$name = is_array($name ?? null) ? $name : [];
$address = is_array($address ?? null) ? $address : [];
$contacts = is_array($contacts ?? null) ? $contacts : [];
$csrfToken = (string) ($csrfToken ?? '');
$saved = (bool) ($saved ?? false);

$e = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

$contactValue = static function (array $contacts, string $type): string {
    foreach ($contacts as $contact) {
        if (($contact['contact_type'] ?? '') === $type) {
            return (string) ($contact['value'] ?? '');
        }
    }

    return '';
};
?>

<section class="section--surface">
    <div class="container container--text-only container--narrow">
        <form method="post" action="/user/profile" class="form form--card">
            <input type="hidden" name="_csrf" value="<?= $e($csrfToken) ?>">

            <div class="form__grid form__grid--2">
                <div class="form__title">
                    <h1>Mein Profil</h1>
                    <p>Verwalten Sie hier Ihre persönlichen Daten, Kontaktdaten und Adresse.</p>
                </div>

                <?php if ($saved): ?>
                    <div class="form__notice form__notice--success" role="status">
                        <strong>Gespeichert:</strong>
                        Ihr Profil wurde aktualisiert.
                    </div>
                <?php endif; ?>

                <div class="form__section-title form__section-title--primary">
                    <h2>Namen</h2>
                    <p>Diese Angaben beschreiben Ihre Person im Portal.</p>
                </div>

                <div class="form__field">
                    <label class="form__label" for="salutation">Anrede</label>
                    <input class="form__control" id="salutation" name="salutation" type="text" value="<?= $e($name['salutation'] ?? '') ?>">
                </div>

                <div class="form__field">
                    <label class="form__label" for="title">Titel</label>
                    <input class="form__control" id="title" name="title" type="text" value="<?= $e($name['title'] ?? '') ?>">
                </div>

                <div class="form__field">
                    <label class="form__label" for="first_name">Vorname</label>
                    <input class="form__control" id="first_name" name="first_name" type="text" value="<?= $e($name['first_name'] ?? '') ?>">
                </div>

                <div class="form__field">
                    <label class="form__label" for="middle_name">Weitere Vornamen</label>
                    <input class="form__control" id="middle_name" name="middle_name" type="text" value="<?= $e($name['middle_name'] ?? '') ?>">
                </div>

                <div class="form__field">
                    <label class="form__label" for="last_name">Nachname</label>
                    <input class="form__control" id="last_name" name="last_name" type="text" value="<?= $e($name['last_name'] ?? '') ?>">
                </div>

                <div class="form__field">
                    <label class="form__label" for="preferred_name">Anzeigename</label>
                    <input class="form__control" id="preferred_name" name="preferred_name" type="text" value="<?= $e($name['preferred_name'] ?? ($user['display_name'] ?? '')) ?>">
                    <p class="form__hint">Dieser Name kann im Portal angezeigt werden.</p>
                </div>

                <div class="form__field form__field--span-full">
                    <label class="form__label" for="pronouns">Pronomen</label>
                    <input class="form__control" id="pronouns" name="pronouns" type="text" value="<?= $e($name['pronouns'] ?? '') ?>">
                </div>

                <div class="form__section-title form__section-title--secondary-cta">
                    <h2>Kontaktdaten</h2>
                    <p>Ihre Login-E-Mail kommt weiterhin aus dem Benutzerkonto.</p>
                </div>

                <div class="form__field form__field--span-full">
                    <label class="form__label" for="account_email">Login-E-Mail</label>
                    <input class="form__control" id="account_email" type="email" value="<?= $e($user['email'] ?? '') ?>" readonly>
                    <p class="form__hint">Die Login-E-Mail ist Teil des Benutzerkontos.</p>
                </div>

                <div class="form__field">
                    <label class="form__label" for="phone">Telefon</label>
                    <input class="form__control" id="phone" name="phone" type="tel" value="<?= $e($contactValue($contacts, 'phone')) ?>">
                </div>

                <div class="form__field">
                    <label class="form__label" for="mobile">Mobil</label>
                    <input class="form__control" id="mobile" name="mobile" type="tel" value="<?= $e($contactValue($contacts, 'mobile')) ?>">
                </div>

                <div class="form__field form__field--span-full">
                    <label class="form__label" for="website">Website</label>
                    <input class="form__control" id="website" name="website" type="url" value="<?= $e($contactValue($contacts, 'website')) ?>" placeholder="https://...">
                </div>

                <div class="form__section-title form__section-title--secondary-highlight">
                    <h2>Adresse</h2>
                    <p>Die Hauptadresse des Benutzerprofils.</p>
                </div>

                <div class="form__field">
                    <label class="form__label" for="address_type">Adresstyp</label>
                    <select class="form__control" id="address_type" name="address_type">
                        <?php foreach (['private' => 'Privat', 'work' => 'Arbeit', 'billing' => 'Rechnung', 'shipping' => 'Lieferung', 'other' => 'Sonstige'] as $value => $label): ?>
                            <option value="<?= $e($value) ?>" <?= ($address['address_type'] ?? 'private') === $value ? 'selected' : '' ?>>
                                <?= $e($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form__field">
                    <label class="form__label" for="recipient_name">Empfängername</label>
                    <input class="form__control" id="recipient_name" name="recipient_name" type="text" value="<?= $e($address['recipient_name'] ?? '') ?>">
                </div>

                <div class="form__field form__field--span-full">
                    <label class="form__label" for="organization">Organisation</label>
                    <input class="form__control" id="organization" name="organization" type="text" value="<?= $e($address['organization'] ?? '') ?>">
                </div>

                <div class="form__field">
                    <label class="form__label" for="street">Straße</label>
                    <input class="form__control" id="street" name="street" type="text" value="<?= $e($address['street'] ?? '') ?>">
                </div>

                <div class="form__field">
                    <label class="form__label" for="house_number">Hausnummer</label>
                    <input class="form__control" id="house_number" name="house_number" type="text" value="<?= $e($address['house_number'] ?? '') ?>">
                </div>

                <div class="form__field form__field--span-full">
                    <label class="form__label" for="address_addition">Adresszusatz</label>
                    <input class="form__control" id="address_addition" name="address_addition" type="text" value="<?= $e($address['address_addition'] ?? '') ?>">
                </div>

                <div class="form__field">
                    <label class="form__label" for="postal_code">Postleitzahl</label>
                    <input class="form__control" id="postal_code" name="postal_code" type="text" value="<?= $e($address['postal_code'] ?? '') ?>">
                </div>

                <div class="form__field">
                    <label class="form__label" for="city">Ort</label>
                    <input class="form__control" id="city" name="city" type="text" value="<?= $e($address['city'] ?? '') ?>">
                </div>

                <div class="form__field">
                    <label class="form__label" for="state">Bundesland / Region</label>
                    <input class="form__control" id="state" name="state" type="text" value="<?= $e($address['state'] ?? '') ?>">
                </div>

                <div class="form__field">
                    <label class="form__label" for="country">Land</label>
                    <input class="form__control" id="country" name="country" type="text" maxlength="2" value="<?= $e($address['country'] ?? 'DE') ?>">
                    <p class="form__hint">ISO-Code, zum Beispiel DE.</p>
                </div>

                <div class="form__actions form__actions--between">
                    <a class="btn btn--primary-transp btn--md" href="/user/settings">
                        Kontoeinstellungen
                    </a>

                    <button class="btn btn--primary btn--md" type="submit">
                        Profil speichern
                    </button>
                </div>
            </div>
        </form>
    </div>
</section>