<?php declare(strict_types=1); ?>

<?php
$intro = 'Layout-Hüllen für Breite, Abstand und saubere Seitenränder.';
$examples = [[
    'title' => 'Basis-Container',
    'lead' => 'Der Container begrenzt Inhalte auf eine lesbare Breite.',
    'html' => <<<'HTML'
<div class="container">
    <p>Inhalt innerhalb eines zentrierten Containers.</p>
</div>
HTML,
]];
require __DIR__ . '/_component-page.php';
