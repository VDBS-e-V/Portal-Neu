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
$erasure = $erasure ?? [];
$message = (string) ($message ?? '');
$requestId = (int) ($erasure['id'] ?? 0);
$status = (string) ($erasure['status'] ?? '');
?>

<section class="section--page-title section--page-title-compact">
    <div class="page-title page-title--card page-title--split page-title--compact">
        <div class="page-title__main">
            <p class="page-title__kicker">Datenschutz</p>
            <h1 class="page-title__title">DSGVO-Vorgang #<?= $requestId ?></h1>
            <p class="page-title__lead">Status, Prüfung und Abschluss eines Löschersuchens.</p>
        </div>
        <div class="page-title__side"><div class="page-title__actions btn-group"><a class="btn btn--outline" href="/verwaltung/datenschutz">Zur Liste</a><a class="btn btn--outline" href="/verwaltung/datenschutz/<?= $requestId ?>/audit">Audit</a></div></div>
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

    <div class="grid">
        <article class="summary-card summary-card--warning">
            <p class="summary-card__kicker">Status</p>
            <h2 class="summary-card__title"><?= $e($status) ?></h2>
            <p class="summary-card__text"><?= $e($erasure['reason'] ?? '') ?></p>
        </article>

        <article class="summary-card summary-card--info">
            <p class="summary-card__kicker">Person</p>
            <h2 class="summary-card__title"><?= $e($erasure['person_display_name'] ?? $erasure['person_label'] ?? '') ?></h2>
            <p class="summary-card__text">Person-ID: <?= (int) ($erasure['person_id'] ?? 0) ?></p>
        </article>
    </div>
</section>

<section>
    <div class="table-block table-block--card">
        <div class="table-block__header">
            <h2 class="table-block__title">Vorgangsdaten</h2>
        </div>
        <div class="table-wrapper table-wrapper--bordered">
            <table class="table table--compact table--striped">
                <tbody>
                <?php foreach ([
                    'Beantragt' => $erasure['requested_at'] ?? $erasure['created_at'] ?? '',
                    'Freigegeben' => $erasure['approved_at'] ?? '',
                    'Abgeschlossen' => $erasure['completed_at'] ?? '',
                    'Abgelehnt' => $erasure['rejected_at'] ?? '',
                    'Storniert' => $erasure['cancelled_at'] ?? '',
                    'Prüfnotiz' => $erasure['review_note'] ?? '',
                ] as $key => $value): ?>
                    <tr><th><?= $e($key) ?></th><td><?= $e($value) ?></td></tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php if (in_array($status, ['requested', 'approved'], true)): ?>
    <section>
        <div class="grid">
            <form class="form form--card" method="post" action="/verwaltung/datenschutz/<?= $requestId ?>/approve">
                <div class="form__grid form__grid--1">
                    <div class="form__title"><h2>Freigeben</h2></div>
                    <div class="form__field"><label class="form__label" for="approve_note">Prüfnotiz</label><textarea class="form__control" id="approve_note" name="review_note"><?= $e($erasure['review_note'] ?? '') ?></textarea></div>
                    <div class="form__actions"><button class="btn btn--primary" type="submit">Freigeben</button></div>
                </div>
            </form>

            <form class="form form--card" method="post" action="/verwaltung/datenschutz/<?= $requestId ?>/reject">
                <div class="form__grid form__grid--1">
                    <div class="form__title"><h2>Ablehnen</h2></div>
                    <div class="form__field"><label class="form__label" for="reject_note">Begründung</label><textarea class="form__control" id="reject_note" name="review_note"></textarea></div>
                    <div class="form__actions"><button class="btn btn--danger" type="submit">Ablehnen</button></div>
                </div>
            </form>

            <form class="form form--card" method="post" action="/verwaltung/datenschutz/<?= $requestId ?>/cancel">
                <div class="form__grid form__grid--1">
                    <div class="form__title"><h2>Stornieren</h2></div>
                    <div class="form__field"><label class="form__label" for="cancel_note">Storno-Notiz</label><textarea class="form__control" id="cancel_note" name="review_note"></textarea></div>
                    <div class="form__actions"><button class="btn btn--outline" type="submit">Stornieren</button></div>
                </div>
            </form>
        </div>
    </section>
<?php endif; ?>

<?php if ($status === 'approved'): ?>
    <section>
        <form class="form form--card" method="post" action="/verwaltung/datenschutz/<?= $requestId ?>/complete">
            <div class="form__grid form__grid--1">
                <div class="form__notice form__notice--error">
                    Die Anonymisierung entfernt oder anonymisiert Personendaten, Kontakte, Adressen, Gruppenzuweisungen, Login-Daten und offene Einladungen.
                </div>
                <div class="form__actions">
                    <button class="btn btn--danger" type="submit">Anonymisierung endgültig ausführen</button>
                </div>
            </div>
        </form>
    </section>
<?php endif; ?>
