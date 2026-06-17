<?php
$e = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

$entries = $entries ?? [];

$decodeJson = static function (mixed $value): array {
    if (!is_string($value) || trim($value) === '') {
        return [];
    }

    $decoded = json_decode($value, true);

    return is_array($decoded) ? $decoded : [];
};
?>

<section class="card">
    <h2>Audit-Verlauf</h2>

    <?php if ($entries === []): ?>
        <p>Keine Audit-Einträge für diese Entität vorhanden.</p>
    <?php else: ?>
        <ol class="audit-timeline">
            <?php foreach ($entries as $entry): ?>
                <?php
                $changes = $decodeJson($entry['changes'] ?? null);
                $metadata = $decodeJson($entry['metadata'] ?? null);
                ?>
                <li class="audit-timeline-item">
                    <article>
                        <header>
                            <strong><code><?= $e($entry['action'] ?? '') ?></code></strong>
                            <span><?= $e($entry['occurred_at'] ?? '') ?></span>
                        </header>

                        <dl>
                            <dt>Entität</dt>
                            <dd>
                                <code><?= $e($entry['entity_type'] ?? '') ?></code>
                                <?php if (!empty($entry['entity_id'])): ?>
                                    #<?= (int) $entry['entity_id'] ?>
                                <?php endif; ?>
                            </dd>

                            <?php if (!empty($entry['entity_label'])): ?>
                                <dt>Label</dt>
                                <dd><?= $e($entry['entity_label']) ?></dd>
                            <?php endif; ?>

                            <?php if (!empty($entry['actor_user_id'])): ?>
                                <dt>Akteur</dt>
                                <dd>User #<?= (int) $entry['actor_user_id'] ?></dd>
                            <?php endif; ?>

                            <?php if (!empty($entry['request_method']) || !empty($entry['request_uri'])): ?>
                                <dt>Request</dt>
                                <dd>
                                    <?= $e($entry['request_method'] ?? '') ?>
                                    <?= $e($entry['request_uri'] ?? '') ?>
                                </dd>
                            <?php endif; ?>
                        </dl>

                        <?php if ($changes !== []): ?>
                            <details>
                                <summary>Änderungen anzeigen</summary>
                                <pre><?= $e(json_encode($changes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) ?></pre>
                            </details>
                        <?php endif; ?>

                        <?php if ($metadata !== []): ?>
                            <details>
                                <summary>Metadaten anzeigen</summary>
                                <pre><?= $e(json_encode($metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) ?></pre>
                            </details>
                        <?php endif; ?>
                    </article>
                </li>
            <?php endforeach; ?>
        </ol>
    <?php endif; ?>
</section>
