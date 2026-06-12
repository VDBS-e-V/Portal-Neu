<?php declare(strict_types=1); ?>

<section id="grids-uebersicht" class="section--surface">
    <div class="container container--text-only container--narrow container--center">
        <div class="container__text">
            <h1>Übersicht: grids.css</h1>

            <p>
                Diese Seite zeigt die wichtigsten Grid-Strukturen, Grid-Block-Header,
                Kachelvarianten, Kompositionslayouts, Listenmodule und Pager aus
                <code>grids.css</code>.
            </p>

            <p>
                Grundregel:
                Für Raster verwendest du zuerst <code>.grid</code>. Danach ergänzt du
                Spalten, Abstände, Ausrichtung und optional <code>.grid-item</code>-Spans.
                Für Inhalte im Raster nutzt du meist <code>.tile-card</code> oder Listenmodule.
            </p>
        </div>

        <div class="container__buttons btn-group btn-group--vertical btn-group--main-center btn-group--gap-sm">
            <a href="/styleguide/grids/generator" class="btn btn--primary btn--md">
                Zum Grid Generator
            </a>

            <a href="#grids-inhaltsverzeichnis" class="btn btn--primary-light btn--md">
                Zum Inhaltsverzeichnis
            </a>

            <a href="#grids-entscheidungshilfe" class="btn btn--primary-transp btn--md">
                Zur Entscheidungshilfe
            </a>
        </div>
    </div>
</section>


<section id="grids-inhaltsverzeichnis">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>Inhaltsverzeichnis</h2>

            <p>
                <a href="/styleguide/grids/generator">Grid Generator öffnen</a><br>
                <a href="#grid-grundstruktur">1. Grundstruktur eines Grids</a><br>
                <a href="#grid-block-header">2. Grid-Block mit Header</a><br>
                <a href="#grid-spalten">3. Spaltenvarianten</a><br>
                <a href="#grid-abstaende-ausrichtung">4. Abstände und Ausrichtung</a><br>
                <a href="#grid-item-spans">5. Grid-Items und Spans</a><br>
                <a href="#tile-card-grundstruktur">6. Tile Card Grundstruktur</a><br>
                <a href="#tile-card-varianten">7. Tile Card Varianten</a><br>
                <a href="#tile-card-media">8. Media, Seitenverhältnisse und Bildnachweis</a><br>
                <a href="#tile-card-layouts">9. Horizontale, Overlay- und Minimal-Kacheln</a><br>
                <a href="#grid-kompositionen">10. Kompositionslayouts</a><br>
                <a href="#grid-listenmodule">11. Event- und News-Listen</a><br>
                <a href="#grid-pager">12. Pager</a><br>
                <a href="#grid-responsive">13. Responsive Verhalten</a><br>
                <a href="#grids-entscheidungshilfe">14. Entscheidungshilfe</a>
            </p>
        </div>
    </div>
</section>


<section id="grid-generator-hinweis" class="section--surface">
    <div class="container container--text-buttons">
        <div class="container__text">
            <h2>Grid Generator</h2>

            <p>
                Mit dem Grid Generator kannst du Raster, Header, Kacheltypen,
                Medien, Spans und Kompositionslayouts visuell zusammenstellen.
                Der fertige HTML-Code kann direkt übernommen werden.
            </p>

            <p>
                Typische Kombination:
                <code>.grid</code>,
                <code>.grid--3col</code>,
                <code>.grid--stretch</code>
            </p>
        </div>

        <div class="container__buttons btn-group btn-group--vertical btn-group--sec-stretch btn-group--gap-sm">
            <a href="/styleguide/grids/generator" class="btn btn--primary btn--md">
                Grid Generator öffnen
            </a>

            <a href="#grid-grundstruktur" class="btn btn--primary-transp btn--md">
                Erst Grundlagen ansehen
            </a>
        </div>
    </div>
</section>


