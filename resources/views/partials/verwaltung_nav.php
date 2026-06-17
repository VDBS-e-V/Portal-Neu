<?php
$e = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

$areaNav = $areaNav ?? [];
?>

<?php if ($areaNav !== []): ?>
    <nav class="area-nav">
        <?php foreach ($areaNav as $item): ?>
            <a
                href="<?= $e($item['href'] ?? '#') ?>"
                class="<?= !empty($item['active']) ? 'is-active' : '' ?>"
            >
                <?= $e($item['label'] ?? '') ?>
            </a>
        <?php endforeach; ?>
    </nav>
<?php endif; ?>
