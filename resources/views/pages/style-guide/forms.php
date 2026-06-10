<?php declare(strict_types=1); ?>

<section id="forms-uebersicht" class="section--surface">
    <div class="container container--text-only container--narrow container--center">
        <div class="container__text">
            <h1>Übersicht: forms.css</h1>

            <p>
                Diese Seite zeigt die wichtigsten Formular-Strukturen aus <code>forms.css</code>:
                Formular-Grids, Felder, Controls, Hinweise, Meldungen, Auswahlfelder,
                Switches, Suchfelder, Datei-Uploads und Aktionsbereiche.
            </p>

            <p>
                Grundregel: Die Seite besteht aus <code>section</code>-Blöcken.
                Innerhalb der Sections liegen Container. Formulare selbst verwenden
                <code>.form</code>, <code>.form__grid</code> und die jeweiligen
                Formular-Elementklassen.
            </p>
        </div>

        <div class="container__buttons btn-group btn-group--vertical btn-group--main-center btn-group--gap-sm">
            <a href="#forms-inhaltsverzeichnis" class="btn btn--primary btn--md">
                Zum Inhaltsverzeichnis
            </a>

            <a href="#forms-komplettbeispiel" class="btn btn--primary-light btn--md">
                Zum Kopierbeispiel
            </a>

            <a href="#forms-entscheidungshilfe" class="btn btn--primary-transp btn--md">
                Zur Entscheidungshilfe
            </a>
        </div>
    </div>
</section>


<section id="forms-inhaltsverzeichnis">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>Inhaltsverzeichnis</h2>

            <p>
                <a href="#forms-grundstruktur">1. Grundstruktur eines Formulars</a><br>
                <a href="#forms-layouts">2. Formular-Layouts und Grids</a><br>
                <a href="#forms-titel">3. Titel, Abschnittstitel und Hinweise</a><br>
                <a href="#forms-felder">4. Standardfelder</a><br>
                <a href="#forms-control-varianten">5. Control-Varianten</a><br>
                <a href="#forms-validierung">6. Validierung und Meldungen</a><br>
                <a href="#forms-auswahlfelder">7. Checkboxen, Radios und Switches</a><br>
                <a href="#forms-suche-datei">8. Suchfelder, Inline-Gruppen und Datei-Upload</a><br>
                <a href="#forms-aktionen">9. Formular-Aktionen</a><br>
                <a href="#forms-komplettbeispiel">10. Komplettes Kopierbeispiel</a><br>
                <a href="#forms-entscheidungshilfe">11. Entscheidungshilfe</a>
            </p>
        </div>
    </div>
</section>


<section id="forms-grundstruktur" class="section--surface">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>1. Grundstruktur eines Formulars</h2>

            <p>
                Ein Formular besteht idealerweise aus einem <code>form.form</code>
                und einem inneren Grid. Dieses Grid steuert die Spalten und die
                Breite der Felder.
            </p>

            <pre><code>&lt;form class="form"&gt;
    &lt;div class="form__grid form__grid--2"&gt;
        &lt;div class="form__title"&gt;
            &lt;h2&gt;Formulartitel&lt;/h2&gt;
            &lt;p&gt;Kurze Erklärung zum Formular.&lt;/p&gt;
        &lt;/div&gt;

        &lt;div class="form__field"&gt;
            &lt;label class="form__label" for="name"&gt;Name&lt;/label&gt;
            &lt;input class="form__control" id="name" name="name" type="text"&gt;
        &lt;/div&gt;

        &lt;div class="form__actions"&gt;
            &lt;button class="btn btn--primary btn--md" type="submit"&gt;
                Speichern
            &lt;/button&gt;
        &lt;/div&gt;
    &lt;/div&gt;
&lt;/form&gt;</code></pre>

            <h3>Wichtige Basisklassen</h3>

            <p>
                <code>.form</code><br>
                Grundklasse für das Formular.
            </p>

            <p>
                <code>.form__grid</code><br>
                Raster für Formularbereiche und Formularfelder.
            </p>

            <p>
                <code>.form__field</code><br>
                Einzelnes Formularfeld mit Label, Control und optionalem Hilfetext.
            </p>

            <p>
                <code>.form__label</code><br>
                Beschriftung des Feldes.
            </p>

            <p>
                <code>.form__control</code><br>
                Styling für <code>input</code>, <code>select</code> und
                <code>textarea</code>.
            </p>
        </div>
    </div>
</section>


<section id="forms-layouts">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>2. Formular-Layouts und Grids</h2>

            <p>
                Das Formularsystem arbeitet mit einem Grid. Du kannst zwischen
                ein-, zwei-, drei- und vierspaltigen Formularen wechseln.
                Feldbreiten steuerst du mit Span-Klassen.
            </p>
        </div>
    </div>
</section>


<section class="section--surface">
    <div class="container container--text-only">
        <form class="form form--card" action="#" method="post">
            <div class="form__grid form__grid--1">
                <div class="form__title">
                    <h2>Einspaltiges Formular</h2>
                    <p>
                        Gut für kurze Formulare, Login, Kontakt oder einfache Suchmasken.
                    </p>
                </div>

                <div class="form__field">
                    <label class="form__label" for="forms-single-email">E-Mail</label>
                    <input class="form__control" id="forms-single-email" type="email" placeholder="name@example.org">
                </div>

                <div class="form__field">
                    <label class="form__label" for="forms-single-message">Nachricht</label>
                    <textarea class="form__control" id="forms-single-message" rows="4"></textarea>
                </div>

                <div class="form__actions">
                    <button class="btn btn--primary btn--md" type="button">Absenden</button>
                </div>
            </div>
        </form>
    </div>
