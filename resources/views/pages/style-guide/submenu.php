<?php declare(strict_types=1); ?>

<?php
$intro = 'Seitennavigation für zusammengehörige Bereiche.';
$examples = [[
    'title' => 'Untermenü',
    'lead' => 'Praktisch für lokale Navigation innerhalb eines Bereichs.',
    'html' => <<<'HTML'
<nav class="submenu" aria-label="Untermenü">
    <a class="submenu__link submenu__link--active" href="#">Übersicht</a>
    <a class="submenu__link" href="#">Einstellungen</a>
    <a class="submenu__link" href="#">Historie</a>
</nav>
HTML,
]];
require __DIR__ . '/_component-page.php';
