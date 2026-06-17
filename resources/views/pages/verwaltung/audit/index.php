<?php
$e = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

$logs = $logs ?? [];
$filters = $filters ?? [];
$actions = $actions ?? [];
$entityTypes = $entityTypes ?? [];
?>

<section class="content-section">
    <header class="content-header">
        <div>
            <h1>Audit-Log</h1>
            <p>Nachvollziehbare Änderungen und sicherheitsrelevante Aktionen.</p>
        </div>
    </header>

    <form method="get" action="/verwaltung/audit" class="filter-form">
        <label>
            Suche
            <input type="search" name="q" value="<?= $e($filters['q'] ?? '') ?>" placeholder="Aktion, Entität, Nutzer, URI">
        </label>

        <label>
            Aktion
            <?php $selectedAction = (string) ($filters['action'] ?? ''); ?>
            <select name="action">
                <option value="">Alle Aktionen</option>
                <?php foreach ($actions as $action): ?>
                    <option value="<?= $e($action) ?>" <?= $selectedAction === (string) $action ? 'selected' : '' ?>>
                        <?= $e($action) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>
            Entität
            <?php $selectedEntityType = (string) ($filters['entity_type'] ?? ''); ?>
            <select name="entity_type">
                <option value="">Alle Entitäten</option>
                <?php foreach ($entityTypes as $entityType): ?>
                    <option value="<?= $e($entityType) ?>" <?= $selectedEntityType === (string) $entityType ? 'selected' : '' ?>>
                        <?= $e($entityType) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>
            Von
            <input type="date" name="from" value="<?= $e($filters['from'] ?? '') ?>">
        </label>

        <label>
            Bis
            <input type="date" name="to" value="<?= $e($filters['to'] ?? '') ?>">
        </label>

        <label>
            Limit
            <input type="number" name="limit" min="1" max="500" value="<?= $e($filters['limit'] ?? 100) ?>">
        </label>

        <button type="submit">Filtern</button>
    </form>

    <table class="data-table">
        <thead>
        <tr>
            <th>Zeitpunkt</th>
            <th>Aktion</th>
            <th>Entität</th>
            <th>Akteur</th>
            <th>Request</th>
            <th>Aktion</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($logs === []): ?>
            <tr>
                <td colspan="6">Keine Audit-Einträge gefunden.</td>
            </tr>
        <?php endif; ?>

        <?php foreach ($logs as $log): ?>
            <?php $logId = (int) ($log['id'] ?? 0); ?>
            <tr>
                <td><?= $e($log['occurred_at'] ?? '') ?></td>
                <td><code><?= $e($log['action'] ?? '') ?></code></td>
                <td>
                    <strong><?= $e($log['entity_type'] ?? '') ?></strong>
                    <?php if (!empty($log['entity_id'])): ?>
                        #<?= (int) $log['entity_id'] ?>
                    <?php endif; ?>
                    <?php if (!empty($log['entity_label'])): ?>
                        <br><?= $e($log['entity_label']) ?>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if (!empty($log['actor_display_name'])): ?>
                        <?= $e($log['actor_display_name']) ?><br>
                    <?php endif; ?>
                    <?= $e($log['actor_email'] ?? '') ?>
                    <?php if (!empty($log['actor_user_id'])): ?>
                        <br>User #<?= (int) $log['actor_user_id'] ?>
                    <?php endif; ?>
                </td>
                <td>
                    <?= $e($log['request_method'] ?? '') ?>
                    <?= $e($log['request_uri'] ?? '') ?>
                    <?php if (!empty($log['ip_address'])): ?>
                        <br><?= $e($log['ip_address']) ?>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="/verwaltung/audit/<?= $logId ?>">Details</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
