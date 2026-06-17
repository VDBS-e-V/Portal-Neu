<?php
$e = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

$mainMenuItems = $mainMenuItems ?? [];

$renderMenu = static function (array $items) use (&$renderMenu, $e): void {
    if ($items === []) {
        return;
    }
    ?>
    <ul>
        <?php foreach ($items as $item): ?>
            <?php
            $href = (string) ($item['url'] ?? '#');
            $target = trim((string) ($item['target'] ?? ''));
            $children = $item['children'] ?? [];
            ?>
            <li>
                <a
                    class="btn btn--sm btn--ghost"
                    href="<?= $e($href) ?>"
                    <?= $target !== '' ? 'target="' . $e($target) . '"' : '' ?>
                >
                    <?= $e($item['title'] ?? '') ?>
                </a>

                <?php if (is_array($children) && $children !== []): ?>
                    <?php $renderMenu($children); ?>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
    <?php
};
?>

<nav class="summary-group summary-group--compact" aria-label="Hauptmenü">
    <?php $renderMenu($mainMenuItems); ?>
</nav>
