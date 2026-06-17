<?php
$e = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

$erasure = $erasure ?? [];
$requestId = (int) ($erasure['id'] ?? 0);
$personId = (int) ($erasure['person_id'] ?? 0);
$status = (string) ($erasure['status'] ?? '');
$message = (string) ($message ?? '');
?>

<section class="content-section">
    <header class="content-header">
        <div>
            <h1><?= $e($pageTitle ?? 'DSGVO-Vorgang') ?></h1>
            <p><?= $e($erasure['display_name'] ?? '') ?></p>
        </div>

        <p>
            <a class="button" href="/verwaltung/datenschutz">Zurück</a>
            <a class="button" href="/verwaltung/personen/<?= $personId ?>">Person anzeigen</a>
        </p>
    </header>

    <?php if ($message !== ''): ?>
        <div class="notice notice-success"><?= $e($message) ?></div>
    <?php endif; ?>

    <section class="card">
        <h2>Vorgang</h2>

        <dl>
            <dt>ID</dt>
            <dd><?= $requestId ?></dd>

            <dt>Status</dt>
            <dd><?= $e($status) ?></dd>

            <dt>Person</dt>
            <dd>
                <?= $e($erasure['display_name'] ?? '') ?>
                <?php if (!empty($erasure['login_email'])): ?>
                    <br><?= $e($erasure['login_email']) ?>
                <?php endif; ?>
                <br>Person #<?= $personId ?>
            </dd>

            <dt>Grund</dt>
            <dd><?= nl2br($e($erasure['reason'] ?? '')) ?></dd>

            <dt>Prüfnotiz</dt>
            <dd><?= nl2br($e($erasure['review_note'] ?? '')) ?></dd>
        </dl>
    </section>

    <section class="card">
        <h2>Zeitpunkte</h2>

        <dl>
            <dt>Beantragt</dt>
            <dd><?= $e($erasure['requested_at'] ?? '') ?> <?= !empty($erasure['requested_by_email']) ? 'durch ' . $e($erasure['requested_by_email']) : '' ?></dd>

            <dt>Freigegeben</dt>
            <dd><?= $e($erasure['approved_at'] ?? '') ?> <?= !empty($erasure['approved_by_email']) ? 'durch ' . $e($erasure['approved_by_email']) : '' ?></dd>

            <dt>Abgeschlossen</dt>
            <dd><?= $e($erasure['completed_at'] ?? '') ?> <?= !empty($erasure['completed_by_email']) ? 'durch ' . $e($erasure['completed_by_email']) : '' ?></dd>

            <dt>Abgelehnt</dt>
            <dd><?= $e($erasure['rejected_at'] ?? '') ?></dd>

            <dt>Storniert</dt>
            <dd><?= $e($erasure['cancelled_at'] ?? '') ?></dd>
        </dl>
    </section>

    <?php if (in_array($status, ['requested', 'approved'], true)): ?>
        <section class="card">
            <h2>Prüfung</h2>

            <form method="post" action="/verwaltung/datenschutz/<?= $requestId ?>/approve" class="stack-form">
                <label>
                    Prüfnotiz
                    <textarea name="review_note" rows="3"><?= $e($erasure['review_note'] ?? '') ?></textarea>
                </label>
                <button type="submit">Freigeben</button>
            </form>

            <form method="post" action="/verwaltung/datenschutz/<?= $requestId ?>/reject" class="stack-form">
                <label>
                    Begründung Ablehnung
                    <textarea name="review_note" rows="3"><?= $e($erasure['review_note'] ?? '') ?></textarea>
                </label>
                <button type="submit">Ablehnen</button>
            </form>

            <form method="post" action="/verwaltung/datenschutz/<?= $requestId ?>/cancel" class="stack-form">
                <label>
                    Storno-Notiz
                    <textarea name="review_note" rows="3"><?= $e($erasure['review_note'] ?? '') ?></textarea>
                </label>
                <button type="submit">Stornieren</button>
            </form>
        </section>
    <?php endif; ?>

    <?php if ($status === 'approved'): ?>
        <section class="card danger-zone">
            <h2>Anonymisierung abschließen</h2>

            <p>
                Dadurch werden Kontakte, Adressen, Namen, Gruppenzuweisungen, Login-Daten und offene Einladungen der Person entfernt oder anonymisiert.
            </p>

            <form method="post" action="/verwaltung/datenschutz/<?= $requestId ?>/complete">
                <button type="submit">Anonymisierung endgültig ausführen</button>
            </form>
        </section>
    <?php endif; ?>
</section>
