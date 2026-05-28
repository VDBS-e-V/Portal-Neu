<?php declare(strict_types=1); ?>

<?php
$intro = 'Listen für strukturierte Inhalte, Regeln und Hinweise.';
$examples = [[
    'title' => 'Aufzählung',
    'lead' => 'Listen geben Inhalten eine saubere Hierarchie.',
    'html' => <<<'HTML'
<ul class="list">
    <li>Erster Punkt</li>
    <li>Zweiter Punkt</li>
    <li>Dritter Punkt</li>
</ul>
HTML,
]];
require __DIR__ . '/_component-page.php';
