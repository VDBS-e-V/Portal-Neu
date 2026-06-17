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
$logs = $logs ?? [];
$filters = $filters ?? [];
$actions = $actions ?? [];
$entityTypes = $entityTypes ?? [];
?>

<section class="section--page-title section--page-title-compact">
    <div class="page-title page-title--card page-title--split page-title--compact">
        <div class="page-title__main">
            <p class="page-title__kicker">Verwaltung</p>
            <h1 class="page-title__title">Audit-Log</h1>
            <p class="page-title__lead">Änderungen, sicherheitsrelevante Aktionen und Verwaltungsereignisse nachvollziehen.</p>
        </div>
        <div class="page-title__side">
            <div class="page-title__actions btn-group">
                <a class="btn btn--outline" href="/verwaltung">Zur Übersicht</a>
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
    <form class="form form--surface" method="get" action="/verwaltung/audit">
        <div class="form__grid form__grid--4 form__grid--compact">
            <div class="form__field">
                <label class="form__label" for="audit-q">Suche</label>
                <input class="form__control" id="audit-q" name="q" value="<?= $e($filters['q'] ?? '') ?>">
            </div>

            <div class="form__field">
                <label class="form__label" for="audit-action">Aktion</label>
                <select class="form__control" id="audit-action" name="action">
                    <option value="">Alle</option>
                    <?php foreach ($actions as $action): ?>
                        <option value="<?= $e($action) ?>" <?= (string) ($filters['action'] ?? '') === (string) $action ? 'selected' : '' ?>>
                            <?= $e($action) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form__field">
                <label class="form__label" for="audit-entity">Entität</label>
                <select class="form__control" id="audit-entity" name="entity_type">
                    <option value="">Alle</option>
                    <?php foreach ($entityTypes as $entityType): ?>
                        <option value="<?= $e($entityType) ?>" <?= (string) ($filters['entity_type'] ?? '') === (string) $entityType ? 'selected' : '' ?>>
                            <?= $e($entityType) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form__field">
                <label class="form__label" for="audit-limit">Limit</label>
                <input class="form__control" id="audit-limit" type="number" name="limit" value="<?= (int) ($filters['limit'] ?? 100) ?>" min="1" max="500">
            </div>

            <div class="form__actions">
                <button class="btn btn--primary" type="submit">Filtern</button>
                <a class="btn btn--outline" href="/verwaltung/audit">Zurücksetzen</a>
            </div>
        </div>
    </form>
</section>

<section>
    <div class="table-block table-block--card">
        <div class="table-block__header">
            <h2 class="table-block__title">Audit-Einträge</h2>
        </div>
        <div class="table-wrapper table-wrapper--bordered">
            <table class="table table--striped table--hover table--compact table--stack">
                <thead>
                <tr><th>Zeitpunkt</th><th>Aktion</th><th>Entität</th><th>Akteur</th><th>Aktion</th></tr>
                </thead>
                <tbody>
                <?php if ($logs === []): ?>
                    <tr><td colspan="5">Keine Audit-Einträge gefunden.</td></tr>
                <?php endif; ?>
                <?php foreach ($logs as $log): ?>
                    <?php $logId = (int) ($log['id'] ?? 0); ?>
                    <tr>
                        <td data-label="Zeitpunkt"><?= $e($log['occurred_at'] ?? '') ?></td>
                        <td data-label="Aktion"><code><?= $e($log['action'] ?? '') ?></code></td>
                        <td data-label="Entität">
                            <?= $e($log['entity_type'] ?? '') ?>
                            <?php if (!empty($log['entity_id'])): ?>#<?= (int) $log['entity_id'] ?><?php endif; ?>
                            <span class="table__subtext"><?= $e($log['entity_label'] ?? '') ?></span>
                        </td>
                        <td data-label="Akteur"><?= $e($log['actor_email'] ?? $log['actor_user_id'] ?? '') ?></td>
                        <td data-label="Aktion" class="table__cell--actions">
                            <a class="btn btn--xs btn--outline" href="/verwaltung/audit/<?= $logId ?>">Details</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