</section>


<section>
    <div class="container container--text-only">
        <form class="form form--card" action="#" method="post">
            <div class="form__grid form__grid--2">
                <div class="form__title">
                    <h2>Zweispaltiges Formular</h2>
                    <p>
                        Gut für Profil-, Verwaltungs- oder Anmeldeformulare mit mehreren kurzen Feldern.
                    </p>
                </div>

                <div class="form__field">
                    <label class="form__label" for="forms-two-first">Vorname</label>
                    <input class="form__control" id="forms-two-first" type="text">
                </div>

                <div class="form__field">
                    <label class="form__label" for="forms-two-last">Nachname</label>
                    <input class="form__control" id="forms-two-last" type="text">
                </div>

                <div class="form__field form__field--span-full">
                    <label class="form__label" for="forms-two-note">Notiz</label>
                    <textarea class="form__control" id="forms-two-note" rows="4"></textarea>
                    <p class="form__hint">
                        Mit <code>.form__field--span-full</code> nutzt ein Feld die gesamte Breite.
                    </p>
                </div>
            </div>
        </form>
    </div>
</section>


<section class="section--surface">
    <div class="container container--text-only">
        <div class="container__text">
            <h3>Grid-Klassen</h3>

            <p>
                <code>.form__grid--1</code> — eine Spalte<br>
                <code>.form__grid--2</code> — zwei Spalten<br>
                <code>.form__grid--3</code> — drei Spalten<br>
                <code>.form__grid--4</code> — vier Spalten<br>
                <code>.form__grid--compact</code> — kleinere Abstände<br>
                <code>.form__grid--loose</code> — größere Abstände
            </p>

            <h3>Span-Klassen</h3>

            <p>
                <code>.form__field--span-2</code> — Feld über zwei Spalten<br>
                <code>.form__field--span-3</code> — Feld über drei Spalten<br>
                <code>.form__field--span-4</code> — Feld über vier Spalten<br>
                <code>.form__field--span-full</code> — Feld über die gesamte Breite
            </p>

            <pre><code>&lt;div class="form__grid form__grid--4"&gt;
    &lt;div class="form__field form__field--span-2"&gt;...&lt;/div&gt;
    &lt;div class="form__field form__field--span-full"&gt;...&lt;/div&gt;
&lt;/div&gt;</code></pre>
        </div>
    </div>
</section>


<section id="forms-titel">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>3. Titel, Abschnittstitel und Hinweise</h2>

            <p>
                Formulare können einen Haupttitel, farbige Abschnittstitel und
                Notice-Boxen für Hinweise, Erfolg, Warnung oder Fehler enthalten.
            </p>
        </div>
    </div>
</section>


<section class="section--surface">
    <div class="container container--text-only">
        <form class="form form--card" action="#" method="post">
            <div class="form__grid form__grid--2">
                <div class="form__title form__title--center">
                    <h2>Zentrierter Formulartitel</h2>
                    <p>
                        Titel und Beschreibung können mit <code>.form__title--center</code>
                        zentriert werden.
                    </p>
                </div>

                <div class="form__notice form__notice--info">
                    <strong>Hinweis:</strong>
                    Diese Notice kann wichtige Informationen vor dem Ausfüllen zeigen.
                </div>

                <div class="form__section-title form__section-title--primary">
                    <h3>Primärer Abschnitt</h3>
                    <p>Abschnittstitel strukturieren lange Formulare.</p>
                </div>

                <div class="form__field">
                    <label class="form__label" for="forms-title-one">Feld eins</label>
                    <input class="form__control" id="forms-title-one" type="text">
                </div>

                <div class="form__field">
                    <label class="form__label" for="forms-title-two">Feld zwei</label>
                    <input class="form__control" id="forms-title-two" type="text">
                </div>

                <div class="form__section-title form__section-title--secondary-cta">
                    <h3>CTA-Abschnitt</h3>
                    <p>Für Bereiche, die stärker handlungsorientiert wirken sollen.</p>
                </div>

                <div class="form__notice form__notice--warning">
                    <strong>Achtung:</strong>
                    Diese Notice hebt eine Bedingung oder Prüfung hervor.
                </div>
            </div>
        </form>
    </div>
</section>


<section id="forms-felder">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>4. Standardfelder</h2>

            <p>
                Standardfelder bestehen immer aus <code>.form__field</code>,
                <code>.form__label</code> und <code>.form__control</code>.
                Hinweise werden mit <code>.form__hint</code> ergänzt.
            </p>
        </div>
    </div>
</section>


<section class="section--surface">
    <div class="container container--text-only">
        <form class="form form--card" action="#" method="post">
            <div class="form__grid form__grid--4">
                <div class="form__title">
                    <h2>Feldtypen</h2>
                    <p>
                        Beispiele für typische Input-, Select- und Textarea-Felder.
                    </p>
                </div>

                <div class="form__field form__field--span-2">
                    <label class="form__label" for="forms-field-name">
                        Name <span class="form__required">*</span>
                    </label>
                    <input class="form__control" id="forms-field-name" type="text" required>
                    <p class="form__hint">Pflichtfelder werden zusätzlich mit <code>required</code> ausgezeichnet.</p>
                </div>

                <div class="form__field form__field--span-2">
                    <label class="form__label" for="forms-field-email">E-Mail-Adresse</label>
                    <input class="form__control" id="forms-field-email" type="email" placeholder="name@example.org">
                </div>

                <div class="form__field form__field--span-2">
                    <label class="form__label" for="forms-field-role">Rolle</label>
                    <select class="form__control" id="forms-field-role">
                        <option>Bitte auswählen</option>
                        <option>Mitglied</option>
                        <option>Team</option>
                        <option>Vorstand</option>
                    </select>
                </div>

                <div class="form__field form__field--span-2">
                    <label class="form__label" for="forms-field-date">Datum</label>
                    <input class="form__control" id="forms-field-date" type="date">
                </div>

                <div class="form__field form__field--span-full">
                    <label class="form__label" for="forms-field-message">Nachricht</label>
                    <textarea class="form__control" id="forms-field-message" rows="5" placeholder="Nachricht eingeben..."></textarea>
                </div>
            </div>
        </form>
    </div>
