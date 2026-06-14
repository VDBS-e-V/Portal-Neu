<?php
declare(strict_types=1);

$prefill = $prefill ?? [
    'output' => 'image',
    'sectionClass' => 'long-width no-padding',
    'heroTone' => '',
    'heroSize' => '',
    'heroAlign' => 'hero--center',
    'heroVerticalAlign' => '',
    'overlay' => 'hero--overlay-dark',
    'imageRatio' => 'hero--ratio-21-9',
    'mediaRatio' => 'hero__media--ratio-4-3',
    'mediaFit' => '',
    'mediaLeft' => '0',
    'imageSrc' => '/assets/images/heros/portal-home.png',
    'imageAlt' => '',
    'bgImage' => '/assets/images/heros/portal-home.png',
    'bgPosition' => 'center center',
    'kicker' => 'Serviceportal',
    'eyebrow' => '',
    'title' => 'Willkommen im VDBS Serviceportal',
    'lead' => 'Zentrale Services, Zugang zu Ihrem Konto und Hilfestellungen — schnell und übersichtlich.',
    'leadBadge' => '1',
    'actions' => '2',
    'primaryLabel' => 'Zugang zum Portal',
    'primaryHref' => '#',
    'primaryVariant' => 'btn--primary',
    'secondaryLabel' => 'Hilfe',
    'secondaryHref' => '#',
    'secondaryVariant' => 'btn--primary-light',
    'showCredit' => '0',
    'credit' => 'Bild: Beispiel',
    'includeTiles' => '0',
    'tileCount' => '4',
    'tileStyle' => 'legacy',
    'includeCards' => '0',
    'cardCount' => '3',
    'cardColumns' => 'hero__cards--3',
    'cardVariant' => 'hero-card--primary',
    'panelVariant' => 'hero__panel--ink',
    'panelTitle' => 'Gemeinsam für bessere Bildung',
    'panelText' => 'Ein redaktioneller Einstieg mit starkem Bild und kompaktem Infopanel.',
    'panelMeta' => 'Bild: Beispiel',
    'panelActionLabel' => 'Weitere Informationen',
    'panelActionHref' => '#',
    'heading' => 'Senatsverwaltung für Bildung, Jugend und Familie',
];

