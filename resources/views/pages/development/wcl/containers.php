<?php declare(strict_types=1); ?>

<section id="container-uebersicht" class="section--surface">
    <div class="container container--text-only container--narrow container--center">
        <div class="container__text">
            <h1>Übersicht: containers.css</h1>

            <p>
                Diese Seite erklärt die wichtigsten Container-Strukturen,
                Erscheinungen und Klassenkombinationen für Inhaltsmodule.
            </p>

            <p>
                Grundregel:
                <code>layout.css</code> steuert die äußere Seite und die
                <code>section</code>-Flächen. <code>containers.css</code>
                steuert die innere Anordnung innerhalb einer Section.
            </p>
        </div>

        <div class="container__buttons btn-group btn-group--vertical btn-group--main-center btn-group--gap-sm">
            <a href="#inhaltsverzeichnis" class="btn btn--primary btn--md">Zum Inhaltsverzeichnis</a>
            <a href="#entscheidungshilfe" class="btn btn--primary-transp btn--md">Zur Entscheidungshilfe</a>
        </div>
    </div>
</section>


<section id="inhaltsverzeichnis">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>Inhaltsverzeichnis</h2>

            <p>
                <a href="#grundstruktur">1. Grundstruktur eines Containers</a><br>
                <a href="#slots">2. Slots: Text, Buttons und Media</a><br>
                <a href="#section-container">3. Zusammenspiel von Section und Container</a><br>
                <a href="#text-only">4. Text-only Container</a><br>
                <a href="#text-buttons">5. Text mit Buttons</a><br>
                <a href="#text-media">6. Text links, Bild rechts</a><br>
                <a href="#media-text">7. Bild links, Text rechts</a><br>
                <a href="#gewichtungen">8. Spaltengewichtungen</a><br>
                <a href="#media-optionen">9. Media-Optionen</a><br>
                <a href="#captions">10. Bilder mit Caption und Bildquelle</a><br>
                <a href="#ausrichtung">11. Ausrichtung und Breite</a><br>
                <a href="#mobile">12. Mobile Reihenfolge</a><br>
                <a href="#entscheidungshilfe">13. Entscheidungshilfe</a>
            </p>
        </div>
    </div>
</section>


<section id="grundstruktur" class="section--surface">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>1. Grundstruktur eines Containers</h2>

            <p>
                Jeder Container folgt demselben Aufbau:
                Außen steht eine <code>section</code>. Darin liegt ein
                <code>div.container</code>. Innerhalb des Containers folgen die
                direkten Slots.
            </p>

            <p>
                Wichtig ist: Die Slots müssen direkte Kinder von
                <code>.container</code> sein. Nur dann kann das Grid aus
                <code>containers.css</code> Text, Buttons und Media korrekt
                positionieren.
            </p>

            <h3>Grundmuster</h3>

            <pre><code>&lt;section&gt;
    &lt;div class="container container--text-media container--balanced"&gt;
        &lt;div class="container__text"&gt;
            &lt;h2&gt;Überschrift&lt;/h2&gt;
            &lt;p&gt;Beschreibungstext.&lt;/p&gt;
        &lt;/div&gt;

        &lt;div class="container__buttons btn-group btn-group--vertical"&gt;
            &lt;a href="#" class="btn btn--primary btn--md"&gt;
                Button
            &lt;/a&gt;
        &lt;/div&gt;

        &lt;figure class="container__media container__media--cover container__media--ratio-16-9 media"&gt;
            &lt;img src="bild.jpg" alt=""&gt;
        &lt;/figure&gt;
    &lt;/div&gt;
&lt;/section&gt;</code></pre>

            <h3>Klassen im Beispiel</h3>

            <p>
                <code>.container</code><br>
                Aktiviert das Container-Grid.
            </p>

            <p>
                <code>.container--text-media</code><br>
                Ordnet Text und Buttons links an, Media rechts.
            </p>

            <p>
                <code>.container--balanced</code><br>
                Gibt Text und Media ungefähr gleich viel Platz.
            </p>

            <p>
                <code>.container__text</code><br>
                Enthält Überschrift, Fließtext, Listen oder Hinweise.
            </p>

            <p>
                <code>.container__buttons</code><br>
                Enthält eine Button-Gruppe.
            </p>

            <p>
                <code>.container__media</code><br>
                Enthält Bild, Video, SVG oder einen Media-Frame.
            </p>
        </div>
    </div>
</section>


<section id="slots">
    <div class="container container--text-media container--balanced">
        <div class="container__text">
            <h2>2. Slots: Text, Buttons und Media</h2>

            <p>
                Ein Container besteht aus drei möglichen Inhaltsbereichen.
                Diese heißen hier Slots.
            </p>

            <h3>Text-Slot</h3>

            <p>
                Der Text-Slot ist für Überschriften, Absätze, Listen und
                erklärende Inhalte gedacht.
            </p>

            <pre><code>&lt;div class="container__text"&gt;
    &lt;h2&gt;Titel&lt;/h2&gt;
    &lt;p&gt;Beschreibungstext.&lt;/p&gt;