<section id="grid-grundstruktur" class="section--surface">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>1. Grundstruktur eines Grids</h2>

            <p>
                Die Klasse <code>.grid</code> erzeugt ein responsives CSS Grid.
                Ohne weitere Modifier nutzt es automatisch passende Spalten mit
                einer Mindestbreite aus <code>--grid-min-column-width</code>.
            </p>

            <h3>Einfaches Auto-Grid</h3>

            <div class="grid">
                <article class="tile-card tile-card--no-media">
                    <div class="tile-card__body">
                        <p class="tile-card__meta">Auto Grid</p>
                        <h3 class="tile-card__title">Erste Kachel</h3>
                        <p class="tile-card__text">Das Raster verteilt die Kacheln automatisch.</p>
                    </div>
                </article>

                <article class="tile-card tile-card--no-media">
                    <div class="tile-card__body">
                        <p class="tile-card__meta">Auto Grid</p>
                        <h3 class="tile-card__title">Zweite Kachel</h3>
                        <p class="tile-card__text">Die Spalten passen sich an die verfügbare Breite an.</p>
                    </div>
                </article>

                <article class="tile-card tile-card--no-media">
                    <div class="tile-card__body">
                        <p class="tile-card__meta">Auto Grid</p>
                        <h3 class="tile-card__title">Dritte Kachel</h3>
                        <p class="tile-card__text">Auf kleineren Viewports wird das Grid einspaltig.</p>
                    </div>
                </article>
            </div>

            <h3>Code</h3>

            <pre><code>&lt;div class="grid"&gt;
    &lt;article class="tile-card tile-card--no-media"&gt;
        ...
    &lt;/article&gt;
&lt;/div&gt;</code></pre>

            <h3>Klassenlogik</h3>

            <p>
                <code>.grid</code><br>
                Grundklasse für das Raster.
            </p>

            <p>
                <code>.tile-card</code><br>
                Standard-Kachel innerhalb eines Grids.
            </p>

            <p>
                <code>.tile-card--no-media</code><br>
                Kachel ohne Bildbereich.
            </p>
        </div>
    </div>
</section>


<section id="grid-block-header">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>2. Grid-Block mit Header</h2>

            <p>
                <code>.grid-block</code> ist ein optionaler Wrapper für Titel,
                Untertitel und Aktionen oberhalb eines Grids. Der eigentliche
                Raster-Container bleibt weiterhin <code>.grid</code>.
            </p>

            <div class="grid-block grid-block--compact">
                <header class="grid-block__header">
                    <p class="grid-block__kicker">Aktuelles</p>
                    <h3 class="grid-block__title">Meldungen und Themen</h3>
                    <p class="grid-block__subtitle">
                        Ausgewählte Beiträge, Hinweise und Veranstaltungen im Überblick.
                    </p>
                </header>

                <div class="grid grid--3col grid--stretch">
                    <article class="tile-card tile-card--no-media">
                        <div class="tile-card__body">
                            <p class="tile-card__meta">Thema</p>
                            <h4 class="tile-card__title">Forschung</h4>
                            <p class="tile-card__text">Kurzer Einstieg in ein Thema.</p>
                        </div>
                    </article>

                    <article class="tile-card tile-card--no-media">
                        <div class="tile-card__body">
                            <p class="tile-card__meta">Thema</p>
                            <h4 class="tile-card__title">Lehre</h4>
                            <p class="tile-card__text">Weitere Informationen kompakt dargestellt.</p>
                        </div>
                    </article>

                    <article class="tile-card tile-card--no-media">
                        <div class="tile-card__body">
                            <p class="tile-card__meta">Thema</p>
                            <h4 class="tile-card__title">Transfer</h4>
                            <p class="tile-card__text">Eine dritte Kachel im gleichen Raster.</p>
                        </div>
                    </article>
                </div>
            </div>

            <h3>Code</h3>

            <pre><code>&lt;div class="grid-block"&gt;
    &lt;header class="grid-block__header"&gt;
        &lt;p class="grid-block__kicker"&gt;Aktuelles&lt;/p&gt;
        &lt;h2 class="grid-block__title"&gt;Meldungen und Themen&lt;/h2&gt;
        &lt;p class="grid-block__subtitle"&gt;Ausgewählte Beiträge im Überblick.&lt;/p&gt;
    &lt;/header&gt;

    &lt;div class="grid grid--3col grid--stretch"&gt;
        ...
    &lt;/div&gt;
