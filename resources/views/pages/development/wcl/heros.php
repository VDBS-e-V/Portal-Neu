<?php declare(strict_types=1); ?>

<section id="heros-uebersicht" class="section--surface">
    <div class="container container--text-only container--narrow container--center">
        <div class="container__text">
            <h1>Übersicht: hero.css</h1>

            <p>
                Diese Seite zeigt die wichtigsten Hero-Strukturen, Bild-Heros,
                Split-Heros, redaktionelle Panel-Layouts, Hero-Cards und
                Schnellzugriff-Tiles aus <code>hero.css</code>.
            </p>

            <p>
                Grundregel:
                Jeder Hero beginnt mit <code>.hero</code>. Danach ergänzt du eine
                Hauptvariante wie <code>.hero--image</code>, <code>.hero--split</code>,
                <code>.hero--editorial</code> oder <code>.hero--tiles-only</code> sowie
                optionale Größen-, Farb- und Ausrichtungsklassen.
            </p>
        </div>

        <div class="container__buttons btn-group btn-group--vertical btn-group--main-center btn-group--gap-sm">
            <a href="/styleguide/heros/generator" class="btn btn--primary btn--md">
                Zum Hero Generator
            </a>

            <a href="#heros-inhaltsverzeichnis" class="btn btn--primary-light btn--md">
                Zum Inhaltsverzeichnis
            </a>

            <a href="#heros-entscheidungshilfe" class="btn btn--primary-transp btn--md">
                Zur Entscheidungshilfe
            </a>
        </div>
    </div>
</section>


<section id="heros-inhaltsverzeichnis">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>Inhaltsverzeichnis</h2>

            <p>
                <a href="/styleguide/heros/generator">Hero Generator öffnen</a><br>
                <a href="#hero-grundstruktur">1. Grundstruktur eines Heros</a><br>
                <a href="#hero-kopf-und-copy">2. Copy, Kicker, Lead und Aktionen</a><br>
                <a href="#hero-flaechenvarianten">3. Farb- und Flächenvarianten</a><br>
                <a href="#hero-groessen-ausrichtung">4. Größen und Ausrichtung</a><br>
                <a href="#hero-image">5. Bild-Hero mit echtem Bild</a><br>
                <a href="#hero-image-bg">6. Legacy-Hintergrundbild-Hero</a><br>
                <a href="#hero-split">7. Split-Hero</a><br>
                <a href="#hero-editorial-panel">8. Editorial-Hero mit Panel</a><br>
                <a href="#hero-cards">9. Hero-Cards</a><br>
                <a href="#hero-tiles">10. Schnellzugriff-Tiles</a><br>
                <a href="#hero-tiles-only">11. Kachel-Hero ohne Bühne</a><br>
                <a href="#hero-related-actions">12. Verwandte Aktionen</a><br>
                <a href="#hero-responsive">13. Responsive Verhalten</a><br>
                <a href="#heros-entscheidungshilfe">14. Entscheidungshilfe</a>
            </p>
        </div>
    </div>
</section>


<section id="hero-generator-hinweis" class="section--surface">
    <div class="container container--text-buttons">
        <div class="container__text">
            <h2>Hero Generator</h2>

            <p>
                Mit dem Hero Generator kannst du Hero-Typ, Bildlogik, Farben,
                Größe, Ausrichtung, Buttons, Panels, Cards und Schnellzugriffe
                visuell zusammenstellen. Der fertige HTML-Code kann direkt
                übernommen werden.
            </p>

            <p>
                Typische Kombination:
                <code>.hero</code>,
                <code>.hero--image</code>,
                <code>.hero--overlay-dark</code>,
                <code>.hero--center</code>
            </p>
        </div>

        <div class="container__buttons btn-group btn-group--vertical btn-group--sec-stretch btn-group--gap-sm">
            <a href="/styleguide/heros/generator" class="btn btn--primary btn--md">
                Hero Generator öffnen
            </a>

            <a href="#hero-grundstruktur" class="btn btn--primary-transp btn--md">
                Erst Grundlagen ansehen
            </a>
        </div>
    </div>
