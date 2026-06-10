<?php declare(strict_types=1); ?>

<section id="buttons-uebersicht" class="section--surface">
    <div class="container container--text-only container--narrow container--center">
        <div class="container__text">
            <h1>Übersicht: buttons.css</h1>

            <p>
                Diese Seite zeigt die wichtigsten Button-Strukturen, Größen,
                Farbvarianten, Icon-Buttons, Button-Gruppen und Zustände.
            </p>

            <p>
                Grundregel:
                Jeder Button erhält zuerst die Basisklasse <code>.btn</code>.
                Danach ergänzt du Größe, Farbvariante und bei Bedarf Gruppen-,
                Icon- oder Statusklassen.
            </p>
        </div>

        <div class="container__buttons btn-group btn-group--vertical btn-group--main-center btn-group--gap-sm">
            <a href="/styleguide/buttons/generator" class="btn btn--primary btn--md">
                Zum Button Generator
            </a>

            <a href="#buttons-inhaltsverzeichnis" class="btn btn--primary-light btn--md">
                Zum Inhaltsverzeichnis
            </a>

            <a href="#buttons-entscheidungshilfe" class="btn btn--primary-transp btn--md">
                Zur Entscheidungshilfe
            </a>
        </div>
    </div>
</section>


<section id="buttons-inhaltsverzeichnis">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>Inhaltsverzeichnis</h2>

            <p>
                <a href="/styleguide/buttons/generator">Button Generator öffnen</a><br>
                <a href="#button-grundstruktur">1. Grundstruktur eines Buttons</a><br>
                <a href="#button-groessen">2. Button-Größen</a><br>
                <a href="#button-farben-dunkel">3. Farbvarianten: Dunkel zu Hell</a><br>
                <a href="#button-farben-hell">4. Farbvarianten: Hell zu Dunkel</a><br>
                <a href="#button-farben-transparent">5. Transparente Varianten</a><br>
                <a href="#button-status">6. Status-Buttons</a><br>
                <a href="#button-outline-ghost">7. Outline, Ghost und Danger</a><br>
                <a href="#button-icons">8. Buttons mit Icons</a><br>
                <a href="#button-icon-only">9. Icon-only Buttons</a><br>
                <a href="#button-groups">10. Button-Groups</a><br>
                <a href="#button-group-ausrichtung">11. Ausrichtung von Button-Groups</a><br>
                <a href="#button-group-abstaende">12. Abstände in Button-Groups</a><br>
                <a href="#button-group-title">13. Button-Groups mit Titel und Untertitel</a><br>
                <a href="#button-disabled">14. Disabled, Fokus und Barrierefreiheit</a><br>
                <a href="#buttons-entscheidungshilfe">15. Entscheidungshilfe</a>
            </p>
        </div>
    </div>
</section>


<section id="button-generator-hinweis" class="section--surface">
    <div class="container container--text-buttons">
        <div class="container__text">
            <h2>Button Generator</h2>

            <p>
                Mit dem Button Generator kannst du Button-Klassen visuell
                zusammenstellen und den fertigen HTML-Code direkt übernehmen.
            </p>

            <p>
                Besonders hilfreich ist der Generator, wenn du Größe,
                Farbvariante, Icon-Optionen und Button-Groups kombinieren
                möchtest.
            </p>

            <p>
                Typische Kombination:
                <code>.btn</code>,
                <code>.btn--primary</code>,
                <code>.btn--md</code>
            </p>
        </div>

        <div class="container__buttons btn-group btn-group--vertical btn-group--sec-stretch btn-group--gap-sm">
            <a href="/styleguide/buttons/generator" class="btn btn--primary btn--md">
                Button Generator öffnen
            </a>

            <a href="#button-grundstruktur" class="btn btn--primary-transp btn--md">
                Erst Grundlagen ansehen
            </a>
        </div>
    </div>
</section>


<section id="button-grundstruktur" class="section--surface">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>1. Grundstruktur eines Buttons</h2>

            <p>
                Jeder Button beginnt mit der Klasse <code>.btn</code>.
                Diese Klasse legt Grundlayout, Ausrichtung, Padding,
                Schriftgewicht, Farbe, Border-Radius und Übergänge fest.
            </p>

            <h3>Einfacher Button</h3>

            <p>
                <a href="#" class="btn btn--primary btn--md">Primärer Button</a>
            </p>

            <pre><code>&lt;a href="#" class="btn btn--primary btn--md"&gt;
    Primärer Button
