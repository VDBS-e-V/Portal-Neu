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
$group = $group ?? [];
$errors = $errors ?? [];
$action = (string) ($action ?? '/verwaltung/gruppen/create');
$mode = (string) ($mode ?? 'create');
?>

<section class="section--page-title section--page-title-compact">
    <div class="page-title page-title--card page-title--split page-title--compact">
        <div class="page-title__main">
            <p class="page-title__kicker">Gruppenverwaltung</p>
            <h1 class="page-title__title"><?= $e($pageTitle ?? ($mode === 'edit' ? 'Gruppe bearbeiten' : 'Gruppe anlegen')) ?></h1>
            <p class="page-title__lead">Gruppenschlüssel, Name und Systemstatus pflegen.</p>
        </div>
        <div class="page-title__side">
            <div class="page-title__actions btn-group">
                <a class="btn btn--outline" href="/verwaltung/gruppen">Zur Gruppenliste</a>
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
        <div class="form__grid form__grid--2">
            <div class="form__field">
                <label class="form__label" for="group_key">Gruppenschlüssel <span class="form__required">*</span></label>
                <input class="form__control" id="group_key" name="group_key" value="<?= $e($group['group_key'] ?? '') ?>" required>
                <p class="form__hint">Beispiel: verwaltung.administrator</p>
            </div>

            <div class="form__field">
                <label class="form__label" for="name">Name <span class="form__required">*</span></label>
                <input class="form__control" id="name" name="name" value="<?= $e($group['name'] ?? '') ?>" required>
            </div>

            <div class="form__field form__field--span-full">
                <label class="form__label" for="description">Beschreibung</label>
                <textarea class="form__control" id="description" name="description"><?= $e($group['description'] ?? '') ?></textarea>
            </div>

            <label class="form__check form__field--span-full">
                <input class="form__check-input" type="checkbox" name="is_system" value="1" <?= !empty($group['is_system']) ? 'checked' : '' ?>>
                <span class="form__check-label">
                    Systemgruppe
                    <small>Systemgruppen sind besonders geschützt und nicht normal löschbar.</small>
                </span>
            </label>

            <div class="form__actions">
                <button class="btn btn--primary" type="submit">Speichern</button>
                <a class="btn btn--outline" href="/verwaltung/gruppen">Abbrechen</a>
            </div>
        </div>
    </form>
</section>