&lt;/div&gt;</code></pre>

            <h3>Button-Slot</h3>

            <p>
                Der Button-Slot enthält keine eigentliche Button-Gestaltung.
                Diese kommt aus <code>buttons.css</code>. Der Container legt nur
                fest, wo die Button-Gruppe steht.
            </p>

            <pre><code>&lt;div class="container__buttons btn-group btn-group--vertical btn-group--gap-sm"&gt;
    &lt;a href="#" class="btn btn--primary btn--md"&gt;
        Primärer Button
    &lt;/a&gt;

    &lt;a href="#" class="btn btn--primary-transp btn--md"&gt;
        Sekundärer Button
    &lt;/a&gt;
&lt;/div&gt;</code></pre>
        </div>

        <div class="container__buttons btn-group btn-group--vertical btn-group--gap-sm">
            <a href="#media-optionen" class="btn btn--primary btn--md">Media-Slot ansehen</a>
            <a href="#text-buttons" class="btn btn--primary-light btn--md">Button-Beispiele ansehen</a>
        </div>

        <figure class="container__media container__media--contain container__media--ratio-4-3 media">
            <svg viewBox="0 0 800 600" role="img" aria-label="Schema eines Containers">
                <rect width="800" height="600" fill="var(--color-surface-bg, #eeeeee)" />
                <rect x="90" y="100" width="260" height="130" rx="24" fill="var(--color-primary, #2FBF71)" />
                <rect x="90" y="270" width="260" height="80" rx="20" fill="var(--color-accent, #22B7A3)" />
                <rect x="430" y="100" width="280" height="250" rx="28" fill="var(--color-accent, #F59E0B)" />
                <path d="M120 450 H680" stroke="var(--color-text, #0F172A)" stroke-width="18" stroke-linecap="round" />
            </svg>
        </figure>
    </div>
</section>


<section id="section-container" class="section--surface">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>3. Zusammenspiel von Section und Container</h2>

            <p>
                Die <code>section</code> entscheidet, wie der Bereich auf der
                Seite liegt. Der Container entscheidet, wie der Inhalt innerhalb
                dieser Fläche aussieht.
            </p>

            <h3>Normale Section</h3>

            <p>
                Eine normale Section hat keine eigene Hintergrundfläche.
                Sie eignet sich für Standardinhalte.
            </p>

            <pre><code>&lt;section&gt;
    &lt;div class="container container--text-only"&gt;
        ...
    &lt;/div&gt;
&lt;/section&gt;</code></pre>

            <h3>Surface Section</h3>

            <p>
                Eine <code>.section--surface</code> hebt den ganzen Bereich
                optisch hervor. Das eignet sich für Einstiege, Call-to-Actions,
                Zwischenbereiche oder Abschlussblöcke.
            </p>

            <pre><code>&lt;section class="section--surface"&gt;
    &lt;div class="container container--text-only container--narrow container--center"&gt;
        ...
    &lt;/div&gt;
&lt;/section&gt;</code></pre>

            <h3>Breitere Section</h3>

            <p>
                Wenn eine Section breiter wirken soll, kannst du Klassen aus
                <code>layout.css</code> an der Section verwenden, zum Beispiel
                <code>.long-width</code> oder <code>.full-width</code>.
            </p>

            <pre><code>&lt;section class="section--surface long-width"&gt;
    &lt;div class="container container--text-media container--media-heavy"&gt;
        ...
    &lt;/div&gt;
&lt;/section&gt;</code></pre>
        </div>
    </div>
</section>


<section id="text-only">
    <div class="container container--text-only container--narrow container--center">
        <div class="container__text">
            <h2>4. Text-only Container</h2>

            <p>
                Der Text-only Container ist die ruhigste Erscheinung. Er eignet
                sich für Einleitungen, Leitbilder, erklärende Zwischenbereiche
                und schmale Informationsabschnitte.
            </p>

            <h3>Struktur</h3>

            <pre><code>&lt;section&gt;
    &lt;div class="container container--text-only container--narrow container--center"&gt;
        &lt;div class="container__text"&gt;
            &lt;h2&gt;Ein ruhiger Einstieg&lt;/h2&gt;
            &lt;p&gt;Kurzer erklärender Text.&lt;/p&gt;
        &lt;/div&gt;
    &lt;/div&gt;
&lt;/section&gt;</code></pre>

            <h3>Typische Klassen</h3>

            <p>
                <code>.container--text-only</code><br>
                Entfernt den Media-Slot und nutzt eine einfache Textstruktur.
            </p>

            <p>
                <code>.container--narrow</code><br>
                Begrenzt die Textbreite und verbessert die Lesbarkeit.
            </p>

            <p>
                <code>.container--center</code><br>
                Zentriert Inhalt und Text. Gut für Start- und Abschlussbereiche.
            </p>
        </div>

        <div class="container__buttons btn-group btn-group--vertical btn-group--main-center btn-group--gap-sm">
            <a href="#" class="btn btn--primary btn--md">Optionaler Button</a>
        </div>
    </div>
