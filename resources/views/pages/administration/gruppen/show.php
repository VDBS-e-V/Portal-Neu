<?php $e = static fn (mixed $v): string => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); $selected = array_flip(array_map('intval', $selectedPermissionIds ?? [])); ?>
<section class="content-card">
    <p class="eyebrow">Gruppe</p>
    <h1><?= $e($group['name'] ?? '') ?></h1>
    <p><code><?= $e($group['system_key'] ?? '') ?>.<?= $e($group['key_name'] ?? '') ?></code></p>
    <p><?= $e($group['description'] ?? '') ?></p>
    <p>
        <a class="button" href="/administration/gruppen/<?= $e($group['id'] ?? 0) ?>/edit">Bearbeiten</a>
        <a class="button" href="/administration/gruppen/<?= $e($group['id'] ?? 0) ?>/permissions">Permissions verwalten</a>
    </p>
</section>
<section class="content-card">
    <h2>Aktive Permissions</h2>
    <ul>
        <?php foreach (($permissions ?? []) as $permission): ?>
            <?php if (!isset($selected[(int) $permission['id']])) { continue; } ?>
            <li><code><?= $e($permission['key_name']) ?></code> — <?= $e($permission['name']) ?></li>
        <?php endforeach; ?>
    </ul>
</section>
