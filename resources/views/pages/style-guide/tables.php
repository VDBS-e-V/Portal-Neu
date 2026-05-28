<?php declare(strict_types=1); ?>

<?php
$intro = 'Tabellarische Daten mit klarer Lesbarkeit und Struktur.';
$examples = [[
    'title' => 'Datentabelle',
    'lead' => 'Tabellen funktionieren am besten mit kurzen Zelleninhalten.',
    'html' => <<<'HTML'
<table class="table">
    <thead>
        <tr>
            <th>Name</th>
            <th>Status</th>
            <th>Datum</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Eintrag 1</td>
            <td>Aktiv</td>
            <td>27.05.2026</td>
        </tr>
    </tbody>
</table>
HTML,
]];
require __DIR__ . '/_component-page.php';
