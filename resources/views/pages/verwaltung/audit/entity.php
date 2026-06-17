<?php
$e = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

$entries = $entries ?? [];
$entityTitle = (string) ($entityTitle ?? '');
$entitySubtitle = (string) ($entitySubtitle ?? '');
$backHref = (string) ($backHref ?? '/verwaltung/audit');
$backLabel = (string) ($backLabel ?? 'Zurück');
?>

<section class="content-section">
    <header class="content-header">
        <div>
            <h1><?= $e($pageTitle ?? 'Audit') ?></h1>
            <?php if ($entitySubtitle !== ''): ?>
                <p><?= $e($entitySubtitle) ?></p>
            <?php endif; ?>
        </div>

        <p>
            <a class="button" href="<?= $e($backHref) ?>"><?= $e($backLabel) ?></a>
            <a class="button" href="/verwaltung/audit">Audit-Log</a>
        </p>
    </header>

    <section class="card">
        <h2>Entität</h2>

        <dl>
            <dt>Titel</dt>
            <dd><?= $e($entityTitle) ?></dd>

            <dt>Typ</dt>
            <dd><code><?= $e($entityType ?? '') ?></code></dd>

            <dt>ID</dt>
            <dd><?= (int) ($entityId ?? 0) ?></dd>
        </dl>
    </section>

    <?php require __DIR__ . '/../../partials/audit_timeline.php'; ?>
</section>