</section>


<section id="hero-grundstruktur" class="section--surface">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>1. Grundstruktur eines Heros</h2>

            <p>
                Die Basisklasse <code>.hero</code> bildet die äußere Fläche.
                Innerhalb davon liegt in der Regel <code>.hero__inner</code> als
                begrenzter Inhaltsbereich und <code>.hero__copy</code> als Textgruppe.
            </p>

            <h3>Minimaler Unterseiten-Hero</h3>

            <section class="section--surface">
                <div class="hero hero--minimal hero--compact">
                    <div class="hero__inner">
                        <div class="hero__copy">
                            <p class="hero__kicker">Bereich</p>
                            <h3 class="hero__title">Mein Konto</h3>
                            <p class="hero__lead">
                                Verwalten Sie persönliche Daten, Einstellungen und Zugänge an einem Ort.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <h3>Code</h3>

            <pre><code>&lt;section class="section--surface"&gt;
    &lt;div class="hero hero--minimal hero--compact"&gt;
        &lt;div class="hero__inner"&gt;
            &lt;div class="hero__copy"&gt;
                &lt;p class="hero__kicker"&gt;Bereich&lt;/p&gt;
                &lt;h1 class="hero__title"&gt;Mein Konto&lt;/h1&gt;
                &lt;p class="hero__lead"&gt;Verwalten Sie Ihre persönlichen Daten.&lt;/p&gt;
            &lt;/div&gt;
        &lt;/div&gt;
    &lt;/div&gt;
&lt;/section&gt;</code></pre>

            <h3>Klassenlogik</h3>

            <p>
                <code>.hero</code><br>
                Grundfläche mit Positionierung, Mindesthöhe, Farbe und Overflow-Regeln.
            </p>

            <p>
                <code>.hero__inner</code><br>
                Maximalbreite, Innenabstand und Grid-Kontext für Inhalt.
            </p>

            <p>
                <code>.hero__copy</code><br>
                Textgruppe für Kicker, Titel, Lead, Eyebrow und Aktionen.
            </p>
        </div>
    </div>
</section>


<section id="hero-kopf-und-copy">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>2. Copy, Kicker, Lead und Aktionen</h2>

            <p>
                Die Textelemente sind modular. Ein Hero kann nur aus Titel und
                Lead bestehen oder zusätzlich Kicker, Eyebrow, Badge und Buttons
                enthalten.
            </p>

            <div class="hero hero--surface hero--compact hero--left">
                <div class="hero__inner">
                    <div class="hero__copy">
                        <p class="hero__kicker">Serviceportal</p>
                        <p class="hero__eyebrow">Schneller Einstieg</p>
                        <h3 class="hero__title">Zugang, Konto und Hilfe</h3>
                        <p class="hero__lead">
                            <span class="hero__lead__badge">
                                Zentrale Funktionen und Informationen kompakt gebündelt.
                            </span>
                        </p>
                        <div class="hero__actions btn-group btn-group--horizontal btn-group--gap-sm">
                            <a href="#" class="btn btn--primary btn--md">Zugang öffnen</a>
                            <a href="#" class="btn btn--primary-transp btn--md">Hilfe ansehen</a>
                        </div>
                    </div>
                </div>
            </div>

            <h3>Code</h3>

            <pre><code>&lt;div class="hero hero--surface hero--compact hero--left"&gt;
    &lt;div class="hero__inner"&gt;
        &lt;div class="hero__copy"&gt;
            &lt;p class="hero__kicker"&gt;Serviceportal&lt;/p&gt;
            &lt;p class="hero__eyebrow"&gt;Schneller Einstieg&lt;/p&gt;
            &lt;h1 class="hero__title"&gt;Zugang, Konto und Hilfe&lt;/h1&gt;
            &lt;p class="hero__lead"&gt;
                &lt;span class="hero__lead__badge"&gt;Zentrale Funktionen kompakt gebündelt.&lt;/span&gt;
            &lt;/p&gt;
            &lt;div class="hero__actions btn-group btn-group--horizontal btn-group--gap-sm"&gt;
                &lt;a href="#" class="btn btn--primary btn--md"&gt;Zugang öffnen&lt;/a&gt;
                &lt;a href="#" class="btn btn--primary-transp btn--md"&gt;Hilfe ansehen&lt;/a&gt;
            &lt;/div&gt;
        &lt;/div&gt;
    &lt;/div&gt;