&lt;/a&gt;</code></pre>

            <h3>Empfohlene Struktur</h3>

            <p>
                Für Links verwendest du <code>&lt;a&gt;</code>.
                Für echte Aktionen in Formularen oder Interfaces verwendest du
                <code>&lt;button&gt;</code>.
            </p>

            <pre><code>&lt;button type="button" class="btn btn--primary btn--md"&gt;
    Aktion ausführen
&lt;/button&gt;</code></pre>

            <h3>Klassenlogik</h3>

            <p>
                <code>.btn</code><br>
                Grundklasse für alle Buttons.
            </p>

            <p>
                <code>.btn--primary</code><br>
                Farbvariante.
            </p>

            <p>
                <code>.btn--md</code><br>
                Größenvariante.
            </p>

            <p>
                Die Reihenfolge im HTML ist nicht entscheidend, aber für die
                Lesbarkeit empfiehlt sich:
            </p>

            <pre><code>class="btn btn--farbe btn--größe"</code></pre>
        </div>
    </div>
</section>


<section id="button-groessen">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>2. Button-Größen</h2>

            <p>
                Die Größenklassen steuern Schriftgröße, Schriftgewicht und
                Innenabstand. Die Farbklasse bleibt unabhängig davon.
            </p>

            <h3>Alle Größen im Vergleich</h3>

            <div class="btn-group btn-group--vertical btn-group--gap-sm">
                <a href="#" class="btn btn--primary btn--xs">Extra small — .btn--xs</a>
                <a href="#" class="btn btn--primary btn--sm">Small — .btn--sm</a>
                <a href="#" class="btn btn--primary btn--md">Medium — .btn--md</a>
                <a href="#" class="btn btn--primary btn--lg">Large — .btn--lg</a>
                <a href="#" class="btn btn--primary btn--xl">Extra large — .btn--xl</a>
            </div>

            <h3>Code</h3>

            <pre><code>&lt;a href="#" class="btn btn--primary btn--xs"&gt;Extra small&lt;/a&gt;
&lt;a href="#" class="btn btn--primary btn--sm"&gt;Small&lt;/a&gt;
&lt;a href="#" class="btn btn--primary btn--md"&gt;Medium&lt;/a&gt;
&lt;a href="#" class="btn btn--primary btn--lg"&gt;Large&lt;/a&gt;
&lt;a href="#" class="btn btn--primary btn--xl"&gt;Extra large&lt;/a&gt;</code></pre>

            <h3>Verwendung</h3>

            <p>
                <code>.btn--xs</code><br>
                Für sehr kleine Interface-Aktionen, Labels oder kompakte Tools.
            </p>

            <p>
                <code>.btn--sm</code><br>
                Für sekundäre Aktionen, Tabellen, Karten oder kompakte Bereiche.
            </p>

            <p>
                <code>.btn--md</code><br>
                Standardgröße für normale Seitenbuttons.
            </p>

            <p>
                <code>.btn--lg</code><br>
                Für wichtige Call-to-Actions.
            </p>

            <p>
                <code>.btn--xl</code><br>
                Für sehr große Einstiege oder Hero-Bereiche.
            </p>
        </div>
    </div>
</section>


<section id="button-farben-dunkel" class="section--surface">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>3. Farbvarianten: Dunkel zu Hell</h2>

            <p>
                Diese Buttons haben im Normalzustand eine starke Farbfläche.
                Beim Hover werden sie heller und wechseln auf dunkle Schrift.
            </p>

            <p>
                Sie eignen sich für primäre oder stark sichtbare Aktionen.
            </p>

            <div class="btn-group btn-group--vertical btn-group--gap-sm">
                <a href="#" class="btn btn--primary btn--md">Primary — .btn--primary</a>
                <a href="#" class="btn btn--secondary-cta btn--md">Secondary CTA — .btn--secondary-cta</a>
                <a href="#" class="btn btn--secondary-highlight btn--md">Secondary Highlight — .btn--secondary-highlight</a>
            </div>

            <h3>Code</h3>

            <pre><code>&lt;a href="#" class="btn btn--primary btn--md"&gt;
    Primary
&lt;/a&gt;