&lt;/div&gt;</code></pre>

            <h3>Varianten</h3>

            <p>
                <code>.grid-block--compact</code><br>
                Reduzierter Abstand zwischen Header und Grid.
            </p>

            <p>
                <code>.grid-block--loose</code><br>
                Größerer Abstand zwischen Header und Grid.
            </p>

            <p>
                <code>.grid-block--center</code><br>
                Zentrierter Header.
            </p>

            <p>
                <code>.grid-block--split</code><br>
                Header-Text links, Aktionen rechts.
            </p>
        </div>
    </div>
</section>


<section id="grid-spalten" class="section--surface">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>3. Spaltenvarianten</h2>

            <p>
                Die Spaltenklassen setzen feste Raster mit zwei, drei oder vier
                Spalten. Auf kleineren Viewports werden sie automatisch reduziert.
            </p>

            <h3>Zwei Spalten</h3>
            <div class="grid grid--2col grid--compact">
                <article class="tile-card tile-card--no-media tile-card--compact"><div class="tile-card__body"><h3 class="tile-card__title">.grid--2col</h3><p class="tile-card__text">Zweiteiliges Layout.</p></div></article>
                <article class="tile-card tile-card--no-media tile-card--compact"><div class="tile-card__body"><h3 class="tile-card__title">.grid--2col</h3><p class="tile-card__text">Gut für gleichwertige Inhalte.</p></div></article>
            </div>

            <h3>Drei Spalten</h3>
            <div class="grid grid--3col grid--compact">
                <article class="tile-card tile-card--no-media tile-card--compact"><div class="tile-card__body"><h3 class="tile-card__title">.grid--3col</h3></div></article>
                <article class="tile-card tile-card--no-media tile-card--compact"><div class="tile-card__body"><h3 class="tile-card__title">.grid--3col</h3></div></article>
                <article class="tile-card tile-card--no-media tile-card--compact"><div class="tile-card__body"><h3 class="tile-card__title">.grid--3col</h3></div></article>
            </div>

            <h3>Vier Spalten</h3>
            <div class="grid grid--4col grid--compact">
                <article class="tile-card tile-card--no-media tile-card--compact"><div class="tile-card__body"><h3 class="tile-card__title">.grid--4col</h3></div></article>
                <article class="tile-card tile-card--no-media tile-card--compact"><div class="tile-card__body"><h3 class="tile-card__title">.grid--4col</h3></div></article>
                <article class="tile-card tile-card--no-media tile-card--compact"><div class="tile-card__body"><h3 class="tile-card__title">.grid--4col</h3></div></article>
                <article class="tile-card tile-card--no-media tile-card--compact"><div class="tile-card__body"><h3 class="tile-card__title">.grid--4col</h3></div></article>
            </div>

            <h3>Code</h3>

            <pre><code>&lt;div class="grid grid--2col"&gt;...&lt;/div&gt;
&lt;div class="grid grid--3col"&gt;...&lt;/div&gt;
&lt;div class="grid grid--4col"&gt;...&lt;/div&gt;</code></pre>
        </div>
    </div>
</section>


<section id="grid-abstaende-ausrichtung">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>4. Abstände und Ausrichtung</h2>

            <p>
                Gap-Modifier verändern den Abstand zwischen den Grid-Items.
                Ausrichtungsmodifier steuern, wie Items innerhalb der Zellen sitzen.
            </p>

            <div class="grid grid--3col grid--compact">
                <article class="tile-card tile-card--no-media tile-card--compact"><div class="tile-card__body"><h3 class="tile-card__title">Compact</h3><p class="tile-card__text"><code>.grid--compact</code></p></div></article>
                <article class="tile-card tile-card--no-media tile-card--compact"><div class="tile-card__body"><h3 class="tile-card__title">Default</h3><p class="tile-card__text"><code>.grid</code></p></div></article>
                <article class="tile-card tile-card--no-media tile-card--compact"><div class="tile-card__body"><h3 class="tile-card__title">Loose</h3><p class="tile-card__text"><code>.grid--loose</code></p></div></article>
            </div>

            <h3>Code</h3>

            <pre><code>&lt;div class="grid grid--compact"&gt;...&lt;/div&gt;