&lt;/div&gt;</code></pre>
        </div>
    </div>
</section>


<section id="hero-flaechenvarianten" class="section--surface">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>3. Farb- und Flächenvarianten</h2>

            <p>
                Flächenvarianten setzen Hintergrund- und Textfarben über Tokens.
                Sie lassen sich mit Größen- und Ausrichtungsklassen kombinieren.
            </p>

            <h3>Surface</h3>
            <div class="hero hero--surface hero--compact">
                <div class="hero__inner">
                    <div class="hero__copy">
                        <h3 class="hero__title">Surface Hero</h3>
                        <p class="hero__lead">Ruhige helle Fläche für Unterseiten oder modulare Einstiege.</p>
                    </div>
                </div>
            </div>

            <h3>Ink</h3>
            <div class="hero hero--ink hero--compact">
                <div class="hero__inner">
                    <div class="hero__copy">
                        <h3 class="hero__title">Ink Hero</h3>
                        <p class="hero__lead">Dunkle Fläche für starke Einstiege oder Kampagnenmodule.</p>
                    </div>
                </div>
            </div>

            <h3>Primary und Secondary</h3>
            <div class="hero hero--primary hero--compact">
                <div class="hero__inner">
                    <div class="hero__copy">
                        <h3 class="hero__title">Primary Hero</h3>
                        <p class="hero__lead">Farbige Fläche mit dunkler Schrift.</p>
                    </div>
                </div>
            </div>

            <h3>Code</h3>

            <pre><code>&lt;div class="hero hero--surface hero--compact"&gt;...&lt;/div&gt;
&lt;div class="hero hero--white hero--compact"&gt;...&lt;/div&gt;
&lt;div class="hero hero--ink hero--compact"&gt;...&lt;/div&gt;
&lt;div class="hero hero--primary hero--compact"&gt;...&lt;/div&gt;
&lt;div class="hero hero--secondary-cta hero--compact"&gt;...&lt;/div&gt;
&lt;div class="hero hero--secondary-highlight hero--compact"&gt;...&lt;/div&gt;</code></pre>
        </div>
    </div>
</section>


<section id="hero-groessen-ausrichtung">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>4. Größen und Ausrichtung</h2>

            <p>
                Größe und Ausrichtung werden unabhängig von der Hauptvariante gesetzt.
                Dadurch kannst du denselben Hero-Typ als kompakten Unterseitenkopf
                oder als große Startseitenfläche verwenden.
            </p>

            <h3>Größen</h3>

            <pre><code>&lt;div class="hero hero--compact"&gt;...&lt;/div&gt;
&lt;div class="hero hero--large"&gt;...&lt;/div&gt;
&lt;div class="hero hero--full"&gt;...&lt;/div&gt;
&lt;div class="hero hero--portal"&gt;...&lt;/div&gt;</code></pre>

            <h3>Horizontale Ausrichtung</h3>

            <pre><code>&lt;div class="hero hero--left"&gt;...&lt;/div&gt;
&lt;div class="hero hero--center"&gt;...&lt;/div&gt;
&lt;div class="hero hero--right"&gt;...&lt;/div&gt;</code></pre>

            <h3>Vertikale Ausrichtung</h3>

            <pre><code>&lt;div class="hero hero--align-start"&gt;...&lt;/div&gt;
