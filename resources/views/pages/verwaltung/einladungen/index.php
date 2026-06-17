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
$invitations = $invitations ?? [];
$lastInvitationUrl = (string) ($lastInvitationUrl ?? '');
$message = (string) ($message ?? '');
?>

<section class="section--page-title section--page-title-compact">
    <div class="page-title page-title--card page-title--split page-title--compact">
        <div class="page-title__main">
            <p class="page-title__kicker">Verwaltung</p>
            <h1 class="page-title__title">Einladungen</h1>
            <p class="page-title__lead">Account-Einladungen prüfen, Links kopieren und offene Einladungen widerrufen.</p>
        </div>
        <div class="page-title__side"><div class="page-title__actions btn-group"><a class="btn btn--outline" href="/verwaltung/personen">Personen</a></div></div>
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

    <?php if ($lastInvitationUrl !== ''): ?>
        <div class="form__notice form__notice--info">
            <strong>Einladungslink:</strong>
            <pre><code><?= $e($lastInvitationUrl) ?></code></pre>
        </div>
    <?php endif; ?>

    <div class="table-block table-block--card">
        <div class="table-block__header">
            <h2 class="table-block__title">Einladungen</h2>
        </div>
        <div class="table-wrapper table-wrapper--bordered">
            <table class="table table--striped table--hover table--compact table--stack">
                <thead>
                <tr><th>ID</th><th>E-Mail</th><th>Person</th><th>Status</th><th>Ablauf</th><th>Aktion</th></tr>
                </thead>
                <tbody>
                <?php if ($invitations === []): ?>
                    <tr><td colspan="6">Keine Einladungen vorhanden.</td></tr>
                <?php endif; ?>
                <?php foreach ($invitations as $invitation): ?>
                    <?php $invitationId = (int) ($invitation['id'] ?? 0); ?>
                    <tr>
                        <td data-label="ID">#<?= $invitationId ?></td>
                        <td data-label="E-Mail"><?= $e($invitation['email'] ?? $invitation['user_email'] ?? '') ?></td>
                        <td data-label="Person"><?= $e($invitation['person_display_name'] ?? '') ?></td>
                        <td data-label="Status"><span class="<?= $statusBadge($invitation['status'] ?? '') ?>"><?= $e($invitation['status'] ?? '') ?></span></td>
                        <td data-label="Ablauf"><?= $e($invitation['expires_at'] ?? '') ?></td>
                        <td data-label="Aktion" class="table__cell--actions">
                            <?php if (($invitation['status'] ?? '') === 'pending'): ?>
                                <form method="post" action="/verwaltung/einladungen/<?= $invitationId ?>/revoke">
                                    <button class="btn btn--xs btn--danger" type="submit">Widerrufen</button>
                                </form>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
