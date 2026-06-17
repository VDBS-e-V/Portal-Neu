<?php
$e = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

$areaNav = $areaNav ?? [];
?>

<?php if ($areaNav !== []): ?>
    <nav class="btn-group btn-group--horizontal btn-group--gap-sm" aria-label="Verwaltungsnavigation">
        <?php foreach ($areaNav as $item): ?>
            <?php $isActive = !empty($item['active']); ?>
            <a
                class="btn btn--sm <?= $isActive ? 'btn--primary' : 'btn--outline' ?>"
                href="<?= $e($item['href'] ?? '#') ?>"
                <?= $isActive ? 'aria-current="page"' : '' ?>
            >
                <?= $e($item['label'] ?? '') ?>
            </a>
        <?php endforeach; ?>
    </nav>
<?php endif; ?>