&lt;div class="hero hero--align-center"&gt;...&lt;/div&gt;
&lt;div class="hero hero--align-end"&gt;...&lt;/div&gt;</code></pre>
        </div>
    </div>
</section>


<section id="hero-image" class="section--surface">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>5. Bild-Hero mit echtem Bild</h2>

            <p>
                <code>.hero--image</code> nutzt ein echtes Bildelement in
                <code>.hero__background</code>. Ohne Ratio-Klasse bestimmt das Bild
                selbst die Höhe. Mit Ratio-Klasse wird das Bild zugeschnitten.
            </p>

            <section class="long-width no-padding">
                <div class="hero hero--image hero--ratio-21-9 hero--overlay-dark hero--center">
                    <figure class="hero__background">
                        <img src="/assets/images/heros/portal-home.png" alt="">
                    </figure>

                    <div class="hero__inner">
                        <div class="hero__copy">
                            <p class="hero__kicker">Portal</p>
                            <h3 class="hero__title">Willkommen im Serviceportal</h3>
                            <p class="hero__lead">Der Text liegt über dem echten Bildbereich.</p>
                        </div>
                    </div>
                </div>
            </section>

            <h3>Code</h3>

            <pre><code>&lt;section class="long-width no-padding"&gt;
    &lt;div class="hero hero--image hero--ratio-21-9 hero--overlay-dark hero--center"&gt;
        &lt;figure class="hero__background"&gt;
            &lt;img src="/assets/images/heros/portal-home.png" alt=""&gt;
        &lt;/figure&gt;

        &lt;div class="hero__inner"&gt;
            &lt;div class="hero__copy"&gt;
                &lt;p class="hero__kicker"&gt;Portal&lt;/p&gt;
                &lt;h1 class="hero__title"&gt;Willkommen im Serviceportal&lt;/h1&gt;
                &lt;p class="hero__lead"&gt;Der Text liegt über dem echten Bildbereich.&lt;/p&gt;
            &lt;/div&gt;
        &lt;/div&gt;
    &lt;/div&gt;
&lt;/section&gt;</code></pre>

            <h3>Ratio-Optionen</h3>

            <p>
                <code>.hero--ratio-21-9</code>, <code>.hero--ratio-16-9</code>,
                <code>.hero--ratio-3-2</code>, <code>.hero--ratio-4-3</code>,
                <code>.hero--ratio-1-1</code>
            </p>
        </div>
    </div>
</section>


<section id="hero-image-bg">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>6. Legacy-Hintergrundbild-Hero</h2>

            <p>
                <code>.hero--image-bg</code> nutzt <code>--hero-bg-image</code> als
                CSS-Hintergrundbild. Diese Variante ist sinnvoll, wenn das Bild
                nicht selbst die Höhe bestimmen soll oder bestehende Templates
                kompatibel bleiben müssen.
            </p>

            <section class="long-width no-padding">
                <div
                    class="hero hero--image-bg hero--portal hero--center hero--overlay-dark"
                    style="--hero-bg-image: url('/assets/images/heros/portal-home.png')"
                    role="banner"
                    aria-label="Willkommen"
                >
                    <div class="hero__inner">
                        <div class="hero__copy">
                            <h3 class="hero__title">Portal-Hero mit Hintergrundbild</h3>
                            <p class="hero__lead">
                                <span class="hero__lead__badge">Schneller Einstieg mit fester Hero-Höhe.</span>
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <h3>Code</h3>

            <pre><code>&lt;div
    class="hero hero--image-bg hero--portal hero--center hero--overlay-dark"
    style="--hero-bg-image: url('/assets/images/heros/portal-home.png')"
    role="banner"
    aria-label="Willkommen"
&gt;
    ...