&lt;a href="#" class="btn btn--secondary-cta btn--md"&gt;
    Secondary CTA
&lt;/a&gt;

&lt;a href="#" class="btn btn--secondary-highlight btn--md"&gt;
    Secondary Highlight
&lt;/a&gt;</code></pre>

            <h3>Verwendung</h3>

            <p>
                <code>.btn--primary</code><br>
                Hauptaktion einer Seite oder eines Moduls.
            </p>

            <p>
                <code>.btn--secondary-cta</code><br>
                Alternative starke Aktion, etwa Anmeldung, Kontakt oder Portalzugang.
            </p>

            <p>
                <code>.btn--secondary-highlight</code><br>
                Hervorhebung für besondere Hinweise, Termine oder Key Facts.
            </p>
        </div>
    </div>
</section>


<section id="button-farben-hell">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>4. Farbvarianten: Hell zu Dunkel</h2>

            <p>
                Diese Buttons starten mit einer helleren Farbfläche und werden
                beim Hover kräftiger. Sie wirken zurückhaltender als die dunklen
                Varianten.
            </p>

            <p>
                Sie eignen sich gut als Nebenaktionen neben einem starken
                Primärbutton.
            </p>

            <div class="btn-group btn-group--vertical btn-group--gap-sm">
                <a href="#" class="btn btn--primary-light btn--md">Primary Light — .btn--primary-light</a>
                <a href="#" class="btn btn--secondary-cta-light btn--md">Secondary CTA Light — .btn--secondary-cta-light</a>
                <a href="#" class="btn btn--secondary-highlight-light btn--md">Secondary Highlight Light — .btn--secondary-highlight-light</a>
            </div>

            <h3>Code</h3>

            <pre><code>&lt;a href="#" class="btn btn--primary-light btn--md"&gt;
    Primary Light
&lt;/a&gt;

&lt;a href="#" class="btn btn--secondary-cta-light btn--md"&gt;
    Secondary CTA Light
&lt;/a&gt;

&lt;a href="#" class="btn btn--secondary-highlight-light btn--md"&gt;
    Secondary Highlight Light
&lt;/a&gt;</code></pre>

            <h3>Verwendung</h3>

            <p>
                Hellvarianten sind gut, wenn ein Button sichtbar sein soll,
                aber nicht die komplette Aufmerksamkeit beanspruchen darf.
            </p>
        </div>
    </div>
</section>


<section id="button-farben-transparent" class="section--surface">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>5. Transparente Varianten</h2>

            <p>
                Transparente Buttons haben im Normalzustand keine Farbfläche.
                Erst beim Hover erhalten sie eine farbige Fläche.
            </p>

            <p>
                Sie eignen sich für dezente Nebenaktionen, Navigation oder
                sekundäre Links innerhalb von Button-Gruppen.
            </p>

            <div class="btn-group btn-group--vertical btn-group--gap-sm">
                <a href="#" class="btn btn--primary-transp btn--md">Primary Transparent — .btn--primary-transp</a>
                <a href="#" class="btn btn--secondary-cta-transp btn--md">Secondary CTA Transparent — .btn--secondary-cta-transp</a>
                <a href="#" class="btn btn--secondary-highlight-transp btn--md">Secondary Highlight Transparent — .btn--secondary-highlight-transp</a>
            </div>

            <h3>Code</h3>

            <pre><code>&lt;a href="#" class="btn btn--primary-transp btn--md"&gt;
    Primary Transparent
&lt;/a&gt;

&lt;a href="#" class="btn btn--secondary-cta-transp btn--md"&gt;
    Secondary CTA Transparent
&lt;/a&gt;

&lt;a href="#" class="btn btn--secondary-highlight-transp btn--md"&gt;
    Secondary Highlight Transparent
&lt;/a&gt;</code></pre>

            <h3>Typische Kombination</h3>

            <pre><code>&lt;div class="btn-group btn-group--vertical btn-group--gap-sm"&gt;
    &lt;a href="#" class="btn btn--primary btn--md"&gt;
        Hauptaktion
    &lt;/a&gt;

    &lt;a href="#" class="btn btn--primary-transp btn--md"&gt;
        Nebenaktion
    &lt;/a&gt;
&lt;/div&gt;</code></pre>
        </div>
    </div>
</section>


