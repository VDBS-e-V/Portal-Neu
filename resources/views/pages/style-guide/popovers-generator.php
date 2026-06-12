<?php
declare(strict_types=1);

$prefill = $prefill ?? [
    'output' => 'dialog',
    'elementMode' => 'dialog',
    'popupType' => 'popup--modal',
    'drawerPosition' => 'popup--drawer-right',
    'position' => '',
    'size' => 'popup--md',
    'tone' => 'popup--info',
    'surface' => '',
    'compact' => '0',
    'id' => 'example-popup',
    'triggerLabel' => 'Popup öffnen',
    'kicker' => 'Hinweis',
    'title' => 'Popup-Titel',
    'text' => 'Hier steht ein kurzer Inhalt für das Popup.',
    'meta' => '',
    'showStatusIcon' => '1',
    'icon' => 'icon-info',
    'actions' => '2',
    'primaryLabel' => 'Bestätigen',
    'primaryVariant' => 'btn--primary',
    'secondaryLabel' => 'Abbrechen',
    'secondaryVariant' => 'btn--primary-transp',
    'popoverVariant' => 'popup-popover--info',
    'toastRegion' => 'toast-region--bottom-right',
    'toastVariant' => 'toast--success',
    'toastLive' => 'polite',
    'showProgress' => '1',
    'tooltipPosition' => 'top',
    'tooltipText' => 'Dieser Button zeigt einen kurzen Hilfetext.',
    'consentPosition' => 'popup-consent--bottom',
];

