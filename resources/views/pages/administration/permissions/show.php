<?php $e = static fn (mixed $v): string => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); ?>
<section class="content-card">
    <p class="eyebrow">Permission</p>
    <h1><code><?= $e($permission['key_name'] ?? '') ?></code></h1>
    <p><?= $e($permission['name'] ?? '') ?></p>
    <p><?= $e($permission['description'] ?? '') ?></p>
    <p>System: <code><?= $e($permission['system_key'] ?? '') ?></code> · Aktiv: <?= ((int) ($permission['is_active'] ?? 0) === 1) ? 'ja' : 'nein' ?></p>
    <?php if (!empty($permission['deprecated_at'])): ?>
        <p class="notice notice--warning">Deprecated seit <?= $e($permission['deprecated_at']) ?>: <?= $e($permission['deprecated_reason'] ?? '') ?></p>
    <?php endif; ?>
    <p><a class="button" href="/administration/permissions/<?= $e($permission['id'] ?? 0) ?>/edit">Bearbeiten</a></p>
    <form method="post" action="/administration/permissions/<?= $e($permission['id'] ?? 0) ?>/deactivate">
        <label>Deprecated-Grund <input name="deprecated_reason" value=""></label>
        <button type="submit" class="button button--danger">Deaktivieren / Deprecated markieren</button>
    </form>
</section>
