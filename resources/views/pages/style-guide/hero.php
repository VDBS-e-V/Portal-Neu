<?php declare(strict_types=1); ?>

<?php
$intro = 'Große Einstiegsfläche mit Bild, Overlay und zentraler Botschaft.';
$examples = [[
    'title' => 'Hero-Block',
    'lead' => 'Ideal für Startseiten oder Landing Pages.',
    'html' => <<<'HTML'
<section class="hero" aria-label="Hero Beispiel">
    <div class="hero__inner">
        <h2 class="hero__title">Willkommen im Portal</h2>
        <p class="hero__lead">Die Hero-Fläche bündelt Marke, Botschaft und erste Aktion.</p>
    </div>
</section>
HTML,
]];
require __DIR__ . '/_component-page.php';