&lt;div class="grid grid--loose"&gt;...&lt;/div&gt;
&lt;div class="grid grid--center"&gt;...&lt;/div&gt;
&lt;div class="grid grid--stretch"&gt;...&lt;/div&gt;
&lt;div class="grid grid--dense"&gt;...&lt;/div&gt;</code></pre>

            <h3>Verwendung</h3>

            <p>
                <code>.grid--compact</code><br>
                Für enge Interface- oder Listenbereiche.
            </p>

            <p>
                <code>.grid--loose</code><br>
                Für Startseitenmodule, Teaser oder großzügige Layouts.
            </p>

            <p>
                <code>.grid--stretch</code><br>
                Für gleich hohe Kacheln innerhalb einer Zeile.
            </p>
        </div>
    </div>
</section>


<section id="grid-item-spans" class="section--surface">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>5. Grid-Items und Spans</h2>

            <p>
                Mit <code>.grid-item</code> und Span-Modifiern können einzelne
                Elemente breiter oder höher dargestellt werden. Auf kleineren
                Viewports werden Spans automatisch auf volle Breite zurückgeführt.
            </p>

            <div class="grid grid--4col grid--dense grid--compact">
                <article class="grid-item grid-item--span-2 tile-card tile-card--no-media tile-card--primary">
                    <div class="tile-card__body">
                        <p class="tile-card__meta">Span</p>
                        <h3 class="tile-card__title">.grid-item--span-2</h3>
                        <p class="tile-card__text">Diese Kachel nimmt zwei Spalten ein.</p>
                    </div>
                </article>

                <article class="grid-item tile-card tile-card--no-media tile-card--compact">
                    <div class="tile-card__body"><h3 class="tile-card__title">Normal</h3></div>
                </article>

                <article class="grid-item tile-card tile-card--no-media tile-card--compact">
                    <div class="tile-card__body"><h3 class="tile-card__title">Normal</h3></div>
                </article>

                <article class="grid-item grid-item--full tile-card tile-card--no-media tile-card--ink">
                    <div class="tile-card__body">
                        <p class="tile-card__meta">Volle Breite</p>
                        <h3 class="tile-card__title">.grid-item--full</h3>
                        <p class="tile-card__text">Für Hinweise, Zwischenmodule oder breite Feature-Inhalte.</p>
                    </div>
                </article>
            </div>

            <h3>Code</h3>

            <pre><code>&lt;div class="grid grid--4col grid--dense"&gt;
    &lt;article class="grid-item grid-item--span-2 tile-card"&gt;...&lt;/article&gt;
    &lt;article class="grid-item grid-item--full tile-card"&gt;...&lt;/article&gt;
&lt;/div&gt;</code></pre>

            <h3>Span-Klassen</h3>

            <p>
                <code>.grid-item--span-2</code>, <code>.grid-item--span-3</code>,
                <code>.grid-item--span-4</code> und <code>.grid-item--full</code>
                steuern die Spaltenbreite.
            </p>

            <p>
                <code>.grid-item--row-span-2</code> und <code>.grid-item--row-span-3</code>
                steuern die Zeilenhöhe in passenden Grid-Kontexten.
            </p>
        </div>
    </div>
</section>


<section id="tile-card-grundstruktur">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>6. Tile Card Grundstruktur</h2>

            <p>
                <code>.tile-card</code> ist die Standardkachel für Grid-Inhalte.
                Als Link wird sie mit <code>.tile-card--link</code> kombiniert.
                Die Klasse schützt die Kachel außerdem vor globalen Link-Styles.
            </p>

            <div class="grid grid--2col grid--stretch">
                <a class="tile-card tile-card--link" href="#">
                    <figure class="tile-card__media tile-card__media--ratio-4-3">
                        <img src="/assets/images/news/news-1.jpg" alt="">
                        <figcaption class="tile-card__credit">Bildquelle: Beispiel</figcaption>
                    </figure>

                    <div class="tile-card__body">
                        <p class="tile-card__meta">Themenartikel</p>
                        <h3 class="tile-card__title">Titel der Meldung</h3>
                        <p class="tile-card__text">Kurzbeschreibung der Meldung mit ein bis zwei Zeilen.</p>
                    </div>

                    <span class="tile-card__action" aria-hidden="true">
                        <svg class="vdb-icon"><use href="/assets/icons/vdb-icons.svg#icon-arrow-right"></use></svg>
                    </span>
                </a>

                <article class="tile-card tile-card--no-media">
                    <div class="tile-card__body">
                        <p class="tile-card__kicker">Ohne Bild</p>
                        <h3 class="tile-card__title">Kachel ohne Media-Bereich</h3>
                        <p class="tile-card__text">Gut für Hinweise, Linksammlungen oder kurze redaktionelle Einstiege.</p>
                        <ul class="tile-card__tags">
                            <li class="tile-card__tag">Tag</li>
                            <li class="tile-card__tag">Thema</li>
                        </ul>
                    </div>
                </article>
            </div>

            <h3>Code</h3>

            <pre><code>&lt;a class="tile-card tile-card--link" href="/news/1"&gt;
    &lt;figure class="tile-card__media tile-card__media--ratio-4-3"&gt;
        &lt;img src="/assets/images/news/news-1.jpg" alt=""&gt;
        &lt;figcaption class="tile-card__credit"&gt;Bildquelle: Beispiel&lt;/figcaption&gt;
    &lt;/figure&gt;

    &lt;div class="tile-card__body"&gt;
        &lt;p class="tile-card__meta"&gt;Themenartikel&lt;/p&gt;
        &lt;h3 class="tile-card__title"&gt;Titel der Meldung&lt;/h3&gt;
        &lt;p class="tile-card__text"&gt;Kurzbeschreibung der Meldung.&lt;/p&gt;
    &lt;/div&gt;

    &lt;span class="tile-card__action" aria-hidden="true"&gt;
        ...
    &lt;/span&gt;