</section>


<section id="text-buttons" class="section--surface">
    <div class="container container--text-buttons">
        <div class="container__text">
            <h2>5. Text mit Buttons</h2>

            <p>
                Diese Erscheinung eignet sich für klare Handlungsbereiche.
                Links steht die Erklärung. Rechts stehen die Aktionen.
            </p>

            <p>
                Gut geeignet für:
                Kontaktbereiche, Downloads, Anmeldungen, Hinweise,
                Servicebereiche und Einstiege in Unterseiten.
            </p>

            <h3>Struktur</h3>

            <pre><code>&lt;section class="section--surface"&gt;
    &lt;div class="container container--text-buttons"&gt;
        &lt;div class="container__text"&gt;
            &lt;h2&gt;Kontakt aufnehmen&lt;/h2&gt;
            &lt;p&gt;Kurzer erklärender Text.&lt;/p&gt;
        &lt;/div&gt;

        &lt;div class="container__buttons btn-group btn-group--vertical btn-group--sec-stretch btn-group--gap-sm"&gt;
            &lt;a href="#" class="btn btn--primary btn--md"&gt;
                Primäre Aktion
            &lt;/a&gt;

            &lt;a href="#" class="btn btn--primary-transp btn--md"&gt;
                Sekundäre Aktion
            &lt;/a&gt;
        &lt;/div&gt;
    &lt;/div&gt;
&lt;/section&gt;</code></pre>

            <h3>Wirkung</h3>

            <p>
                Die Darstellung ist funktional und stark auf Entscheidung
                ausgerichtet. Sie ist weniger erzählerisch als ein Text-Bild-Modul.
            </p>
        </div>

        <div class="container__buttons btn-group btn-group--vertical btn-group--sec-stretch btn-group--gap-sm">
            <a href="#" class="btn btn--primary btn--md">Primäre Aktion</a>
            <a href="#" class="btn btn--primary-transp btn--md">Sekundäre Aktion</a>
        </div>
    </div>
</section>


<section id="text-media">
    <div class="container container--text-media container--balanced">
        <div class="container__text">
            <h2>6. Text links, Bild rechts</h2>

            <p>
                Dies ist das klassische Inhaltsmodul. Der Text erklärt den Inhalt,
                das Bild unterstützt ihn visuell.
            </p>

            <p>
                Gut geeignet für:
                Startseitenmodule, Themenübersichten, Projektvorstellungen,
                Servicebereiche und erklärende Abschnitte.
            </p>

            <h3>Struktur</h3>

            <pre><code>&lt;section&gt;
    &lt;div class="container container--text-media container--balanced"&gt;
        &lt;div class="container__text"&gt;
            &lt;h2&gt;Thema&lt;/h2&gt;
            &lt;p&gt;Beschreibungstext.&lt;/p&gt;
        &lt;/div&gt;

        &lt;div class="container__buttons btn-group btn-group--vertical btn-group--gap-sm"&gt;
            &lt;a href="#" class="btn btn--primary btn--md"&gt;
                Mehr erfahren
            &lt;/a&gt;
        &lt;/div&gt;

        &lt;figure class="container__media container__media--cover container__media--ratio-16-9 media"&gt;
            &lt;img src="bild.jpg" alt=""&gt;
        &lt;/figure&gt;
    &lt;/div&gt;
&lt;/section&gt;</code></pre>

            <h3>Klassen</h3>

            <p>
                <code>.container--text-media</code><br>
                Text und Buttons links, Media rechts.
            </p>

            <p>
                <code>.container--balanced</code><br>
                Text und Media erhalten ungefähr gleich viel Raum.
            </p>

            <p>
                <code>.container__media--ratio-16-9</code><br>
                Das Bild bekommt ein ruhiges Querformat.
            </p>
        </div>

        <div class="container__buttons btn-group btn-group--vertical btn-group--gap-sm">
            <a href="#" class="btn btn--primary btn--md">Mehr erfahren</a>
            <a href="#" class="btn btn--primary-light btn--md">Zweite Aktion</a>
        </div>

        <figure class="container__media container__media--cover container__media--ratio-16-9 media">
            <svg viewBox="0 0 800 450" role="img" aria-label="Text links Bild rechts">
                <rect width="800" height="450" fill="var(--color-surface-bg, #eeeeee)" />
                <circle cx="230" cy="225" r="90" fill="var(--color-primary, #2FBF71)" />
                <rect x="390" y="135" width="260" height="180" rx="28" fill="var(--color-accent, #22B7A3)" />
                <path d="M120 350 C260 270, 360 390, 520 300 C620 245, 690 290, 750 235"
                      fill="none"
                      stroke="var(--color-text, #0F172A)"
                      stroke-width="16"
                      stroke-linecap="round" />
            </svg>
        </figure>
    </div>
</section>


