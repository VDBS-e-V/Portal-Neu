<?php
declare(strict_types=1);

$prefill = $prefill ?? [
    'output' => 'grid',
    'gridColumns' => 'grid--3col',
    'gridGap' => '',
    'gridAlign' => 'grid--stretch',
    'gridDense' => '0',
    'itemCount' => '3',
    'firstItemSpan' => '',
    'wrapBlock' => '1',
    'blockSpacing' => '',
    'headerVariant' => '',
    'kicker' => 'Aktuelles',
    'title' => 'Meldungen und Themen',
    'subtitle' => 'Ausgewählte Beiträge, Hinweise und Veranstaltungen im Überblick.',
    'showAction' => '0',
    'actionLabel' => 'Alle Meldungen',
    'actionHref' => '#',
    'cardElement' => 'a',
    'href' => '#',
    'cardVariant' => 'tile-card--surface',
    'cardSize' => '',
    'cardLayout' => '',
    'actionVariant' => '',
    'mediaMode' => 'with-media',
    'mediaRatio' => 'tile-card__media--ratio-4-3',
    'mediaFit' => '',
    'imageSrc' => '/assets/images/news/news-1.jpg',
    'imageAlt' => '',
    'credit' => 'Bildquelle: Beispiel',
    'creditPosition' => '',
    'meta' => 'Themenartikel',
    'cardTitle' => 'Titel der Meldung',
    'text' => 'Kurzbeschreibung der Meldung mit ein bis zwei Zeilen.',
    'showTags' => '0',
    'composition' => 'grid-composition--cards-aside',
    'includePager' => '0',
];