&lt;/a&gt;</code></pre>
        </div>
    </div>
</section>


<section id="tile-card-varianten" class="section--surface">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>7. Tile Card Varianten</h2>

            <p>
                Varianten steuern Fläche, Größe und Aktionsfarbe. Die eigentliche
                Struktur der Kachel bleibt gleich.
            </p>

            <div class="grid grid--3col grid--stretch">
                <article class="tile-card tile-card--no-media tile-card--surface">
                    <div class="tile-card__body"><p class="tile-card__meta">Fläche</p><h3 class="tile-card__title">Surface</h3><p class="tile-card__text"><code>.tile-card--surface</code></p></div>
                </article>
                <article class="tile-card tile-card--no-media tile-card--white">
                    <div class="tile-card__body"><p class="tile-card__meta">Fläche</p><h3 class="tile-card__title">White</h3><p class="tile-card__text"><code>.tile-card--white</code></p></div>
                </article>
                <article class="tile-card tile-card--no-media tile-card--ink">
                    <div class="tile-card__body"><p class="tile-card__meta">Dunkel</p><h3 class="tile-card__title">Ink</h3><p class="tile-card__text"><code>.tile-card--ink</code></p></div>
                </article>
            </div>

            <h3>Code</h3>

            <pre><code>&lt;article class="tile-card tile-card--surface"&gt;...&lt;/article&gt;
&lt;article class="tile-card tile-card--white"&gt;...&lt;/article&gt;
&lt;article class="tile-card tile-card--flat"&gt;...&lt;/article&gt;
&lt;article class="tile-card tile-card--ink"&gt;...&lt;/article&gt;
&lt;article class="tile-card tile-card--compact"&gt;...&lt;/article&gt;
&lt;article class="tile-card tile-card--large"&gt;...&lt;/article&gt;</code></pre>

            <h3>Aktionsfarben</h3>

            <p>
                <code>.tile-card--primary</code>, <code>.tile-card--secondary-cta</code>,
                <code>.tile-card--secondary-highlight</code> und <code>.tile-card--plum</code>
                verändern vor allem die kleine Aktionsfläche unten rechts.
            </p>
        </div>
    </div>
</section>


