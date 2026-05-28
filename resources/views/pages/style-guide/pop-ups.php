<?php declare(strict_types=1); ?>

<?php
$intro = 'Dialoge und Hinweise für bestätigungspflichtige Aktionen.';
$examples = [[
    'title' => 'Dialogfenster',
    'lead' => 'Ein Popup sollte kurz, klar und handlungsorientiert bleiben.',
    'html' => <<<'HTML'
<div class="popup">
    <h2 class="popup__title">Änderungen verwerfen?</h2>
    <p class="popup__text">Nicht gespeicherte Eingaben gehen verloren.</p>
    <div class="btn-group" aria-label="Popup Aktionen">
        <button class="btn btn--danger btn--md" type="button">Verwerfen</button>
        <button class="btn btn--outline btn--md" type="button">Abbrechen</button>
    </div>
</div>
HTML,
]];
require __DIR__ . '/_component-page.php';