</section>


<section id="forms-control-varianten">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>5. Control-Varianten</h2>

            <p>
                Neben dem Standard-Control gibt es Varianten für reduzierte oder
                stärker gefüllte Eingabefelder.
            </p>

            <p>
                <code>.form__control--underline</code> — nur untere Linie<br>
                <code>.form__control--filled</code> — dezente Füllfläche<br>
                <code>.form__control--file</code> — Datei-Upload
            </p>
        </div>
    </div>
</section>


<section class="section--surface">
    <div class="container container--text-only">
        <form class="form form--card" action="#" method="post">
            <div class="form__grid form__grid--3">
                <div class="form__title">
                    <h2>Control-Varianten im Vergleich</h2>
                </div>

                <div class="form__field">
                    <label class="form__label" for="forms-control-standard">Standard</label>
                    <input class="form__control" id="forms-control-standard" type="text" value="Standard-Control">
                </div>

                <div class="form__field">
                    <label class="form__label" for="forms-control-filled">Filled</label>
                    <input class="form__control form__control--filled" id="forms-control-filled" type="text" value="Gefülltes Control">
                </div>

                <div class="form__field">
                    <label class="form__label" for="forms-control-underline">Underline</label>
                    <input class="form__control form__control--underline" id="forms-control-underline" type="text" value="Underline-Control">
                </div>
            </div>
        </form>
    </div>
</section>


<section id="forms-validierung">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>6. Validierung und Meldungen</h2>

            <p>
                Validierungszustände werden am Feld über <code>.is-valid</code>
                oder <code>.is-invalid</code> gesetzt. Meldungen nutzen
                <code>.form__message</code> mit passender Variante.
            </p>
        </div>
    </div>
</section>


<section class="section--surface">
    <div class="container container--text-only">
        <form class="form form--card" action="#" method="post">
            <div class="form__grid form__grid--2">
                <div class="form__title">
                    <h2>Feldzustände</h2>
                    <p>Beispiele für gültige, fehlerhafte und deaktivierte Felder.</p>
                </div>

                <div class="form__field is-valid">
                    <label class="form__label" for="forms-valid">Gültiges Feld</label>
                    <input class="form__control" id="forms-valid" type="text" value="Alles korrekt">
                    <p class="form__message form__message--success">Die Eingabe wurde erfolgreich geprüft.</p>
                </div>

                <div class="form__field is-invalid">
                    <label class="form__label" for="forms-invalid">Fehlerhaftes Feld</label>
                    <input class="form__control" id="forms-invalid" type="text" value="Fehler" aria-invalid="true" aria-describedby="forms-invalid-error">
                    <p class="form__message form__message--error" id="forms-invalid-error">Bitte überprüfen Sie diese Eingabe.</p>
                </div>

                <div class="form__field">
                    <label class="form__label" for="forms-readonly">Read-only Feld</label>
                    <input class="form__control" id="forms-readonly" type="text" value="Nicht bearbeitbar" readonly>
                </div>

                <div class="form__field">
                    <label class="form__label" for="forms-disabled">Disabled Feld</label>
                    <input class="form__control" id="forms-disabled" type="text" value="Deaktiviert" disabled>
                </div>

                <div class="form__notice form__notice--success">
                    <strong>Erfolg:</strong>
                    Daten wurden gespeichert.
                </div>

                <div class="form__notice form__notice--error">
                    <strong>Fehler:</strong>
                    Einige Eingaben müssen korrigiert werden.
                </div>
            </div>
        </form>
    </div>
</section>


<section id="forms-auswahlfelder">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>7. Checkboxen, Radios und Switches</h2>

            <p>
                Zusammengehörige Auswahlfelder sollten in einem
                <code>fieldset.form__fieldset</code> mit
                <code>legend.form__legend</code> stehen.
            </p>
        </div>
    </div>
</section>


<section class="section--surface">
    <div class="container container--text-only">
        <form class="form form--card" action="#" method="post">
            <div class="form__grid form__grid--2">
                <div class="form__title">
                    <h2>Auswahlfelder</h2>
                </div>

                <fieldset class="form__fieldset">
                    <legend class="form__legend">Kontaktweg</legend>

                    <div class="form__choice-group">
                        <div class="form__check">
                            <input class="form__check-input" id="forms-radio-mail" name="forms-contact-way" type="radio">
                            <label class="form__check-label" for="forms-radio-mail">E-Mail</label>
                        </div>

                        <div class="form__check">
                            <input class="form__check-input" id="forms-radio-phone" name="forms-contact-way" type="radio">
                            <label class="form__check-label" for="forms-radio-phone">Telefon</label>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="form__fieldset">
                    <legend class="form__legend">Optionen</legend>

                    <div class="form__choice-group">
                        <div class="form__check">
                            <input class="form__check-input" id="forms-check-copy" type="checkbox">
                            <label class="form__check-label" for="forms-check-copy">
                                Ich möchte eine Kopie erhalten.
                            </label>
                        </div>

                        <div class="form__check">
                            <input class="form__check-input" id="forms-check-privacy" type="checkbox" required>
                            <label class="form__check-label" for="forms-check-privacy">
                                Ich akzeptiere die Datenschutzhinweise.
                                <small>Dieses Feld ist erforderlich.</small>
                            </label>
                        </div>
                    </div>
                </fieldset>

                <div class="form__field form__field--span-full">
                    <div class="form__switch">
                        <input class="form__switch-input" id="forms-switch-newsletter" type="checkbox">
                        <label class="form__switch-label" for="forms-switch-newsletter">
                            Newsletter abonnieren
                        </label>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>