if (!function_exists('vdb_popover_generator_e')) {
    function vdb_popover_generator_e($value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('vdb_popover_generator_selected')) {
    function vdb_popover_generator_selected($value, $selectedValue): string
    {
        return (string) $value === (string) $selectedValue ? 'selected' : '';
    }
}

$outputModes = [
    'dialog' => 'Dialog / Modal — .popup',
    'fallback' => 'Fallback Popup mit .is-open',
    'drawer' => 'Drawer / Seitenpanel',
    'popover' => 'Native Popover — .popup-popover',
    'toast' => 'Toast Benachrichtigung',
    'tooltip' => 'Tooltip mit data-tooltip',
    'consent' => 'Consent-Banner',
];

$elementModes = [
    'dialog' => 'Native &lt;dialog&gt;',
    'div' => 'Fallback &lt;div class="popup is-open"&gt;',
];

$popupTypes = [
    'popup--modal' => 'Modal — .popup--modal',
    'popup--alert' => 'Alert — .popup--alert',
    'popup--fullscreen' => 'Fullscreen — .popup--fullscreen',
    'popup--sheet' => 'Sheet — .popup--sheet',
];

$drawerPositions = [
    'popup--drawer-right' => 'Drawer rechts — .popup--drawer-right',
    'popup--drawer-left' => 'Drawer links — .popup--drawer-left',
    'popup--drawer-top' => 'Drawer oben — .popup--drawer-top',
    'popup--drawer-bottom' => 'Drawer unten — .popup--drawer-bottom',
];

$positions = [
    '' => 'Standard / zentriert',
    'popup--top' => 'Oben — .popup--top',
    'popup--bottom' => 'Unten — .popup--bottom',
];

$sizes = [
    'popup--xs' => 'XS — .popup--xs',
    'popup--sm' => 'SM — .popup--sm',
    'popup--md' => 'MD — .popup--md',
    'popup--lg' => 'LG — .popup--lg',
    'popup--xl' => 'XL — .popup--xl',
    'popup--full' => 'Full — .popup--full',
];

$tones = [
    '' => 'Standard / Primary-Akzent',
    'popup--primary' => 'Primary — .popup--primary',
    'popup--secondary-cta' => 'Secondary CTA — .popup--secondary-cta',
    'popup--secondary-highlight' => 'Secondary Highlight — .popup--secondary-highlight',
    'popup--plum' => 'Plum Alias — .popup--plum',
    'popup--success' => 'Success — .popup--success',
    'popup--error' => 'Error — .popup--error',
    'popup--danger' => 'Danger — .popup--danger',
    'popup--warning' => 'Warning — .popup--warning',
    'popup--info' => 'Info — .popup--info',
];

$surfaces = [
    '' => 'Standard / Weiß',
    'popup--surface' => 'Surface — .popup--surface',
    'popup--ink' => 'Ink — .popup--ink',
];

$actionCounts = [
    '0' => 'Keine Aktionen',
    '1' => 'Eine Aktion',
    '2' => 'Zwei Aktionen',
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
    'btn--danger' => 'Danger — .btn--danger',
];

$popoverVariants = [
    'popup-popover--primary' => 'Primary — .popup-popover--primary',
    'popup-popover--secondary-cta' => 'Secondary CTA — .popup-popover--secondary-cta',
    'popup-popover--secondary-highlight' => 'Secondary Highlight — .popup-popover--secondary-highlight',
    'popup-popover--plum' => 'Plum Alias — .popup-popover--plum',
    'popup-popover--success' => 'Success — .popup-popover--success',
    'popup-popover--error' => 'Error — .popup-popover--error',
    'popup-popover--danger' => 'Danger — .popup-popover--danger',
    'popup-popover--warning' => 'Warning — .popup-popover--warning',
    'popup-popover--info' => 'Info — .popup-popover--info',
];

$toastRegions = [
    'toast-region--top-right' => 'Oben rechts — .toast-region--top-right',
    'toast-region--top-left' => 'Oben links — .toast-region--top-left',
    'toast-region--bottom-right' => 'Unten rechts — .toast-region--bottom-right',
    'toast-region--bottom-left' => 'Unten links — .toast-region--bottom-left',
    'toast-region--top-center' => 'Oben zentriert — .toast-region--top-center',
    'toast-region--bottom-center' => 'Unten zentriert — .toast-region--bottom-center',
];

$toastVariants = [
    'toast--primary' => 'Primary — .toast--primary',
    'toast--secondary-cta' => 'Secondary CTA — .toast--secondary-cta',
    'toast--success' => 'Success — .toast--success',
    'toast--error' => 'Error — .toast--error',
    'toast--danger' => 'Danger — .toast--danger',
    'toast--warning' => 'Warning — .toast--warning',
    'toast--info' => 'Info — .toast--info',
];

$tooltipPositions = [
    'top' => 'Oben / Standard',
    'right' => 'Rechts',
    'bottom' => 'Unten',
    'left' => 'Links',
];

$consentPositions = [
    'popup-consent--bottom' => 'Unten — .popup-consent--bottom',
    'popup-consent--center' => 'Zentriert — .popup-consent--center',
];

$icons = [
    'icon-info',
    'icon-success',
    'icon-error',
    'icon-warning',
    'icon-close',
    'icon-bell',
    'icon-help',
    'icon-lock',
    'icon-cookie',
    'icon-settings',
];
?>

<section id="popover-generator-intro" class="section--surface">
    <div class="container container--text-only container--narrow container--center">
        <div class="container__text">
            <h1>Popover Generator</h1>

            <p>
                Wähle Popup-Art, Größe, Status, Text, Aktionen und Ausgabeform.
                Der fertige HTML-Code wird live erzeugt und kann direkt kopiert werden.
            </p>
        </div>

        <div class="container__buttons btn-group btn-group--vertical btn-group--main-center btn-group--gap-sm">
            <a href="#popover-generator-form-section" class="btn btn--primary btn--md">Generator öffnen</a>
            <a href="#popover-generator-code" class="btn btn--primary-transp btn--md">Zum HTML-Code</a>
        </div>
    </div>
</section>

<section id="popover-generator-form-section" class="long-width">
    <div class="container container--text-only container--wide">
        <form id="popover-generator-form" class="form form--card">
            <div class="form__grid form__grid--4">
                <div class="form__title form__title--center">
                    <h2>Popover konfigurieren</h2>
                    <p>
                        Die Ausgabe nutzt Klassen aus <code>popover.css</code> sowie
                        optional Buttons aus <code>buttons.css</code> und Icons aus dem SVG-Sprite.
                    </p>
                </div>

                <div class="form__notice form__notice--info form__field--span-full">
                    <strong>Hinweis:</strong>
                    Für echte Modals ist <code>&lt;dialog&gt;</code> empfohlen.
                    Für kleine kontextuelle Hinweise nutze <code>.popup-popover</code>
                    mit dem nativen <code>popover</code>-Attribut.
                </div>

                <div class="form__section-title form__section-title--primary">
                    <h3>Ausgabe</h3>
                    <p>Wähle die Komponente und die technische Ausgabeform.</p>
                </div>

                <div class="form__field form__field--span-2">
                    <label class="form__label" for="popover-output">Komponente</label>
                    <select class="form__control" id="popover-output" name="output">
                        <?php foreach ($outputModes as $value => $label): ?>
                            <option value="<?= vdb_popover_generator_e($value) ?>" <?= vdb_popover_generator_selected($value, $prefill['output']) ?>><?= vdb_popover_generator_e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form__field form__field--span-2">
                    <label class="form__label" for="popover-element-mode">Dialog-Ausgabe</label>
                    <select class="form__control" id="popover-element-mode" name="elementMode">
                        <?php foreach ($elementModes as $value => $label): ?>
                            <option value="<?= vdb_popover_generator_e($value) ?>" <?= vdb_popover_generator_selected($value, $prefill['elementMode']) ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                    <p class="form__hint">Gilt nur für Dialoge. Drawer nutzen im Generator ebenfalls <code>&lt;dialog&gt;</code>.</p>
                </div>

                <div class="form__section-title form__section-title--secondary-cta">
                    <h3>Layout</h3>
                    <p>Bestimme Typ, Position, Größe, Statusfarbe und Surface-Variante.</p>
                </div>

                <div class="form__field">
                    <label class="form__label" for="popover-popup-type">Popup-Typ</label>
                    <select class="form__control" id="popover-popup-type" name="popupType">
                        <?php foreach ($popupTypes as $class => $label): ?>
                            <option value="<?= vdb_popover_generator_e($class) ?>" <?= vdb_popover_generator_selected($class, $prefill['popupType']) ?>><?= vdb_popover_generator_e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form__field">
                    <label class="form__label" for="popover-drawer-position">Drawer-Position</label>
                    <select class="form__control" id="popover-drawer-position" name="drawerPosition">
                        <?php foreach ($drawerPositions as $class => $label): ?>
                            <option value="<?= vdb_popover_generator_e($class) ?>" <?= vdb_popover_generator_selected($class, $prefill['drawerPosition']) ?>><?= vdb_popover_generator_e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form__field">
                    <label class="form__label" for="popover-position">Modal-Position</label>
                    <select class="form__control" id="popover-position" name="position">
                        <?php foreach ($positions as $class => $label): ?>
                            <option value="<?= vdb_popover_generator_e($class) ?>" <?= vdb_popover_generator_selected($class, $prefill['position']) ?>><?= vdb_popover_generator_e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form__field">
                    <label class="form__label" for="popover-size">Größe</label>
                    <select class="form__control" id="popover-size" name="size">
                        <?php foreach ($sizes as $class => $label): ?>
                            <option value="<?= vdb_popover_generator_e($class) ?>" <?= vdb_popover_generator_selected($class, $prefill['size']) ?>><?= vdb_popover_generator_e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form__field">
                    <label class="form__label" for="popover-tone">Status / Farbe</label>
                    <select class="form__control" id="popover-tone" name="tone">
                        <?php foreach ($tones as $class => $label): ?>
                            <option value="<?= vdb_popover_generator_e($class) ?>" <?= vdb_popover_generator_selected($class, $prefill['tone']) ?>><?= vdb_popover_generator_e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form__field">
                    <label class="form__label" for="popover-surface">Surface</label>
                    <select class="form__control" id="popover-surface" name="surface">
                        <?php foreach ($surfaces as $class => $label): ?>
                            <option value="<?= vdb_popover_generator_e($class) ?>" <?= vdb_popover_generator_selected($class, $prefill['surface']) ?>><?= vdb_popover_generator_e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form__field">
                    <label class="form__label" for="popover-compact">Padding</label>
                    <select class="form__control" id="popover-compact" name="compact">
                        <option value="0" <?= vdb_popover_generator_selected('0', $prefill['compact']) ?>>Normal</option>
                        <option value="1" <?= vdb_popover_generator_selected('1', $prefill['compact']) ?>>Kompakt — .popup--compact</option>
                    </select>
                </div>

                <div class="form__field">
                    <label class="form__label" for="popover-icon">Status-Icon</label>
                    <select class="form__control" id="popover-icon" name="icon">
                        <?php foreach ($icons as $icon): ?>
                            <option value="<?= vdb_popover_generator_e($icon) ?>" <?= vdb_popover_generator_selected($icon, $prefill['icon']) ?>><?= vdb_popover_generator_e($icon) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form__section-title form__section-title--secondary-highlight">
                    <h3>Text</h3>
                    <p>Lege ID, Trigger, Kicker, Titel, Inhalt und Meta-Text fest.</p>
                </div>

                <div class="form__field form__field--span-2">
                    <label class="form__label" for="popover-id">ID</label>
                    <input class="form__control" type="text" id="popover-id" name="id" value="<?= vdb_popover_generator_e($prefill['id']) ?>">
                </div>

                <div class="form__field form__field--span-2">
                    <label class="form__label" for="popover-trigger-label">Trigger-Text</label>
                    <input class="form__control" type="text" id="popover-trigger-label" name="triggerLabel" value="<?= vdb_popover_generator_e($prefill['triggerLabel']) ?>">
                </div>

                <div class="form__field form__field--span-2">
                    <label class="form__label" for="popover-kicker">Kicker</label>
                    <input class="form__control" type="text" id="popover-kicker" name="kicker" value="<?= vdb_popover_generator_e($prefill['kicker']) ?>">
                </div>

                <div class="form__field form__field--span-2">
                    <label class="form__label" for="popover-title">Titel</label>
                    <input class="form__control" type="text" id="popover-title" name="title" value="<?= vdb_popover_generator_e($prefill['title']) ?>">
                </div>

                <div class="form__field form__field--span-full">
                    <label class="form__label" for="popover-text">Text</label>
                    <textarea class="form__control" id="popover-text" name="text" rows="3"><?= vdb_popover_generator_e($prefill['text']) ?></textarea>
                </div>

                <div class="form__field form__field--span-full">
                    <label class="form__label" for="popover-meta">Meta-Text</label>
                    <input class="form__control" type="text" id="popover-meta" name="meta" value="<?= vdb_popover_generator_e($prefill['meta']) ?>">
                </div>

                <div class="form__section-title">
                    <h3>Aktionen</h3>
                    <p>Optional werden Buttons im Footer ausgegeben.</p>
                </div>

                <div class="form__field">
                    <label class="form__label" for="popover-actions">Aktionen</label>
                    <select class="form__control" id="popover-actions" name="actions">
                        <?php foreach ($actionCounts as $value => $label): ?>
                            <option value="<?= vdb_popover_generator_e($value) ?>" <?= vdb_popover_generator_selected($value, $prefill['actions']) ?>><?= vdb_popover_generator_e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form__field">
                    <label class="form__label" for="popover-secondary-label">Sekundärtext</label>
                    <input class="form__control" type="text" id="popover-secondary-label" name="secondaryLabel" value="<?= vdb_popover_generator_e($prefill['secondaryLabel']) ?>">
                </div>

                <div class="form__field">
                    <label class="form__label" for="popover-secondary-variant">Sekundärvariante</label>
                    <select class="form__control" id="popover-secondary-variant" name="secondaryVariant">
                        <?php foreach ($buttonVariants as $class => $label): ?>
                            <option value="<?= vdb_popover_generator_e($class) ?>" <?= vdb_popover_generator_selected($class, $prefill['secondaryVariant']) ?>><?= vdb_popover_generator_e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form__field">
                    <label class="form__label" for="popover-primary-label">Primärtext</label>
                    <input class="form__control" type="text" id="popover-primary-label" name="primaryLabel" value="<?= vdb_popover_generator_e($prefill['primaryLabel']) ?>">
                </div>

                <div class="form__field">
                    <label class="form__label" for="popover-primary-variant">Primärvariante</label>
                    <select class="form__control" id="popover-primary-variant" name="primaryVariant">
                        <?php foreach ($buttonVariants as $class => $label): ?>
                            <option value="<?= vdb_popover_generator_e($class) ?>" <?= vdb_popover_generator_selected($class, $prefill['primaryVariant']) ?>><?= vdb_popover_generator_e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form__section-title form__section-title--primary">
                    <h3>Spezialoptionen</h3>
                    <p>Optionen für Popover, Toasts, Tooltips und Consent-Banner.</p>
                </div>

                <div class="form__field">
                    <label class="form__label" for="popover-popover-variant">Popover-Variante</label>
                    <select class="form__control" id="popover-popover-variant" name="popoverVariant">
                        <?php foreach ($popoverVariants as $class => $label): ?>
                            <option value="<?= vdb_popover_generator_e($class) ?>" <?= vdb_popover_generator_selected($class, $prefill['popoverVariant']) ?>><?= vdb_popover_generator_e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form__field">
                    <label class="form__label" for="popover-toast-region">Toast-Position</label>
                    <select class="form__control" id="popover-toast-region" name="toastRegion">
                        <?php foreach ($toastRegions as $class => $label): ?>
                            <option value="<?= vdb_popover_generator_e($class) ?>" <?= vdb_popover_generator_selected($class, $prefill['toastRegion']) ?>><?= vdb_popover_generator_e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form__field">
                    <label class="form__label" for="popover-toast-variant">Toast-Variante</label>
                    <select class="form__control" id="popover-toast-variant" name="toastVariant">
                        <?php foreach ($toastVariants as $class => $label): ?>
                            <option value="<?= vdb_popover_generator_e($class) ?>" <?= vdb_popover_generator_selected($class, $prefill['toastVariant']) ?>><?= vdb_popover_generator_e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form__field">
                    <label class="form__label" for="popover-toast-live">Toast aria-live</label>
                    <select class="form__control" id="popover-toast-live" name="toastLive">
                        <option value="polite" <?= vdb_popover_generator_selected('polite', $prefill['toastLive']) ?>>polite</option>
                        <option value="assertive" <?= vdb_popover_generator_selected('assertive', $prefill['toastLive']) ?>>assertive</option>
                    </select>
                </div>

                <div class="form__field">
                    <label class="form__label" for="popover-show-progress">Toast Progress</label>
                    <select class="form__control" id="popover-show-progress" name="showProgress">
                        <option value="0" <?= vdb_popover_generator_selected('0', $prefill['showProgress']) ?>>Nicht ausgeben</option>
                        <option value="1" <?= vdb_popover_generator_selected('1', $prefill['showProgress']) ?>>Ausgeben</option>
                    </select>
                </div>

                <div class="form__field">
                    <label class="form__label" for="popover-tooltip-position">Tooltip-Position</label>
                    <select class="form__control" id="popover-tooltip-position" name="tooltipPosition">
                        <?php foreach ($tooltipPositions as $value => $label): ?>
                            <option value="<?= vdb_popover_generator_e($value) ?>" <?= vdb_popover_generator_selected($value, $prefill['tooltipPosition']) ?>><?= vdb_popover_generator_e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form__field form__field--span-2">
                    <label class="form__label" for="popover-tooltip-text">Tooltip-Text</label>
                    <input class="form__control" type="text" id="popover-tooltip-text" name="tooltipText" value="<?= vdb_popover_generator_e($prefill['tooltipText']) ?>">
                </div>

                <div class="form__field">
                    <label class="form__label" for="popover-consent-position">Consent-Position</label>
                    <select class="form__control" id="popover-consent-position" name="consentPosition">
                        <?php foreach ($consentPositions as $class => $label): ?>
                            <option value="<?= vdb_popover_generator_e($class) ?>" <?= vdb_popover_generator_selected($class, $prefill['consentPosition']) ?>><?= vdb_popover_generator_e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form__actions form__actions--right">
                    <button class="btn btn--primary-light btn--md" type="reset" id="popover-generator-reset">Zurücksetzen</button>
                    <button class="btn btn--primary btn--md" type="button" id="popover-generator-jump-code">HTML-Code ansehen</button>
                </div>
            </div>
        </form>
    </div>
</section>

<section id="popover-generator-preview" class="section--surface long-width">
    <div class="container container--text-only container--wide">
        <div class="form form--card">
            <div class="form__grid form__grid--1">
                <div class="form__title">
                    <h2>Vorschau</h2>
                    <p>Die Vorschau aktualisiert sich automatisch. Dialoge und Popover können direkt getestet werden.</p>
                </div>

                <div class="form__notice form__notice--info">
                    <strong>Live-Vorschau:</strong>
                    Prüfe hier Popup-Art, Status, Text, Aktionen und Verhalten.
                </div>

                <div class="form__field">
                    <label class="form__label">Darstellung</label>
                    <div id="popover-preview" class="form__notice"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="popover-generator-code" class="long-width">
    <div class="container container--text-only container--wide">
        <div class="form form--card">
            <div class="form__grid form__grid--1">
                <div class="form__title">
                    <h2>HTML-Code</h2>
                    <p>Kopiere den fertigen Code und füge ihn an der gewünschten Stelle ein.</p>
                </div>

                <div class="form__field">
                    <label class="form__label" for="popover-example-code">Generierter Code</label>
                    <textarea class="form__control" id="popover-example-code" rows="22" readonly></textarea>
                </div>

                <div class="form__actions form__actions--right">
                    <button class="btn btn--primary btn--md" type="button" id="copy-popover-code">Code kopieren</button>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
(function(){
    const form = document.getElementById('popover-generator-form');
    const preview = document.getElementById('popover-preview');
    const example = document.getElementById('popover-example-code');
    const copyBtn = document.getElementById('copy-popover-code');
    const jumpBtn = document.getElementById('popover-generator-jump-code');

    const fields = {
        output: document.getElementById('popover-output'),
        elementMode: document.getElementById('popover-element-mode'),
        popupType: document.getElementById('popover-popup-type'),
        drawerPosition: document.getElementById('popover-drawer-position'),
        position: document.getElementById('popover-position'),
        size: document.getElementById('popover-size'),
        tone: document.getElementById('popover-tone'),
        surface: document.getElementById('popover-surface'),
        compact: document.getElementById('popover-compact'),
        icon: document.getElementById('popover-icon'),
        id: document.getElementById('popover-id'),
        triggerLabel: document.getElementById('popover-trigger-label'),
        kicker: document.getElementById('popover-kicker'),
        title: document.getElementById('popover-title'),
        text: document.getElementById('popover-text'),
        meta: document.getElementById('popover-meta'),
        actions: document.getElementById('popover-actions'),
        primaryLabel: document.getElementById('popover-primary-label'),
        primaryVariant: document.getElementById('popover-primary-variant'),
        secondaryLabel: document.getElementById('popover-secondary-label'),
        secondaryVariant: document.getElementById('popover-secondary-variant'),
        popoverVariant: document.getElementById('popover-popover-variant'),
        toastRegion: document.getElementById('popover-toast-region'),
        toastVariant: document.getElementById('popover-toast-variant'),
        toastLive: document.getElementById('popover-toast-live'),
        showProgress: document.getElementById('popover-show-progress'),
        tooltipPosition: document.getElementById('popover-tooltip-position'),
        tooltipText: document.getElementById('popover-tooltip-text'),
        consentPosition: document.getElementById('popover-consent-position'),
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

    function value(name) {
        return fields[name] ? fields[name].value : '';
    }

    function classes(list) {
        return list.filter(Boolean).join(' ');
    }

    function indent(code, level = 1) {
        const prefix = '    '.repeat(level);
        return code.split('\n').map((line) => line ? prefix + line : line).join('\n');
    }

    function safeId() {
        const id = value('id').trim() || 'example-popup';
        return id.replace(/[^a-zA-Z0-9_-]/g, '-');
    }

    function icon(name) {
        return `<svg class="vdb-icon" aria-hidden="true">\n    <use href="/assets/icons/vdb-icons.svg#${attr(name || 'icon-info')}"></use>\n</svg>`;
    }

    function buildTrigger(targetId, popover = false) {
        if (popover) {
            return `<button class="btn btn--primary btn--md" type="button" popovertarget="${attr(targetId)}">${escapeHtml(value('triggerLabel') || 'Popover öffnen')}</button>`;
        }
        return `<button class="btn btn--primary btn--md" type="button" data-popup-open="${attr(targetId)}">${escapeHtml(value('triggerLabel') || 'Popup öffnen')}</button>`;
    }

    function buildHeader(titleId, closeLabel = 'Popup schließen') {
        let html = '<header class="popup__header">';
        if (value('popupType') === 'popup--alert') {
            html += `\n    <div class="popup__status-icon" aria-hidden="true">\n${indent(icon(value('icon')), 2)}\n    </div>`;
        }
        html += '\n    <div>';
        if (value('kicker').trim()) {
            html += `\n        <p class="popup__kicker">${escapeHtml(value('kicker'))}</p>`;
        }
        html += `\n        <h2 class="popup__title" id="${attr(titleId)}">${escapeHtml(value('title') || 'Popup-Titel')}</h2>`;
        html += '\n    </div>';
        html += `\n\n    <form method="dialog">\n        <button class="popup__close" aria-label="${attr(closeLabel)}">\n${indent(icon('icon-close'), 3)}\n        </button>\n    </form>`;
        html += '\n</header>';
        return html;
    }

    function buildBody() {
        let html = '<div class="popup__body">';
        html += `\n    <p class="popup__text">${escapeHtml(value('text') || 'Hier steht der Inhalt des Popups.')}</p>`;
        if (value('meta').trim()) {
            html += `\n    <p class="popup__meta">${escapeHtml(value('meta'))}</p>`;
        }
        html += '\n</div>';
        return html;
    }

    function buildFooter() {
        const count = parseInt(value('actions'), 10) || 0;
        if (count < 1) {
            return '';
        }
        let html = '<footer class="popup__footer">';
        html += '\n    <form method="dialog" class="btn-group btn-group--horizontal btn-group--main-end btn-group--gap-sm">';
        if (count > 1) {
            html += `\n        <button class="btn ${attr(value('secondaryVariant') || 'btn--primary-transp')} btn--md" value="cancel">${escapeHtml(value('secondaryLabel') || 'Abbrechen')}</button>`;
        }
        html += `\n        <button class="btn ${attr(value('primaryVariant') || 'btn--primary')} btn--md" value="confirm">${escapeHtml(value('primaryLabel') || 'Bestätigen')}</button>`;
        html += '\n    </form>';
        html += '\n</footer>';
        return html;
    }

    function popupClasses(forDrawer = false) {
        return classes([
            'popup',
            forDrawer ? 'popup--drawer' : value('popupType'),
            forDrawer ? value('drawerPosition') : value('position'),
            value('size'),
            value('tone'),
            value('surface'),
            value('compact') === '1' ? 'popup--compact' : '',
        ]);
    }

    function buildDialogContent(titleId, closeLabel) {
        let html = '<div class="popup__surface">';
        html += `\n${indent(buildHeader(titleId, closeLabel), 1)}`;
        html += `\n\n${indent(buildBody(), 1)}`;
        const footer = buildFooter();
        if (footer) {
            html += `\n\n${indent(footer, 1)}`;
        }
        html += '\n</div>';
        return html;
    }

    function buildDialog() {
        const id = safeId();
        const titleId = `${id}-title`;
        const mode = value('output') === 'fallback' ? 'div' : value('elementMode');
        if (mode === 'div') {
            let html = `<div class="${popupClasses(false)}" id="${attr(id)}" role="presentation">`;
            html += '\n    <div class="popup__backdrop" aria-hidden="true" data-popup-close></div>';
            html += `\n\n    <div class="popup__surface" role="dialog" aria-modal="true" aria-labelledby="${attr(titleId)}">`;
            html += `\n${indent(buildHeader(titleId), 2)}`;
            html += `\n\n${indent(buildBody(), 2)}`;
            const footer = buildFooter();
            if (footer) {
                html += `\n\n${indent(footer, 2)}`;
            }
            html += '\n    </div>';
            html += '\n</div>';
            return buildTrigger(id) + '\n\n' + html;
        }
        return buildTrigger(id) + `\n\n<dialog class="${popupClasses(false)}" id="${attr(id)}" aria-labelledby="${attr(titleId)}">\n${indent(buildDialogContent(titleId), 1)}\n</dialog>`;
    }

    function buildDrawer() {
        const id = safeId();
        const titleId = `${id}-title`;
        return buildTrigger(id) + `\n\n<dialog class="${popupClasses(true)}" id="${attr(id)}" aria-labelledby="${attr(titleId)}">\n${indent(buildDialogContent(titleId, 'Drawer schließen'), 1)}\n</dialog>`;
    }

    function buildPopover() {
        const id = safeId();
        let html = buildTrigger(id, true);
        html += `\n\n<div class="${classes(['popup-popover', value('popoverVariant')])}" id="${attr(id)}" popover>`;
        if (value('title').trim()) {
            html += `\n    <div class="popup-popover__header">\n        <strong>${escapeHtml(value('title'))}</strong>\n    </div>`;
        }
        html += `\n\n    <p>${escapeHtml(value('text') || 'Hier steht ein kurzer Hilfetext.')}</p>`;
        html += '\n</div>';
        return html;
    }

    function buildToast() {
        const id = safeId();
        let html = `<div class="toast-region ${attr(value('toastRegion'))}" aria-live="${attr(value('toastLive'))}" aria-label="Benachrichtigungen">`;
        html += `\n    <div class="toast ${attr(value('toastVariant'))} is-visible" id="${attr(id)}">`;
        html += `\n        <div class="toast__icon" aria-hidden="true">\n${indent(icon(value('icon')), 3)}\n        </div>`;
        html += '\n\n        <div class="toast__content">';
        html += `\n            <p class="toast__title">${escapeHtml(value('title') || 'Gespeichert')}</p>`;
        html += `\n            <p class="toast__text">${escapeHtml(value('text') || 'Die Änderungen wurden erfolgreich gespeichert.')}</p>`;
        html += '\n        </div>';
        html += '\n\n        <button class="toast__close" type="button" aria-label="Meldung schließen">×</button>';
        if (value('showProgress') === '1') {
            html += '\n        <span class="toast__progress" aria-hidden="true"></span>';
        }
        html += '\n    </div>';
        html += '\n</div>';
        return html;
    }

    function buildTooltip() {
        const position = value('tooltipPosition');
        const posAttr = position && position !== 'top' ? `\n    data-tooltip-position="${attr(position)}"` : '';
        return `<button\n    class="btn btn--primary btn--md"\n    type="button"\n    data-tooltip="${attr(value('tooltipText') || 'Kurzer Hilfetext.')}"${posAttr}\n>\n    ${escapeHtml(value('triggerLabel') || 'Tooltip anzeigen')}\n</button>`;
    }

    function buildConsent() {
        let html = `<div class="popup-consent ${attr(value('consentPosition'))} is-open">`;
        html += '\n    <div class="popup-consent__content">';
        html += '\n        <div>';
        html += `\n            <h2 class="popup-consent__title">${escapeHtml(value('title') || 'Datenschutzhinweis')}</h2>`;
        html += `\n            <p class="popup-consent__text">${escapeHtml(value('text') || 'Wir verwenden notwendige Cookies, um diese Website bereitzustellen.')}</p>`;
        html += '\n        </div>';
        html += '\n\n        <div class="popup-consent__actions btn-group btn-group--horizontal btn-group--gap-sm">';
        html += `\n            <button class="btn ${attr(value('secondaryVariant') || 'btn--primary-transp')} btn--md" type="button">${escapeHtml(value('secondaryLabel') || 'Einstellungen')}</button>`;
        html += `\n            <button class="btn ${attr(value('primaryVariant') || 'btn--primary')} btn--md" type="button">${escapeHtml(value('primaryLabel') || 'Verstanden')}</button>`;
        html += '\n        </div>';
        html += '\n    </div>';
        html += '\n</div>';
        return html;
    }

    function buildScript() {
        return `<script>\n(function () {\n    document.querySelectorAll('[data-popup-open]').forEach(function (button) {\n        button.addEventListener('click', function () {\n            const popup = document.getElementById(button.getAttribute('data-popup-open'));\n            if (!popup) return;\n            if (typeof popup.showModal === 'function') {\n                popup.showModal();\n            } else {\n                popup.classList.add('is-open');\n            }\n        });\n    });\n\n    document.querySelectorAll('.popup:not(dialog)').forEach(function (popup) {\n        popup.querySelectorAll('[data-popup-close], .popup__close, .popup__footer button').forEach(function (button) {\n            button.addEventListener('click', function () {\n                popup.classList.remove('is-open');\n            });\n        });\n    });\n})();\n<\/script>`;
    }

    function buildOutput() {
        let html;
        if (value('output') === 'drawer') {
            html = buildDrawer();
        } else if (value('output') === 'popover') {
            html = buildPopover();
        } else if (value('output') === 'toast') {
            html = buildToast();
        } else if (value('output') === 'tooltip') {
            html = buildTooltip();
        } else if (value('output') === 'consent') {
            html = buildConsent();
        } else {
            html = buildDialog();
        }

        if (['dialog', 'fallback', 'drawer'].includes(value('output'))) {
            html += '\n\n' + buildScript();
            if (value('output') === 'fallback') {
                html += '\n\n<!-- Bei .is-open-Fallbacks solltest du zusätzlich Fokusfalle und Escape-Taste per JavaScript ergänzen. -->';
            }
        }

        return html;
    }

    function update() {
        const html = buildOutput();
        example.value = html;
        preview.innerHTML = html;

        preview.querySelectorAll('[data-popup-open]').forEach(function (button) {
            button.addEventListener('click', function () {
                const dialog = preview.querySelector('#' + CSS.escape(button.getAttribute('data-popup-open')));
                if (!dialog) return;
                if (typeof dialog.showModal === 'function') {
                    dialog.showModal();
                } else {
                    dialog.classList.add('is-open');
                }
            });
        });

        preview.querySelectorAll('.toast-region').forEach(function (region) {
            region.style.position = 'relative';
            region.style.inset = 'auto';
            region.style.transform = 'none';
            region.style.margin = '0';
        });

        preview.querySelectorAll('.popup:not(dialog)').forEach(function (popup) {
            popup.querySelectorAll('[data-popup-close], .popup__close, .popup__footer button').forEach(function (button) {
                button.addEventListener('click', function () {
                    popup.classList.remove('is-open');
                });
            });
        });

        preview.querySelectorAll('.popup-consent').forEach(function (consent) {
            consent.style.position = 'relative';
            consent.style.inset = 'auto';
            consent.style.transform = 'none';
            consent.style.width = 'auto';
        });
    }

    Object.keys(fields).forEach(function(key) {
        const field = fields[key];
        field.addEventListener('input', update);
        field.addEventListener('change', update);
    });

    form.addEventListener('reset', function() {
        window.setTimeout(update, 0);
    });

    jumpBtn.addEventListener('click', function() {
        document.getElementById('popover-generator-code').scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    copyBtn.addEventListener('click', function() {
        navigator.clipboard.writeText(example.value).then(function() {
            copyBtn.textContent = 'Kopiert!';
            setTimeout(function() {
                copyBtn.textContent = 'Code kopieren';
            }, 1500);
        });
    });

    update();
})();
</script>