if (!function_exists('vdb_hero_generator_e')) {
    function vdb_hero_generator_e(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}

$outputModes = [
    'minimal' => 'Minimaler Text-Hero',
    'image' => 'Bild-Hero mit echtem Bild — .hero--image',
    'imageBg' => 'Hintergrundbild-Hero — .hero--image-bg',
    'split' => 'Split-Hero — .hero--split',
    'editorial' => 'Editorial-Hero mit Panel — .hero--editorial',
    'tilesOnly' => 'Kachel-Hero ohne Bühne — .hero--tiles-only',
];

$sectionClasses = [
    'long-width no-padding' => 'Volle Breite ohne Padding — .long-width .no-padding',
    'section--surface' => 'Section Surface — .section--surface',
    '' => 'Kein äußerer Section-Modifier',
];

$heroTones = [
    '' => 'Standard / Weiß',
    'hero--surface' => 'Surface — .hero--surface',
    'hero--white' => 'White — .hero--white',
    'hero--ink' => 'Ink — .hero--ink',
    'hero--primary' => 'Primary — .hero--primary',
    'hero--secondary-cta' => 'Secondary CTA — .hero--secondary-cta',
    'hero--secondary-highlight' => 'Secondary Highlight — .hero--secondary-highlight',
    'hero--plum' => 'Plum Alias — .hero--plum',
];

$heroSizes = [
    '' => 'Standard',
    'hero--compact' => 'Kompakt — .hero--compact',
    'hero--large' => 'Large — .hero--large',
    'hero--full' => 'Full — .hero--full',
    'hero--portal' => 'Portal — .hero--portal',
];

$heroAlign = [
    'hero--left' => 'Links — .hero--left',
    'hero--center' => 'Zentriert — .hero--center',
    'hero--right' => 'Rechts — .hero--right',
];

$heroVerticalAlign = [
    '' => 'Standard',
    'hero--align-start' => 'Oben — .hero--align-start',
    'hero--align-center' => 'Vertikal mittig — .hero--align-center',
    'hero--align-end' => 'Unten — .hero--align-end',
];

$overlays = [
    '' => 'Helles Overlay / Standard',
    'hero--overlay-dark' => 'Dunkles Overlay — .hero--overlay-dark',
    'hero--overlay-none' => 'Kein Overlay — .hero--overlay-none',
];

$imageRatios = [
    '' => 'Keine feste Ratio',
    'hero--ratio-21-9' => '21:9 — .hero--ratio-21-9',
    'hero--ratio-16-9' => '16:9 — .hero--ratio-16-9',
    'hero--ratio-3-2' => '3:2 — .hero--ratio-3-2',
    'hero--ratio-4-3' => '4:3 — .hero--ratio-4-3',
    'hero--ratio-1-1' => '1:1 — .hero--ratio-1-1',
];

$mediaRatios = [
    '' => 'Keine feste Ratio',
    'hero__media--ratio-16-9' => '16:9 — .hero__media--ratio-16-9',
    'hero__media--ratio-4-3' => '4:3 — .hero__media--ratio-4-3',
    'hero__media--ratio-3-2' => '3:2 — .hero__media--ratio-3-2',
    'hero__media--ratio-1-1' => '1:1 — .hero__media--ratio-1-1',
];

$mediaFits = [
    '' => 'Cover / Standard',
    'hero__media--contain' => 'Contain — .hero__media--contain',
];

$actionCounts = [
    '0' => 'Keine Buttons',
    '1' => 'Ein Button',
    '2' => 'Zwei Buttons',
];

$buttonVariants = [
    'btn--primary' => 'Primary — .btn--primary',
    'btn--primary-light' => 'Primary Light — .btn--primary-light',
    'btn--primary-transp' => 'Primary Transparent — .btn--primary-transp',
    'btn--secondary-cta' => 'Secondary CTA — .btn--secondary-cta',
    'btn--secondary-cta-light' => 'Secondary CTA Light — .btn--secondary-cta-light',
    'btn--secondary-highlight' => 'Secondary Highlight — .btn--secondary-highlight',
    'btn--outline' => 'Outline — .btn--outline',
    'btn--ghost' => 'Ghost — .btn--ghost',
];

$panelVariants = [
    'hero__panel--ink' => 'Ink — .hero__panel--ink',
    'hero__panel--primary' => 'Primary — .hero__panel--primary',
    'hero__panel--secondary-cta' => 'Secondary CTA — .hero__panel--secondary-cta',
];

$cardColumns = [
    'hero__cards--2' => '2 Spalten — .hero__cards--2',
    'hero__cards--3' => '3 Spalten — .hero__cards--3',
    'hero__cards--4' => '4 Spalten — .hero__cards--4',
];

$cardVariants = [
    '' => 'Standard',
    'hero-card--primary' => 'Primary — .hero-card--primary',
    'hero-card--secondary-cta' => 'Secondary CTA — .hero-card--secondary-cta',
    'hero-card--secondary-highlight' => 'Secondary Highlight — .hero-card--secondary-highlight',
    'hero-card--plum' => 'Plum Alias — .hero-card--plum',
    'hero-card--white' => 'White — .hero-card--white',
];

$tileStyles = [
    'legacy' => 'Legacy A–E — .hero__tile--a bis --e',
    'semantic' => 'Semantisch — primary / secondary / ink',
    'accent' => 'Akzentfarben — amber / teal / berry / sun / lime',
];
?>

<section id="hero-generator-intro" class="section--surface">
    <div class="container container--text-only container--narrow container--center">
        <div class="container__text">
            <h1>Hero Generator</h1>

            <p>
                Wähle Hero-Typ, Bildlogik, Fläche, Ausrichtung, Buttons, Cards,
                Panel und Schnellzugriff. Der fertige HTML-Code wird live erzeugt
                und kann direkt kopiert werden.
            </p>
        </div>

        <div class="container__buttons btn-group btn-group--vertical btn-group--main-center btn-group--gap-sm">
            <a href="#hero-generator-form-section" class="btn btn--primary btn--md">Generator öffnen</a>
            <a href="#hero-generator-code" class="btn btn--primary-transp btn--md">Zum HTML-Code</a>
        </div>
    </div>
</section>

<section id="hero-generator-form-section" class="long-width">
    <div class="container container--text-only container--wide">
        <form id="hero-generator-form" class="form form--card">
            <div class="form__grid form__grid--4">
                <div class="form__title form__title--center">
                    <h2>Hero konfigurieren</h2>
                    <p>
                        Die Felder nutzen das Formularsystem aus <code>forms.css</code>.
                        Die Ausgabe nutzt Klassen aus <code>hero.css</code> sowie optional
                        Buttons aus <code>buttons.css</code>.
                    </p>
                </div>

                <div class="form__notice form__notice--info form__field--span-full">
                    <strong>Hinweis:</strong>
                    Für neue Bild-Heros ist <code>.hero--image</code> mit echtem
                    <code>&lt;img&gt;</code> meistens besser. <code>.hero--image-bg</code>
                    bleibt für feste Höhen und bestehende Templates verfügbar.
                </div>

                <div class="form__section-title form__section-title--primary">
                    <h3>Ausgabe</h3>
                    <p>Wähle den grundsätzlichen Hero-Typ und den äußeren Section-Rahmen.</p>
                </div>

                <div class="form__field form__field--span-2">
                    <label class="form__label" for="hero-output">Hero-Typ</label>
                    <select class="form__control" id="hero-output" name="output">
                        <?php foreach ($outputModes as $value => $label): ?>
                            <option value="<?= vdb_hero_generator_e($value) ?>" <?= $value === $prefill['output'] ? 'selected' : '' ?>><?= vdb_hero_generator_e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form__field form__field--span-2">
                    <label class="form__label" for="hero-section-class">Äußere Section</label>
                    <select class="form__control" id="hero-section-class" name="sectionClass">
                        <?php foreach ($sectionClasses as $class => $label): ?>
                            <option value="<?= vdb_hero_generator_e($class) ?>" <?= $class === $prefill['sectionClass'] ? 'selected' : '' ?>><?= vdb_hero_generator_e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form__section-title form__section-title--secondary-cta">
                    <h3>Hero-Fläche</h3>
                    <p>Bestimme Farbe, Größe, horizontale und vertikale Ausrichtung.</p>
                </div>

                <div class="form__field">
                    <label class="form__label" for="hero-tone">Farbe / Fläche</label>
                    <select class="form__control" id="hero-tone" name="heroTone">
                        <?php foreach ($heroTones as $class => $label): ?>
                            <option value="<?= vdb_hero_generator_e($class) ?>" <?= $class === $prefill['heroTone'] ? 'selected' : '' ?>><?= vdb_hero_generator_e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form__field">
                    <label class="form__label" for="hero-size">Größe</label>
                    <select class="form__control" id="hero-size" name="heroSize">
                        <?php foreach ($heroSizes as $class => $label): ?>
                            <option value="<?= vdb_hero_generator_e($class) ?>" <?= $class === $prefill['heroSize'] ? 'selected' : '' ?>><?= vdb_hero_generator_e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form__field">
                    <label class="form__label" for="hero-align">Ausrichtung</label>
                    <select class="form__control" id="hero-align" name="heroAlign">
                        <?php foreach ($heroAlign as $class => $label): ?>
                            <option value="<?= vdb_hero_generator_e($class) ?>" <?= $class === $prefill['heroAlign'] ? 'selected' : '' ?>><?= vdb_hero_generator_e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form__field">
                    <label class="form__label" for="hero-vertical-align">Vertikale Ausrichtung</label>
                    <select class="form__control" id="hero-vertical-align" name="heroVerticalAlign">
                        <?php foreach ($heroVerticalAlign as $class => $label): ?>
                            <option value="<?= vdb_hero_generator_e($class) ?>" <?= $class === $prefill['heroVerticalAlign'] ? 'selected' : '' ?>><?= vdb_hero_generator_e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form__section-title form__section-title--secondary-highlight">
                    <h3>Text</h3>
                    <p>Lege Kicker, Eyebrow, Titel, Lead und optional Badge fest.</p>
                </div>

                <div class="form__field form__field--span-2">
                    <label class="form__label" for="hero-kicker">Kicker</label>
                    <input class="form__control" type="text" id="hero-kicker" name="kicker" value="<?= vdb_hero_generator_e($prefill['kicker']) ?>">
                </div>

                <div class="form__field form__field--span-2">
                    <label class="form__label" for="hero-eyebrow">Eyebrow</label>
                    <input class="form__control" type="text" id="hero-eyebrow" name="eyebrow" value="<?= vdb_hero_generator_e($prefill['eyebrow']) ?>">
                </div>

                <div class="form__field form__field--span-full">
                    <label class="form__label" for="hero-title">Titel</label>
                    <input class="form__control" type="text" id="hero-title" name="title" value="<?= vdb_hero_generator_e($prefill['title']) ?>">
                </div>

                <div class="form__field form__field--span-full">
                    <label class="form__label" for="hero-lead">Lead</label>
                    <textarea class="form__control" id="hero-lead" name="lead" rows="3"><?= vdb_hero_generator_e($prefill['lead']) ?></textarea>
                </div>

                <div class="form__field">
                    <label class="form__label" for="hero-lead-badge">Lead-Badge</label>
                    <select class="form__control" id="hero-lead-badge" name="leadBadge">
                        <option value="0" <?= $prefill['leadBadge'] === '0' ? 'selected' : '' ?>>Normaler Lead</option>
                        <option value="1" <?= $prefill['leadBadge'] === '1' ? 'selected' : '' ?>>Als .hero__lead__badge</option>
                    </select>
                </div>

                <div class="form__section-title">
                    <h3>Aktionen</h3>
                    <p>Optional werden Buttons in <code>.hero__actions</code> ausgegeben.</p>
                </div>

                <div class="form__field">
                    <label class="form__label" for="hero-actions">Buttons</label>
                    <select class="form__control" id="hero-actions" name="actions">
                        <?php foreach ($actionCounts as $value => $label): ?>
                            <option value="<?= vdb_hero_generator_e($value) ?>" <?= $value === $prefill['actions'] ? 'selected' : '' ?>><?= vdb_hero_generator_e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form__field">
                    <label class="form__label" for="hero-primary-label">Button 1 Text</label>
                    <input class="form__control" type="text" id="hero-primary-label" name="primaryLabel" value="<?= vdb_hero_generator_e($prefill['primaryLabel']) ?>">
                </div>

                <div class="form__field">
                    <label class="form__label" for="hero-primary-href">Button 1 href</label>
                    <input class="form__control" type="text" id="hero-primary-href" name="primaryHref" value="<?= vdb_hero_generator_e($prefill['primaryHref']) ?>">
                </div>

                <div class="form__field">
                    <label class="form__label" for="hero-primary-variant">Button 1 Variante</label>
                    <select class="form__control" id="hero-primary-variant" name="primaryVariant">
                        <?php foreach ($buttonVariants as $class => $label): ?>
                            <option value="<?= vdb_hero_generator_e($class) ?>" <?= $class === $prefill['primaryVariant'] ? 'selected' : '' ?>><?= vdb_hero_generator_e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form__field">
                    <label class="form__label" for="hero-secondary-label">Button 2 Text</label>
                    <input class="form__control" type="text" id="hero-secondary-label" name="secondaryLabel" value="<?= vdb_hero_generator_e($prefill['secondaryLabel']) ?>">
                </div>

                <div class="form__field">
                    <label class="form__label" for="hero-secondary-href">Button 2 href</label>
                    <input class="form__control" type="text" id="hero-secondary-href" name="secondaryHref" value="<?= vdb_hero_generator_e($prefill['secondaryHref']) ?>">
                </div>

                <div class="form__field">
                    <label class="form__label" for="hero-secondary-variant">Button 2 Variante</label>
                    <select class="form__control" id="hero-secondary-variant" name="secondaryVariant">
                        <?php foreach ($buttonVariants as $class => $label): ?>
                            <option value="<?= vdb_hero_generator_e($class) ?>" <?= $class === $prefill['secondaryVariant'] ? 'selected' : '' ?>><?= vdb_hero_generator_e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form__section-title form__section-title--primary">
                    <h3>Bild und Medien</h3>
                    <p>Diese Felder greifen je nach Hero-Typ für Background, Stage oder Split-Media.</p>
                </div>

                <div class="form__field">
                    <label class="form__label" for="hero-overlay">Overlay</label>
                    <select class="form__control" id="hero-overlay" name="overlay">
                        <?php foreach ($overlays as $class => $label): ?>
                            <option value="<?= vdb_hero_generator_e($class) ?>" <?= $class === $prefill['overlay'] ? 'selected' : '' ?>><?= vdb_hero_generator_e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form__field">
                    <label class="form__label" for="hero-image-ratio">Bild-Hero Ratio</label>
                    <select class="form__control" id="hero-image-ratio" name="imageRatio">
                        <?php foreach ($imageRatios as $class => $label): ?>
                            <option value="<?= vdb_hero_generator_e($class) ?>" <?= $class === $prefill['imageRatio'] ? 'selected' : '' ?>><?= vdb_hero_generator_e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form__field">
                    <label class="form__label" for="hero-media-ratio">Split-Media Ratio</label>
                    <select class="form__control" id="hero-media-ratio" name="mediaRatio">
                        <?php foreach ($mediaRatios as $class => $label): ?>
                            <option value="<?= vdb_hero_generator_e($class) ?>" <?= $class === $prefill['mediaRatio'] ? 'selected' : '' ?>><?= vdb_hero_generator_e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form__field">
                    <label class="form__label" for="hero-media-fit">Media Fit</label>
                    <select class="form__control" id="hero-media-fit" name="mediaFit">
                        <?php foreach ($mediaFits as $class => $label): ?>
                            <option value="<?= vdb_hero_generator_e($class) ?>" <?= $class === $prefill['mediaFit'] ? 'selected' : '' ?>><?= vdb_hero_generator_e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form__field form__field--span-2">
                    <label class="form__label" for="hero-image-src">Bild src</label>
                    <input class="form__control" type="text" id="hero-image-src" name="imageSrc" value="<?= vdb_hero_generator_e($prefill['imageSrc']) ?>">
                </div>

                <div class="form__field form__field--span-2">
                    <label class="form__label" for="hero-image-alt">Bild alt</label>
                    <input class="form__control" type="text" id="hero-image-alt" name="imageAlt" value="<?= vdb_hero_generator_e($prefill['imageAlt']) ?>">
                </div>

                <div class="form__field form__field--span-2">
                    <label class="form__label" for="hero-bg-image">Background Image URL</label>
                    <input class="form__control" type="text" id="hero-bg-image" name="bgImage" value="<?= vdb_hero_generator_e($prefill['bgImage']) ?>">
                    <p class="form__hint">Wird nur für <code>.hero--image-bg</code> ausgegeben.</p>
                </div>

                <div class="form__field">
                    <label class="form__label" for="hero-bg-position">Background Position</label>
                    <input class="form__control" type="text" id="hero-bg-position" name="bgPosition" value="<?= vdb_hero_generator_e($prefill['bgPosition']) ?>">
                </div>

                <div class="form__field">
                    <label class="form__label" for="hero-media-left">Split-Bild</label>
                    <select class="form__control" id="hero-media-left" name="mediaLeft">
                        <option value="0" <?= $prefill['mediaLeft'] === '0' ? 'selected' : '' ?>>Rechts</option>
                        <option value="1" <?= $prefill['mediaLeft'] === '1' ? 'selected' : '' ?>>Links — .hero--media-left</option>
                    </select>
                </div>

                <div class="form__field">
                    <label class="form__label" for="hero-show-credit">Bildnachweis</label>
                    <select class="form__control" id="hero-show-credit" name="showCredit">
                        <option value="0" <?= $prefill['showCredit'] === '0' ? 'selected' : '' ?>>Nicht ausgeben</option>
                        <option value="1" <?= $prefill['showCredit'] === '1' ? 'selected' : '' ?>>Ausgeben — .hero__credit</option>
                    </select>
                </div>

                <div class="form__field form__field--span-3">
                    <label class="form__label" for="hero-credit">Credit</label>
                    <input class="form__control" type="text" id="hero-credit" name="credit" value="<?= vdb_hero_generator_e($prefill['credit']) ?>">
                </div>

                <div class="form__section-title form__section-title--secondary-cta">
                    <h3>Editorial Panel</h3>
                    <p>Diese Felder werden für <code>.hero--editorial</code> genutzt.</p>
                </div>

                <div class="form__field form__field--span-full">
                    <label class="form__label" for="hero-heading">Editorial Heading</label>
                    <input class="form__control" type="text" id="hero-heading" name="heading" value="<?= vdb_hero_generator_e($prefill['heading']) ?>">
                </div>

                <div class="form__field">
                    <label class="form__label" for="hero-panel-variant">Panel-Variante</label>
                    <select class="form__control" id="hero-panel-variant" name="panelVariant">
                        <?php foreach ($panelVariants as $class => $label): ?>
                            <option value="<?= vdb_hero_generator_e($class) ?>" <?= $class === $prefill['panelVariant'] ? 'selected' : '' ?>><?= vdb_hero_generator_e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form__field form__field--span-3">
                    <label class="form__label" for="hero-panel-title">Panel-Titel</label>
                    <input class="form__control" type="text" id="hero-panel-title" name="panelTitle" value="<?= vdb_hero_generator_e($prefill['panelTitle']) ?>">
                </div>

                <div class="form__field form__field--span-full">
                    <label class="form__label" for="hero-panel-text">Panel-Text</label>
                    <textarea class="form__control" id="hero-panel-text" name="panelText" rows="3"><?= vdb_hero_generator_e($prefill['panelText']) ?></textarea>
                </div>

                <div class="form__field form__field--span-2">
                    <label class="form__label" for="hero-panel-meta">Panel-Meta</label>
                    <input class="form__control" type="text" id="hero-panel-meta" name="panelMeta" value="<?= vdb_hero_generator_e($prefill['panelMeta']) ?>">
                </div>

                <div class="form__field">
                    <label class="form__label" for="hero-panel-action-label">Panel-Link Text</label>
                    <input class="form__control" type="text" id="hero-panel-action-label" name="panelActionLabel" value="<?= vdb_hero_generator_e($prefill['panelActionLabel']) ?>">
                </div>

                <div class="form__field">
                    <label class="form__label" for="hero-panel-action-href">Panel-Link href</label>
                    <input class="form__control" type="text" id="hero-panel-action-href" name="panelActionHref" value="<?= vdb_hero_generator_e($prefill['panelActionHref']) ?>">
                </div>

                <div class="form__section-title form__section-title--secondary-highlight">
                    <h3>Cards und Schnellzugriff</h3>
                    <p>Optional können Cards oder Schnellzugriff-Tiles ergänzt werden.</p>
                </div>

                <div class="form__field">
                    <label class="form__label" for="hero-include-cards">Hero-Cards</label>
                    <select class="form__control" id="hero-include-cards" name="includeCards">
                        <option value="0" <?= $prefill['includeCards'] === '0' ? 'selected' : '' ?>>Nicht ausgeben</option>
                        <option value="1" <?= $prefill['includeCards'] === '1' ? 'selected' : '' ?>>Cards ausgeben</option>
                    </select>
                </div>

                <div class="form__field">
                    <label class="form__label" for="hero-card-count">Anzahl Cards</label>
                    <select class="form__control" id="hero-card-count" name="cardCount">
                        <?php foreach (['2', '3', '4'] as $count): ?>
                            <option value="<?= vdb_hero_generator_e($count) ?>" <?= $count === $prefill['cardCount'] ? 'selected' : '' ?>><?= vdb_hero_generator_e($count) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form__field">
                    <label class="form__label" for="hero-card-columns">Card-Spalten</label>
                    <select class="form__control" id="hero-card-columns" name="cardColumns">
                        <?php foreach ($cardColumns as $class => $label): ?>
                            <option value="<?= vdb_hero_generator_e($class) ?>" <?= $class === $prefill['cardColumns'] ? 'selected' : '' ?>><?= vdb_hero_generator_e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form__field">
                    <label class="form__label" for="hero-card-variant">Card-Variante</label>
                    <select class="form__control" id="hero-card-variant" name="cardVariant">
                        <?php foreach ($cardVariants as $class => $label): ?>
                            <option value="<?= vdb_hero_generator_e($class) ?>" <?= $class === $prefill['cardVariant'] ? 'selected' : '' ?>><?= vdb_hero_generator_e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form__field">
                    <label class="form__label" for="hero-include-tiles">Schnellzugriff-Tiles</label>
                    <select class="form__control" id="hero-include-tiles" name="includeTiles">
                        <option value="0" <?= $prefill['includeTiles'] === '0' ? 'selected' : '' ?>>Nicht ausgeben</option>
                        <option value="1" <?= $prefill['includeTiles'] === '1' ? 'selected' : '' ?>>Tiles ausgeben</option>
                    </select>
                </div>

                <div class="form__field">
                    <label class="form__label" for="hero-tile-count">Anzahl Tiles</label>
                    <select class="form__control" id="hero-tile-count" name="tileCount">
                        <?php foreach (['3', '4', '5'] as $count): ?>
                            <option value="<?= vdb_hero_generator_e($count) ?>" <?= $count === $prefill['tileCount'] ? 'selected' : '' ?>><?= vdb_hero_generator_e($count) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form__field form__field--span-2">
                    <label class="form__label" for="hero-tile-style">Tile-Stil</label>
                    <select class="form__control" id="hero-tile-style" name="tileStyle">
                        <?php foreach ($tileStyles as $value => $label): ?>
                            <option value="<?= vdb_hero_generator_e($value) ?>" <?= $value === $prefill['tileStyle'] ? 'selected' : '' ?>><?= vdb_hero_generator_e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form__actions form__actions--right">
                    <button class="btn btn--primary-light btn--md" type="reset" id="hero-generator-reset">Zurücksetzen</button>
                    <button class="btn btn--primary btn--md" type="button" id="hero-generator-jump-code">HTML-Code ansehen</button>
                </div>
            </div>
        </form>
    </div>
</section>

<section id="hero-generator-preview" class="section--surface long-width">
    <div class="container container--text-only container--wide">
        <div class="form form--card">
            <div class="form__grid form__grid--1">
                <div class="form__title">
                    <h2>Vorschau</h2>
                    <p>Die Vorschau aktualisiert sich automatisch. Klicks auf Vorschau-Links werden verhindert.</p>
                </div>

                <div class="form__notice form__notice--info">
                    <strong>Live-Vorschau:</strong>
                    Prüfe hier Hero-Typ, Text, Bild, Buttons, Cards und Schnellzugriff.
                </div>

                <div class="form__field">
                    <label class="form__label">Darstellung</label>
                    <div id="hero-preview" class="form__notice"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="hero-generator-code" class="long-width">
    <div class="container container--text-only container--wide">
        <div class="form form--card">
            <div class="form__grid form__grid--1">
                <div class="form__title">
                    <h2>HTML-Code</h2>
                    <p>Kopiere den fertigen Code und füge ihn an der gewünschten Stelle ein.</p>
                </div>

                <div class="form__field">
                    <label class="form__label" for="hero-example-code">Generierter Code</label>
                    <textarea class="form__control" id="hero-example-code" rows="20" readonly></textarea>
                </div>

                <div class="form__actions form__actions--right">
                    <button class="btn btn--primary btn--md" type="button" id="copy-hero-code">Code kopieren</button>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
(function(){
    const form = document.getElementById('hero-generator-form');
    const preview = document.getElementById('hero-preview');
    const example = document.getElementById('hero-example-code');
    const copyBtn = document.getElementById('copy-hero-code');
    const jumpBtn = document.getElementById('hero-generator-jump-code');

    const fields = {
        output: document.getElementById('hero-output'),
        sectionClass: document.getElementById('hero-section-class'),
        heroTone: document.getElementById('hero-tone'),
        heroSize: document.getElementById('hero-size'),
        heroAlign: document.getElementById('hero-align'),
        heroVerticalAlign: document.getElementById('hero-vertical-align'),
        overlay: document.getElementById('hero-overlay'),
        imageRatio: document.getElementById('hero-image-ratio'),
        mediaRatio: document.getElementById('hero-media-ratio'),
        mediaFit: document.getElementById('hero-media-fit'),
        mediaLeft: document.getElementById('hero-media-left'),
        imageSrc: document.getElementById('hero-image-src'),
        imageAlt: document.getElementById('hero-image-alt'),
        bgImage: document.getElementById('hero-bg-image'),
        bgPosition: document.getElementById('hero-bg-position'),
        kicker: document.getElementById('hero-kicker'),
        eyebrow: document.getElementById('hero-eyebrow'),
        title: document.getElementById('hero-title'),
        lead: document.getElementById('hero-lead'),
        leadBadge: document.getElementById('hero-lead-badge'),
        actions: document.getElementById('hero-actions'),
        primaryLabel: document.getElementById('hero-primary-label'),
        primaryHref: document.getElementById('hero-primary-href'),
        primaryVariant: document.getElementById('hero-primary-variant'),
        secondaryLabel: document.getElementById('hero-secondary-label'),
        secondaryHref: document.getElementById('hero-secondary-href'),
        secondaryVariant: document.getElementById('hero-secondary-variant'),
        showCredit: document.getElementById('hero-show-credit'),
        credit: document.getElementById('hero-credit'),
        includeTiles: document.getElementById('hero-include-tiles'),
        tileCount: document.getElementById('hero-tile-count'),
        tileStyle: document.getElementById('hero-tile-style'),
        includeCards: document.getElementById('hero-include-cards'),
        cardCount: document.getElementById('hero-card-count'),
        cardColumns: document.getElementById('hero-card-columns'),
        cardVariant: document.getElementById('hero-card-variant'),
        panelVariant: document.getElementById('hero-panel-variant'),
        panelTitle: document.getElementById('hero-panel-title'),
        panelText: document.getElementById('hero-panel-text'),
        panelMeta: document.getElementById('hero-panel-meta'),
        panelActionLabel: document.getElementById('hero-panel-action-label'),
        panelActionHref: document.getElementById('hero-panel-action-href'),
        heading: document.getElementById('hero-heading'),
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

    function buildActions() {
        const count = parseInt(value('actions'), 10) || 0;
        if (count < 1) {
            return '';
        }

        let html = '<div class="hero__actions btn-group btn-group--horizontal btn-group--gap-sm">';
        html += `\n    <a href="${attr(value('primaryHref') || '#')}" class="btn ${attr(value('primaryVariant') || 'btn--primary')} btn--md">${escapeHtml(value('primaryLabel') || 'Mehr erfahren')}</a>`;

        if (count > 1) {
            html += `\n    <a href="${attr(value('secondaryHref') || '#')}" class="btn ${attr(value('secondaryVariant') || 'btn--primary-light')} btn--md">${escapeHtml(value('secondaryLabel') || 'Weitere Informationen')}</a>`;
        }

        html += '\n</div>';
        return html;
    }

    function buildCopy(titleTag = 'h1') {
        const kicker = value('kicker').trim();
        const eyebrow = value('eyebrow').trim();
        const title = value('title').trim() || 'Hero Titel';
        const lead = value('lead').trim();

        let html = '<div class="hero__copy">';
        if (kicker) {
            html += `\n    <p class="hero__kicker">${escapeHtml(kicker)}</p>`;
        }
        if (eyebrow) {
            html += `\n    <p class="hero__eyebrow">${escapeHtml(eyebrow)}</p>`;
        }
        html += `\n    <${titleTag} class="hero__title">${escapeHtml(title)}</${titleTag}>`;
        if (lead) {
            if (value('leadBadge') === '1') {
                html += `\n    <p class="hero__lead">\n        <span class="hero__lead__badge">${escapeHtml(lead)}</span>\n    </p>`;
            } else {
                html += `\n    <p class="hero__lead">${escapeHtml(lead)}</p>`;
            }
        }
        const actions = buildActions();
        if (actions) {
            html += `\n${indent(actions, 1)}`;
        }
        html += '\n</div>';
        return html;
    }

    function buildCredit() {
        if (value('showCredit') !== '1' || !value('credit').trim()) {
            return '';
        }
        return `<figcaption class="hero__credit">${escapeHtml(value('credit'))}</figcaption>`;
    }

    function buildMedia(className = 'hero__media') {
        const mediaClasses = classes([className, value('mediaRatio'), value('mediaFit')]);
        let html = `<figure class="${mediaClasses}">`;
        html += `\n    <img src="${attr(value('imageSrc') || '/assets/images/heros/portal.jpg')}" alt="${attr(value('imageAlt'))}">`;
        const credit = buildCredit();
        if (credit) {
            html += `\n${indent(credit, 1)}`;
        }
        html += '\n</figure>';
        return html;
    }

    function heroBaseClasses(extra = []) {
        return classes([
            'hero',
            ...extra,
            value('heroTone'),
            value('heroSize'),
            value('heroAlign'),
            value('heroVerticalAlign'),
        ]);
    }

    function buildMinimal() {
        const heroClasses = heroBaseClasses(['hero--minimal']);
        return `<div class="${heroClasses}">\n    <div class="hero__inner">\n${indent(buildCopy(), 2)}\n    </div>\n</div>`;
    }

    function buildImage() {
        const heroClasses = heroBaseClasses(['hero--image', value('imageRatio'), value('overlay')]);
        let html = `<div class="${heroClasses}">`;
        html += `\n    <figure class="hero__background">`;
        html += `\n        <img src="${attr(value('imageSrc') || '/assets/images/heros/portal-home.png')}" alt="${attr(value('imageAlt'))}">`;
        html += '\n    </figure>';
        html += `\n\n    <div class="hero__inner">\n${indent(buildCopy(), 2)}\n    </div>`;
        html += '\n</div>';
        return html;
    }

    function buildImageBg() {
        const heroClasses = heroBaseClasses(['hero--image-bg', value('overlay')]);
        const bg = value('bgImage') || '/assets/images/heros/portal-home.png';
        const pos = value('bgPosition').trim();
        const styleParts = [`--hero-bg-image: url('${attr(bg)}')`];
        if (pos) {
            styleParts.push(`--hero-bg-position: ${attr(pos)}`);
        }
        let html = `<div\n    class="${heroClasses}"\n    style="${styleParts.join('; ')}"\n    role="banner"\n    aria-label="${attr(value('title') || 'Hero')}"\n>`;
        html += `\n    <div class="hero__inner">\n${indent(buildCopy(), 2)}\n    </div>`;
        html += '\n</div>';
        return html;
    }

    function buildSplit() {
        const heroClasses = heroBaseClasses(['hero--split', value('mediaLeft') === '1' ? 'hero--media-left' : '']);
        let html = `<div class="${heroClasses}">`;
        html += '\n    <div class="hero__inner">';
        html += `\n${indent(buildCopy(), 2)}`;
        html += `\n\n${indent(buildMedia(), 2)}`;
        html += '\n    </div>';
        html += '\n</div>';
        return html;
    }

    function buildPanelAction() {
        const label = value('panelActionLabel').trim();
        if (!label) {
            return '';
        }
        let html = `<a href="${attr(value('panelActionHref') || '#')}" class="hero__panel-action">`;
        html += `\n    <span>${escapeHtml(label)}</span>`;
        html += `\n${indent(icon('icon-arrow-right'), 1)}`;
        html += '\n</a>';
        return html;
    }

    function buildEditorial() {
        let html = '<div class="hero hero--editorial">';
        const heading = value('heading').trim();
        if (heading) {
            html += `\n    <div class="hero__heading">\n        <h1>${escapeHtml(heading)}</h1>\n    </div>`;
        }

        html += '\n\n    <div class="hero__stage">';
        html += `\n${indent(buildMedia('hero__media'), 2)}`;
        const panelClasses = classes(['hero__panel', value('panelVariant')]);
        html += `\n\n        <aside class="${panelClasses}">`;
        html += `\n            <h2 class="hero__panel-title">${escapeHtml(value('panelTitle') || 'Panel-Titel')}</h2>`;
        if (value('panelText').trim()) {
            html += `\n            <p class="hero__panel-text">${escapeHtml(value('panelText'))}</p>`;
        }
        if (value('panelMeta').trim()) {
            html += `\n            <p class="hero__panel-meta">${escapeHtml(value('panelMeta'))}</p>`;
        }
        const action = buildPanelAction();
        if (action) {
            html += `\n${indent(action, 3)}`;
        }
        html += '\n        </aside>';
        html += '\n    </div>';

        if (value('includeCards') === '1') {
            html += `\n\n${indent(buildCards(), 1)}`;
        }
        html += '\n</div>';
        return html;
    }

    function cardVariantForIndex(index) {
        const selected = value('cardVariant');
        if (selected) {
            return selected;
        }
        const variants = ['hero-card--primary', 'hero-card--secondary-cta', 'hero-card--white', 'hero-card--secondary-highlight'];
        return variants[(index - 1) % variants.length];
    }

    function buildCards() {
        const count = parseInt(value('cardCount'), 10) || 3;
        const columns = value('cardColumns') || 'hero__cards--3';
        const titles = ['Anmelden', 'Registrieren', 'Hilfe', 'Kontakt'];
        const texts = ['Zugang mit bestehendem Konto.', 'Neues Konto erstellen.', 'Antworten und Unterstützung finden.', 'Direkt Kontakt aufnehmen.'];
        let html = `<div class="hero__cards ${columns}">`;
        for (let i = 1; i <= count; i++) {
            const variant = cardVariantForIndex(i);
            html += `\n    <a class="${classes(['hero-card', variant])}" href="#">`;
            html += `\n        <h3 class="hero-card__title">${escapeHtml(titles[i - 1] || ('Thema ' + i))}</h3>`;
            html += `\n        <p class="hero-card__text">${escapeHtml(texts[i - 1] || 'Kurzer Beschreibungstext.')}</p>`;
            html += '\n        <span class="hero-card__action">';
            html += '\n            <span>Weitere Informationen</span>';
            html += `\n${indent(icon('icon-arrow-right'), 3)}`;
            html += '\n        </span>';
            html += '\n    </a>';
        }
        html += '\n</div>';
        return html;
    }

    function tileClass(index) {
        const legacy = ['hero__tile--a', 'hero__tile--b', 'hero__tile--c', 'hero__tile--d', 'hero__tile--e'];
        const semantic = ['hero__tile--primary', 'hero__tile--secondary-cta', 'hero__tile--secondary-highlight', 'hero__tile--ink', 'hero__tile--primary'];
        const accent = ['hero__tile--amber', 'hero__tile--teal', 'hero__tile--berry', 'hero__tile--sun', 'hero__tile--lime'];
        const mode = value('tileStyle');
        if (mode === 'semantic') {
            return semantic[(index - 1) % semantic.length];
        }
        if (mode === 'accent') {
            return accent[(index - 1) % accent.length];
        }
        return legacy[(index - 1) % legacy.length];
    }

    function buildTiles() {
        const count = parseInt(value('tileCount'), 10) || 4;
        const labels = ['Über das Portal', 'Zugang zum Portal', 'Mein Konto', 'Hilfe', 'Kontakt'];
        let html = '<nav class="hero__tiles" aria-label="Portal Schnellzugriff">';
        for (let i = 1; i <= count; i++) {
            html += `\n    <a class="hero__tile ${tileClass(i)}" href="#">${escapeHtml(labels[i - 1] || ('Link ' + i))}</a>`;
        }
        html += '\n</nav>';
        return html;
    }

    function buildTilesOnly() {
        const heroClasses = heroBaseClasses(['hero--tiles-only']);
        let html = `<div class="${heroClasses}">`;
        html += `\n    <div class="hero__inner">\n${indent(buildCopy(), 2)}\n    </div>`;
        html += `\n\n${indent(buildCards(), 1)}`;
        html += '\n</div>';
        return html;
    }

    function buildHero() {
        let html;
        if (value('output') === 'imageBg') {
            html = buildImageBg();
        } else if (value('output') === 'split') {
            html = buildSplit();
        } else if (value('output') === 'editorial') {
            html = buildEditorial();
        } else if (value('output') === 'tilesOnly') {
            html = buildTilesOnly();
        } else if (value('output') === 'minimal') {
            html = buildMinimal();
        } else {
            html = buildImage();
        }

        if (value('output') !== 'editorial' && value('output') !== 'tilesOnly' && value('includeCards') === '1') {
            html += `\n\n${buildCards()}`;
        }

        if (value('output') !== 'tilesOnly' && value('includeTiles') === '1') {
            html += `\n\n${buildTiles()}`;
        }

        return html;
    }

    function wrapSection(content) {
        const sectionClass = value('sectionClass');
        const classAttr = sectionClass ? ` class="${attr(sectionClass)}"` : '';
        return `<section${classAttr}>\n${indent(content, 1)}\n</section>`;
    }

    function update() {
        const html = wrapSection(buildHero());
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
        document.getElementById('hero-generator-code').scrollIntoView({ behavior: 'smooth', block: 'start' });
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
