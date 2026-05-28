<?php declare(strict_types=1); ?>

<?php
$intro = 'Kompakte Kennzahlen für Dashboards und Übersichten.';
$examples = [[
    'title' => 'Kennzahlen',
    'lead' => 'Mehrere Werte in einer visuell ruhigen Anordnung.',
    'html' => <<<'HTML'
<div class="stats-grid">
    <div class="stat">
        <div class="stat__value">128</div>
        <div class="stat__label">Tickets</div>
    </div>
    <div class="stat">
        <div class="stat__value">24</div>
        <div class="stat__label">Offen</div>
    </div>
</div>
HTML,
]];
require __DIR__ . '/_component-page.php';