<section id="media-text" class="section--surface">
    <div class="container container--media-text container--balanced">
        <figure class="container__media container__media--cover container__media--ratio-4-3 media">
            <svg viewBox="0 0 800 600" role="img" aria-label="Bild links Text rechts">
                <rect width="800" height="600" fill="var(--color-surface-bg, #eeeeee)" />
                <rect x="100" y="120" width="250" height="330" rx="32" fill="var(--color-primary, #2FBF71)" />
                <circle cx="540" cy="260" r="120" fill="var(--color-accent, #F59E0B)" />
                <path d="M130 500 L300 350 L440 430 L650 240 L760 360"
                      fill="none"
                      stroke="var(--color-text, #0F172A)"
                      stroke-width="20"
                      stroke-linecap="round"
                      stroke-linejoin="round" />
            </svg>
        </figure>

        <div class="container__text">
            <h2>7. Bild links, Text rechts</h2>

            <p>
                Diese Variante ist stärker bildgeführt. Das Bild bildet den
                Einstieg, der Text ordnet danach ein.
            </p>

            <p>
                Gut geeignet für:
                Projektseiten, Reportagen, visuelle Teaser, Themen mit starkem
                Bildmaterial oder wechselnde Startseitenmodule.
            </p>

            <h3>Struktur</h3>

            <pre><code>&lt;section class="section--surface"&gt;
    &lt;div class="container container--media-text container--balanced"&gt;
        &lt;figure class="container__media container__media--cover container__media--ratio-4-3 media"&gt;
            &lt;img src="bild.jpg" alt=""&gt;
        &lt;/figure&gt;

        &lt;div class="container__text"&gt;
            &lt;h2&gt;Thema&lt;/h2&gt;
            &lt;p&gt;Beschreibungstext.&lt;/p&gt;
        &lt;/div&gt;

        &lt;div class="container__buttons btn-group btn-group--vertical"&gt;
            &lt;a href="#" class="btn btn--primary btn--md"&gt;
                Button
            &lt;/a&gt;
        &lt;/div&gt;
    &lt;/div&gt;
&lt;/section&gt;</code></pre>

            <h3>Klassen</h3>

            <p>
                <code>.container--media-text</code><br>
                Media links, Text und Buttons rechts.
            </p>

            <p>
                <code>.container__media--cover</code><br>
                Das Bild füllt die Fläche.
            </p>
        </div>

        <div class="container__buttons btn-group btn-group--vertical btn-group--gap-sm">
            <a href="#" class="btn btn--primary btn--md">Beispiel öffnen</a>
        </div>
    </div>
</section>


<section id="gewichtungen">
    <div class="container container--text-only container--narrow container--center">
        <div class="container__text">
            <h2>8. Spaltengewichtungen</h2>

            <p>
                Mit Gewichtungsklassen steuerst du, ob Text oder Media stärker
                wirken soll. Diese Klassen werden zusätzlich zu
                <code>.container--text-media</code> oder
                <code>.container--media-text</code> verwendet.
            </p>
        </div>
    </div>
</section>


<section>
    <div class="container container--text-media container--media-dominant">
        <div class="container__text">
            <h3>8.1 Media dominant</h3>

            <p>
                Das Bild bekommt etwas mehr Platz als der Text.
                Diese Variante ist gut, wenn das Bild wichtig ist, der Text aber
                weiterhin stark erklären soll.
            </p>

            <p>
                Klassen:
                <code>.container--text-media</code>,
                <code>.container--media-dominant</code>
            </p>

            <pre><code>&lt;div class="container container--text-media container--media-dominant"&gt;
    ...
&lt;/div&gt;</code></pre>
        </div>

        <div class="container__buttons btn-group btn-group--vertical btn-group--gap-sm">
            <a href="#" class="btn btn--primary btn--md">Bilddominant</a>
        </div>

        <figure class="container__media container__media--cover container__media--ratio-16-9 media">
            <svg viewBox="0 0 800 450" role="img" aria-label="Media dominant">
                <rect width="800" height="450" fill="var(--color-surface-bg, #eeeeee)" />
                <rect x="80" y="90" width="270" height="250" rx="30" fill="var(--color-primary, #2FBF71)" />
                <circle cx="560" cy="220" r="135" fill="var(--color-accent, #22B7A3)" />
            </svg>
        </figure>
    </div>
</section>


<section class="section--surface">
    <div class="container container--text-media container--media-heavy">
        <div class="container__text">
            <h3>8.2 Media heavy</h3>

            <p>
                Das Bild nimmt deutlich mehr Raum ein. Diese Erscheinung wirkt
                wie ein kleiner Hero-Bereich innerhalb der Seite.
            </p>

            <p>
                Gut geeignet für:
                Kampagnen, starke Teaser, visuelle Einstiege und emotionale
                Projektvorstellungen.
            </p>

            <p>
                Klassen:
                <code>.container--text-media</code>,
                <code>.container--media-heavy</code>
            </p>

            <pre><code>&lt;div class="container container--text-media container--media-heavy"&gt;
    ...
