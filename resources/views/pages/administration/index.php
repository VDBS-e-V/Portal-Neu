<?php
$e = static fn (mixed $v): string => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
$stats = $stats ?? [];
$quickLinks = $quickLinks ?? [];
?>
<section class="content-card">
    <p class="eyebrow">Administration</p>
    <h1>Administration</h1>
    <p>Zentrale Identity-Verwaltung für Systeme, Gruppen, Permissions und Gruppenzuweisungen.</p>
</section>

<section class="dashboard-grid">
    <?php foreach ([
        'systems' => 'Systeme',
        'groups' => 'Gruppen',
        'permissions' => 'Permissions',
        'subjects' => 'Subjects',
        'assignments' => 'Gruppenzuweisungen',
        'expiredAssignments' => 'Abgelaufen',
    ] as $key => $label): ?>
        <article class="content-card">
            <p class="eyebrow"><?= $e($label) ?></p>
            <h2><?= $e((string) ($stats[$key] ?? 0)) ?></h2>
        </article>
    <?php endforeach; ?>
</section>

<section class="content-card">
    <h2>Schnellzugriffe</h2>
    <div class="action-grid">
        <?php foreach ($quickLinks as $link): ?>
            <a class="action-card" href="<?= $e($link['href'] ?? '#') ?>">
                <strong><?= $e($link['label'] ?? '') ?></strong>
                <span><?= $e($link['description'] ?? '') ?></span>
            </a>
        <?php endforeach; ?>
        <?php if ($quickLinks === []): ?>
            <p>Keine Schnellzugriffe verfügbar.</p>
        <?php endif; ?>
    </div>
</section>