<section id="tile-card-media">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>8. Media, Seitenverhältnisse und Bildnachweis</h2>

            <p>
                Der Media-Bereich sitzt bündig in der Kachel. Mit Ratio-Klassen
                steuerst du das Seitenverhältnis. Bildnachweise können vertikal
                oder am unteren Rand erscheinen.
            </p>

            <div class="grid grid--3col grid--stretch">
                <article class="tile-card">
                    <figure class="tile-card__media tile-card__media--ratio-16-9">
                        <img src="/assets/images/news/news-1.jpg" alt="">
                        <figcaption class="tile-card__credit">16:9</figcaption>
                    </figure>
                    <div class="tile-card__body"><h3 class="tile-card__title">16:9</h3></div>
                </article>

                <article class="tile-card">
                    <figure class="tile-card__media tile-card__media--ratio-4-3">
                        <img src="/assets/images/news/news-2.jpg" alt="">
                        <figcaption class="tile-card__credit">4:3</figcaption>
                    </figure>
                    <div class="tile-card__body"><h3 class="tile-card__title">4:3</h3></div>
                </article>

                <article class="tile-card">
                    <figure class="tile-card__media tile-card__media--ratio-1-1">
                        <img src="/assets/images/news/news-3.jpg" alt="">
                        <figcaption class="tile-card__credit tile-card__credit--bottom">1:1</figcaption>
                    </figure>
                    <div class="tile-card__body"><h3 class="tile-card__title">1:1</h3></div>
                </article>
            </div>

            <h3>Code</h3>

            <pre><code>&lt;figure class="tile-card__media tile-card__media--ratio-16-9"&gt;...&lt;/figure&gt;
&lt;figure class="tile-card__media tile-card__media--ratio-4-3"&gt;...&lt;/figure&gt;
&lt;figure class="tile-card__media tile-card__media--ratio-3-2"&gt;...&lt;/figure&gt;
&lt;figure class="tile-card__media tile-card__media--ratio-1-1"&gt;...&lt;/figure&gt;
&lt;figure class="tile-card__media tile-card__media--ratio-21-9"&gt;...&lt;/figure&gt;
&lt;figure class="tile-card__media tile-card__media--tall"&gt;...&lt;/figure&gt;
&lt;figure class="tile-card__media tile-card__media--wide"&gt;...&lt;/figure&gt;</code></pre>
        </div>
    </div>
</section>


<section id="tile-card-layouts" class="section--surface">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>9. Horizontale, Overlay- und Minimal-Kacheln</h2>

            <p>
                Neben der Standardkachel gibt es Layoutvarianten für horizontale
                Teaser, bildstarke Overlay-Kacheln und sehr reduzierte Kacheln.
            </p>

            <h3>Horizontale Kachel</h3>
            <a class="tile-card tile-card--link tile-card--horizontal" href="#">
                <figure class="tile-card__media tile-card__media--ratio-4-3">
                    <img src="/assets/images/news/news-1.jpg" alt="">
                </figure>
                <div class="tile-card__body">
                    <p class="tile-card__meta">Horizontal</p>
                    <h3 class="tile-card__title">Bild links, Text rechts</h3>
                    <p class="tile-card__text">Diese Variante eignet sich für breite Listen- oder Teaserbereiche.</p>
                </div>
                <span class="tile-card__action" aria-hidden="true"><svg class="vdb-icon"><use href="/assets/icons/vdb-icons.svg#icon-arrow-right"></use></svg></span>
            </a>

            <h3>Overlay-Kachel</h3>
            <a class="tile-card tile-card--link tile-card--overlay" href="#">
                <figure class="tile-card__media">
                    <img src="/assets/images/news/news-2.jpg" alt="">
                </figure>
                <div class="tile-card__body">
                    <p class="tile-card__meta">Overlay</p>
                    <h3 class="tile-card__title">Text auf Bildverlauf</h3>
                    <p class="tile-card__text">Für Feature-Teaser mit starkem Bildbezug.</p>
                </div>
                <span class="tile-card__action" aria-hidden="true"><svg class="vdb-icon"><use href="/assets/icons/vdb-icons.svg#icon-arrow-right"></use></svg></span>
            </a>

            <h3>Code</h3>

            <pre><code>&lt;a class="tile-card tile-card--link tile-card--horizontal" href="#"&gt;...&lt;/a&gt;
&lt;a class="tile-card tile-card--link tile-card--overlay" href="#"&gt;...&lt;/a&gt;
&lt;article class="tile-card tile-card--minimal"&gt;...&lt;/article&gt;</code></pre>
        </div>
    </div>
</section>


