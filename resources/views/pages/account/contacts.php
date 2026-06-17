<?php
$e = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

$statusBadge = static function (mixed $status): string {
    $status = strtolower(trim((string) $status));

    return match ($status) {
        'active', 'accepted', 'completed', 'used', 'success' => 'table-badge table-badge--success',
        'disabled', 'revoked', 'cancelled', 'rejected', 'expired', 'error' => 'table-badge table-badge--danger',
        'invited', 'pending', 'requested', 'approved', 'warning' => 'table-badge table-badge--warning',
        default => 'table-badge table-badge--neutral',
    };
};

$messageText = static function (string $message): string {
    return match ($message) {
        'created' => 'Der Datensatz wurde angelegt.',
        'updated' => 'Die Änderungen wurden gespeichert.',
        'deleted' => 'Der Datensatz wurde gelöscht.',
        'status' => 'Der Status wurde geändert.',
        'revoked' => 'Die Einladung wurde widerrufen.',
        'requested' => 'Der Vorgang wurde beantragt.',
        'approved' => 'Der Vorgang wurde freigegeben.',
        'rejected' => 'Der Vorgang wurde abgelehnt.',
        'cancelled' => 'Der Vorgang wurde storniert.',
        'completed' => 'Der Vorgang wurde abgeschlossen.',
        default => $message,
    };
};
?>
<?php
$contacts = $contacts ?? [];
$errors = $errors ?? [];
$message = (string) ($message ?? '');
$csrfToken = (string) ($csrfToken ?? '');
$deleteTokens = $deleteTokens ?? [];
$personId = 0;
$label = 'Meine Kontakte';
?>

<section class="section--page-title section--page-title-compact">
    <div class="page-title page-title--card page-title--split page-title--compact">
        <div class="page-title__main">
            <p class="page-title__kicker">Konto</p>
            <h1 class="page-title__title">Meine Kontakte</h1>
            <p class="page-title__lead">Eigene Kontaktmöglichkeiten verwalten.</p>
        </div>
        <div class="page-title__side">
            <div class="page-title__actions btn-group">
                <a class="btn btn--outline" href="/konto/profil">Zur Person</a>
            </div>
        </div>
    </div>
</section>

<section>
    
<?php if (trim((string) ($message ?? '')) !== ''): ?>
    <div class="form__notice form__notice--success">
        <?= $e($messageText((string) $message)) ?>
    </div>
<?php endif; ?>

<?php if (($errors ?? []) !== []): ?>
    <div class="form__notice form__notice--error">
        <strong>Bitte prüfen:</strong>
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= $e($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

    <div class="table-block table-block--card">
        <div class="table-block__header">
            <h2 class="table-block__title">Kontakte</h2>
        </div>
        <div class="table-wrapper table-wrapper--bordered">
            <table class="table table--striped table--hover table--compact table--stack">
                <thead>
                <tr><th>Typ</th><th>Wert</th><th>Bevorzugt</th><th>Aktion</th></tr>
                </thead>
                <tbody>
                <?php if ($contacts === []): ?>
                    <tr><td colspan="4">Keine Kontakte vorhanden.</td></tr>
                <?php endif; ?>
                <?php foreach ($contacts as $contact): ?>
                    <?php $contactId = (int) ($contact['id'] ?? 0); ?>
                    <tr>
                        <td data-label="Typ"><?= $e($contact['label'] ?? $contact['type'] ?? '') ?></td>
                        <td data-label="Wert"><?= $e($contact['value'] ?? '') ?></td>
                        <td data-label="Bevorzugt"><?= !empty($contact['is_primary']) ? 'Ja' : 'Nein' ?></td>
                        <td data-label="Aktion" class="table__cell--actions">
                            <form method="post" action="/konto/kontakte/<?= $contactId ?>/delete">
                                <button class="btn btn--xs btn--danger" type="submit">Löschen</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<section>
    <form class="form form--card" method="post" action="/konto/kontakte">
        <div class="form__grid form__grid--4">
            <div class="form__title">
                <h2>Kontakt hinzufügen</h2>
            </div>

            <div class="form__field">
                <label class="form__label" for="type">Typ</label>
                <select class="form__control" id="type" name="type">
                    <option value="email">E-Mail</option>
                    <option value="phone">Telefon</option>
                    <option value="mobile">Mobil</option>
                    <option value="website">Website</option>
                    <option value="other">Sonstiges</option>
                </select>
            </div>

            <div class="form__field form__field--span-2">
                <label class="form__label" for="value">Wert</label>
                <input class="form__control" id="value" name="value" required>
            </div>

            <label class="form__check">
                <input class="form__check-input" type="checkbox" name="is_primary" value="1">
                <span class="form__check-label">Bevorzugt</span>
            </label>

            <div class="form__actions">
                <button class="btn btn--primary" type="submit">Kontakt speichern</button>
            </div>
        </div>
    </form>
</section>