&lt;/div&gt;</code></pre>
        </div>

        <div class="container__buttons btn-group btn-group--vertical btn-group--gap-sm">
            <a href="#" class="btn btn--primary btn--md">Starker Bildbereich</a>
        </div>

        <figure class="container__media container__media--cover container__media--ratio-21-9 media">
            <svg viewBox="0 0 1050 450" role="img" aria-label="Media heavy">
                <rect width="1050" height="450" fill="var(--color-surface-bg, #eeeeee)" />
                <circle cx="245" cy="220" r="125" fill="var(--color-primary, #2FBF71)" />
                <rect x="455" y="105" width="400" height="230" rx="42" fill="var(--color-accent, #F59E0B)" />
                <path d="M100 365 C260 250, 390 405, 560 290 C690 200, 800 330, 980 185"
                      fill="none"
                      stroke="var(--color-text, #0F172A)"
                      stroke-width="20"
                      stroke-linecap="round" />
            </svg>
        </figure>
    </div>
</section>


<section>
    <div class="container container--text-media container--text-dominant">
        <div class="container__text">
            <h3>8.3 Text dominant</h3>

            <p>
                Der Text bekommt etwas mehr Platz als das Bild.
                Diese Variante eignet sich für erklärende Inhalte mit
                unterstützender Illustration.
            </p>

            <p>
                Klassen:
                <code>.container--text-media</code>,
                <code>.container--text-dominant</code>
            </p>

            <pre><code>&lt;div class="container container--text-media container--text-dominant"&gt;
    ...
&lt;/div&gt;</code></pre>
        </div>

        <div class="container__buttons btn-group btn-group--vertical btn-group--gap-sm">
            <a href="#" class="btn btn--primary btn--md">Textdominant</a>
        </div>

        <figure class="container__media container__media--contain container__media--ratio-1-1 media">
            <svg viewBox="0 0 600 600" role="img" aria-label="Text dominant">
                <rect width="600" height="600" fill="var(--color-surface-bg, #eeeeee)" />
                <circle cx="300" cy="300" r="155" fill="var(--color-primary, #2FBF71)" />
                <path d="M205 315 L275 385 L410 220"
                      fill="none"
                      stroke="var(--color-text, #0F172A)"
                      stroke-width="32"
                      stroke-linecap="round"
                      stroke-linejoin="round" />
            </svg>
        </figure>
    </div>
</section>


<section class="section--surface">
    <div class="container container--text-media container--text-heavy">
        <div class="container__text">
            <h3>8.4 Text heavy</h3>

            <p>
                Der Text steht klar im Vordergrund. Das Bild ist nur noch
                Ergänzung.
            </p>

            <p>
                Gut geeignet für:
                Dokumentationen, längere Erklärtexte, redaktionelle Seiten und
                sachliche Informationsbereiche.
            </p>

            <p>
                Klassen:
                <code>.container--text-media</code>,
                <code>.container--text-heavy</code>
            </p>

            <pre><code>&lt;div class="container container--text-media container--text-heavy"&gt;
    ...
&lt;/div&gt;</code></pre>
        </div>

        <div class="container__buttons btn-group btn-group--vertical btn-group--gap-sm">
            <a href="#" class="btn btn--primary btn--md">Textstark</a>
        </div>

        <figure class="container__media container__media--contain container__media--ratio-4-3 media">
            <svg viewBox="0 0 800 600" role="img" aria-label="Text heavy">
                <rect width="800" height="600" fill="var(--color-surface-bg, #eeeeee)" />
                <rect x="250" y="130" width="300" height="340" rx="28" fill="var(--color-primary, #2FBF71)" />
                <path d="M310 240 H490 M310 310 H490 M310 380 H430"
                      stroke="var(--color-text, #0F172A)"
                      stroke-width="22"
                      stroke-linecap="round" />
            </svg>
        </figure>
    </div>
</section>


<section id="media-optionen">
    <div class="container container--text-only container--narrow container--center">
        <div class="container__text">
            <h2>9. Media-Optionen</h2>

            <p>
                Media-Optionen steuern, wie Bilder, Illustrationen, SVGs oder
                Videos im Media-Slot erscheinen.
            </p>
        </div>
    </div>
</section>


<section>
    <div class="container container--media-text container--balanced">
        <figure class="container__media container__media--cover container__media--ratio-16-9 media">
            <svg viewBox="0 0 800 450" role="img" aria-label="Cover Beispiel">
                <rect width="800" height="450" fill="var(--color-surface-bg, #eeeeee)" />
                <circle cx="240" cy="225" r="180" fill="var(--color-primary, #2FBF71)" />
                <circle cx="560" cy="225" r="180" fill="var(--color-accent, #22B7A3)" />
            </svg>
        </figure>

        <div class="container__text">
            <h3>9.1 Cover</h3>

            <p>
                <code>.container__media--cover</code> sorgt dafür, dass das
                Bild die Fläche vollständig füllt. Das Bild kann dabei
                angeschnitten werden.
            </p>

            <p>
                Verwende Cover für:
                Fotos, Teaserbilder, Kampagnenbilder und starke visuelle Flächen.
            </p>

            <pre><code>&lt;figure class="container__media container__media--cover container__media--ratio-16-9 media"&gt;
    &lt;img src="foto.jpg" alt=""&gt;
