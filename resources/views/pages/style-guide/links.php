<?php declare(strict_types=1); ?>

<?php
$intro = 'Link-Stile für Text, Navigation und neutrale Verweise.';
$examples = [[
    'title' => 'Textlinks',
    'lead' => 'Links bleiben im Fließtext klar erkennbar.',
    'html' => <<<'HTML'
<p>Lesen Sie die <a class="link" href="#">Dokumentation</a> oder öffnen Sie die <a class="link--no-style" href="#">Schnellansicht</a>.</p>
HTML,
]];
require __DIR__ . '/_component-page.php';
