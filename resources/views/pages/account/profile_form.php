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
$profile = $profile ?? $person ?? [];
$errors = $errors ?? [];
$csrfToken = (string) ($csrfToken ?? '');
?>

<section class="section--page-title section--page-title-compact">
    <div class="page-title page-title--card page-title--split page-title--compact">
        <div class="page-title__main">
            <p class="page-title__kicker">Konto</p>
            <h1 class="page-title__title">Profil bearbeiten</h1>
            <p class="page-title__lead">Eigene Stammdaten aktualisieren.</p>
        </div>
        <div class="page-title__side"><div class="page-title__actions btn-group"><a class="btn btn--outline" href="/konto/profil">Zurück</a></div></div>
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

    <form class="form form--card" method="post" action="/konto/profil/bearbeiten">
        <input type="hidden" name="_csrf_token" value="<?= $e($csrfToken) ?>">
        <div class="form__grid form__grid--4">
            <?php foreach ([
                'display_name' => 'Anzeigename',
                'salutation' => 'Anrede',
                'title' => 'Titel',
                'first_name' => 'Vorname',
                'middle_name' => 'Weitere Vornamen',
                'last_name' => 'Nachname',
                'preferred_name' => 'Rufname',
                'pronouns' => 'Pronomen',
            ] as $field => $label): ?>
                <div class="form__field <?= $field === 'display_name' ? 'form__field--span-2' : '' ?>">
                    <label class="form__label" for="<?= $e($field) ?>"><?= $e($label) ?></label>
                    <input class="form__control" id="<?= $e($field) ?>" name="<?= $e($field) ?>" value="<?= $e($profile[$field] ?? '') ?>">
                </div>
            <?php endforeach; ?>

            <div class="form__actions">
                <button class="btn btn--primary" type="submit">Speichern</button>
                <a class="btn btn--outline" href="/konto/profil">Abbrechen</a>
            </div>
        </div>
    </form>
</section>