&lt;/figure&gt;</code></pre>
        </div>

        <div class="container__buttons btn-group btn-group--vertical btn-group--gap-sm">
            <a href="#" class="btn btn--primary btn--md">Cover nutzen</a>
        </div>
    </div>
</section>


<section class="section--surface">
    <div class="container container--media-text container--balanced">
        <figure class="container__media container__media--contain container__media--ratio-16-9 media">
            <svg viewBox="0 0 800 450" role="img" aria-label="Contain Beispiel">
                <rect width="800" height="450" fill="var(--color-surface-bg, #eeeeee)" />
                <circle cx="400" cy="225" r="120" fill="var(--color-primary, #2FBF71)" />
                <path d="M330 225 H470 M400 155 V295"
                      stroke="var(--color-text, #0F172A)"
                      stroke-width="24"
                      stroke-linecap="round" />
            </svg>
        </figure>

        <div class="container__text">
            <h3>9.2 Contain</h3>

            <p>
                <code>.container__media--contain</code> sorgt dafür, dass das
                gesamte Bild sichtbar bleibt. Es wird nicht angeschnitten.
            </p>

            <p>
                Verwende Contain für:
                Logos, Icons, Illustrationen, UI-Screenshots und Infografiken.
            </p>

            <pre><code>&lt;figure class="container__media container__media--contain container__media--ratio-16-9 media"&gt;
    &lt;img src="illustration.svg" alt=""&gt;
&lt;/figure&gt;</code></pre>
        </div>

        <div class="container__buttons btn-group btn-group--vertical btn-group--gap-sm">
            <a href="#" class="btn btn--primary btn--md">Contain nutzen</a>
        </div>
    </div>
</section>


<section>
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h3>9.3 Seitenverhältnisse</h3>

            <p>
                Seitenverhältnisse geben dem Media-Slot eine feste visuelle Form.
                Dadurch bleiben Bildmodule auf der Seite ruhig und vergleichbar.
            </p>

            <p>
                <code>.container__media--ratio-16-9</code><br>
                Klassisches Querformat. Gut für Fotos, Videos und Hero-nahe Module.
            </p>

            <p>
                <code>.container__media--ratio-4-3</code><br>
                Etwas höheres Querformat. Gut für dokumentarische Bilder.
            </p>

            <p>
                <code>.container__media--ratio-1-1</code><br>
                Quadrat. Gut für Icons, Logos, Porträts oder kompakte Illustrationen.
            </p>

            <p>
                <code>.container__media--ratio-21-9</code><br>
                Sehr breites Panorama. Gut für starke, breite Bildflächen.
            </p>

            <p>
                <code>.container__media--ratio-3-2</code><br>
                Ruhiges Fotoformat. Gut für redaktionelle Bildmodule.
            </p>

            <p>
                <code>.container__media--ratio-auto</code><br>
                Natürliche Höhe. Gut, wenn das Bild nicht beschnitten oder
                künstlich gerahmt werden soll.
            </p>
        </div>
    </div>
</section>


<section id="captions" class="section--surface">
    <div class="container container--text-media container--balanced">
        <div class="container__text">
            <h2>10. Bilder mit Caption und Bildquelle</h2>

            <p>
                Wenn ein Bild erklärt oder mit einer Quelle versehen werden soll,
                sollte das Seitenverhältnis nicht auf dem äußeren
                <code>figure</code> liegen. Stattdessen nutzt du einen
                inneren <code>.media__frame</code>.
            </p>

            <h3>Richtige Struktur</h3>

            <pre><code>&lt;figure class="container__media media"&gt;
    &lt;div class="media__frame media__frame--cover media__frame--ratio-16-9"&gt;
        &lt;img class="media__image" src="bild.jpg" alt=""&gt;
    &lt;/div&gt;

    &lt;figcaption class="media__caption"&gt;
        &lt;span class="media__caption-title"&gt;
            Bildbeschreibung
        &lt;/span&gt;

        &lt;span class="media__credit media__credit--below"&gt;
            Bildquelle: VDBS / Beispiel
        &lt;/span&gt;
    &lt;/figcaption&gt;