<section id="grid-kompositionen">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>10. Kompositionslayouts</h2>

            <p>
                <code>.grid-composition</code> erzeugt zusammengesetzte Layouts,
                zum Beispiel ein Kartenraster neben einer Liste oder eine Feature-
                Kachel neben Kurzmeldungen.
            </p>

            <div class="grid-composition grid-composition--cards-aside">
                <div class="grid grid--2col grid--compact">
                    <article class="tile-card tile-card--no-media"><div class="tile-card__body"><h3 class="tile-card__title">Kachel 1</h3><p class="tile-card__text">Hauptbereich.</p></div></article>
                    <article class="tile-card tile-card--no-media"><div class="tile-card__body"><h3 class="tile-card__title">Kachel 2</h3><p class="tile-card__text">Hauptbereich.</p></div></article>
                </div>

                <aside class="news-panel">
                    <h3 class="news-panel__title">Kurzmeldungen</h3>
                    <div class="news-list">
                        <a href="#" class="news-list__item">
                            <span class="news-list__date">12.06.2026</span>
                            <h4 class="news-list__title">Erste Kurzmeldung</h4>
                            <p class="news-list__text">Kurzer Begleittext.</p>
                        </a>
                        <a href="#" class="news-list__item">
                            <span class="news-list__date">13.06.2026</span>
                            <h4 class="news-list__title">Zweite Kurzmeldung</h4>
                            <p class="news-list__text">Kurzer Begleittext.</p>
                        </a>
                    </div>
                </aside>
            </div>

            <h3>Code</h3>

            <pre><code>&lt;div class="grid-composition grid-composition--cards-aside"&gt;
    &lt;div class="grid grid--2col"&gt;...&lt;/div&gt;
    &lt;aside class="news-panel"&gt;...&lt;/aside&gt;
&lt;/div&gt;</code></pre>

            <h3>Varianten</h3>

            <p>
                <code>.grid-composition--cards-aside</code>,
                <code>.grid-composition--feature-list</code>,
                <code>.grid-composition--list-feature</code>,
                <code>.grid-composition--equal</code> und
                <code>.grid-composition--wide-aside</code>.
            </p>
        </div>
    </div>
</section>


<section id="grid-listenmodule" class="section--surface">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>11. Event- und News-Listen</h2>

            <p>
                Für Mischlayouts stehen einfache Listenmodule bereit. Auch diese
                Links werden gegen globale Link-Styles abgesichert.
            </p>

            <div class="grid grid--2col grid--loose">
                <div class="event-panel">
                    <h3 class="event-panel__title">Veranstaltungen</h3>
                    <div class="event-list">
                        <a href="#" class="event-item">
                            <time class="event-item__date" datetime="2026-06-12">
                                <span class="event-item__day">12. Jun</span>
                                <span class="event-item__time">14:00</span>
                            </time>
                            <span class="event-item__content">
                                <span class="event-item__title">Workshop zur Einführung</span>
                            </span>
                        </a>
                        <a href="#" class="event-item">
                            <time class="event-item__date" datetime="2026-06-20">
                                <span class="event-item__day">20. Jun</span>
                                <span class="event-item__time">10:00</span>
                            </time>
                            <span class="event-item__content">
                                <span class="event-item__title">Informationsveranstaltung</span>
                            </span>
                        </a>
                    </div>
                </div>

                <div class="news-panel">
                    <h3 class="news-panel__title">News</h3>
                    <div class="news-list">
                        <a href="#" class="news-list__item">
                            <span class="news-list__date">12.06.2026</span>
                            <h4 class="news-list__title">Neue Meldung im Überblick</h4>
                            <p class="news-list__text">Kurzbeschreibung mit einer ruhigen Textlänge.</p>
                        </a>
                        <a href="#" class="news-list__item">
                            <span class="news-list__date">13.06.2026</span>
                            <h4 class="news-list__title">Weitere Kurzmeldung</h4>
                            <p class="news-list__text">Zusätzlicher Kontext für die Meldung.</p>
                        </a>
                    </div>
                </div>
            </div>

            <h3>Code</h3>

            <pre><code>&lt;div class="event-panel"&gt;
    &lt;h3 class="event-panel__title"&gt;Veranstaltungen&lt;/h3&gt;
    &lt;div class="event-list"&gt;
        &lt;a href="#" class="event-item"&gt;...&lt;/a&gt;
    &lt;/div&gt;
&lt;/div&gt;

&lt;div class="news-panel"&gt;
    &lt;h3 class="news-panel__title"&gt;News&lt;/h3&gt;
    &lt;div class="news-list"&gt;
        &lt;a href="#" class="news-list__item"&gt;...&lt;/a&gt;
    &lt;/div&gt;
