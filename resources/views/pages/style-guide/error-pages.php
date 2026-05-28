<?php declare(strict_types=1); ?>

<?php
$intro = 'Vorlagen für Fehlerzustände mit klarer Nachricht und Handlung.';
$examples = [[
    'title' => '404-Situation',
    'lead' => 'Eine Fehlerseite soll Orientierung und eine nächste Aktion geben.',
    'html' => <<<'HTML'
<section class="error-page">
    <h2>Seite nicht gefunden</h2>
    <p>Die angeforderte Seite existiert nicht mehr oder wurde verschoben.</p>
    <a class="btn btn--primary btn--md" href="/">Zur Startseite</a>
</section>
HTML,
]];
require __DIR__ . '/_component-page.php';