<section id="forms-suche-datei">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>8. Suchfelder, Inline-Gruppen und Datei-Upload</h2>

            <p>
                Für Suchfelder mit Icon nutzt du <code>.form__input-icon</code>.
                Eingaben mit direkt danebenliegendem Button nutzt du mit
                <code>.form__inline-group</code>.
            </p>
        </div>
    </div>
</section>


<section class="section--surface">
    <div class="container container--text-only">
        <form class="form form--card" action="#" method="post" enctype="multipart/form-data">
            <div class="form__grid form__grid--2">
                <div class="form__title">
                    <h2>Suche, Inline-Gruppe und Upload</h2>
                </div>

                <div class="form__field form__field--span-full">
                    <label class="form__label" for="forms-search">Suche mit Icon</label>

                    <div class="form__input-icon">
                        <svg class="vdb-icon" aria-hidden="true">
                            <use href="/assets/icons/vdb-icons.svg#icon-search"></use>
                        </svg>

                        <input class="form__control" id="forms-search" type="search" placeholder="Suchbegriff eingeben">
                    </div>
                </div>

                <div class="form__field form__field--span-full">
                    <label class="form__label" for="forms-inline-search">Inline-Suche</label>

                    <div class="form__inline-group">
                        <input class="form__control" id="forms-inline-search" type="search" placeholder="Suchen...">
                        <button class="btn btn--primary btn--md" type="button">Suchen</button>
                    </div>
                </div>

                <div class="form__field form__field--span-full">
                    <label class="form__label" for="forms-file">Datei hochladen</label>
                    <input class="form__control form__control--file" id="forms-file" type="file">
                    <p class="form__hint">Für Datei-Uploads zusätzlich <code>.form__control--file</code> nutzen.</p>
                </div>
            </div>
        </form>
    </div>
</section>


<section id="forms-aktionen">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>9. Formular-Aktionen</h2>

            <p>
                Aktionsbuttons stehen in <code>.form__actions</code>.
                Die Ausrichtung kann über Varianten gesteuert werden.
            </p>

            <p>
                <code>.form__actions</code> — Standard<br>
                <code>.form__actions--right</code> — rechtsbündig<br>
                <code>.form__actions--center</code> — zentriert<br>
                <code>.form__actions--between</code> — verteilt<br>
                <code>.form__actions--stack</code> — gestapelt
            </p>
        </div>
    </div>
</section>


<section class="section--surface">
    <div class="container container--text-only">
        <form class="form form--card" action="#" method="post">
            <div class="form__grid form__grid--1">
                <div class="form__title">
                    <h2>Aktionsbereich</h2>
                    <p>Typische Kombination aus Abbrechen, Zwischenspeichern und Absenden.</p>
                </div>

                <div class="form__actions form__actions--right">
                    <button class="btn btn--primary-transp btn--md" type="reset">
                        Zurücksetzen
                    </button>

                    <button class="btn btn--primary-light btn--md" type="button">
                        Zwischenspeichern
                    </button>

                    <button class="btn btn--primary btn--md" type="submit">
                        Absenden
                    </button>
                </div>
            </div>
        </form>
    </div>
</section>


<section id="forms-komplettbeispiel">
    <div class="container container--text-only container--narrow container--center">
        <div class="container__text">
            <h2>10. Komplettes Kopierbeispiel</h2>

            <p>
                Die vollständige Formularvorlage liegt als eingeklappter Codeblock vor.
                So bleibt die Übersichtsseite kurz, aber du kannst bei Bedarf ein Formular
                mit fast allen vorgesehenen Inputs direkt kopieren.
            </p>
        </div>
    </div>
</section>


