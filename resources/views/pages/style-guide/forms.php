<?php declare(strict_types=1); ?>

<?php
$intro = 'Formulare mit klaren Feldern, Gruppen, Statushinweisen und Layout-Varianten.';
$examples = [[
    'title' => 'Basisformular',
    'lead' => 'Ein klassisches Formular mit Textfeld, Textarea und Aktionsleiste.',
    'html' => <<<'HTML'
<form class="form">
    <div class="form__field">
        <label class="form__label form__label--required" for="demo-name">Name</label>
        <input class="form__input" id="demo-name" name="demo-name" type="text" placeholder="Max Mustermann">
        <div class="form__help">Pflichtfeld für die interne Zuordnung.</div>
    </div>

    <div class="form__field">
        <label class="form__label" for="demo-message">Nachricht</label>
        <textarea class="form__textarea" id="demo-message" name="demo-message" rows="4" placeholder="Ihre Nachricht"></textarea>
        <div class="form__hint">Kurz und klar formulieren, damit das Team schneller reagieren kann.</div>
    </div>

    <div class="form__actions form__actions--end">
        <button class="btn btn--primary btn--md" type="submit">Speichern</button>
        <button class="btn btn--ghost btn--md" type="reset">Zurücksetzen</button>
    </div>
</form>
HTML,
], [
    'title' => 'Geteiltes Layout',
    'lead' => 'Zwei Spalten für Bearbeitungsformulare, Filter oder Generatoren.',
    'html' => <<<'HTML'
<form class="form form__layout form__layout--split">
    <section class="form__section">
        <h3 class="form__section-title">Inhalt</h3>

        <div class="form__grid form__grid--2">
            <div class="form__field">
                <label class="form__label" for="demo-email">E-Mail</label>
                <input class="form__input" id="demo-email" name="demo-email" type="email" placeholder="name@beispiel.de">
            </div>

            <div class="form__field">
                <label class="form__label" for="demo-role">Rolle</label>
                <select class="form__select" id="demo-role" name="demo-role">
                    <option>Redaktion</option>
                    <option>Administration</option>
                    <option>Organisation</option>
                </select>
            </div>
        </div>

        <div class="form__choice-group">
            <label class="form__choice">
                <input type="radio" name="visibility" checked>
                <span>
                    <strong>Öffentlich</strong>
                    <span class="form__meta">Sichtbar für alle Besucher.</span>
                </span>
            </label>
            <label class="form__choice">
                <input type="radio" name="visibility">
                <span>
                    <strong>Intern</strong>
                    <span class="form__meta">Nur für das Team sichtbar.</span>
                </span>
            </label>
        </div>
    </section>

    <aside class="form__panel form__panel--soft">
        <h3 class="form__panel-title">Hinweis</h3>
        <div class="form__success">Die Eingaben werden sofort im Layout geprüft und stilistisch hervorgehoben.</div>
        <div class="form__actions form__actions--stack">
            <button class="btn btn--primary btn--md" type="submit">Änderungen speichern</button>
            <button class="btn btn--outline btn--md" type="button">Vorschau aktualisieren</button>
        </div>
    </aside>
</form>
HTML,
], [
    'title' => 'Inline Suche',
    'lead' => 'Kompakte Felder für Filter, Suchzeilen und Schnellaktionen.',
    'html' => <<<'HTML'
<form class="form form--inline" action="#" method="get">
    <div class="form__field">
        <label class="form__label" for="demo-search">Suche</label>
        <input class="form__input" id="demo-search" name="demo-search" type="search" placeholder="Begriff eingeben">
    </div>

    <div class="form__field">
        <label class="form__label" for="demo-filter">Filter</label>
        <select class="form__select" id="demo-filter" name="demo-filter">
            <option>Alle</option>
            <option>Aktiv</option>
            <option>Archiviert</option>
        </select>
    </div>

    <div class="form__actions">
        <button class="btn btn--secondary-cta btn--md" type="submit">Anwenden</button>
        <button class="btn btn--ghost btn--md" type="reset">Zurücksetzen</button>
    </div>
</form>
HTML,
]];
require __DIR__ . '/_component-page.php';
