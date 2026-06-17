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
$log = $log ?? [];
$logId = (int) ($log['id'] ?? 0);
$decode = static function (mixed $value): string {
    if ($value === null || $value === '') {
        return '';
    }

    if (is_array($value)) {
        return json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?: '';
    }

    $decoded = json_decode((string) $value, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        return json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?: '';
    }

    return (string) $value;
};
?>

<section class="section--page-title section--page-title-compact">
    <div class="page-title page-title--card page-title--split page-title--compact">
        <div class="page-title__main">
            <p class="page-title__kicker">Audit-Log</p>
            <h1 class="page-title__title">Audit-Eintrag #<?= $logId ?></h1>
            <p class="page-title__lead">Technische Detailansicht eines protokollierten Ereignisses.</p>
        </div>
        <div class="page-title__side">
            <div class="page-title__actions btn-group">
                <a class="btn btn--outline" href="/verwaltung/audit">Zurück zum Audit-Log</a>
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
    <div class="grid">
        <article class="summary-card summary-card--info">
            <p class="summary-card__kicker">Aktion</p>
            <h2 class="summary-card__title"><code><?= $e($log['action'] ?? '') ?></code></h2>
            <p class="summary-card__text"><?= $e($log['occurred_at'] ?? '') ?></p>
        </article>

        <article class="summary-card summary-card--primary">
            <p class="summary-card__kicker">Entität</p>
            <h2 class="summary-card__title"><?= $e($log['entity_type'] ?? '') ?> #<?= (int) ($log['entity_id'] ?? 0) ?></h2>
            <p class="summary-card__text"><?= $e($log['entity_label'] ?? '') ?></p>
        </article>
    </div>
</section>

<section>
    <div class="table-block table-block--card">
        <div class="table-block__header">
            <h2 class="table-block__title">Details</h2>
        </div>
        <div class="table-wrapper table-wrapper--bordered">
            <table class="table table--compact table--striped">
                <tbody>
                <?php foreach ([
                    'ID' => $logId,
                    'Akteur User-ID' => $log['actor_user_id'] ?? '',
                    'Akteur Person-ID' => $log['actor_person_id'] ?? '',
                    'IP-Adresse' => $log['ip_address'] ?? '',
                    'User-Agent' => $log['user_agent'] ?? '',
                ] as $key => $value): ?>
                    <tr><th><?= $e($key) ?></th><td><?= $e($value) ?></td></tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<section>
    <div class="summary-group summary-group--card">
        <?php foreach (['old_values' => 'Alte Werte', 'new_values' => 'Neue Werte', 'metadata' => 'Metadaten'] as $key => $label): ?>
            <details class="summary summary--info">
                <summary>
                    <span class="summary__main">
                        <span class="summary__title"><?= $e($label) ?></span>
                    </span>
                </summary>
                <div class="summary__content">
                    <pre><code><?= $e($decode($log[$key] ?? '')) ?></code></pre>
                </div>
            </details>
        <?php endforeach; ?>
    </div>
</section>
