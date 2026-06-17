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
$loginEvents = $loginEvents ?? $events ?? [];
?>

<section class="section--page-title section--page-title-compact">
    <div class="page-title page-title--card page-title--split page-title--compact">
        <div class="page-title__main">
            <p class="page-title__kicker">Konto</p>
            <h1 class="page-title__title">Sicherheit</h1>
            <p class="page-title__lead">Letzte Login- und Sicherheitsereignisse.</p>
        </div>
        <div class="page-title__side"><div class="page-title__actions btn-group"><a class="btn btn--outline" href="/konto">Konto</a></div></div>
    </div>
</section>

<section>
    <div class="table-block table-block--card">
        <div class="table-block__header">
            <h2 class="table-block__title">Sicherheitsereignisse</h2>
        </div>
        <div class="table-wrapper table-wrapper--bordered">
            <table class="table table--striped table--hover table--compact table--stack">
                <thead><tr><th>Zeitpunkt</th><th>Typ</th><th>IP-Adresse</th><th>Ergebnis</th></tr></thead>
                <tbody>
                <?php if ($loginEvents === []): ?><tr><td colspan="4">Keine Ereignisse vorhanden.</td></tr><?php endif; ?>
                <?php foreach ($loginEvents as $event): ?>
                    <tr>
                        <td data-label="Zeitpunkt"><?= $e($event['occurred_at'] ?? $event['created_at'] ?? '') ?></td>
                        <td data-label="Typ"><code><?= $e($event['event_type'] ?? $event['type'] ?? '') ?></code></td>
                        <td data-label="IP-Adresse"><?= $e($event['ip_address'] ?? '') ?></td>
                        <td data-label="Ergebnis"><span class="<?= $statusBadge($event['status'] ?? $event['result'] ?? 'info') ?>"><?= $e($event['status'] ?? $event['result'] ?? '') ?></span></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