<section class="section--surface long-width">
    <div class="container container--text-only">
        <div class="container__text">
            <h3>Formular-Kopiervorlage als Code</h3>

            <p>
                Enthalten sind Textfelder, Kontaktfelder, Passwort, URL, Zahlen,
                Datum, Zeit, Monat, Woche, Farbe, Range, Selects, Mehrfachauswahl,
                Datalist, Suche mit Icon, Inline-Gruppe, Textarea, Checkboxen,
                Radios, Switch, Datei-Upload, Hidden Input, Read-only, Disabled,
                Validierung, Notices und Formularaktionen.
            </p>

            <div class="form__actions">
                <button class="btn btn--primary btn--md" type="button" data-copy-template="#forms-copy-template-code">
                    Formular-Code kopieren
                </button>
            </div>

            <details>
                <summary>
                    <span class="btn btn--primary-light btn--md">Kopiervorlage anzeigen</span>
                </summary>

                <pre><code id="forms-copy-template-code">&lt;form class="form form--card" action="#" method="post" enctype="multipart/form-data"&gt;
            &lt;div class="form__grid form__grid--4"&gt;
                &lt;div class="form__title form__title--center"&gt;
                    &lt;h2&gt;Vollständige Formular-Kopiervorlage&lt;/h2&gt;
                    &lt;p&gt;
                        Dieses Formular ist als Baukasten gedacht. Kopiere es und entferne
                        anschließend alle Felder, die du für deinen konkreten Zweck nicht brauchst.
                    &lt;/p&gt;
                &lt;/div&gt;

                &lt;input type="hidden" name="form_source" value="styleguide-copy-template"&gt;

                &lt;div class="form__notice form__notice--info"&gt;
                    &lt;strong&gt;Hinweis:&lt;/strong&gt;
                    Felder mit &lt;span class="form__required"&gt;*&lt;/span&gt; sind Pflichtfelder.
                    Die Vorlage zeigt bewusst viele Feldtypen auf einmal.
                &lt;/div&gt;

                &lt;div class="form__section-title form__section-title--primary"&gt;
                    &lt;h3&gt;Persönliche Daten&lt;/h3&gt;
                    &lt;p&gt;Grundlegende Text-, Kontakt- und Zugangsfelder.&lt;/p&gt;
                &lt;/div&gt;

                &lt;div class="form__field form__field--span-2"&gt;
                    &lt;label class="form__label" for="forms-copy-first"&gt;
                        Vorname &lt;span class="form__required"&gt;*&lt;/span&gt;
                    &lt;/label&gt;
                    &lt;input class="form__control" id="forms-copy-first" name="first_name" type="text" required autocomplete="given-name"&gt;
                    &lt;p class="form__hint"&gt;Normales Textfeld mit Pflichtfeld-Markierung.&lt;/p&gt;
                &lt;/div&gt;

                &lt;div class="form__field form__field--span-2"&gt;
                    &lt;label class="form__label" for="forms-copy-last"&gt;
                        Nachname &lt;span class="form__required"&gt;*&lt;/span&gt;
                    &lt;/label&gt;
                    &lt;input class="form__control" id="forms-copy-last" name="last_name" type="text" required autocomplete="family-name"&gt;
                &lt;/div&gt;

                &lt;div class="form__field form__field--span-2"&gt;
                    &lt;label class="form__label" for="forms-copy-email"&gt;
                        E-Mail-Adresse &lt;span class="form__required"&gt;*&lt;/span&gt;
                    &lt;/label&gt;
                    &lt;input class="form__control" id="forms-copy-email" name="email" type="email" required autocomplete="email" aria-describedby="forms-copy-email-hint"&gt;
                    &lt;p class="form__hint" id="forms-copy-email-hint"&gt;
                        Beispiel für &lt;code&gt;type="email"&lt;/code&gt; mit Hilfetext.
                    &lt;/p&gt;
                &lt;/div&gt;

                &lt;div class="form__field form__field--span-2"&gt;
                    &lt;label class="form__label" for="forms-copy-phone"&gt;Telefonnummer&lt;/label&gt;
                    &lt;input class="form__control" id="forms-copy-phone" name="phone" type="tel" placeholder="+49 ..." autocomplete="tel"&gt;
                &lt;/div&gt;

                &lt;div class="form__field form__field--span-2"&gt;
                    &lt;label class="form__label" for="forms-copy-password"&gt;Passwort&lt;/label&gt;
                    &lt;input class="form__control" id="forms-copy-password" name="password" type="password" autocomplete="new-password"&gt;
                &lt;/div&gt;

                &lt;div class="form__field form__field--span-2"&gt;
                    &lt;label class="form__label" for="forms-copy-website"&gt;Website&lt;/label&gt;
                    &lt;input class="form__control" id="forms-copy-website" name="website" type="url" placeholder="https://..."&gt;
                &lt;/div&gt;

                &lt;div class="form__field form__field--span-2"&gt;
                    &lt;label class="form__label" for="forms-copy-organization"&gt;Organisation&lt;/label&gt;
                    &lt;input class="form__control form__control--filled" id="forms-copy-organization" name="organization" type="text" placeholder="VDBS e.V."&gt;
                    &lt;p class="form__hint"&gt;Beispiel für &lt;code&gt;.form__control--filled&lt;/code&gt;.&lt;/p&gt;
                &lt;/div&gt;

                &lt;div class="form__field form__field--span-2"&gt;
                    &lt;label class="form__label" for="forms-copy-alias"&gt;Kurzbezeichnung&lt;/label&gt;
                    &lt;input class="form__control form__control--underline" id="forms-copy-alias" name="alias" type="text" placeholder="z. B. Portalteam"&gt;
                    &lt;p class="form__hint"&gt;Beispiel für &lt;code&gt;.form__control--underline&lt;/code&gt;.&lt;/p&gt;
                &lt;/div&gt;

                &lt;div class="form__section-title form__section-title--secondary-cta"&gt;
                    &lt;h3&gt;Organisation und Auswahl&lt;/h3&gt;
                    &lt;p&gt;Selects, Mehrfachauswahl und Datalist-artige Eingaben.&lt;/p&gt;
                &lt;/div&gt;

                &lt;div class="form__field form__field--span-2"&gt;
                    &lt;label class="form__label" for="forms-copy-role"&gt;Rolle&lt;/label&gt;
                    &lt;select class="form__control" id="forms-copy-role" name="role"&gt;
                        &lt;option value=""&gt;Bitte auswählen&lt;/option&gt;
                        &lt;option value="member"&gt;Mitglied&lt;/option&gt;
                        &lt;option value="team"&gt;Team&lt;/option&gt;
                        &lt;option value="board"&gt;Vorstand&lt;/option&gt;
                        &lt;option value="admin"&gt;Administration&lt;/option&gt;
                    &lt;/select&gt;
                &lt;/div&gt;

                &lt;div class="form__field form__field--span-2"&gt;
                    &lt;label class="form__label" for="forms-copy-area"&gt;Bereich&lt;/label&gt;
                    &lt;select class="form__control" id="forms-copy-area" name="area"&gt;
                        &lt;option value=""&gt;Bitte auswählen&lt;/option&gt;
                        &lt;option value="portal"&gt;Portal&lt;/option&gt;
                        &lt;option value="support"&gt;Support&lt;/option&gt;
                        &lt;option value="bibliothek"&gt;Bibliothek&lt;/option&gt;
                        &lt;option value="demokratiebildung"&gt;Demokratiebildung&lt;/option&gt;
                    &lt;/select&gt;
                &lt;/div&gt;

                &lt;div class="form__field form__field--span-full"&gt;
                    &lt;label class="form__label" for="forms-copy-interests"&gt;Interessen / Zuständigkeiten&lt;/label&gt;
                    &lt;select class="form__control" id="forms-copy-interests" name="interests[]" multiple&gt;
                        &lt;option value="workshops"&gt;Workshops&lt;/option&gt;
                        &lt;option value="schulbibliotheken"&gt;Schulbibliotheken&lt;/option&gt;
                        &lt;option value="serviceportal"&gt;Serviceportal&lt;/option&gt;
                        &lt;option value="veranstaltungen"&gt;Veranstaltungen&lt;/option&gt;
                        &lt;option value="verwaltung"&gt;Verwaltung&lt;/option&gt;
                    &lt;/select&gt;
                    &lt;p class="form__hint"&gt;Mehrfachauswahl ist möglich.&lt;/p&gt;
                &lt;/div&gt;

                &lt;div class="form__field form__field--span-full"&gt;
                    &lt;label class="form__label" for="forms-copy-browser"&gt;Browser / System&lt;/label&gt;
                    &lt;input class="form__control" id="forms-copy-browser" name="browser" type="text" list="forms-copy-browser-list" placeholder="Browser auswählen oder selbst eintragen"&gt;
                    &lt;datalist id="forms-copy-browser-list"&gt;
                        &lt;option value="Firefox"&gt;&lt;/option&gt;
                        &lt;option value="Chrome"&gt;&lt;/option&gt;
                        &lt;option value="Safari"&gt;&lt;/option&gt;
                        &lt;option value="Edge"&gt;&lt;/option&gt;
                    &lt;/datalist&gt;
                    &lt;p class="form__hint"&gt;Beispiel für ein Textfeld mit &lt;code&gt;datalist&lt;/code&gt;.&lt;/p&gt;
                &lt;/div&gt;

                &lt;div class="form__section-title form__section-title--secondary-highlight"&gt;
                    &lt;h3&gt;Zeit, Zahlen und Werte&lt;/h3&gt;
                    &lt;p&gt;Datum, Uhrzeit, Zahlenwerte, Farbe und Priorität.&lt;/p&gt;
                &lt;/div&gt;

                &lt;div class="form__field"&gt;
                    &lt;label class="form__label" for="forms-copy-age"&gt;Alter&lt;/label&gt;
                    &lt;input class="form__control" id="forms-copy-age" name="age" type="number" min="0" max="120" step="1"&gt;
                &lt;/div&gt;

                &lt;div class="form__field"&gt;
                    &lt;label class="form__label" for="forms-copy-date"&gt;Datum&lt;/label&gt;
                    &lt;input class="form__control" id="forms-copy-date" name="date" type="date"&gt;
                &lt;/div&gt;

                &lt;div class="form__field"&gt;
                    &lt;label class="form__label" for="forms-copy-time"&gt;Uhrzeit&lt;/label&gt;
                    &lt;input class="form__control" id="forms-copy-time" name="time" type="time"&gt;
                &lt;/div&gt;

                &lt;div class="form__field"&gt;
                    &lt;label class="form__label" for="forms-copy-datetime"&gt;Datum und Uhrzeit&lt;/label&gt;
                    &lt;input class="form__control" id="forms-copy-datetime" name="datetime" type="datetime-local"&gt;
                &lt;/div&gt;

                &lt;div class="form__field"&gt;
                    &lt;label class="form__label" for="forms-copy-month"&gt;Monat&lt;/label&gt;
                    &lt;input class="form__control" id="forms-copy-month" name="month" type="month"&gt;
                &lt;/div&gt;

                &lt;div class="form__field"&gt;
                    &lt;label class="form__label" for="forms-copy-week"&gt;Woche&lt;/label&gt;
                    &lt;input class="form__control" id="forms-copy-week" name="week" type="week"&gt;
                &lt;/div&gt;

                &lt;div class="form__field"&gt;
                    &lt;label class="form__label" for="forms-copy-color"&gt;Farbe&lt;/label&gt;
                    &lt;input class="form__control" id="forms-copy-color" name="color" type="color" value="#2FBF71"&gt;
                &lt;/div&gt;

                &lt;div class="form__field"&gt;
                    &lt;label class="form__label" for="forms-copy-range"&gt;Priorität&lt;/label&gt;
                    &lt;input class="form__control" id="forms-copy-range" name="priority" type="range" min="1" max="10" value="5"&gt;
                &lt;/div&gt;

                &lt;div class="form__section-title"&gt;
                    &lt;h3&gt;Suche und Nachricht&lt;/h3&gt;
                    &lt;p&gt;Suchfeld mit Icon, Inline-Gruppe und lange Texteingabe.&lt;/p&gt;
                &lt;/div&gt;

                &lt;div class="form__field form__field--span-full"&gt;
                    &lt;label class="form__label" for="forms-copy-search"&gt;Suche mit Icon&lt;/label&gt;

                    &lt;div class="form__input-icon"&gt;
                        &lt;svg class="vdb-icon" aria-hidden="true"&gt;
                            &lt;use href="/assets/icons/vdb-icons.svg#icon-search"&gt;&lt;/use&gt;
                        &lt;/svg&gt;

                        &lt;input class="form__control" id="forms-copy-search" name="search" type="search" placeholder="Suchbegriff eingeben"&gt;
                    &lt;/div&gt;
                &lt;/div&gt;

                &lt;div class="form__field form__field--span-full"&gt;
                    &lt;label class="form__label" for="forms-copy-quick-search"&gt;Inline-Suche&lt;/label&gt;

                    &lt;div class="form__inline-group"&gt;
                        &lt;input class="form__control" id="forms-copy-quick-search" name="quick_search" type="search" placeholder="Suchen..."&gt;

                        &lt;button class="btn btn--primary btn--md" type="button"&gt;
                            Suchen
                        &lt;/button&gt;
                    &lt;/div&gt;
                    &lt;p class="form__hint"&gt;Beispiel für &lt;code&gt;.form__inline-group&lt;/code&gt;.&lt;/p&gt;
                &lt;/div&gt;

                &lt;div class="form__field form__field--span-full"&gt;
                    &lt;label class="form__label" for="forms-copy-message"&gt;
                        Nachricht &lt;span class="form__required"&gt;*&lt;/span&gt;
                    &lt;/label&gt;

                    &lt;textarea class="form__control" id="forms-copy-message" name="message" rows="6" required placeholder="Schreiben Sie hier Ihre Nachricht..."&gt;&lt;/textarea&gt;
                &lt;/div&gt;

                &lt;div class="form__section-title"&gt;
                    &lt;h3&gt;Auswahlfelder&lt;/h3&gt;
                    &lt;p&gt;Radio-Gruppe, Checkboxen und Switch.&lt;/p&gt;
                &lt;/div&gt;

                &lt;fieldset class="form__fieldset"&gt;
                    &lt;legend class="form__legend"&gt;Kontaktweg&lt;/legend&gt;

                    &lt;div class="form__choice-group form__choice-group--horizontal"&gt;
                        &lt;div class="form__check"&gt;
                            &lt;input class="form__check-input" id="forms-copy-contact-email" name="contact_way" type="radio" value="email"&gt;
                            &lt;label class="form__check-label" for="forms-copy-contact-email"&gt;E-Mail&lt;/label&gt;
                        &lt;/div&gt;

                        &lt;div class="form__check"&gt;
                            &lt;input class="form__check-input" id="forms-copy-contact-phone" name="contact_way" type="radio" value="phone"&gt;
                            &lt;label class="form__check-label" for="forms-copy-contact-phone"&gt;Telefon&lt;/label&gt;
                        &lt;/div&gt;

                        &lt;div class="form__check"&gt;
                            &lt;input class="form__check-input" id="forms-copy-contact-none" name="contact_way" type="radio" value="none"&gt;
                            &lt;label class="form__check-label" for="forms-copy-contact-none"&gt;Keine Präferenz&lt;/label&gt;
                        &lt;/div&gt;
                    &lt;/div&gt;
                &lt;/fieldset&gt;

                &lt;fieldset class="form__fieldset"&gt;
                    &lt;legend class="form__legend"&gt;Einwilligungen&lt;/legend&gt;

                    &lt;div class="form__choice-group"&gt;
                        &lt;div class="form__check"&gt;
                            &lt;input class="form__check-input" id="forms-copy-privacy" name="privacy" type="checkbox" required&gt;
                            &lt;label class="form__check-label" for="forms-copy-privacy"&gt;
                                Ich akzeptiere die Datenschutzhinweise.
                                &lt;small&gt;Dieses Feld ist erforderlich.&lt;/small&gt;
                            &lt;/label&gt;
                        &lt;/div&gt;

                        &lt;div class="form__check"&gt;
                            &lt;input class="form__check-input" id="forms-copy-copy" name="copy" type="checkbox"&gt;
                            &lt;label class="form__check-label" for="forms-copy-copy"&gt;
                                Ich möchte eine Kopie meiner Anfrage erhalten.
                            &lt;/label&gt;
                        &lt;/div&gt;

                        &lt;div class="form__check"&gt;
                            &lt;input class="form__check-input" id="forms-copy-updates" name="updates" type="checkbox"&gt;
                            &lt;label class="form__check-label" for="forms-copy-updates"&gt;
                                Ich möchte über Updates zu diesem Vorgang informiert werden.
                            &lt;/label&gt;
                        &lt;/div&gt;
                    &lt;/div&gt;
                &lt;/fieldset&gt;

                &lt;div class="form__field form__field--span-full"&gt;
                    &lt;div class="form__switch"&gt;
                        &lt;input class="form__switch-input" id="forms-copy-newsletter" name="newsletter" type="checkbox"&gt;
                        &lt;label class="form__switch-label" for="forms-copy-newsletter"&gt;
                            Newsletter abonnieren
                        &lt;/label&gt;
                    &lt;/div&gt;
                &lt;/div&gt;

                &lt;div class="form__section-title"&gt;
                    &lt;h3&gt;Dateien und besondere Zustände&lt;/h3&gt;
                    &lt;p&gt;Datei-Upload, Read-only, Disabled, Erfolgs- und Fehlerzustand.&lt;/p&gt;
                &lt;/div&gt;

                &lt;div class="form__field form__field--span-2"&gt;
                    &lt;label class="form__label" for="forms-copy-file"&gt;Datei hochladen&lt;/label&gt;
                    &lt;input class="form__control form__control--file" id="forms-copy-file" name="file" type="file"&gt;
                &lt;/div&gt;

                &lt;div class="form__field form__field--span-2"&gt;
                    &lt;label class="form__label" for="forms-copy-readonly"&gt;Read-only Feld&lt;/label&gt;
                    &lt;input class="form__control" id="forms-copy-readonly" name="readonly" type="text" value="Dieser Wert kann nicht bearbeitet werden" readonly&gt;
                &lt;/div&gt;

                &lt;div class="form__field form__field--span-2"&gt;
                    &lt;label class="form__label" for="forms-copy-disabled"&gt;Disabled Feld&lt;/label&gt;
                    &lt;input class="form__control" id="forms-copy-disabled" name="disabled" type="text" value="Dieses Feld ist deaktiviert" disabled&gt;
                &lt;/div&gt;

                &lt;div class="form__field form__field--span-2 is-valid"&gt;
                    &lt;label class="form__label" for="forms-copy-valid"&gt;Gültiges Feld&lt;/label&gt;
                    &lt;input class="form__control" id="forms-copy-valid" name="valid_example" type="text" value="Alles korrekt"&gt;
                    &lt;p class="form__message form__message--success"&gt;
                        Die Eingabe wurde erfolgreich geprüft.
                    &lt;/p&gt;
                &lt;/div&gt;

                &lt;div class="form__field form__field--span-2 is-invalid"&gt;
                    &lt;label class="form__label" for="forms-copy-invalid"&gt;Fehlerhaftes Feld&lt;/label&gt;
                    &lt;input class="form__control" id="forms-copy-invalid" name="invalid_example" type="text" value="Fehler" aria-invalid="true" aria-describedby="forms-copy-invalid-error"&gt;
                    &lt;p class="form__message form__message--error" id="forms-copy-invalid-error"&gt;
                        Bitte überprüfen Sie diese Eingabe.
                    &lt;/p&gt;
                &lt;/div&gt;

                &lt;div class="form__field form__field--span-2"&gt;
                    &lt;label class="form__label" for="forms-copy-disabled-select"&gt;Disabled Select&lt;/label&gt;
                    &lt;select class="form__control" id="forms-copy-disabled-select" name="disabled_select" disabled&gt;
                        &lt;option&gt;Derzeit nicht verfügbar&lt;/option&gt;
                    &lt;/select&gt;
                &lt;/div&gt;

                &lt;div class="form__notice form__notice--success"&gt;
                    &lt;strong&gt;Erfolg:&lt;/strong&gt;
                    Diese Meldung kann nach erfolgreichem Speichern angezeigt werden.
                &lt;/div&gt;

                &lt;div class="form__notice form__notice--warning"&gt;
                    &lt;strong&gt;Achtung:&lt;/strong&gt;
                    Diese Meldung weist auf eine wichtige Bedingung hin.
                &lt;/div&gt;

                &lt;div class="form__notice form__notice--error"&gt;
                    &lt;strong&gt;Fehler:&lt;/strong&gt;
                    Diese Meldung kann bei einem Formularfehler angezeigt werden.
                &lt;/div&gt;

                &lt;div class="form__actions form__actions--right"&gt;
                    &lt;button class="btn btn--primary-transp btn--md" type="reset"&gt;
                        Zurücksetzen
                    &lt;/button&gt;

                    &lt;button class="btn btn--primary-light btn--md" type="button"&gt;
                        Zwischenspeichern
                    &lt;/button&gt;

                    &lt;button class="btn btn--primary btn--md" type="submit"&gt;
                        Absenden
                    &lt;/button&gt;
                &lt;/div&gt;
            &lt;/div&gt;
        &lt;/form&gt;</code></pre>
            </details>
        </div>
    </div>

    <script>
    (function () {
        document.querySelectorAll('[data-copy-template]').forEach(function (button) {
            button.addEventListener('click', function () {
                var targetSelector = button.getAttribute('data-copy-template');
                var target = document.querySelector(targetSelector);

                if (!target) {
                    return;
                }

                navigator.clipboard.writeText(target.textContent).then(function () {
                    var originalText = button.textContent;
                    button.textContent = 'Code kopiert';

                    window.setTimeout(function () {
                        button.textContent = originalText;
                    }, 1500);
                });
            });
        });
    })();
    </script>