&lt;/figure&gt;</code></pre>

            <h3>Warum so?</h3>

            <p>
                Die Caption gehört nicht in das Seitenverhältnis des Bildes.
                Deshalb bekommt nur der innere Frame die Ratio-Klasse.
            </p>
        </div>

        <div class="container__buttons btn-group btn-group--vertical btn-group--gap-sm">
            <a href="#" class="btn btn--primary btn--md">Caption-Beispiel</a>
        </div>

        <figure class="container__media media">
            <div class="media__frame media__frame--cover media__frame--ratio-16-9">
                <svg viewBox="0 0 800 450" role="img" aria-label="Bild mit Caption">
                    <rect width="800" height="450" fill="var(--color-surface-bg, #eeeeee)" />
                    <rect x="130" y="100" width="220" height="250" rx="32" fill="var(--color-primary, #2FBF71)" />
                    <circle cx="560" cy="225" r="120" fill="var(--color-accent, #F59E0B)" />
                </svg>
            </div>

            <figcaption class="media__caption">
                <span class="media__caption-title">
                    Beispiel für ein dokumentarisches Bildmodul
                </span>

                <span class="media__credit media__credit--below">
                    Bildquelle: VDBS / Beispiel
                </span>
            </figcaption>
        </figure>
    </div>
</section>


<section>
    <div class="container container--text-media container--media-dominant">
        <div class="container__text">
            <h3>10.1 Bildquelle als Overlay</h3>

            <p>
                Wenn das Bild stark wirken soll, aber trotzdem eine Quelle
                sichtbar sein muss, kannst du ein Credit-Overlay verwenden.
            </p>

            <pre><code>&lt;figure class="container__media container__media--cover container__media--ratio-16-9 media media--credit-overlay"&gt;
    &lt;img class="media__image" src="bild.jpg" alt=""&gt;

    &lt;figcaption class="media__credit media__credit--overlay media__credit--bottom-right"&gt;
        &lt;span class="media__credit-label"&gt;Bildquelle:&lt;/span&gt;
        &lt;span class="media__credit-text"&gt;VDBS / Beispiel&lt;/span&gt;
    &lt;/figcaption&gt;
&lt;/figure&gt;</code></pre>

            <p>
                Diese Variante eignet sich besonders für Fotos und Teaserbilder.
            </p>
        </div>

        <div class="container__buttons btn-group btn-group--vertical btn-group--gap-sm">
            <a href="#" class="btn btn--primary btn--md">Overlay nutzen</a>
        </div>

        <figure class="container__media container__media--cover container__media--ratio-16-9 media media--credit-overlay">
            <svg viewBox="0 0 800 450" role="img" aria-label="Bild mit Credit Overlay">
                <rect width="800" height="450" fill="var(--color-surface-bg, #eeeeee)" />
                <circle cx="270" cy="225" r="135" fill="var(--color-primary, #2FBF71)" />
                <rect x="470" y="125" width="200" height="200" rx="36" fill="var(--color-accent, #22B7A3)" />
            </svg>

            <figcaption class="media__credit media__credit--overlay media__credit--bottom-right">
                <span class="media__credit-label">Bildquelle:</span>
                <span class="media__credit-text">VDBS / Beispiel</span>
            </figcaption>
        </figure>
    </div>
</section>


<section id="ausrichtung" class="section--surface">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>11. Ausrichtung und Breite</h2>

            <p>
                Mit Ausrichtungs- und Breitenklassen steuerst du, wie ruhig,
                kompakt oder präsent ein Container wirkt.
            </p>

            <h3>Breiten</h3>

            <p>
                <code>.container--narrow</code><br>
                Schmaler, zentrierter Container. Ideal für Lesetexte und
                Einleitungen.
            </p>

            <p>
                <code>.container--wide</code><br>
                Keine zusätzliche Begrenzung innerhalb der Section.
                Gut für breite Inhaltsmodule.
            </p>

            <h3>Ausrichtung</h3>

            <p>
                <code>.container--center</code><br>
                Zentriert Text, Buttons und Media. Gut für ruhige Einstiege.
            </p>

            <p>
                <code>.container--align-start</code><br>
                Richtet Inhalte oben aus. Gut bei unterschiedlich langen Spalten.
            </p>

            <p>
                <code>.container--align-center</code><br>
                Richtet Inhalte vertikal mittig aus. Gut für ausgewogene Teaser.
            </p>

            <p>
                <code>.container--align-end</code><br>
                Richtet Inhalte unten aus. Nur sparsam verwenden.
            </p>

            <h3>Beispiel</h3>

            <pre><code>&lt;div class="container container--text-media container--balanced container--align-start"&gt;
    ...
&lt;/div&gt;</code></pre>
        </div>
    </div>
</section>


<section id="mobile">
    <div class="container container--text-only container--narrow container--center">
        <div class="container__text">
            <h2>12. Mobile Reihenfolge</h2>

            <p>
                Auf mobilen Screens werden mehrspaltige Container automatisch
                einspaltig. Standard-Reihenfolge ist:
                Text, Media, Buttons.
            </p>
        </div>
    </div>
</section>


<section>
    <div class="container container--text-media container--balanced container--mobile-media-first">
        <div class="container__text">
            <h3>12.1 Mobil: Bild zuerst</h3>

            <p>
                Mit <code>.container--mobile-media-first</code> erscheint das
                Bild auf kleinen Screens vor dem Text.
            </p>

            <p>
                Sinnvoll, wenn das Bild mobil als Einstieg funktionieren soll.
            </p>

            <pre><code>&lt;div class="container container--text-media container--balanced container--mobile-media-first"&gt;
    ...