<section id="button-status">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>6. Status-Buttons</h2>

            <p>
                Status-Buttons zeigen eine semantische Bedeutung:
                Erfolg, Fehler, Warnung oder Information.
            </p>

            <p>
                Sie sollten nicht beliebig als Schmuckfarbe verwendet werden,
                sondern nur dann, wenn die Bedeutung wirklich passt.
            </p>

            <div class="btn-group btn-group--vertical btn-group--gap-sm">
                <a href="#" class="btn btn--success btn--md">Success — .btn--success</a>
                <a href="#" class="btn btn--error btn--md">Error — .btn--error</a>
                <a href="#" class="btn btn--warning btn--md">Warning — .btn--warning</a>
                <a href="#" class="btn btn--info btn--md">Info — .btn--info</a>
            </div>

            <h3>Code</h3>

            <pre><code>&lt;button type="button" class="btn btn--success btn--md"&gt;
    Speichern erfolgreich
&lt;/button&gt;

&lt;button type="button" class="btn btn--error btn--md"&gt;
    Fehler anzeigen
&lt;/button&gt;

&lt;button type="button" class="btn btn--warning btn--md"&gt;
    Warnung prüfen
&lt;/button&gt;

&lt;button type="button" class="btn btn--info btn--md"&gt;
    Information öffnen
&lt;/button&gt;</code></pre>

            <h3>Verwendung</h3>

            <p>
                <code>.btn--success</code><br>
                Positive Bestätigung oder abgeschlossene Aktion.
            </p>

            <p>
                <code>.btn--error</code><br>
                Fehler, Abbruch oder problematische Aktion.
            </p>

            <p>
                <code>.btn--warning</code><br>
                Warnung, Prüfung oder riskante Zwischenentscheidung.
            </p>

            <p>
                <code>.btn--info</code><br>
                Zusatzinformationen oder neutrale Hinweise.
            </p>
        </div>
    </div>
</section>


<section id="button-outline-ghost" class="section--surface">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>7. Outline, Ghost und Danger</h2>

            <p>
                Zusätzlich zu den Farbvarianten gibt es reduzierte Varianten
                für ruhigere Interfaces.
            </p>

            <div class="btn-group btn-group--vertical btn-group--gap-sm">
                <a href="#" class="btn btn--outline btn--md">Outline — .btn--outline</a>
                <a href="#" class="btn btn--ghost btn--md">Ghost — .btn--ghost</a>
                <a href="#" class="btn btn--danger btn--md">Danger — .btn--danger</a>
            </div>

            <h3>Code</h3>

            <pre><code>&lt;a href="#" class="btn btn--outline btn--md"&gt;
    Outline
&lt;/a&gt;

&lt;a href="#" class="btn btn--ghost btn--md"&gt;
    Ghost
&lt;/a&gt;

&lt;button type="button" class="btn btn--danger btn--md"&gt;
    Löschen
&lt;/button&gt;</code></pre>

            <h3>Verwendung</h3>

            <p>
                <code>.btn--outline</code><br>
                Ruhiger Button mit Rahmen. Gut für sekundäre Aktionen.
            </p>

            <p>
                <code>.btn--ghost</code><br>
                Sehr zurückhaltender Button ohne sichtbare Fläche im Normalzustand.
            </p>

            <p>
                <code>.btn--danger</code><br>
                Kritische Aktion, zum Beispiel Löschen oder endgültiges Entfernen.
            </p>
        </div>
    </div>
</section>


<section id="button-icons">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>8. Buttons mit Icons</h2>

            <p>
                Für Buttons mit Symbol verwendest du zusätzlich
                <code>.btn-icon</code>. Das SVG erhält automatisch Abstand zum
                Text. Mit <code>.btn--gap-*</code> kannst du den Abstand anpassen.
            </p>

            <div class="btn-group btn-group--vertical btn-group--gap-sm">
                <a href="#" class="btn btn-icon btn--primary btn--md">
                    <svg width="20" height="20" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M5 12H19M13 6L19 12L13 18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    Weiter
                </a>

                <a href="#" class="btn btn-icon btn--secondary-cta btn--md btn--gap-lg">
                    <svg width="20" height="20" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 5V19M5 12H19" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>
                    Mit größerem Icon-Abstand
                </a>
            </div>

            <h3>Code</h3>

            <pre><code>&lt;a href="#" class="btn btn-icon btn--primary btn--md"&gt;
    &lt;svg width="20" height="20" viewBox="0 0 24 24" aria-hidden="true"&gt;
        ...
    &lt;/svg&gt;
    Weiter
