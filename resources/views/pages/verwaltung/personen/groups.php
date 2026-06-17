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
$assignedGroups = $assignedGroups ?? [];
$allGroups = $allGroups ?? [];
$message = (string) ($message ?? '');
$personId = (int) ($person['id'] ?? 0);
$assignedIds = [];
foreach ($assignedGroups as $group) {
    $assignedIds[(int) ($group['id'] ?? 0)] = true;
}
$label = trim((string) ($person['display_name'] ?? 'Person #' . $personId));
?>

<section class="section--page-title section--page-title-compact">
    <div class="page-title page-title--card page-title--split page-title--compact">
        <div class="page-title__main">
            <p class="page-title__kicker">Personenverwaltung</p>
            <h1 class="page-title__title">Gruppen: <?= $e($label) ?></h1>
            <p class="page-title__lead">Berechtigungsgruppen dieser Person pflegen.</p>
        </div>
        <div class="page-title__side">
            <div class="page-title__actions btn-group">
                <a class="btn btn--outline" href="/verwaltung/personen/<?= $personId ?>">Zur Person</a>
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

    <form class="form form--card" method="post" action="/verwaltung/personen/<?= $personId ?>/gruppen">
        <div class="form__grid form__grid--1">
            <fieldset class="form__fieldset">
                <legend class="form__legend">Gruppen auswählen</legend>
                <div class="form__choice-group">
                    <?php foreach ($allGroups as $group): ?>
                        <?php $groupId = (int) ($group['id'] ?? 0); ?>
                        <label class="form__check">
                            <input
                                class="form__check-input"
                                type="checkbox"
                                name="group_ids[]"
                                value="<?= $groupId ?>"
                                <?= isset($assignedIds[$groupId]) ? 'checked' : '' ?>
                            >
                            <span class="form__check-label">
                                <?= $e($group['name'] ?? $group['group_key'] ?? '') ?>
                                <small><?= $e($group['description'] ?? $group['group_key'] ?? '') ?></small>
                            </span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </fieldset>

            <div class="form__actions">
                <button class="btn btn--primary" type="submit">Gruppen speichern</button>
                <a class="btn btn--outline" href="/verwaltung/personen/<?= $personId ?>">Abbrechen</a>
            </div>
        </div>
    </form>
</section>