&lt;/div&gt;</code></pre>
        </div>
    </div>
</section>


<section id="hero-split" class="section--surface">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>7. Split-Hero</h2>

            <p>
                <code>.hero--split</code> teilt den Hero in Text und Medienbereich.
                Mit <code>.hero--media-left</code> kann das Bild auf Desktop links
                stehen. Auf kleineren Viewports wird das Layout einspaltig.
            </p>

            <section class="long-width no-padding">
                <div class="hero hero--split hero--surface">
                    <div class="hero__inner">
                        <div class="hero__copy">
                            <p class="hero__kicker">Serviceportal</p>
                            <h3 class="hero__title">Zugang, Konto und Hilfe an einem Ort</h3>
                            <p class="hero__lead">
                                Das Serviceportal bündelt zentrale Funktionen und Informationen.
                            </p>
                            <div class="hero__actions btn-group btn-group--horizontal btn-group--gap-sm">
                                <a href="#" class="btn btn--primary btn--md">Zugang öffnen</a>
                                <a href="#" class="btn btn--primary-transp btn--md">Kontakt</a>
                            </div>
                        </div>

                        <figure class="hero__media hero__media--ratio-4-3">
                            <img src="/assets/images/heros/portal.jpg" alt="Personen am Laptop">
                            <figcaption class="hero__credit">Bild: Beispiel</figcaption>
                        </figure>
                    </div>
                </div>
            </section>

            <h3>Code</h3>

            <pre><code>&lt;div class="hero hero--split hero--surface"&gt;
    &lt;div class="hero__inner"&gt;
        &lt;div class="hero__copy"&gt;
            ...
        &lt;/div&gt;

        &lt;figure class="hero__media hero__media--ratio-4-3"&gt;
            &lt;img src="/assets/images/heros/portal.jpg" alt="Personen am Laptop"&gt;
            &lt;figcaption class="hero__credit"&gt;Bild: Beispiel&lt;/figcaption&gt;
        &lt;/figure&gt;
    &lt;/div&gt;
&lt;/div&gt;</code></pre>
        </div>
    </div>
</section>


<section id="hero-editorial-panel">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>8. Editorial-Hero mit Panel</h2>

            <p>
                Der redaktionelle Hero besteht aus <code>.hero--editorial</code>,
                optionaler <code>.hero__heading</code>, einer Bühne
                <code>.hero__stage</code>, Bild <code>.hero__media</code> und einem
                Textpanel <code>.hero__panel</code>.
            </p>

            <section class="long-width no-padding">
                <div class="hero hero--editorial">
                    <div class="hero__heading">
                        <h3>Senatsverwaltung für Bildung, Jugend und Familie</h3>
                    </div>

                    <div class="hero__stage">
                        <figure class="hero__media">
                            <img src="/assets/images/heros/bildung.jpg" alt="Schülerinnen und Schüler im Gespräch">
                        </figure>

                        <aside class="hero__panel hero__panel--ink">
                            <h4 class="hero__panel-title">Gemeinsam für bessere Bildung</h4>
                            <p class="hero__panel-text">
                                Ein redaktioneller Einstieg mit starkem Bild und kompaktem Infopanel.
                            </p>
                            <p class="hero__panel-meta">Bild: Beispiel</p>
                            <a href="#" class="hero__panel-action">
                                <span>Weitere Informationen</span>
                                <svg class="vdb-icon" aria-hidden="true">
                                    <use href="/assets/icons/vdb-icons.svg#icon-arrow-right"></use>
                                </svg>
                            </a>
                        </aside>
                    </div>
                </div>
            </section>

            <h3>Code</h3>

            <pre><code>&lt;div class="hero hero--editorial"&gt;
    &lt;div class="hero__heading"&gt;
        &lt;h1&gt;Senatsverwaltung für Bildung, Jugend und Familie&lt;/h1&gt;
    &lt;/div&gt;

    &lt;div class="hero__stage"&gt;
        &lt;figure class="hero__media"&gt;
            &lt;img src="/assets/images/heros/bildung.jpg" alt="..."&gt;
        &lt;/figure&gt;

        &lt;aside class="hero__panel hero__panel--ink"&gt;
            &lt;h2 class="hero__panel-title"&gt;Gemeinsam für bessere Bildung&lt;/h2&gt;
            &lt;p class="hero__panel-text"&gt;...&lt;/p&gt;
            &lt;a href="#" class="hero__panel-action"&gt;...&lt;/a&gt;
        &lt;/aside&gt;
    &lt;/div&gt;
