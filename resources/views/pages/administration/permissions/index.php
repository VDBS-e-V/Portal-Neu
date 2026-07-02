<?php $e = static fn (mixed $v): string => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); $filters = $filters ?? []; ?>
<section class="content-card">
    <div class="page-actions">
        <div>
            <p class="eyebrow">Identity</p>
            <h1>Permissions</h1>
            <p>Feine technische Rechte. Code prüft Permissions, nicht Gruppen.</p>
        </div>
        <a class="button" href="/administration/permissions/create">Permission anlegen</a>
    </div>
</section>

<section class="content-card">
    <form method="get" class="inline-form">
        <label>System
            <select name="system_id">
                <option value="0">Alle</option>
                <?php foreach (($systems ?? []) as $system): ?>
                    <option value="<?= $e($system['id']) ?>" <?= (int) ($filters['system_id'] ?? 0) === (int) $system['id'] ? 'selected' : '' ?>><?= $e($system['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <button class="button" type="submit">Filtern</button>
    </form>
    <table class="data-table">
        <thead><tr><th>ID</th><th>System</th><th>Key</th><th>Name</th><th>Aktiv</th><th>Deprecated</th><th>Gruppen</th><th></th></tr></thead>
        <tbody>
        <?php foreach (($permissions ?? []) as $permission): ?>
            <tr>
                <td><?= $e($permission['id']) ?></td>
                <td><code><?= $e($permission['system_key']) ?></code></td>
                <td><code><?= $e($permission['key_name']) ?></code></td>
                <td><?= $e($permission['name']) ?></td>
                <td><?= ((int) $permission['is_active'] === 1) ? 'ja' : 'nein' ?></td>
                <td><?= $e($permission['deprecated_at'] ?? '') ?></td>
                <td><?= $e($permission['group_count'] ?? 0) ?></td>
                <td><a href="/administration/permissions/<?= $e($permission['id']) ?>">Öffnen</a></td>
            </tr>
        <?php endforeach; ?>
        <?php if (($permissions ?? []) === []): ?><tr><td colspan="8">Keine Permissions vorhanden.</td></tr><?php endif; ?>
        </tbody>
    </table>
</section>
