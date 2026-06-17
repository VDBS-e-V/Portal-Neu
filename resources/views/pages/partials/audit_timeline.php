<?php
$e = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

$auditEntries = $auditEntries ?? $entries ?? [];
?>

<div class="summary-group summary-group--card summary-group--compact">
    <div class="summary-group__header">
        <p class="summary-group__kicker">Audit</p>
        <h2 class="summary-group__title">Timeline</h2>
    </div>

    <?php if ($auditEntries === []): ?>
        <p>Keine Audit-Einträge vorhanden.</p>
    <?php endif; ?>

    <?php foreach ($auditEntries as $entry): ?>
        <details class="summary summary--info">
            <summary>
                <span class="summary__main">
                    <span class="summary__title"><?= $e($entry['action'] ?? '') ?></span>
                    <span class="summary__subtitle"><?= $e($entry['entity_label'] ?? '') ?></span>
                </span>
                <span class="summary__meta"><?= $e($entry['occurred_at'] ?? '') ?></span>
            </summary>
            <div class="summary__content">
                <p>
                    Entität:
                    <?= $e($entry['entity_type'] ?? '') ?>
                    <?php if (!empty($entry['entity_id'])): ?>
                        #<?= (int) $entry['entity_id'] ?>
                    <?php endif; ?>
                </p>
                <?php if (!empty($entry['id'])): ?>
                    <div class="summary__actions">
                        <a class="btn btn--sm btn--outline" href="/verwaltung/audit/<?= (int) $entry['id'] ?>">Audit-Eintrag öffnen</a>
                    </div>
                <?php endif; ?>
            </div>
        </details>
    <?php endforeach; ?>
</div>
