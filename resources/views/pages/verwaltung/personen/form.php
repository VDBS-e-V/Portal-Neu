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
$person = $person ?? [];
$errors = $errors ?? [];
$mode = (string) ($mode ?? 'create');
$action = (string) ($action ?? '/verwaltung/personen/create');
?>

<section class="section--page-title section--page-title-compact">
    <div class="page-title page-title--card page-title--split page-title--compact">
        <div class="page-title__main">
            <p class="page-title__kicker">Personenverwaltung</p>
            <h1 class="page-title__title"><?= $e($pageTitle ?? ($mode === 'edit' ? 'Person bearbeiten' : 'Person anlegen')) ?></h1>
            <p class="page-title__lead">Personendaten pflegen und optional einen Login-Zugang vorbereiten.</p>
        </div>
        <div class="page-title__side">
            <div class="page-title__actions btn-group">
                <a class="btn btn--outline" href="/verwaltung/personen">Zur Personenliste</a>
            </div>
        </div>
    </div>
</section>

<section class="no-padding">
    <?php
    $navFile = __DIR__ . '/../../../partials/verwaltung_nav.php';
    if (is_file($navFile)) {
        require $navFile;
    }
    ?>
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

    <form class="form form--card" method="post" action="<?= $e($action) ?>">
        <div class="form__grid form__grid--4">
            <div class="form__section-title">
                <h2>Stammdaten</h2>
                <p>Mindestens ein Anzeigename oder ein Vor-/Nachname sollte gepflegt werden.</p>
            </div>

            <div class="form__field">
                <label class="form__label" for="salutation">Anrede</label>
                <input class="form__control" id="salutation" name="salutation" value="<?= $e($person['salutation'] ?? '') ?>">
            </div>

            <div class="form__field">
                <label class="form__label" for="title">Titel</label>
                <input class="form__control" id="title" name="title" value="<?= $e($person['title'] ?? '') ?>">
            </div>

            <div class="form__field form__field--span-2">
                <label class="form__label" for="display_name">Anzeigename</label>
                <input class="form__control" id="display_name" name="display_name" value="<?= $e($person['display_name'] ?? '') ?>">
            </div>

            <div class="form__field">
                <label class="form__label" for="first_name">Vorname</label>
                <input class="form__control" id="first_name" name="first_name" value="<?= $e($person['first_name'] ?? '') ?>">
            </div>

            <div class="form__field">
                <label class="form__label" for="middle_name">Weitere Vornamen</label>
                <input class="form__control" id="middle_name" name="middle_name" value="<?= $e($person['middle_name'] ?? '') ?>">
            </div>

            <div class="form__field">
                <label class="form__label" for="last_name">Nachname</label>
                <input class="form__control" id="last_name" name="last_name" value="<?= $e($person['last_name'] ?? '') ?>">
            </div>

            <div class="form__field">
                <label class="form__label" for="preferred_name">Rufname</label>
                <input class="form__control" id="preferred_name" name="preferred_name" value="<?= $e($person['preferred_name'] ?? '') ?>">
            </div>

            <div class="form__field">
                <label class="form__label" for="pronouns">Pronomen</label>
                <input class="form__control" id="pronouns" name="pronouns" value="<?= $e($person['pronouns'] ?? '') ?>">
            </div>

            <div class="form__field">
                <label class="form__label" for="status">Status</label>
                <select class="form__control" id="status" name="status">
                    <?php foreach (['active' => 'Aktiv', 'disabled' => 'Deaktiviert', 'invited' => 'Eingeladen', 'erasure_requested' => 'DSGVO beantragt'] as $value => $label): ?>
                        <option value="<?= $e($value) ?>" <?= (string) ($person['status'] ?? 'active') === $value ? 'selected' : '' ?>>
                            <?= $e($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form__section-title">
                <h2>Optionaler Login</h2>
                <p>Ein Login ist optional. Eine E-Mail ist nur nötig, wenn ein Login eingerichtet wird.</p>
            </div>

            <div class="form__field form__field--span-2">
                <label class="form__label" for="login_email">Login-E-Mail</label>
                <input class="form__control" id="login_email" name="login_email" type="email" value="<?= $e($person['login_email'] ?? $person['email'] ?? '') ?>">
            </div>

            <div class="form__field form__field--span-2">
                <label class="form__label" for="login_password">Initiales Passwort</label>
                <input class="form__control" id="login_password" name="login_password" type="password" minlength="8" autocomplete="new-password">
                <p class="form__hint">Leer lassen, wenn kein Passwort gesetzt oder geändert werden soll.</p>
            </div>

            <div class="form__actions">
                <button class="btn btn--primary" type="submit">Speichern</button>
                <a class="btn btn--outline" href="/verwaltung/personen">Abbrechen</a>
            </div>
        </div>
    </form>
</section>
