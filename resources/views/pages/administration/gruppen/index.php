<?php $e = static fn (mixed $v): string => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); $filters = $filters ?? []; ?>
<section class="content-card">
    <div class="page-actions">
        <div>
            <p class="eyebrow">Identity</p>
            <h1>Gruppen</h1>
            <p>Gruppen gehören immer zu genau einem System und bündeln Permissions.</p>
        </div>
        <a class="button" href="/administration/gruppen/create">Gruppe anlegen</a>
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
    <?php if (($message ?? '') !== ''): ?><p class="notice notice--success"><?= $e($message) ?></p><?php endif; ?>
    <table class="data-table">
        <thead><tr><th>ID</th><th>System</th><th>Key</th><th>Name</th><th>Aktiv</th><th>Default</th><th>Permissions</th><th>Subjects</th><th></th></tr></thead>
        <tbody>
        <?php foreach (($groups ?? []) as $group): ?>
            <tr>
                <td><?= $e($group['id']) ?></td>
                <td><code><?= $e($group['system_key']) ?></code></td>
                <td><code><?= $e($group['key_name']) ?></code></td>
                <td><?= $e($group['name']) ?></td>
                <td><?= ((int) $group['is_active'] === 1) ? 'ja' : 'nein' ?></td>
                <td><?= ((int) $group['is_default'] === 1) ? 'ja' : 'nein' ?></td>
                <td><?= $e($group['permission_count'] ?? 0) ?></td>
                <td><?= $e($group['subject_count'] ?? 0) ?></td>
                <td><a href="/administration/gruppen/<?= $e($group['id']) ?>">Öffnen</a></td>
            </tr>
        <?php endforeach; ?>
        <?php if (($groups ?? []) === []): ?><tr><td colspan="9">Keine Gruppen vorhanden.</td></tr><?php endif; ?>
        </tbody>
    </table>
</section>