&lt;/div&gt;</code></pre>

            <h3>Panel-Varianten</h3>

            <p>
                <code>.hero__panel--ink</code>, <code>.hero__panel--primary</code>,
                <code>.hero__panel--secondary-cta</code>
            </p>
        </div>
    </div>
</section>


<section id="hero-cards" class="section--surface">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>9. Hero-Cards</h2>

            <p>
                <code>.hero__cards</code> ergänzt einen Hero um Kacheln. Mit
                <code>.hero__cards--2</code>, <code>.hero__cards--3</code> oder
                <code>.hero__cards--4</code> wird die Desktop-Spaltenzahl gesetzt.
            </p>

            <div class="hero__cards hero__cards--3">
                <a class="hero-card hero-card--primary" href="#">
                    <h3 class="hero-card__title">Anmelden</h3>
                    <p class="hero-card__text">Zugang mit bestehendem Konto.</p>
                    <span class="hero-card__action">
                        <span>Öffnen</span>
                        <svg class="vdb-icon" aria-hidden="true">
                            <use href="/assets/icons/vdb-icons.svg#icon-arrow-right"></use>
                        </svg>
                    </span>
                </a>

                <a class="hero-card hero-card--secondary-cta" href="#">
                    <h3 class="hero-card__title">Registrieren</h3>
                    <p class="hero-card__text">Neues Konto erstellen.</p>
                    <span class="hero-card__action">
                        <span>Starten</span>
                        <svg class="vdb-icon" aria-hidden="true">
                            <use href="/assets/icons/vdb-icons.svg#icon-arrow-right"></use>
                        </svg>
                    </span>
                </a>

                <a class="hero-card hero-card--white" href="#">
                    <h3 class="hero-card__title">Hilfe</h3>
                    <p class="hero-card__text">Antworten und Kontaktmöglichkeiten.</p>
                    <span class="hero-card__action">
                        <span>Ansehen</span>
                        <svg class="vdb-icon" aria-hidden="true">
                            <use href="/assets/icons/vdb-icons.svg#icon-arrow-right"></use>
                        </svg>
                    </span>
                </a>
            </div>

            <h3>Code</h3>

            <pre><code>&lt;div class="hero__cards hero__cards--3"&gt;
    &lt;a class="hero-card hero-card--primary" href="#"&gt;
        &lt;h3 class="hero-card__title"&gt;Anmelden&lt;/h3&gt;
        &lt;p class="hero-card__text"&gt;Zugang mit bestehendem Konto.&lt;/p&gt;
        &lt;span class="hero-card__action"&gt;...&lt;/span&gt;
    &lt;/a&gt;
&lt;/div&gt;</code></pre>

            <h3>Card-Varianten</h3>

            <p>
                <code>.hero-card--primary</code>, <code>.hero-card--secondary-cta</code>,
                <code>.hero-card--secondary-highlight</code>, <code>.hero-card--plum</code>,
                <code>.hero-card--white</code>
            </p>
        </div>
    </div>
</section>


