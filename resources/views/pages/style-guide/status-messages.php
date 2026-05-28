<?php declare(strict_types=1); ?>

<?php
$intro = 'Erfolgs-, Fehler- und Hinweiszustände für Feedback.';
$examples = [[
    'title' => 'Hinweis',
    'lead' => 'Feedback sollte kurz, sichtbar und eindeutig sein.',
    'html' => <<<'HTML'
<div class="status-message status-message--success">Ihre Änderungen wurden gespeichert.</div>
<div class="status-message status-message--warning">Bitte prüfen Sie noch einmal die Eingaben.</div>
<div class="status-message status-message--error">Beim Speichern ist ein Fehler aufgetreten.</div>
HTML,
]];
require __DIR__ . '/_component-page.php';
