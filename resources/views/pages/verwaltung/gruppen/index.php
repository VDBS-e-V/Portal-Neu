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
$groups = $groups ?? [];
$message = (string) ($message ?? '');
?>

<section class="section--page-title section--page-title-compact">
    <div class="page-title page-title--card page-title--split page-title--compact">
        <div class="page-title__main">
            <p class="page-title__kicker">Verwaltung</p>
            <h1 class="page-title__title">Gruppen</h1>
            <p class="page-title__lead">Berechtigungsgruppen und Systemgruppen verwalten.</p>
        </div>
        <div class="page-title__side">
            <div class="page-title__actions btn-group">
                <a class="btn btn--primary" href="/verwaltung/gruppen/create">Gruppe anlegen</a>
                <a class="btn btn--outline" href="/verwaltung/berechtigungen">Berechtigungen</a>
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

    <div class="table-block table-block--card">
        <div class="table-block__header">
            <h2 class="table-block__title">Gruppenübersicht</h2>
        </div>
        <div class="table-wrapper table-wrapper--bordered">
            <table class="table table--striped table--hover table--compact table--stack">
                <thead>
                <tr><th>ID</th><th>Key</th><th>Name</th><th>System</th><th>Aktionen</th></tr>
                </thead>
                <tbody>
                <?php foreach ($groups as $group): ?>
                    <?php $groupId = (int) ($group['id'] ?? 0); ?>
                    <tr>
                        <td data-label="ID">#<?= $groupId ?></td>
                        <td data-label="Key"><code><?= $e($group['group_key'] ?? '') ?></code></td>
                        <td data-label="Name">
                            <a href="/verwaltung/gruppen/<?= $groupId ?>"><?= $e($group['name'] ?? '') ?></a>
                            <span class="table__subtext"><?= $e($group['description'] ?? '') ?></span>
                        </td>
                        <td data-label="System">
                            <span class="<?= !empty($group['is_system']) ? 'table-badge table-badge--info' : 'table-badge table-badge--neutral' ?>">
                                <?= !empty($group['is_system']) ? 'System' : 'Normal' ?>
                            </span>
                        </td>
                        <td data-label="Aktionen" class="table__cell--actions">
                            <div class="btn-group btn-group--gap-xs">
                                <a class="btn btn--xs btn--outline" href="/verwaltung/gruppen/<?= $groupId ?>">Öffnen</a>
                                <a class="btn btn--xs btn--ghost" href="/verwaltung/gruppen/<?= $groupId ?>/edit">Bearbeiten</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($groups === []): ?>
                    <tr><td colspan="5">Keine Gruppen vorhanden.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