&lt;/a&gt;</code></pre>

            <h3>Icon-Abstände</h3>

            <p>
                <code>.btn--gap-xs</code><br>
                Sehr kleiner Abstand zwischen Icon und Text.
            </p>

            <p>
                <code>.btn--gap-sm</code><br>
                Kleiner Abstand.
            </p>

            <p>
                <code>.btn--gap-md</code><br>
                Mittlerer Abstand.
            </p>

            <p>
                <code>.btn--gap-lg</code><br>
                Großer Abstand.
            </p>

            <p>
                <code>.btn--gap-xl</code><br>
                Sehr großer Abstand.
            </p>

            <pre><code>&lt;a href="#" class="btn btn-icon btn--primary btn--md btn--gap-sm"&gt;
    ...
&lt;/a&gt;</code></pre>
        </div>
    </div>
</section>


<section id="button-icon-only" class="section--surface">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>9. Icon-only Buttons</h2>

            <p>
                Icon-only Buttons enthalten nur ein Symbol. Sie brauchen immer
                ein aussagekräftiges <code>aria-label</code>, weil sonst nicht
                klar ist, was der Button macht.
            </p>

            <div class="btn-group btn-group--horizontal btn-group--gap-sm">
                <button type="button" class="btn btn-icon btn--icon-only btn--primary btn--xs" aria-label="Schließen">
                    <svg width="16" height="16" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M6 6L18 18M18 6L6 18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>
                </button>

                <button type="button" class="btn btn-icon btn--icon-only btn--primary btn--sm" aria-label="Schließen">
                    <svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M6 6L18 18M18 6L6 18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>
                </button>

                <button type="button" class="btn btn-icon btn--icon-only btn--primary btn--md" aria-label="Schließen">
                    <svg width="20" height="20" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M6 6L18 18M18 6L6 18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>
                </button>

                <button type="button" class="btn btn-icon btn--icon-only btn--primary btn--lg" aria-label="Schließen">
                    <svg width="22" height="22" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M6 6L18 18M18 6L6 18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>
                </button>

                <button type="button" class="btn btn-icon btn--icon-only btn--primary btn--xl" aria-label="Schließen">
                    <svg width="24" height="24" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M6 6L18 18M18 6L6 18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>
                </button>
            </div>

            <h3>Code</h3>

            <pre><code>&lt;button
    type="button"
    class="btn btn-icon btn--icon-only btn--primary btn--md"
    aria-label="Schließen"
&gt;
    &lt;svg width="20" height="20" viewBox="0 0 24 24" aria-hidden="true"&gt;
        ...
    &lt;/svg&gt;
&lt;/button&gt;</code></pre>

            <h3>Wichtige Regel</h3>

            <p>
                Bei Icon-only Buttons darf die Bedeutung nicht nur visuell
                vermittelt werden. Deshalb immer:
            </p>

            <pre><code>aria-label="Schließen"</code></pre>
        </div>
    </div>
</section>


<section id="button-groups">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>10. Button-Groups</h2>

            <p>
                Button-Groups ordnen mehrere Buttons gemeinsam an.
                Standardmäßig ist eine <code>.btn-group</code> horizontal.
                Mit Modifiern steuerst du Richtung, Ausrichtung und Abstand.
            </p>

            <h3>Horizontale Button-Group</h3>

            <div class="btn-group btn-group--horizontal btn-group--gap-sm">
                <a href="#" class="btn btn--primary btn--md">Speichern</a>
                <a href="#" class="btn btn--primary-transp btn--md">Abbrechen</a>
            </div>

            <pre><code>&lt;div class="btn-group btn-group--horizontal btn-group--gap-sm"&gt;
    &lt;a href="#" class="btn btn--primary btn--md"&gt;Speichern&lt;/a&gt;
    &lt;a href="#" class="btn btn--primary-transp btn--md"&gt;Abbrechen&lt;/a&gt;