if (!function_exists('vdb_grid_generator_e')) {
    function vdb_grid_generator_e(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}

$outputModes = [
    'grid' => 'Grid mit Tile Cards',
    'composition' => 'Kompositionslayout',
    'events' => 'Event-Liste',
    'news' => 'News-Liste',
];

$gridColumns = [
    '' => 'Auto — .grid',
    'grid--auto' => 'Auto explizit — .grid--auto',
    'grid--2col' => '2 Spalten — .grid--2col',
    'grid--3col' => '3 Spalten — .grid--3col',
    'grid--4col' => '4 Spalten — .grid--4col',
];

$gridGaps = [
    '' => 'Standard',
    'grid--compact' => 'Kompakt — .grid--compact',
    'grid--loose' => 'Großzügig — .grid--loose',
];

$gridAlign = [
    '' => 'Standard',
    'grid--center' => 'Zentriert — .grid--center',
    'grid--stretch' => 'Stretch — .grid--stretch',
];

$itemSpans = [
    '' => 'Kein Span',
    'grid-item--span-2' => 'Erstes Item: 2 Spalten — .grid-item--span-2',
    'grid-item--span-3' => 'Erstes Item: 3 Spalten — .grid-item--span-3',
    'grid-item--full' => 'Erstes Item: volle Breite — .grid-item--full',
];

$blockSpacing = [
    '' => 'Standard',
    'grid-block--compact' => 'Kompakt — .grid-block--compact',
    'grid-block--loose' => 'Großzügig — .grid-block--loose',
];

$headerVariants = [
    '' => 'Normaler Header',
    'grid-block--center' => 'Zentriert — .grid-block--center',
    'grid-block--split' => 'Split mit Aktion rechts — .grid-block--split',
];

$cardVariants = [
    '' => 'Standardfläche',
    'tile-card--surface' => 'Surface — .tile-card--surface',
    'tile-card--white' => 'White — .tile-card--white',
    'tile-card--flat' => 'Flat — .tile-card--flat',
    'tile-card--ink' => 'Ink / dunkel — .tile-card--ink',
];

$cardSizes = [
    '' => 'Standard',
    'tile-card--compact' => 'Kompakt — .tile-card--compact',
    'tile-card--large' => 'Groß — .tile-card--large',
    'tile-card--feature' => 'Feature — .tile-card--feature',
];

$cardLayouts = [
    '' => 'Standard',
    'tile-card--horizontal' => 'Horizontal — .tile-card--horizontal',
    'tile-card--overlay' => 'Overlay — .tile-card--overlay',
    'tile-card--minimal' => 'Minimal — .tile-card--minimal',
];

$actionVariants = [
    '' => 'Standard-Aktion',
    'tile-card--primary' => 'Primary-Aktion — .tile-card--primary',
    'tile-card--secondary-cta' => 'Secondary CTA — .tile-card--secondary-cta',
    'tile-card--secondary-highlight' => 'Secondary Highlight — .tile-card--secondary-highlight',
    'tile-card--plum' => 'Plum Alias — .tile-card--plum',
];

$mediaModes = [
    'with-media' => 'Mit Bildbereich',
    'no-media' => 'Ohne Bild — .tile-card--no-media',
];

$mediaRatios = [
    'tile-card__media--ratio-16-9' => '16:9 — .tile-card__media--ratio-16-9',
    'tile-card__media--ratio-4-3' => '4:3 — .tile-card__media--ratio-4-3',
    'tile-card__media--ratio-3-2' => '3:2 — .tile-card__media--ratio-3-2',
    'tile-card__media--ratio-1-1' => '1:1 — .tile-card__media--ratio-1-1',
    'tile-card__media--ratio-21-9' => '21:9 — .tile-card__media--ratio-21-9',
    'tile-card__media--tall' => 'Hoch — .tile-card__media--tall',
    'tile-card__media--wide' => 'Breit — .tile-card__media--wide',
];

$mediaFits = [
    '' => 'Cover / Standard',
    'tile-card__media--cover' => 'Cover — .tile-card__media--cover',
    'tile-card__media--contain' => 'Contain — .tile-card__media--contain',
];

$creditPositions = [
    '' => 'Vertikal rechts',
    'tile-card__credit--bottom' => 'Unten — .tile-card__credit--bottom',
    'tile-card__credit--hidden' => 'Ausblenden — .tile-card__credit--hidden',
];

$compositionModes = [
    'grid-composition--cards-aside' => 'Karten + Aside — .grid-composition--cards-aside',
    'grid-composition--feature-list' => 'Feature + Liste — .grid-composition--feature-list',
    'grid-composition--list-feature' => 'Liste + Feature — .grid-composition--list-feature',
    'grid-composition--equal' => 'Gleichwertig — .grid-composition--equal',
    'grid-composition--wide-aside' => 'Breit + Aside — .grid-composition--wide-aside',
];
?>

<section id="grid-generator-intro" class="section--surface">
    <div class="container container--text-only container--narrow container--center">
        <div class="container__text">
            <h1>Grid Generator</h1>

            <p>
                Wähle Raster, Header, Kachelvarianten, Medien, Spans und Ausgabeart.
                Der fertige HTML-Code wird live erzeugt und kann direkt kopiert werden.
            </p>
        </div>

        <div class="container__buttons btn-group btn-group--vertical btn-group--main-center btn-group--gap-sm">
            <a href="#grid-generator-form-section" class="btn btn--primary btn--md">Generator öffnen</a>
            <a href="#grid-generator-code" class="btn btn--primary-transp btn--md">Zum HTML-Code</a>
        </div>
    </div>
</section>

<section id="grid-generator-form-section" class="long-width">
    <div class="container container--text-only container--wide">
        <form id="grid-generator-form" class="form form--card">
            <div class="form__grid form__grid--4">
                <div class="form__title form__title--center">
                    <h2>Grid konfigurieren</h2>
                    <p>
                        Die Felder nutzen das Formularsystem aus <code>forms.css</code>.
                        Die Ausgabe nutzt Klassen aus <code>grids.css</code> und optional
                        Buttons aus <code>buttons.css</code> im Grid-Block-Header.
                    </p>
                </div>

                <div class="form__notice form__notice--info form__field--span-full">
                    <strong>Hinweis:</strong>
                    Für klickbare Kacheln nutze <code>&lt;a class="tile-card tile-card--link"&gt;</code>.
                    Für rein redaktionelle Kacheln nutze <code>&lt;article class="tile-card"&gt;</code>.
                </div>

                <div class="form__section-title form__section-title--primary">
                    <h3>Ausgabe</h3>
                    <p>Wähle, ob ein normales Grid, ein Kompositionslayout oder eine Liste erzeugt wird.</p>
                </div>

                <div class="form__field form__field--span-2">
                    <label class="form__label" for="grid-output">Ausgabeart</label>
                    <select class="form__control" id="grid-output" name="output">
                        <?php foreach ($outputModes as $value => $label): ?>
                            <option value="<?= vdb_grid_generator_e($value) ?>" <?= $value === $prefill['output'] ? 'selected' : '' ?>><?= vdb_grid_generator_e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form__field">
                    <label class="form__label" for="grid-item-count">Anzahl Items</label>
                    <select class="form__control" id="grid-item-count" name="itemCount">
                        <?php foreach (['2', '3', '4', '5', '6', '8'] as $count): ?>
                            <option value="<?= vdb_grid_generator_e($count) ?>" <?= $count === $prefill['itemCount'] ? 'selected' : '' ?>><?= vdb_grid_generator_e($count) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form__field">
                    <label class="form__label" for="grid-include-pager">Pager</label>
                    <select class="form__control" id="grid-include-pager" name="includePager">
                        <option value="0" <?= $prefill['includePager'] === '0' ? 'selected' : '' ?>>Nicht ausgeben</option>
                        <option value="1" <?= $prefill['includePager'] === '1' ? 'selected' : '' ?>>Pager anhängen</option>
                    </select>
                </div>

                <div class="form__section-title form__section-title--secondary-cta">
                    <h3>Grid</h3>
                    <p>Bestimme Spalten, Abstand, Ausrichtung und optional Item-Spans.</p>
                </div>

                <div class="form__field">
                    <label class="form__label" for="grid-columns">Spalten</label>
                    <select class="form__control" id="grid-columns" name="gridColumns">
                        <?php foreach ($gridColumns as $class => $label): ?>
                            <option value="<?= vdb_grid_generator_e($class) ?>" <?= $class === $prefill['gridColumns'] ? 'selected' : '' ?>><?= vdb_grid_generator_e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form__field">
                    <label class="form__label" for="grid-gap">Abstand</label>
                    <select class="form__control" id="grid-gap" name="gridGap">
                        <?php foreach ($gridGaps as $class => $label): ?>
                            <option value="<?= vdb_grid_generator_e($class) ?>" <?= $class === $prefill['gridGap'] ? 'selected' : '' ?>><?= vdb_grid_generator_e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form__field">
                    <label class="form__label" for="grid-align">Ausrichtung</label>
                    <select class="form__control" id="grid-align" name="gridAlign">
                        <?php foreach ($gridAlign as $class => $label): ?>
                            <option value="<?= vdb_grid_generator_e($class) ?>" <?= $class === $prefill['gridAlign'] ? 'selected' : '' ?>><?= vdb_grid_generator_e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form__field">
                    <label class="form__label" for="grid-dense">Dense</label>
                    <select class="form__control" id="grid-dense" name="gridDense">
                        <option value="0" <?= $prefill['gridDense'] === '0' ? 'selected' : '' ?>>Nein</option>
                        <option value="1" <?= $prefill['gridDense'] === '1' ? 'selected' : '' ?>>Ja — .grid--dense</option>
                    </select>
                </div>

                <div class="form__field form__field--span-2">
                    <label class="form__label" for="grid-first-item-span">Erstes Item</label>
                    <select class="form__control" id="grid-first-item-span" name="firstItemSpan">
                        <?php foreach ($itemSpans as $class => $label): ?>
                            <option value="<?= vdb_grid_generator_e($class) ?>" <?= $class === $prefill['firstItemSpan'] ? 'selected' : '' ?>><?= vdb_grid_generator_e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form__field form__field--span-2">
                    <label class="form__label" for="grid-composition">Komposition</label>
                    <select class="form__control" id="grid-composition" name="composition">
                        <?php foreach ($compositionModes as $class => $label): ?>
                            <option value="<?= vdb_grid_generator_e($class) ?>" <?= $class === $prefill['composition'] ? 'selected' : '' ?>><?= vdb_grid_generator_e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <p class="form__hint">Wird nur bei Ausgabeart „Kompositionslayout“ verwendet.</p>
                </div>

                <div class="form__section-title form__section-title--secondary-highlight">
                    <h3>Grid-Block Header</h3>
                    <p>Optionaler Wrapper mit Kicker, Titel, Untertitel und Aktion.</p>
                </div>

                <div class="form__field">
                    <label class="form__label" for="grid-wrap-block">Grid-Block</label>
                    <select class="form__control" id="grid-wrap-block" name="wrapBlock">
                        <option value="1" <?= $prefill['wrapBlock'] === '1' ? 'selected' : '' ?>>Mit .grid-block</option>
                        <option value="0" <?= $prefill['wrapBlock'] === '0' ? 'selected' : '' ?>>Ohne .grid-block</option>
                    </select>
                </div>

                <div class="form__field">
                    <label class="form__label" for="grid-block-spacing">Block-Abstand</label>
                    <select class="form__control" id="grid-block-spacing" name="blockSpacing">
                        <?php foreach ($blockSpacing as $class => $label): ?>
                            <option value="<?= vdb_grid_generator_e($class) ?>" <?= $class === $prefill['blockSpacing'] ? 'selected' : '' ?>><?= vdb_grid_generator_e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form__field form__field--span-2">
                    <label class="form__label" for="grid-header-variant">Header-Variante</label>
                    <select class="form__control" id="grid-header-variant" name="headerVariant">
                        <?php foreach ($headerVariants as $class => $label): ?>
                            <option value="<?= vdb_grid_generator_e($class) ?>" <?= $class === $prefill['headerVariant'] ? 'selected' : '' ?>><?= vdb_grid_generator_e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form__field form__field--span-2">
                    <label class="form__label" for="grid-kicker">Kicker</label>
                    <input class="form__control" type="text" id="grid-kicker" name="kicker" value="<?= vdb_grid_generator_e($prefill['kicker']) ?>">
                </div>

                <div class="form__field form__field--span-2">
                    <label class="form__label" for="grid-title">Titel</label>
                    <input class="form__control" type="text" id="grid-title" name="title" value="<?= vdb_grid_generator_e($prefill['title']) ?>">
                </div>

                <div class="form__field form__field--span-full">
                    <label class="form__label" for="grid-subtitle">Untertitel</label>
                    <input class="form__control" type="text" id="grid-subtitle" name="subtitle" value="<?= vdb_grid_generator_e($prefill['subtitle']) ?>">
                </div>

                <div class="form__field">
                    <label class="form__label" for="grid-show-action">Header-Aktion</label>
                    <select class="form__control" id="grid-show-action" name="showAction">
                        <option value="0" <?= $prefill['showAction'] === '0' ? 'selected' : '' ?>>Nicht ausgeben</option>
                        <option value="1" <?= $prefill['showAction'] === '1' ? 'selected' : '' ?>>Button ausgeben</option>
                    </select>
                </div>

                <div class="form__field">
                    <label class="form__label" for="grid-action-label">Button-Text</label>
                    <input class="form__control" type="text" id="grid-action-label" name="actionLabel" value="<?= vdb_grid_generator_e($prefill['actionLabel']) ?>">
                </div>

                <div class="form__field form__field--span-2">
                    <label class="form__label" for="grid-action-href">Button-Link</label>
                    <input class="form__control" type="text" id="grid-action-href" name="actionHref" value="<?= vdb_grid_generator_e($prefill['actionHref']) ?>">
                </div>

                <div class="form__section-title">
                    <h3>Tile Card</h3>
                    <p>Bestimme Elementtyp, Link, Variante, Layout, Media und Texte der Kachel.</p>
                </div>

                <div class="form__field">
                    <label class="form__label" for="grid-card-element">Element</label>
                    <select class="form__control" id="grid-card-element" name="cardElement">
                        <option value="a" <?= $prefill['cardElement'] === 'a' ? 'selected' : '' ?>>Link: &lt;a&gt;</option>
                        <option value="article" <?= $prefill['cardElement'] === 'article' ? 'selected' : '' ?>>Artikel: &lt;article&gt;</option>
                    </select>
                </div>

                <div class="form__field">
                    <label class="form__label" for="grid-card-href">Kachel-Link</label>
                    <input class="form__control" type="text" id="grid-card-href" name="href" value="<?= vdb_grid_generator_e($prefill['href']) ?>">
                    <p class="form__hint">Wird nur für <code>&lt;a&gt;</code>-Kacheln ausgegeben.</p>
                </div>

                <div class="form__field">
                    <label class="form__label" for="grid-card-variant">Fläche</label>
                    <select class="form__control" id="grid-card-variant" name="cardVariant">
                        <?php foreach ($cardVariants as $class => $label): ?>
                            <option value="<?= vdb_grid_generator_e($class) ?>" <?= $class === $prefill['cardVariant'] ? 'selected' : '' ?>><?= vdb_grid_generator_e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form__field">
                    <label class="form__label" for="grid-card-size">Größe</label>
                    <select class="form__control" id="grid-card-size" name="cardSize">
                        <?php foreach ($cardSizes as $class => $label): ?>
                            <option value="<?= vdb_grid_generator_e($class) ?>" <?= $class === $prefill['cardSize'] ? 'selected' : '' ?>><?= vdb_grid_generator_e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form__field">
                    <label class="form__label" for="grid-card-layout">Layout</label>
                    <select class="form__control" id="grid-card-layout" name="cardLayout">
                        <?php foreach ($cardLayouts as $class => $label): ?>
                            <option value="<?= vdb_grid_generator_e($class) ?>" <?= $class === $prefill['cardLayout'] ? 'selected' : '' ?>><?= vdb_grid_generator_e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form__field">
                    <label class="form__label" for="grid-action-variant">Aktionsfarbe</label>
                    <select class="form__control" id="grid-action-variant" name="actionVariant">
                        <?php foreach ($actionVariants as $class => $label): ?>
                            <option value="<?= vdb_grid_generator_e($class) ?>" <?= $class === $prefill['actionVariant'] ? 'selected' : '' ?>><?= vdb_grid_generator_e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form__field">
                    <label class="form__label" for="grid-media-mode">Media</label>
                    <select class="form__control" id="grid-media-mode" name="mediaMode">
                        <?php foreach ($mediaModes as $value => $label): ?>
                            <option value="<?= vdb_grid_generator_e($value) ?>" <?= $value === $prefill['mediaMode'] ? 'selected' : '' ?>><?= vdb_grid_generator_e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form__field">
                    <label class="form__label" for="grid-media-ratio">Bildformat</label>
                    <select class="form__control" id="grid-media-ratio" name="mediaRatio">
                        <?php foreach ($mediaRatios as $class => $label): ?>
                            <option value="<?= vdb_grid_generator_e($class) ?>" <?= $class === $prefill['mediaRatio'] ? 'selected' : '' ?>><?= vdb_grid_generator_e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form__field">
                    <label class="form__label" for="grid-media-fit">Bildmodus</label>
                    <select class="form__control" id="grid-media-fit" name="mediaFit">
                        <?php foreach ($mediaFits as $class => $label): ?>
                            <option value="<?= vdb_grid_generator_e($class) ?>" <?= $class === $prefill['mediaFit'] ? 'selected' : '' ?>><?= vdb_grid_generator_e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form__field">
                    <label class="form__label" for="grid-credit-position">Bildnachweis</label>
                    <select class="form__control" id="grid-credit-position" name="creditPosition">
                        <?php foreach ($creditPositions as $class => $label): ?>
                            <option value="<?= vdb_grid_generator_e($class) ?>" <?= $class === $prefill['creditPosition'] ? 'selected' : '' ?>><?= vdb_grid_generator_e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form__field form__field--span-2">
                    <label class="form__label" for="grid-image-src">Bildpfad</label>
                    <input class="form__control" type="text" id="grid-image-src" name="imageSrc" value="<?= vdb_grid_generator_e($prefill['imageSrc']) ?>">
                </div>

                <div class="form__field form__field--span-2">
                    <label class="form__label" for="grid-image-alt">Alt-Text</label>
                    <input class="form__control" type="text" id="grid-image-alt" name="imageAlt" value="<?= vdb_grid_generator_e($prefill['imageAlt']) ?>">
                </div>

                <div class="form__field form__field--span-2">
                    <label class="form__label" for="grid-credit">Credit</label>
                    <input class="form__control" type="text" id="grid-credit" name="credit" value="<?= vdb_grid_generator_e($prefill['credit']) ?>">
                </div>

                <div class="form__field form__field--span-2">
                    <label class="form__label" for="grid-meta">Meta / Kategorie</label>
                    <input class="form__control" type="text" id="grid-meta" name="meta" value="<?= vdb_grid_generator_e($prefill['meta']) ?>">
                </div>

                <div class="form__field form__field--span-2">
                    <label class="form__label" for="grid-card-title">Kachel-Titel</label>
                    <input class="form__control" type="text" id="grid-card-title" name="cardTitle" value="<?= vdb_grid_generator_e($prefill['cardTitle']) ?>">
                </div>

                <div class="form__field form__field--span-full">
                    <label class="form__label" for="grid-card-text">Kachel-Text</label>
                    <textarea class="form__control" id="grid-card-text" name="text" rows="3"><?= vdb_grid_generator_e($prefill['text']) ?></textarea>
                </div>

                <div class="form__field">
                    <label class="form__label" for="grid-show-tags">Tags</label>
                    <select class="form__control" id="grid-show-tags" name="showTags">
                        <option value="0" <?= $prefill['showTags'] === '0' ? 'selected' : '' ?>>Nicht ausgeben</option>
                        <option value="1" <?= $prefill['showTags'] === '1' ? 'selected' : '' ?>>Tags ausgeben</option>
                    </select>
                </div>

                <div class="form__actions form__actions--right form__field--span-full">
                    <button class="btn btn--primary-light btn--md" type="reset" id="grid-generator-reset">Zurücksetzen</button>
                    <button class="btn btn--primary btn--md" type="button" id="grid-generator-jump-code">HTML-Code ansehen</button>
                </div>
            </div>
        </form>
    </div>
</section>

<section id="grid-generator-preview" class="section--surface long-width">
    <div class="container container--text-only container--wide">
        <div class="form form--card">
            <div class="form__grid form__grid--2">
                <div class="form__title">
                    <h2>Vorschau</h2>
                    <p>Die Vorschau aktualisiert sich automatisch. Klicks auf Vorschau-Links werden verhindert.</p>
                </div>

                <div class="form__notice form__notice--info form__field--span-full">
                    <strong>Live-Vorschau:</strong>
                    Prüfe hier Raster, Kachelvarianten, Header und Listenwirkung.
                </div>

                <div class="form__field form__field--span-full">
                    <label class="form__label">Darstellung</label>
                    <div id="grid-preview" class="form__notice"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="grid-generator-code" class="long-width">
    <div class="container container--text-only container--wide">
        <div class="form form--card">
            <div class="form__grid form__grid--1">
                <div class="form__title">
                    <h2>HTML-Code</h2>
                    <p>Kopiere den fertigen Code und füge ihn an der gewünschten Stelle ein.</p>
                </div>

                <div class="form__field">
                    <label class="form__label" for="grid-example-code">Generierter Code</label>
                    <textarea class="form__control" id="grid-example-code" rows="18" readonly></textarea>
                </div>

                <div class="form__actions form__actions--right">
                    <button class="btn btn--primary btn--md" type="button" id="copy-grid-code">Code kopieren</button>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
(function(){
    const form = document.getElementById('grid-generator-form');
    const preview = document.getElementById('grid-preview');
    const example = document.getElementById('grid-example-code');
    const copyBtn = document.getElementById('copy-grid-code');
    const jumpBtn = document.getElementById('grid-generator-jump-code');

    const fields = {
        output: document.getElementById('grid-output'),
        gridColumns: document.getElementById('grid-columns'),
        gridGap: document.getElementById('grid-gap'),
        gridAlign: document.getElementById('grid-align'),
        gridDense: document.getElementById('grid-dense'),
        itemCount: document.getElementById('grid-item-count'),
        firstItemSpan: document.getElementById('grid-first-item-span'),
        wrapBlock: document.getElementById('grid-wrap-block'),
        blockSpacing: document.getElementById('grid-block-spacing'),
        headerVariant: document.getElementById('grid-header-variant'),
        kicker: document.getElementById('grid-kicker'),
        title: document.getElementById('grid-title'),
        subtitle: document.getElementById('grid-subtitle'),
        showAction: document.getElementById('grid-show-action'),
        actionLabel: document.getElementById('grid-action-label'),
        actionHref: document.getElementById('grid-action-href'),
        cardElement: document.getElementById('grid-card-element'),
        href: document.getElementById('grid-card-href'),
        cardVariant: document.getElementById('grid-card-variant'),
        cardSize: document.getElementById('grid-card-size'),
        cardLayout: document.getElementById('grid-card-layout'),
        actionVariant: document.getElementById('grid-action-variant'),
        mediaMode: document.getElementById('grid-media-mode'),
        mediaRatio: document.getElementById('grid-media-ratio'),
        mediaFit: document.getElementById('grid-media-fit'),
        imageSrc: document.getElementById('grid-image-src'),
        imageAlt: document.getElementById('grid-image-alt'),
        credit: document.getElementById('grid-credit'),
        creditPosition: document.getElementById('grid-credit-position'),
        meta: document.getElementById('grid-meta'),
        cardTitle: document.getElementById('grid-card-title'),
        text: document.getElementById('grid-card-text'),
        showTags: document.getElementById('grid-show-tags'),
        composition: document.getElementById('grid-composition'),
        includePager: document.getElementById('grid-include-pager'),
    };

    function escapeHtml(value) {
        return String(value || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function attr(value) {
        return escapeHtml(value || '');
    }

    function indent(code, level = 1) {
        const prefix = '    '.repeat(level);
        return code.split('\n').map((line) => line ? prefix + line : line).join('\n');
    }

    function classes(list) {
        return list.filter(Boolean).join(' ');
    }

    function value(name) {
        return fields[name] ? fields[name].value : '';
    }

    function icon(name) {
        return `<svg class="vdb-icon" aria-hidden="true">\n    <use href="/assets/icons/vdb-icons.svg#${attr(name)}"></use>\n</svg>`;
    }

    function buildActionIcon() {
        return `<span class="tile-card__action" aria-hidden="true">\n${indent(icon('icon-arrow-right'), 1)}\n</span>`;
    }

    function buildMedia(index) {
        if (value('mediaMode') === 'no-media') {
            return '';
        }

        const figureClasses = classes([
            'tile-card__media',
            value('mediaRatio'),
            value('mediaFit'),
        ]);
        const src = value('imageSrc') || `/assets/images/news/news-${index}.jpg`;
        const alt = value('imageAlt');
        const credit = value('credit');
        const creditClass = classes(['tile-card__credit', value('creditPosition')]);

        let html = `<figure class="${figureClasses}">`;
        html += `\n    <img src="${attr(src)}" alt="${attr(alt)}">`;
        if (credit) {
            html += `\n    <figcaption class="${creditClass}">${escapeHtml(credit)}</figcaption>`;
        }
        html += `\n</figure>`;
        return html;
    }

    function buildTags() {
        if (value('showTags') !== '1') {
            return '';
        }
        return `<ul class="tile-card__tags">\n    <li class="tile-card__tag">Tag</li>\n    <li class="tile-card__tag">Thema</li>\n</ul>`;
    }

    function buildCard(index, withGridItem = true) {
        const isLink = value('cardElement') === 'a';
        const element = isLink ? 'a' : 'article';
        const firstSpan = index === 1 ? value('firstItemSpan') : '';
        const noMedia = value('mediaMode') === 'no-media' ? 'tile-card--no-media' : '';
        const cardClasses = classes([
            withGridItem ? 'grid-item' : '',
            withGridItem ? firstSpan : '',
            'tile-card',
            isLink ? 'tile-card--link' : '',
            value('cardVariant'),
            value('cardSize'),
            value('cardLayout'),
            value('actionVariant'),
            noMedia,
        ]);

        const title = value('cardTitle') || 'Titel der Meldung';
        const meta = value('meta');
        const text = value('text');

        let body = '<div class="tile-card__body">';
        if (meta) {
            body += `\n    <p class="tile-card__meta">${escapeHtml(meta)}</p>`;
        }
        body += `\n    <h3 class="tile-card__title">${escapeHtml(index > 1 ? title + ' ' + index : title)}</h3>`;
        if (text) {
            body += `\n    <p class="tile-card__text">${escapeHtml(text)}</p>`;
        }
        const tags = buildTags();
        if (tags) {
            body += `\n${indent(tags, 1)}`;
        }
        body += '\n</div>';

        const attrs = isLink ? ` class="${cardClasses}" href="${attr(value('href') || '#')}"` : ` class="${cardClasses}"`;
        let html = `<${element}${attrs}>`;
        const media = buildMedia(index);
        if (media) {
            html += `\n${indent(media, 1)}`;
        }
        html += `\n${indent(body, 1)}`;
        if (isLink) {
            html += `\n${indent(buildActionIcon(), 1)}`;
        }
        html += `\n</${element}>`;
        return html;
    }

    function buildGrid() {
        const gridClasses = classes([
            'grid',
            value('gridColumns'),
            value('gridGap'),
            value('gridAlign'),
            value('gridDense') === '1' ? 'grid--dense' : '',
        ]);
        const count = parseInt(value('itemCount'), 10) || 3;
        const items = [];
        for (let i = 1; i <= count; i++) {
            items.push(indent(buildCard(i), 1));
        }
        return `<div class="${gridClasses}">\n${items.join('\n\n')}\n</div>`;
    }

    function buildHeader() {
        const kicker = value('kicker').trim();
        const title = value('title').trim();
        const subtitle = value('subtitle').trim();
        const showAction = value('showAction') === '1' || value('headerVariant') === 'grid-block--split';

        if (!kicker && !title && !subtitle && !showAction) {
            return '';
        }

        let html = '<header class="grid-block__header">';
        if (kicker) {
            html += `\n    <p class="grid-block__kicker">${escapeHtml(kicker)}</p>`;
        }
        if (title) {
            html += `\n    <h2 class="grid-block__title">${escapeHtml(title)}</h2>`;
        }
        if (subtitle) {
            html += `\n    <p class="grid-block__subtitle">${escapeHtml(subtitle)}</p>`;
        }
        if (showAction) {
            html += `\n    <div class="grid-block__actions btn-group btn-group--horizontal btn-group--gap-sm">`;
            html += `\n        <a class="btn btn--primary btn--md" href="${attr(value('actionHref') || '#')}">${escapeHtml(value('actionLabel') || 'Mehr erfahren')}</a>`;
            html += '\n    </div>';
        }
        html += '\n</header>';
        return html;
    }

    function wrapGridBlock(content) {
        if (value('wrapBlock') !== '1') {
            return content;
        }
        const blockClasses = classes(['grid-block', value('blockSpacing'), value('headerVariant')]);
        const header = buildHeader();
        let html = `<div class="${blockClasses}">`;
        if (header) {
            html += `\n${indent(header, 1)}\n`;
        }
        html += `\n${indent(content, 1)}`;
        html += '\n</div>';
        return html;
    }

    function buildNewsList() {
        const count = parseInt(value('itemCount'), 10) || 3;
        let html = '<div class="news-panel">';
        html += '\n    <h3 class="news-panel__title">News</h3>';
        html += '\n    <div class="news-list">';
        for (let i = 1; i <= count; i++) {
            html += `\n        <a href="${attr(value('href') || '#')}" class="news-list__item">`;
            html += `\n            <span class="news-list__date">${String(10 + i).padStart(2, '0')}.06.2026</span>`;
            html += `\n            <h4 class="news-list__title">${escapeHtml((value('cardTitle') || 'Titel der Meldung') + ' ' + i)}</h4>`;
            html += `\n            <p class="news-list__text">${escapeHtml(value('text') || 'Kurzbeschreibung der Meldung.')}</p>`;
            html += '\n        </a>';
        }
        html += '\n    </div>';
        html += '\n</div>';
        return html;
    }

    function buildEventList() {
        const count = parseInt(value('itemCount'), 10) || 3;
        let html = '<div class="event-panel">';
        html += '\n    <h3 class="event-panel__title">Veranstaltungen</h3>';
        html += '\n    <div class="event-list">';
        for (let i = 1; i <= count; i++) {
            html += `\n        <a href="${attr(value('href') || '#')}" class="event-item">`;
            html += `\n            <time class="event-item__date" datetime="2026-06-${String(10 + i).padStart(2, '0')}">`;
            html += `\n                <span class="event-item__day">${10 + i}. Jun</span>`;
            html += '\n                <span class="event-item__time">14:00</span>';
            html += '\n            </time>';
            html += '\n            <span class="event-item__content">';
            html += `\n                <span class="event-item__title">${escapeHtml((value('cardTitle') || 'Veranstaltung') + ' ' + i)}</span>`;
            html += '\n            </span>';
            html += '\n        </a>';
        }
        html += '\n    </div>';
        html += '\n</div>';
        return html;
    }

    function buildComposition() {
        const compositionClasses = classes(['grid-composition', value('composition')]);
        const card = buildCard(1, false);
        const list = value('output') === 'composition' ? buildNewsList() : '';

        if (value('composition') === 'grid-composition--list-feature') {
            return `<div class="${compositionClasses}">\n${indent(buildNewsList(), 1)}\n\n${indent(card, 1)}\n</div>`;
        }

        if (value('composition') === 'grid-composition--equal') {
            return `<div class="${compositionClasses}">\n${indent(buildGrid(), 1)}\n\n${indent(buildNewsList(), 1)}\n</div>`;
        }

        return `<div class="${compositionClasses}">\n${indent(card, 1)}\n\n${indent(list, 1)}\n</div>`;
    }

    function buildPager() {
        if (value('includePager') !== '1') {
            return '';
        }
        return `<div class="grid-pager" aria-label="Pager">\n    <button class="grid-pager__button" type="button" aria-label="Vorherige Seite">\n${indent(icon('icon-arrow-left'), 2)}\n    </button>\n    <span class="grid-pager__count">1 / 4</span>\n    <button class="grid-pager__button" type="button" aria-label="Nächste Seite">\n${indent(icon('icon-arrow-right'), 2)}\n    </button>\n</div>`;
    }

    function buildOutput() {
        let content;
        if (value('output') === 'events') {
            content = buildEventList();
        } else if (value('output') === 'news') {
            content = buildNewsList();
        } else if (value('output') === 'composition') {
            content = buildComposition();
        } else {
            content = buildGrid();
        }

        const pager = buildPager();
        if (pager) {
            content += `\n\n${pager}`;
        }

        return wrapGridBlock(content);
    }

    function update() {
        const html = buildOutput();
        preview.innerHTML = html;
        example.value = html;
    }

    Object.keys(fields).forEach(function(key) {
        const field = fields[key];
        field.addEventListener('input', update);
        field.addEventListener('change', update);
    });

    preview.addEventListener('click', function(event) {
        event.preventDefault();
    });

    form.addEventListener('reset', function() {
        window.setTimeout(update, 0);
    });

    jumpBtn.addEventListener('click', function() {
        document.getElementById('grid-generator-code').scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    copyBtn.addEventListener('click', function() {
        const text = example.value;
        navigator.clipboard.writeText(text).then(function() {
            copyBtn.textContent = 'Kopiert!';
            setTimeout(function() {
                copyBtn.textContent = 'Code kopieren';
            }, 1500);
        });
    });

    update();
})();
</script>
