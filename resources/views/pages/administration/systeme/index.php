<?php $e = static fn (mixed $v): string => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); ?>
<section class="content-card">
    <div class="page-actions">
        <div>
            <p class="eyebrow">Identity</p>
            <h1>Systeme</h1>
            <p>Systeme sind zentrale Identity-Kontexte wie identity, portal, bibliocollect und methodenmatrix.</p>
        </div>
        <a class="button" href="/administration/systeme/create">System anlegen</a>
    </div>
</section>

<section class="content-card">
    <?php if (($message ?? '') !== ''): ?><p class="notice notice--success"><?= $e($message) ?></p><?php endif; ?>
    <table class="data-table">
        <thead><tr><th>ID</th><th>Key</th><th>Name</th><th>Aktiv</th><th>Extern</th><th>Gruppen</th><th>Permissions</th><th></th></tr></thead>
        <tbody>
        <?php foreach (($systems ?? []) as $system): ?>
            <tr>
                <td><?= $e($system['id']) ?></td>
                <td><code><?= $e($system['key_name']) ?></code></td>
                <td><?= $e($system['name']) ?></td>
                <td><?= ((int) $system['is_active'] === 1) ? 'ja' : 'nein' ?></td>
                <td><?= ((int) $system['is_external'] === 1) ? 'ja' : 'nein' ?></td>
                <td><?= $e($system['group_count'] ?? 0) ?></td>
                <td><?= $e($system['permission_count'] ?? 0) ?></td>
                <td><a href="/administration/systeme/<?= $e($system['id']) ?>">Öffnen</a></td>
            </tr>
        <?php endforeach; ?>
        <?php if (($systems ?? []) === []): ?><tr><td colspan="8">Keine Systeme vorhanden.</td></tr><?php endif; ?>
        </tbody>
    </table>
</section>
