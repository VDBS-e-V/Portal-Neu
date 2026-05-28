<?php declare(strict_types=1); ?>

<?php
$intro = 'Suchfelder mit klarer Eingabe und direkter Auslösung.';
$examples = [[
    'title' => 'Suche',
    'lead' => 'Für globale Suche oder Filter in Listen.',
    'html' => <<<'HTML'
<form class="search" action="#" method="get">
    <input class="search__input" type="search" name="q" placeholder="Suche starten">
    <button class="btn btn--primary btn--md" type="submit">Suchen</button>
</form>
HTML,
]];
require __DIR__ . '/_component-page.php';
