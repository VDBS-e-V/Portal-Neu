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
$pageGroups = $pageGroups ?? [];
$message = (string) ($message ?? '');
?>

<section class="section--page-title section--page-title-compact">
    <div class="page-title page-title--card page-title--split page-title--compact">
        <div class="page-title__main">
            <p class="page-title__kicker">Berechtigungen</p>
            <h1 class="page-title__title">PageGroups</h1>
            <p class="page-title__lead">Technische Seitenbereiche, die über Gruppen freigeschaltet werden.</p>
        </div>
        <div class="page-title__side">
            <div class="page-title__actions btn-group">
                <a class="btn btn--outline" href="/verwaltung/berechtigungen">Zur Matrix</a>
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
            <h2 class="table-block__title">PageGroups</h2>
        </div>
        <div class="table-wrapper table-wrapper--bordered">
            <table class="table table--striped table--hover table--compact table--stack">
                <thead>
                <tr><th>ID</th><th>Area</th><th>Key</th><th>Name</th><th>Status</th></tr>
                </thead>
                <tbody>
                <?php foreach ($pageGroups as $pageGroup): ?>
                    <tr>
                        <td data-label="ID">#<?= (int) ($pageGroup['id'] ?? 0) ?></td>
                        <td data-label="Area"><code><?= $e($pageGroup['area_key'] ?? '') ?></code></td>
                        <td data-label="Key"><code><?= $e($pageGroup['page_group_key'] ?? '') ?></code></td>
                        <td data-label="Name"><?= $e($pageGroup['name'] ?? '') ?></td>
                        <td data-label="Status">
                            <span class="<?= !empty($pageGroup['is_active']) ? 'table-badge table-badge--success' : 'table-badge table-badge--neutral' ?>">
                                <?= !empty($pageGroup['is_active']) ? 'Aktiv' : 'Inaktiv' ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($pageGroups === []): ?>
                    <tr><td colspan="5">Keine PageGroups vorhanden.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
