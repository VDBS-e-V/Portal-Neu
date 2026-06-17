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
$stats = $stats ?? [];
$quickLinks = $quickLinks ?? [];
$latestPersons = $latestPersons ?? [];
$latestAuditEntries = $latestAuditEntries ?? [];
$openTasks = $openTasks ?? [];
$number = static fn (mixed $value): string => number_format((int) $value, 0, ',', '.');
?>

<section class="section--page-title section--page-title-compact">
    <div class="page-title page-title--card page-title--split page-title--compact">
        <div class="page-title__main">
            <p class="page-title__kicker">Verwaltung</p>
            <h1 class="page-title__title">Verwaltung</h1>
            <p class="page-title__lead">Übersicht über Personen, Logins, Gruppen, Berechtigungen und offene Aufgaben.</p>
        </div>
        
    </div>
</section>

<section class="no-padding">
    <?php
    $navFile = __DIR__ . '/../../partials/verwaltung_nav.php';
    if (is_file($navFile)) {
        require $navFile;
    }
    ?>
</section>

<section>
    <div class="grid">
        <?php foreach ([
            ['Personen', $stats['persons_total'] ?? 0, 'Aktiv: ' . $number($stats['persons_active'] ?? 0) . ' · Deaktiviert: ' . $number($stats['persons_disabled'] ?? 0), 'primary'],
            ['Logins', $stats['logins_total'] ?? 0, 'Aktiv: ' . $number($stats['logins_active'] ?? 0) . ' · Eingeladen: ' . $number($stats['logins_invited'] ?? 0), 'secondary-cta'],
            ['Gruppen', $stats['groups_total'] ?? 0, 'Systemgruppen: ' . $number($stats['groups_system'] ?? 0), 'info'],
            ['PageGroups', $stats['page_groups_total'] ?? 0, 'Aktiv: ' . $number($stats['page_groups_active'] ?? 0), 'success'],
            ['Einladungen', $stats['pending_invitations'] ?? 0, 'offen', 'warning'],
            ['DSGVO', $stats['open_erasure_requests'] ?? 0, 'offene Vorgänge', 'danger'],
        ] as $card): ?>
            <article class="summary-card summary-card--<?= $e($card[3]) ?>">
                <p class="summary-card__kicker"><?= $e($card[0]) ?></p>
                <h2 class="summary-card__title"><?= $number($card[1]) ?></h2>
                <p class="summary-card__text"><?= $e($card[2]) ?></p>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section>
    <div class="grid">
        <div class="summary-group summary-group--card">
            <div class="summary-group__header">
                <p class="summary-group__kicker">Schnellzugriffe</p>
                <h2 class="summary-group__title">Direkt starten</h2>
            </div>
            <?php foreach ($quickLinks as $link): ?>
                <article class="summary-card summary-card--primary">
                    <h3 class="summary-card__title"><?= $e($link['label'] ?? '') ?></h3>
                    <p class="summary-card__text"><?= $e($link['description'] ?? '') ?></p>
                    <div class="summary-card__actions">
                        <a class="btn btn--sm btn--outline" href="<?= $e($link['href'] ?? '#') ?>">Öffnen</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <div class="summary-group summary-group--card">
            <div class="summary-group__header">
                <p class="summary-group__kicker">Aufgaben</p>
                <h2 class="summary-group__title">Offene Aufgaben</h2>
            </div>
            <?php if ($openTasks === []): ?>
                <p>Keine offenen Aufgaben.</p>
            <?php endif; ?>
            <?php foreach ($openTasks as $task): ?>
                <details class="summary summary--warning">
                    <summary><span class="summary__main"><span class="summary__title"><?= $e($task['label'] ?? '') ?></span><span class="summary__subtitle"><?= $e($task['title'] ?? '') ?></span></span></summary>
                    <div class="summary__content">
                        <p><?= $e($task['subtitle'] ?? '') ?></p>
                        <div class="summary__actions"><a class="btn btn--sm btn--outline" href="<?= $e($task['href'] ?? '#') ?>">Öffnen</a></div>
                    </div>
                </details>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section>
    <div class="grid">
        <div class="table-block table-block--card">
            <div class="table-block__header"><h2 class="table-block__title">Neue Personen</h2></div>
            <div class="table-wrapper table-wrapper--bordered">
                <table class="table table--striped table--compact table--stack">
                    <thead><tr><th>ID</th><th>Name</th><th>Login</th><th>Status</th></tr></thead>
                    <tbody>
                    <?php if ($latestPersons === []): ?><tr><td colspan="4">Keine Personen vorhanden.</td></tr><?php endif; ?>
                    <?php foreach ($latestPersons as $person): ?>
                        <?php $personId = (int) ($person['id'] ?? 0); ?>
                        <tr>
                            <td data-label="ID">#<?= $personId ?></td>
                            <td data-label="Name"><a href="/verwaltung/personen/<?= $personId ?>"><?= $e($person['display_name'] ?? 'Person #' . $personId) ?></a></td>
                            <td data-label="Login"><?= $e($person['login_email'] ?? '') ?></td>
                            <td data-label="Status"><span class="<?= $statusBadge($person['status'] ?? '') ?>"><?= $e($person['status'] ?? '') ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="table-block table-block--card">
            <div class="table-block__header"><h2 class="table-block__title">Letzte Audit-Einträge</h2></div>
            <div class="table-wrapper table-wrapper--bordered">
                <table class="table table--striped table--compact table--stack">
                    <thead><tr><th>Zeitpunkt</th><th>Aktion</th><th>Entität</th></tr></thead>
                    <tbody>
                    <?php if ($latestAuditEntries === []): ?><tr><td colspan="3">Keine Audit-Einträge vorhanden.</td></tr><?php endif; ?>
                    <?php foreach ($latestAuditEntries as $entry): ?>
                        <?php $entryId = (int) ($entry['id'] ?? 0); ?>
                        <tr>
                            <td data-label="Zeitpunkt"><?= $e($entry['occurred_at'] ?? '') ?></td>
                            <td data-label="Aktion"><a href="/verwaltung/audit/<?= $entryId ?>"><code><?= $e($entry['action'] ?? '') ?></code></a></td>
                            <td data-label="Entität"><?= $e($entry['entity_type'] ?? '') ?><?php if (!empty($entry['entity_id'])): ?> #<?= (int) $entry['entity_id'] ?><?php endif; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