<section id="hero-tiles">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>10. Schnellzugriff-Tiles</h2>

            <p>
                <code>.hero__tiles</code> ist eine eigene Schnellzugriff-Leiste unter
                einem Hero. Die Link-Styles werden bewusst neutralisiert, damit
                globale Linkregeln nicht in die Tiles hineinwirken.
            </p>

            <nav class="hero__tiles" aria-label="Portal Schnellzugriff">
                <a class="hero__tile hero__tile--a" href="#">Über das Portal</a>
                <a class="hero__tile hero__tile--b" href="#">Zugang zum Portal</a>
                <a class="hero__tile hero__tile--c" href="#">Mein Konto</a>
                <a class="hero__tile hero__tile--d" href="#">Hilfe</a>
            </nav>

            <h3>Code</h3>

            <pre><code>&lt;nav class="hero__tiles" aria-label="Portal Schnellzugriff"&gt;
    &lt;a class="hero__tile hero__tile--a" href="#"&gt;Über das Portal&lt;/a&gt;
    &lt;a class="hero__tile hero__tile--b" href="#"&gt;Zugang zum Portal&lt;/a&gt;
    &lt;a class="hero__tile hero__tile--c" href="#"&gt;Mein Konto&lt;/a&gt;
    &lt;a class="hero__tile hero__tile--d" href="#"&gt;Hilfe&lt;/a&gt;
&lt;/nav&gt;</code></pre>

            <h3>Tile-Farben</h3>

            <p>
                <code>.hero__tile--primary</code>, <code>.hero__tile--secondary-cta</code>,
                <code>.hero__tile--secondary-highlight</code>, <code>.hero__tile--amber</code>,
                <code>.hero__tile--teal</code>, <code>.hero__tile--berry</code>,
                <code>.hero__tile--sun</code>, <code>.hero__tile--lime</code>,
                <code>.hero__tile--ink</code>
            </p>
        </div>
    </div>
</section>


<section id="hero-tiles-only" class="section--surface">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>11. Kachel-Hero ohne Bühne</h2>

            <p>
                <code>.hero--tiles-only</code> eignet sich für Einstiegsbereiche, in
                denen die Kacheln selbst die Hauptnavigation bilden und kein großes
                Bild benötigt wird.
            </p>

            <section class="section--surface">
                <div class="hero hero--tiles-only hero--center">
                    <div class="hero__inner">
                        <div class="hero__copy">
                            <h3 class="hero__title">Was möchten Sie tun?</h3>
                            <p class="hero__lead">Wählen Sie einen direkten Einstieg.</p>
                        </div>
                    </div>

                    <div class="hero__cards hero__cards--4">
                        <a class="hero-card hero-card--primary" href="#">
                            <h4 class="hero-card__title">Anmelden</h4>
                            <p class="hero-card__text">Bestehendes Konto nutzen.</p>
                        </a>

                        <a class="hero-card hero-card--secondary-cta" href="#">
                            <h4 class="hero-card__title">Registrieren</h4>
                            <p class="hero-card__text">Neues Konto erstellen.</p>
                        </a>

                        <a class="hero-card hero-card--white" href="#">
                            <h4 class="hero-card__title">Hilfe</h4>
                            <p class="hero-card__text">Unterstützung finden.</p>
                        </a>

                        <a class="hero-card hero-card--secondary-highlight" href="#">
                            <h4 class="hero-card__title">Kontakt</h4>
                            <p class="hero-card__text">Direkt anfragen.</p>
                        </a>
                    </div>
                </div>
            </section>

            <h3>Code</h3>

            <pre><code>&lt;div class="hero hero--tiles-only hero--center"&gt;
    &lt;div class="hero__inner"&gt;
        &lt;div class="hero__copy"&gt;
            &lt;h1 class="hero__title"&gt;Was möchten Sie tun?&lt;/h1&gt;
        &lt;/div&gt;
    &lt;/div&gt;

    &lt;div class="hero__cards hero__cards--4"&gt;
        &lt;a class="hero-card hero-card--primary" href="#"&gt;...&lt;/a&gt;
    &lt;/div&gt;
&lt;/div&gt;</code></pre>
        </div>
    </div>
