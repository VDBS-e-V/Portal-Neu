<?php $e = static fn (mixed $v): string => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); ?>
<section class="content-card">
    <p class="eyebrow">System</p>
    <h1><?= $e($system['name'] ?? '') ?></h1>
    <p><code><?= $e($system['key_name'] ?? '') ?></code></p>
    <p><?= $e($system['description'] ?? '') ?></p>
    <p><a class="button" href="/administration/systeme/<?= $e($system['id'] ?? 0) ?>/edit">Bearbeiten</a></p>
</section>

<section class="content-card">
    <h2>Gruppen</h2>
    <ul>
        <?php foreach (($groups ?? []) as $group): ?>
            <li><a href="/administration/gruppen/<?= $e($group['id']) ?>"><code><?= $e($group['system_key']) ?>.<?= $e($group['key_name']) ?></code></a> — <?= $e($group['name']) ?></li>
        <?php endforeach; ?>
    </ul>
</section>

<section class="content-card">
    <h2>Permissions</h2>
    <ul>
        <?php foreach (($permissions ?? []) as $permission): ?>
            <li><a href="/administration/permissions/<?= $e($permission['id']) ?>"><code><?= $e($permission['key_name']) ?></code></a> — <?= $e($permission['name']) ?></li>
        <?php endforeach; ?>
    </ul>
</section>