</section>


<section id="forms-entscheidungshilfe">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>11. Entscheidungshilfe</h2>

            <h3>Kurzes Formular</h3>
            <pre><code>&lt;form class="form"&gt;
    &lt;div class="form__grid form__grid--1"&gt;
        ...
    &lt;/div&gt;
&lt;/form&gt;</code></pre>

            <h3>Normales zweispaltiges Formular</h3>
            <pre><code>&lt;form class="form form--card"&gt;
    &lt;div class="form__grid form__grid--2"&gt;
        ...
    &lt;/div&gt;
&lt;/form&gt;</code></pre>

            <h3>Komplexes Verwaltungsformular</h3>
            <pre><code>&lt;form class="form form--card"&gt;
    &lt;div class="form__grid form__grid--4"&gt;
        ...
    &lt;/div&gt;
&lt;/form&gt;</code></pre>

            <h3>Feld über ganze Breite</h3>
            <pre><code>&lt;div class="form__field form__field--span-full"&gt;
    ...
&lt;/div&gt;</code></pre>

            <h3>Barrierefreiheit</h3>
            <p>
                Labels immer über <code>for</code> und <code>id</code> verbinden.
                Pflichtfelder mit <code>required</code> auszeichnen.
                Fehlermeldungen mit <code>aria-describedby</code> verbinden.
                Ungültige Felder mit <code>aria-invalid="true"</code> markieren.
            </p>
        </div>

        <div class="container__buttons btn-group btn-group--vertical btn-group--gap-sm">
            <a href="#forms-uebersicht" class="btn btn--primary btn--md">
                Zurück nach oben
            </a>
        </div>
    </div>
</section>