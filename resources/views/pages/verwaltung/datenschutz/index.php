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
$requests = $requests ?? [];
$filters = $filters ?? [];
$message = (string) ($message ?? '');
?>

<section class="section--page-title section--page-title-compact">
    <div class="page-title page-title--card page-title--split page-title--compact">
        <div class="page-title__main">
            <p class="page-title__kicker">Verwaltung</p>
            <h1 class="page-title__title">DSGVO-Löschung</h1>
            <p class="page-title__lead">Löschersuchen prüfen, freigeben und Anonymisierung nachvollziehbar abschließen.</p>
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

    <form class="form form--surface" method="get" action="/verwaltung/datenschutz">
        <div class="form__grid form__grid--4 form__grid--compact">
            <div class="form__field form__field--span-2">
                <label class="form__label" for="datenschutz-q">Suche</label>
                <input class="form__control" id="datenschutz-q" name="q" value="<?= $e($filters['q'] ?? '') ?>">
            </div>

            <div class="form__field">
                <label class="form__label" for="datenschutz-status">Status</label>
                <select class="form__control" id="datenschutz-status" name="status">
                    <option value="">Alle</option>
                    <?php foreach (['requested', 'approved', 'rejected', 'cancelled', 'completed'] as $value): ?>
                        <option value="<?= $e($value) ?>" <?= (string) ($filters['status'] ?? '') === $value ? 'selected' : '' ?>>
                            <?= $e($value) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form__actions">
                <button class="btn btn--primary" type="submit">Filtern</button>
                <a class="btn btn--outline" href="/verwaltung/datenschutz">Zurücksetzen</a>
            </div>
        </div>
    </form>
</section>

<section>
    <div class="table-block table-block--card">
        <div class="table-block__header">
            <h2 class="table-block__title">DSGVO-Vorgänge</h2>
        </div>
        <div class="table-wrapper table-wrapper--bordered">
            <table class="table table--striped table--hover table--compact table--stack">
                <thead>
                <tr><th>ID</th><th>Person</th><th>Status</th><th>Beantragt</th><th>Aktion</th></tr>
                </thead>
                <tbody>
                <?php if ($requests === []): ?>
                    <tr><td colspan="5">Keine Vorgänge gefunden.</td></tr>
                <?php endif; ?>
                <?php foreach ($requests as $erasure): ?>
                    <?php $requestId = (int) ($erasure['id'] ?? 0); ?>
                    <tr>
                        <td data-label="ID">#<?= $requestId ?></td>
                        <td data-label="Person"><?= $e($erasure['person_display_name'] ?? $erasure['person_label'] ?? '') ?></td>
                        <td data-label="Status"><span class="<?= $statusBadge($erasure['status'] ?? '') ?>"><?= $e($erasure['status'] ?? '') ?></span></td>
                        <td data-label="Beantragt"><?= $e($erasure['requested_at'] ?? $erasure['created_at'] ?? '') ?></td>
                        <td data-label="Aktion" class="table__cell--actions">
                            <a class="btn btn--xs btn--outline" href="/verwaltung/datenschutz/<?= $requestId ?>">Öffnen</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