&lt;/div&gt;</code></pre>

            <h3>Vertikale Button-Group</h3>

            <div class="btn-group btn-group--vertical btn-group--gap-sm">
                <a href="#" class="btn btn--primary btn--md">Primäre Aktion</a>
                <a href="#" class="btn btn--primary-light btn--md">Sekundäre Aktion</a>
                <a href="#" class="btn btn--primary-transp btn--md">Weitere Aktion</a>
            </div>

            <pre><code>&lt;div class="btn-group btn-group--vertical btn-group--gap-sm"&gt;
    &lt;a href="#" class="btn btn--primary btn--md"&gt;Primäre Aktion&lt;/a&gt;
    &lt;a href="#" class="btn btn--primary-light btn--md"&gt;Sekundäre Aktion&lt;/a&gt;
    &lt;a href="#" class="btn btn--primary-transp btn--md"&gt;Weitere Aktion&lt;/a&gt;
&lt;/div&gt;</code></pre>
        </div>
    </div>
</section>


<section id="button-group-ausrichtung" class="section--surface">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>11. Ausrichtung von Button-Groups</h2>

            <p>
                Button-Groups sind Flexbox-Container. Deshalb gibt es zwei
                Ausrichtungsachsen:
                Hauptachse und Nebenachse.
            </p>

            <h3>Hauptachse</h3>

            <p>
                Die Hauptachse hängt von der Richtung ab.
                Bei horizontalen Gruppen läuft sie von links nach rechts.
                Bei vertikalen Gruppen läuft sie von oben nach unten.
            </p>

            <p>
                Klassen:
                <code>.btn-group--main-start</code>,
                <code>.btn-group--main-center</code>,
                <code>.btn-group--main-end</code>,
                <code>.btn-group--main-space-between</code>,
                <code>.btn-group--main-space-around</code>,
                <code>.btn-group--main-space-evenly</code>
            </p>

            <h3>Beispiel: zentriert</h3>

            <div class="btn-group btn-group--horizontal btn-group--main-center btn-group--gap-sm">
                <a href="#" class="btn btn--primary btn--md">Button 1</a>
                <a href="#" class="btn btn--primary-light btn--md">Button 2</a>
            </div>

            <pre><code>&lt;div class="btn-group btn-group--horizontal btn-group--main-center btn-group--gap-sm"&gt;
    ...
&lt;/div&gt;</code></pre>

            <h3>Nebenachse</h3>

            <p>
                Die Nebenachse steuert, wie Buttons quer zur Richtung
                ausgerichtet werden.
            </p>

            <p>
                Klassen:
                <code>.btn-group--sec-start</code>,
                <code>.btn-group--sec-center</code>,
                <code>.btn-group--sec-end</code>,
                <code>.btn-group--sec-stretch</code>
            </p>

            <h3>Beispiel: Buttons auf gleiche Breite strecken</h3>

            <div class="btn-group btn-group--vertical btn-group--sec-stretch btn-group--gap-sm">
                <a href="#" class="btn btn--primary btn--md">Langer Button</a>
                <a href="#" class="btn btn--primary-light btn--md">Kurz</a>
            </div>

            <pre><code>&lt;div class="btn-group btn-group--vertical btn-group--sec-stretch btn-group--gap-sm"&gt;
    &lt;a href="#" class="btn btn--primary btn--md"&gt;Langer Button&lt;/a&gt;
    &lt;a href="#" class="btn btn--primary-light btn--md"&gt;Kurz&lt;/a&gt;
&lt;/div&gt;</code></pre>
        </div>
    </div>
</section>


<section id="button-group-abstaende">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>12. Abstände in Button-Groups</h2>

            <p>
                Die Gap-Klassen steuern den Abstand zwischen Buttons innerhalb
                einer Gruppe.
            </p>

            <h3>Gap-Varianten</h3>

            <p>
                <code>.btn-group--gap-xs</code><br>
                Sehr kleiner Abstand.
            </p>

            <p>
                <code>.btn-group--gap-sm</code><br>
                Kleiner Standardabstand.
            </p>

            <p>
                <code>.btn-group--gap-md</code><br>
                Mittlerer Abstand.
            </p>

            <p>
                <code>.btn-group--gap-lg</code><br>
                Großer Abstand.
            </p>

            <p>
                <code>.btn-group--gap-xl</code><br>
                Sehr großer Abstand.
            </p>

            <h3>Beispiel</h3>

            <div class="btn-group btn-group--horizontal btn-group--gap-lg">
                <a href="#" class="btn btn--primary btn--md">Button 1</a>
                <a href="#" class="btn btn--primary-light btn--md">Button 2</a>
                <a href="#" class="btn btn--primary-transp btn--md">Button 3</a>
            </div>

            <pre><code>&lt;div class="btn-group btn-group--horizontal btn-group--gap-lg"&gt;
    ...
