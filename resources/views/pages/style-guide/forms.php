<?php declare(strict_types=1); ?>

<?php
$intro = 'Formularelemente mit Labels, Eingaben und Aktionsleiste.';
$examples = [[
    'title' => 'Formularfeld',
    'lead' => 'Einfaches Such- oder Eingabefeld mit Aktionen.',
    'html' => <<<'HTML'
<form class="form">
    <label class="form__label" for="demo-name">Name</label>
    <input class="form__input" id="demo-name" name="demo-name" type="text" placeholder="Max Mustermann">

    <label class="form__label" for="demo-message">Nachricht</label>
    <textarea class="form__textarea" id="demo-message" name="demo-message" rows="4" placeholder="Ihre Nachricht"></textarea>

    <div class="btn-group" aria-label="Formularaktionen">
        <button class="btn btn--primary btn--md" type="submit">Speichern</button>
        <button class="btn btn--ghost btn--md" type="reset">Zurücksetzen</button>
    </div>
</form>
HTML,
]];
require __DIR__ . '/_component-page.php';