&lt;/div&gt;</code></pre>
        </div>

        <div class="container__buttons btn-group btn-group--vertical btn-group--gap-sm">
            <a href="#" class="btn btn--primary btn--md">Mobil testen</a>
        </div>

        <figure class="container__media container__media--cover container__media--ratio-16-9 media">
            <svg viewBox="0 0 800 450" role="img" aria-label="Mobil Bild zuerst">
                <rect width="800" height="450" fill="var(--color-surface-bg, #eeeeee)" />
                <circle cx="400" cy="225" r="135" fill="var(--color-primary, #2FBF71)" />
                <path d="M265 230 H535"
                      stroke="var(--color-text, #0F172A)"
                      stroke-width="24"
                      stroke-linecap="round" />
            </svg>
        </figure>
    </div>
</section>


<section class="section--surface">
    <div class="container container--text-media container--balanced container--mobile-buttons-before-media">
        <div class="container__text">
            <h3>12.2 Mobil: Buttons vor Bild</h3>

            <p>
                Mit <code>.container--mobile-buttons-before-media</code> stehen
                Buttons auf mobilen Screens vor dem Bild.
            </p>

            <p>
                Sinnvoll, wenn die Handlung wichtiger ist als die Illustration.
            </p>

            <pre><code>&lt;div class="container container--text-media container--balanced container--mobile-buttons-before-media"&gt;
    ...
&lt;/div&gt;</code></pre>
        </div>

        <div class="container__buttons btn-group btn-group--vertical btn-group--gap-sm">
            <a href="#" class="btn btn--primary btn--md">Wichtige Aktion</a>
            <a href="#" class="btn btn--primary-transp btn--md">Zweite Aktion</a>
        </div>

        <figure class="container__media container__media--contain container__media--ratio-4-3 media">
            <svg viewBox="0 0 800 600" role="img" aria-label="Mobil Buttons vor Bild">
                <rect width="800" height="600" fill="var(--color-surface-bg, #eeeeee)" />
                <rect x="190" y="150" width="420" height="300" rx="44" fill="var(--color-primary, #2FBF71)" />
                <circle cx="400" cy="300" r="70" fill="var(--color-text, #0F172A)" />
            </svg>
        </figure>
    </div>
</section>


<section id="entscheidungshilfe">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>13. Entscheidungshilfe</h2>

            <p>
                Nutze diese Übersicht, um schnell die passende Container-Struktur
                auszuwählen.
            </p>

            <h3>Ruhiger Einstieg</h3>

            <pre><code>&lt;div class="container container--text-only container--narrow container--center"&gt;
    ...
&lt;/div&gt;</code></pre>

            <p>
                Für Einleitungen, Leitbilder und Abschlussbereiche.
            </p>

            <h3>Sachlicher Informationstext</h3>

            <pre><code>&lt;div class="container container--text-only container--narrow"&gt;
    ...
&lt;/div&gt;</code></pre>

            <p>
                Für längere erklärende Texte.
            </p>

            <h3>Handlungsaufforderung</h3>

            <pre><code>&lt;div class="container container--text-buttons"&gt;
    ...
&lt;/div&gt;</code></pre>

            <p>
                Für Kontakt, Anmeldung, Download oder Serviceaktionen.
            </p>

            <h3>Klassisches Inhaltsmodul</h3>

            <pre><code>&lt;div class="container container--text-media container--balanced"&gt;
    ...
&lt;/div&gt;</code></pre>

            <p>
                Für normale Text-Bild-Bereiche.
            </p>

            <h3>Bildstarker Aufmacher</h3>

            <pre><code>&lt;div class="container container--text-media container--media-heavy"&gt;
    ...
&lt;/div&gt;</code></pre>

            <p>
                Für Kampagnen, Projekte und visuelle Einstiege.
            </p>

            <h3>Textstarker Erklärbereich</h3>

            <pre><code>&lt;div class="container container--text-media container--text-heavy"&gt;
    ...
&lt;/div&gt;</code></pre>

            <p>
                Für dokumentarische, sachliche oder stark erklärende Inhalte.
            </p>

            <h3>Foto als Fläche</h3>

            <pre><code>&lt;figure class="container__media container__media--cover container__media--ratio-16-9 media"&gt;
    ...
&lt;/figure&gt;</code></pre>

            <p>
                Für Fotos, die den Rahmen vollständig füllen sollen.
            </p>

            <h3>Illustration vollständig sichtbar</h3>

            <pre><code>&lt;figure class="container__media container__media--contain container__media--ratio-16-9 media"&gt;
    ...
&lt;/figure&gt;</code></pre>

            <p>
                Für Logos, Grafiken, Screenshots und Illustrationen.
            </p>
        </div>

        <div class="container__buttons btn-group btn-group--vertical btn-group--gap-sm">
            <a href="#container-uebersicht" class="btn btn--primary btn--md">Zurück nach oben</a>
        </div>
    </div>
</section>