&lt;/div&gt;</code></pre>
        </div>
    </div>
</section>


<section id="button-group-title" class="section--surface">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>13. Button-Groups mit Titel und Untertitel</h2>

            <p>
                Button-Groups können einen eigenen Titel- und Untertitelbereich
                erhalten. Dafür wird innerhalb der Button-Group ein
                <code>.btn-group__title-container</code> ergänzt.
            </p>

            <div class="btn-group btn-group--vertical btn-group--sec-stretch btn-group--gap-sm btn-group--title-center">
                <div class="btn-group__title-container">
                    <h3 class="btn-group__title">Servicebereich</h3>
                    <p class="btn-group__subtitle">
                        Wähle eine der folgenden Aktionen aus.
                    </p>
                </div>

                <a href="#" class="btn btn--primary btn--md">Zum Portal</a>
                <a href="#" class="btn btn--primary-light btn--md">Kontakt aufnehmen</a>
                <a href="#" class="btn btn--primary-transp btn--md">Hilfe lesen</a>
            </div>

            <h3>Code</h3>

            <pre><code>&lt;div class="btn-group btn-group--vertical btn-group--sec-stretch btn-group--gap-sm btn-group--title-center"&gt;
    &lt;div class="btn-group__title-container"&gt;
        &lt;h3 class="btn-group__title"&gt;Servicebereich&lt;/h3&gt;
        &lt;p class="btn-group__subtitle"&gt;
            Wähle eine der folgenden Aktionen aus.
        &lt;/p&gt;
    &lt;/div&gt;

    &lt;a href="#" class="btn btn--primary btn--md"&gt;Zum Portal&lt;/a&gt;
    &lt;a href="#" class="btn btn--primary-light btn--md"&gt;Kontakt aufnehmen&lt;/a&gt;
    &lt;a href="#" class="btn btn--primary-transp btn--md"&gt;Hilfe lesen&lt;/a&gt;
&lt;/div&gt;</code></pre>

            <h3>Titel-Ausrichtung</h3>

            <p>
                <code>.btn-group--title-start</code><br>
                Titel und Untertitel linksbündig.
            </p>

            <p>
                <code>.btn-group--title-center</code><br>
                Titel und Untertitel zentriert.
            </p>

            <p>
                <code>.btn-group--title-end</code><br>
                Titel und Untertitel rechtsbündig.
            </p>
        </div>
    </div>
</section>


<section id="button-disabled">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>14. Disabled, Fokus und Barrierefreiheit</h2>

            <p>
                Deaktivierte Buttons können entweder über das Attribut
                <code>disabled</code>, über <code>aria-disabled="true"</code>
                oder über die Klasse <code>.is-disabled</code> dargestellt werden.
            </p>

            <div class="btn-group btn-group--vertical btn-group--gap-sm">
                <button type="button" class="btn btn--primary btn--md" disabled>
                    Button mit disabled
                </button>

                <a href="#" class="btn btn--primary btn--md" aria-disabled="true">
                    Link mit aria-disabled
                </a>

                <a href="#" class="btn btn--md is-disabled">
                    Button mit .is-disabled
                </a>
            </div>

            <h3>Code</h3>

            <pre><code>&lt;button type="button" class="btn btn--primary btn--md" disabled&gt;
    Button mit disabled
&lt;/button&gt;

&lt;a href="#" class="btn btn--primary btn--md" aria-disabled="true"&gt;
    Link mit aria-disabled
&lt;/a&gt;

&lt;a href="#" class="btn btn--md is-disabled"&gt;
    Button mit .is-disabled
&lt;/a&gt;</code></pre>

            <h3>Fokus</h3>

            <p>
                Die Klasse <code>.btn</code> besitzt einen sichtbaren
                Fokuszustand über <code>:focus-visible</code>. Dieser Zustand
                sollte erhalten bleiben, damit Tastaturnutzer erkennen können,
                welcher Button gerade fokussiert ist.
            </p>

            <h3>Barrierefreie Regeln</h3>

            <p>
                Verwende für echte Aktionen ein <code>&lt;button&gt;</code>.
            </p>

            <p>
                Verwende für Navigation ein <code>&lt;a href=""&gt;</code>.
            </p>

            <p>
                Icon-only Buttons brauchen immer ein <code>aria-label</code>.
            </p>

            <p>
                Kritische Aktionen sollten nicht nur farblich markiert werden,
                sondern auch eindeutig beschriftet sein.
            </p>
        </div>
    </div>
