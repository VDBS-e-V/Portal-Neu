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
$token = (string) ($token ?? '');
$reset = $reset ?? [];
$errors = $errors ?? [];
$csrfToken = (string) ($csrfToken ?? '');
$label = trim((string) ($reset['person_display_name'] ?? $reset['user_email'] ?? $reset['email'] ?? ''));
?>

<section class="section--page-title section--page-title-compact">
    <div class="page-title page-title--card page-title--split page-title--compact">
        <div class="page-title__main">
            <p class="page-title__kicker">Passwort</p>
            <h1 class="page-title__title">Passwort neu setzen</h1>
            <p class="page-title__lead">Lege ein neues Passwort für deinen Login fest.</p>
        </div>
        
    </div>
</section>

<section class="small">
    
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

    <?php if ($label !== ''): ?>
        <div class="form__notice form__notice--info"><?= $e($label) ?></div>
    <?php endif; ?>

    <form class="form form--card" method="post" action="/passwort/zuruecksetzen/<?= $e(rawurlencode($token)) ?>">
        <input type="hidden" name="_csrf_token" value="<?= $e($csrfToken) ?>">
        <div class="form__grid form__grid--1">
            <div class="form__field">
                <label class="form__label" for="password">Neues Passwort</label>
                <input class="form__control" id="password" type="password" name="password" minlength="8" required autocomplete="new-password">
            </div>
            <div class="form__field">
                <label class="form__label" for="password_repeat">Passwort wiederholen</label>
                <input class="form__control" id="password_repeat" type="password" name="password_repeat" minlength="8" required autocomplete="new-password">
            </div>
            <div class="form__actions">
                <button class="btn btn--primary" type="submit">Passwort speichern</button>
            </div>
        </div>
    </form>
</section>