&lt;/div&gt;</code></pre>
        </div>
    </div>
</section>


<section id="grid-pager">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>12. Pager</h2>

            <p>
                Der Pager dient als reduzierte Navigation für Slider, Kachelstrecken
                oder paginierte Teaserbereiche.
            </p>

            <div class="grid-pager" aria-label="Beispiel-Pager">
                <button class="grid-pager__button" type="button" aria-label="Vorherige Seite">
                    <svg class="vdb-icon" aria-hidden="true"><use href="/assets/icons/vdb-icons.svg#icon-arrow-left"></use></svg>
                </button>
                <span class="grid-pager__count">1 / 4</span>
                <button class="grid-pager__button" type="button" aria-label="Nächste Seite">
                    <svg class="vdb-icon" aria-hidden="true"><use href="/assets/icons/vdb-icons.svg#icon-arrow-right"></use></svg>
                </button>
            </div>

            <h3>Code</h3>

            <pre><code>&lt;div class="grid-pager" aria-label="Pager"&gt;
    &lt;button class="grid-pager__button" type="button" aria-label="Vorherige Seite"&gt;...&lt;/button&gt;
    &lt;span class="grid-pager__count"&gt;1 / 4&lt;/span&gt;
    &lt;button class="grid-pager__button" type="button" aria-label="Nächste Seite"&gt;...&lt;/button&gt;
&lt;/div&gt;</code></pre>
        </div>
    </div>
</section>


<section id="grid-responsive" class="section--surface">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>13. Responsive Verhalten</h2>

            <p>
                Die wichtigsten Breakpoints sind bereits in <code>grids.css</code>
                enthalten. Vier Spalten werden unterhalb größerer Viewports auf
                zwei Spalten reduziert, drei Spalten ebenfalls auf zwei und später
                werden alle Grid-Varianten einspaltig.
            </p>

            <p>
                Auch Kompositionslayouts wechseln auf eine einzelne Spalte. Spans
                wie <code>.grid-item--span-2</code> oder <code>.grid-item--full</code>
                werden auf kleineren Viewports neutralisiert, damit Inhalte nicht
                aus dem Raster laufen.
            </p>

            <h3>Praktische Regel</h3>

            <p>
                Wähle die Desktop-Struktur nach Inhalt: <code>.grid--2col</code>
                für ruhige Vergleiche, <code>.grid--3col</code> für klassische
                Teaserstrecken und <code>.grid--4col</code> nur für kompakte,
                gleichwertige Inhalte.
            </p>
        </div>
    </div>
</section>


<section id="grids-entscheidungshilfe">
    <div class="container container--text-only container--narrow">
        <div class="container__text">
            <h2>14. Entscheidungshilfe</h2>

            <p>
                <code>.grid</code><br>
                Wenn du mehrere gleichartige Inhalte automatisch verteilen möchtest.
            </p>

            <p>
                <code>.grid--3col grid--stretch</code><br>
                Standard für redaktionelle Teaser mit gleich hohen Kacheln.
            </p>

            <p>
                <code>.grid-block</code><br>
                Wenn das Grid einen eigenen Titel, Untertitel oder eine Aktion braucht.
            </p>

            <p>
                <code>.tile-card tile-card--link</code><br>
                Wenn die gesamte Kachel klickbar sein soll.
            </p>

            <p>
                <code>.tile-card--horizontal</code><br>
                Für breite Listen-Teaser mit Bild und Text nebeneinander.
            </p>

            <p>
                <code>.tile-card--overlay</code><br>
                Nur für bildstarke Feature-Teaser mit guter Bildqualität.
            </p>

            <p>
                <code>.grid-composition</code><br>
                Wenn Karten, Listen oder Featurebereiche gemeinsam angeordnet werden.
            </p>

            <p>
                <code>.event-list</code> und <code>.news-list</code><br>
                Für kompakte Listen neben oder unter Kachelrastern.
            </p>
        </div>

        <div class="container__buttons btn-group btn-group--vertical btn-group--sec-stretch btn-group--gap-sm">
            <a href="/styleguide/grids/generator" class="btn btn--primary btn--md">
                Passendes Grid generieren
            </a>

            <a href="#grids-uebersicht" class="btn btn--primary-transp btn--md">
                Zurück nach oben
            </a>
        </div>
    </div>
</section>