</section>


<section id="buttons-entscheidungshilfe" class="section--surface">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>15. Entscheidungshilfe</h2>

            <p>
                Nutze diese Übersicht, um schnell die passende Button-Struktur
                auszuwählen.
            </p>

            <h3>Normale Hauptaktion</h3>

            <pre><code>&lt;a href="#" class="btn btn--primary btn--md"&gt;
    Mehr erfahren
&lt;/a&gt;</code></pre>

            <p>
                Für die wichtigste Aktion in einem Abschnitt.
            </p>

            <h3>Nebenaktion neben Hauptaktion</h3>

            <pre><code>&lt;a href="#" class="btn btn--primary-light btn--md"&gt;
    Weitere Informationen
&lt;/a&gt;</code></pre>

            <p>
                Für sichtbare, aber weniger dominante Aktionen.
            </p>

            <h3>Sehr dezente Nebenaktion</h3>

            <pre><code>&lt;a href="#" class="btn btn--primary-transp btn--md"&gt;
    Abbrechen
&lt;/a&gt;</code></pre>

            <p>
                Für Links oder Aktionen, die nicht im Vordergrund stehen sollen.
            </p>

            <h3>Starke CTA-Farbe</h3>

            <pre><code>&lt;a href="#" class="btn btn--secondary-cta btn--md"&gt;
    Jetzt anmelden
&lt;/a&gt;</code></pre>

            <p>
                Für Anmeldung, Kontakt, Portalzugang oder zentrale Handlungen.
            </p>

            <h3>Hinweis oder Hervorhebung</h3>

            <pre><code>&lt;a href="#" class="btn btn--secondary-highlight btn--md"&gt;
    Termin ansehen
&lt;/a&gt;</code></pre>

            <p>
                Für Termine, Key Facts oder hervorgehobene Inhalte.
            </p>

            <h3>Button mit Icon</h3>

            <pre><code>&lt;a href="#" class="btn btn-icon btn--primary btn--md"&gt;
    &lt;svg aria-hidden="true"&gt;...&lt;/svg&gt;
    Weiter
&lt;/a&gt;</code></pre>

            <p>
                Für Aktionen, die durch ein Symbol schneller erfassbar werden.
            </p>

            <h3>Icon-only Button</h3>

            <pre><code>&lt;button
    type="button"
    class="btn btn-icon btn--icon-only btn--primary btn--md"
    aria-label="Schließen"
&gt;
    &lt;svg aria-hidden="true"&gt;...&lt;/svg&gt;
&lt;/button&gt;</code></pre>

            <p>
                Für kompakte Interface-Aktionen.
            </p>

            <h3>Vertikale Button-Gruppe</h3>

            <pre><code>&lt;div class="btn-group btn-group--vertical btn-group--gap-sm"&gt;
    &lt;a href="#" class="btn btn--primary btn--md"&gt;Hauptaktion&lt;/a&gt;
    &lt;a href="#" class="btn btn--primary-light btn--md"&gt;Nebenaktion&lt;/a&gt;
&lt;/div&gt;</code></pre>

            <p>
                Für Container, Servicebereiche oder mobile Darstellungen.
            </p>

            <h3>Buttons auf gleiche Breite</h3>

            <pre><code>&lt;div class="btn-group btn-group--vertical btn-group--sec-stretch btn-group--gap-sm"&gt;
    &lt;a href="#" class="btn btn--primary btn--md"&gt;Aktion 1&lt;/a&gt;
    &lt;a href="#" class="btn btn--primary-light btn--md"&gt;Aktion 2&lt;/a&gt;
&lt;/div&gt;</code></pre>

            <p>
                Für klare, ruhige Button-Spalten.
            </p>
        </div>

        <div class="container__buttons btn-group btn-group--vertical btn-group--gap-sm">
            <a href="#buttons-uebersicht" class="btn btn--primary btn--md">Zurück nach oben</a>
        </div>
    </div>
</section>