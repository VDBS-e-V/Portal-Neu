<?php declare(strict_types=1); ?>

<?php
$intro = 'Raster für flexible Spalten und responsive Anordnungen.';
$examples = [[
    'title' => 'Drei Spalten',
    'lead' => 'Ein Grid verteilt Inhalte gleichmäßig.',
    'html' => <<<'HTML'
<div class="grid grid--3">
    <div class="grid__item">Spalte 1</div>
    <div class="grid__item">Spalte 2</div>
    <div class="grid__item">Spalte 3</div>
</div>
HTML,
]];
require __DIR__ . '/_component-page.php';
