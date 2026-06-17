<?php
$e = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

$log = $log ?? [];

$jsonPretty = static function (mixed $value): string {
    if ($value === null || $value === '') {
        return '';
    }

    if (is_string($value)) {
        $decoded = json_decode($value, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            return json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: $value;
        }

        return $value;
    }

    return json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '';
};
?>

<section class="content-section">
    <header class="content-header">
        <div>
            <h1><?= $e($pageTitle ?? 'Audit-Eintrag') ?></h1>
            <p><code><?= $e($log['action'] ?? '') ?></code></p>
        </div>

        <p>
            <a class="button" href="/verwaltung/audit">Zurück</a>
        </p>
    </header>

    <section class="card">
        <h2>Basisdaten</h2>

        <dl>
            <dt>ID</dt>
            <dd><?= (int) ($log['id'] ?? 0) ?></dd>

            <dt>Zeitpunkt</dt>
            <dd><?= $e($log['occurred_at'] ?? '') ?></dd>

            <dt>Aktion</dt>
            <dd><code><?= $e($log['action'] ?? '') ?></code></dd>

            <dt>Entität</dt>
            <dd>
                <code><?= $e($log['entity_type'] ?? '') ?></code>
                <?php if (!empty($log['entity_id'])): ?>
                    #<?= (int) $log['entity_id'] ?>
                <?php endif; ?>
                <?php if (!empty($log['entity_label'])): ?>
                    <br><?= $e($log['entity_label']) ?>
                <?php endif; ?>
            </dd>
        </dl>
    </section>

    <section class="card">
        <h2>Akteur</h2>

        <dl>
            <dt>Person</dt>
            <dd>
                <?= $e($log['actor_display_name'] ?? '') ?>
                <?php if (!empty($log['actor_person_id'])): ?>
                    <br>Person #<?= (int) $log['actor_person_id'] ?>
                <?php endif; ?>
            </dd>

            <dt>Login</dt>
            <dd>
                <?= $e($log['actor_email'] ?? '') ?>
                <?php if (!empty($log['actor_user_id'])): ?>
                    <br>User #<?= (int) $log['actor_user_id'] ?>
                <?php endif; ?>
            </dd>
        </dl>
    </section>

    <section class="card">
        <h2>Request</h2>

        <dl>
            <dt>Request-ID</dt>
            <dd><?= $e($log['request_id'] ?? '') ?></dd>

            <dt>Methode</dt>
            <dd><?= $e($log['request_method'] ?? '') ?></dd>

            <dt>URI</dt>
            <dd><?= $e($log['request_uri'] ?? '') ?></dd>

            <dt>IP</dt>
            <dd><?= $e($log['ip_address'] ?? '') ?></dd>

            <dt>User-Agent</dt>
            <dd><?= $e($log['user_agent'] ?? '') ?></dd>
        </dl>
    </section>

    <section class="card">
        <h2>Alte Werte</h2>
        <pre><?= $e($jsonPretty($log['old_values'] ?? null)) ?></pre>
    </section>

    <section class="card">
        <h2>Neue Werte</h2>
        <pre><?= $e($jsonPretty($log['new_values'] ?? null)) ?></pre>
    </section>

    <section class="card">
        <h2>Metadaten</h2>
        <pre><?= $e($jsonPretty($log['metadata'] ?? null)) ?></pre>
    </section>
</section>