</section>


<section id="hero-related-actions">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>12. Verwandte Aktionen</h2>

            <p>
                <code>.hero-related-actions</code> ist als neutrale Utility gedacht,
                wenn nach dem Hero eine Button-Gruppe begrenzt und zentriert stehen
                soll. Sie ersetzt seitenbezogene Spezialregeln.
            </p>

            <div class="hero-related-actions">
                <div class="btn-group btn-group--vertical btn-group--main-center btn-group--gap-sm">
                    <a href="#" class="btn btn--primary btn--md">Portal öffnen</a>
                    <a href="#" class="btn btn--primary-transp btn--md">Weitere Hilfe</a>
                </div>
            </div>

            <h3>Code</h3>

            <pre><code>&lt;div class="hero-related-actions"&gt;
    &lt;div class="btn-group btn-group--vertical btn-group--main-center btn-group--gap-sm"&gt;
        &lt;a href="#" class="btn btn--primary btn--md"&gt;Portal öffnen&lt;/a&gt;
        &lt;a href="#" class="btn btn--primary-transp btn--md"&gt;Weitere Hilfe&lt;/a&gt;
    &lt;/div&gt;
&lt;/div&gt;</code></pre>
        </div>
    </div>
</section>


<section id="hero-responsive" class="section--surface">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>13. Responsive Verhalten</h2>

            <p>
                Split- und Editorial-Heros wechseln bei kleineren Viewports in ein
                einspaltiges Layout. Hero-Cards reduzieren die Spaltenzahl und
                Schnellzugriff-Tiles werden mobil untereinander dargestellt.
            </p>

            <h3>Wichtig für die Nutzung</h3>

            <p>
                Auf mobilen Viewports sollten Titel kurz bleiben. Bei Bild-Heros
                empfiehlt sich ein aussagekräftiges <code>alt</code>-Attribut für
                echte Bilder. Reine dekorative Hintergrundbilder können leeres
                <code>alt=""</code> oder die CSS-Background-Variante nutzen.
            </p>
        </div>
    </div>
</section>


<section id="heros-entscheidungshilfe">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>14. Entscheidungshilfe</h2>

            <h3>Welchen Hero nutzen?</h3>

            <p>
                <code>.hero--minimal</code><br>
                Für ruhige Unterseitenköpfe mit Titel und kurzem Lead.
            </p>

            <p>
                <code>.hero--image</code><br>
                Wenn ein echtes Bild im Markup stehen soll und die Bildhöhe oder
                eine Ratio die Hero-Höhe bestimmen darf.
            </p>

            <p>
                <code>.hero--image-bg</code><br>
                Für bestehende Portalseiten oder feste Hero-Höhen mit CSS-Hintergrundbild.
            </p>

            <p>
                <code>.hero--split</code><br>
                Wenn Text und Bild gleichwertig nebeneinander stehen sollen.
            </p>

            <p>
                <code>.hero--editorial</code><br>
                Für redaktionelle Einstiege mit großem Bild, Infopanel und optionalen Cards.
            </p>

            <p>
                <code>.hero--tiles-only</code><br>
                Für Startpunkte, bei denen mehrere Aktionen oder Bereiche direkt
                als Kacheln angeboten werden.
            </p>

            <h3>Empfohlene Kombinationen</h3>

            <pre><code>&lt;div class="hero hero--minimal hero--surface hero--compact"&gt;...&lt;/div&gt;
&lt;div class="hero hero--image hero--ratio-21-9 hero--overlay-dark hero--center"&gt;...&lt;/div&gt;
&lt;div class="hero hero--split hero--surface"&gt;...&lt;/div&gt;
&lt;div class="hero hero--editorial"&gt;...&lt;/div&gt;
&lt;div class="hero hero--tiles-only hero--center"&gt;...&lt;/div&gt;</code></pre>
        </div>
    </div>
</section>
