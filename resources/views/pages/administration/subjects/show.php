<?php $e = static fn (mixed $v): string => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); ?>
<section class="content-card">
    <p class="eyebrow">Subject</p>
    <h1><code><?= $e($subject['uuid'] ?? '') ?></code></h1>
    <p>Status: <?= $e($subject['status'] ?? '') ?> · Permission-Version: <?= $e($subject['permission_version'] ?? '') ?></p>
    <p>Person: <?= $e($subject['display_name'] ?? '') ?> · E-Mail: <?= $e($subject['email'] ?? '') ?></p>
</section>
<section class="content-card">
    <h2>Gruppen</h2>
    <ul>
        <?php foreach (($groups ?? []) as $group): ?>
            <li><code><?= $e($group['system_key']) ?>.<?= $e($group['group_key']) ?></code> — <?= $e($group['group_name']) ?><?= !empty($group['expires_at']) ? ' bis ' . $e($group['expires_at']) : '' ?></li>
        <?php endforeach; ?>
    </ul>
</section>
