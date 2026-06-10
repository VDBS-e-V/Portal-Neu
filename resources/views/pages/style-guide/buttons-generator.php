<?php
declare(strict_types=1);

$prefill = $prefill ?? [
    'label' => 'Mehr erfahren',
    'href' => '#',
    'element' => 'a',
    'type' => 'button',
    'size' => 'btn--md',
    'variant' => 'btn--primary',
    'disabled' => 'none',
    'ariaLabel' => '',
    'iconMode' => 'none',
    'icon' => 'icon-arrow-right',
    'iconGap' => 'btn--gap-sm',
    'output' => 'single',
    'secondLabel' => 'Weitere Informationen',
    'secondVariant' => 'btn--primary-light',
    'groupDirection' => 'btn-group--vertical',
    'groupMain' => 'btn-group--main-start',
    'groupSec' => 'btn-group--sec-stretch',
    'groupGap' => 'btn-group--gap-sm',
    'groupTitle' => '',
    'groupSubtitle' => '',
];

if (!function_exists('vdb_button_generator_e')) {
    function vdb_button_generator_e(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}

$sizes = [
    'btn--xs' => 'Extra small',
    'btn--sm' => 'Small',
    'btn--md' => 'Medium',
    'btn--lg' => 'Large',
    'btn--xl' => 'Extra large',
];

$variants = [
    'Dunkel zu Hell' => [
        'btn--primary' => 'Primary',
        'btn--secondary-cta' => 'Secondary CTA',
        'btn--secondary-highlight' => 'Secondary Highlight',
    ],
    'Hell zu Dunkel' => [
        'btn--primary-light' => 'Primary Light',
        'btn--secondary-cta-light' => 'Secondary CTA Light',
        'btn--secondary-highlight-light' => 'Secondary Highlight Light',
    ],
    'Transparent' => [
        'btn--primary-transp' => 'Primary Transparent',
        'btn--secondary-cta-transp' => 'Secondary CTA Transparent',
        'btn--secondary-highlight-transp' => 'Secondary Highlight Transparent',
    ],
    'Status' => [
        'btn--success' => 'Success',
        'btn--error' => 'Error',
        'btn--warning' => 'Warning',
        'btn--info' => 'Info',
    ],
    'Weitere Varianten' => [
        'btn--outline' => 'Outline',
        'btn--ghost' => 'Ghost',
        'btn--danger' => 'Danger',
    ],
];

$disabledModes = [
    'none' => 'Aktiv',
    'disabled' => 'disabled / deaktiviert',
    'aria' => 'aria-disabled="true"',
    'class' => '.is-disabled',
];

$iconModes = [
    'none' => 'Kein Icon',
    'left' => 'Icon links',
    'right' => 'Icon rechts',
    'only' => 'Nur Icon',
];

$iconGaps = [
    '' => 'Standard',
    'btn--gap-xs' => 'Gap XS',
    'btn--gap-sm' => 'Gap SM',
    'btn--gap-md' => 'Gap MD',
    'btn--gap-lg' => 'Gap LG',
    'btn--gap-xl' => 'Gap XL',
];

$outputModes = [
    'single' => 'Einzelner Button',
    'group' => 'Button-Group mit zwei Buttons',
];

$groupDirections = [
    'btn-group--vertical' => 'Vertikal',
    'btn-group--horizontal' => 'Horizontal',
];

$groupMain = [
    'btn-group--main-start' => 'Hauptachse: Start',
    'btn-group--main-center' => 'Hauptachse: Mitte',
    'btn-group--main-end' => 'Hauptachse: Ende',
    'btn-group--main-space-between' => 'Hauptachse: Space between',
    'btn-group--main-space-around' => 'Hauptachse: Space around',
    'btn-group--main-space-evenly' => 'Hauptachse: Space evenly',
];

$groupSec = [
    'btn-group--sec-start' => 'Nebenachse: Start',
    'btn-group--sec-center' => 'Nebenachse: Mitte',
    'btn-group--sec-end' => 'Nebenachse: Ende',
    'btn-group--sec-stretch' => 'Nebenachse: Stretch',
];

$groupGaps = [
    'btn-group--gap-xs' => 'Gap XS',
    'btn-group--gap-sm' => 'Gap SM',
    'btn-group--gap-md' => 'Gap MD',
    'btn-group--gap-lg' => 'Gap LG',
    'btn-group--gap-xl' => 'Gap XL',
];
?>

<section id="button-generator-intro" class="section--surface">
    <div class="container container--text-only container--narrow container--center">
        <div class="container__text">
            <h1>Button Generator</h1>

            <p>
                Wähle Element, Größe, Farbe, Icon und optional eine Button-Group.
                Der fertige HTML-Code wird live erzeugt und kann direkt kopiert werden.
            </p>
        </div>

        <div class="container__buttons btn-group btn-group--vertical btn-group--main-center btn-group--gap-sm">
            <a href="#button-generator-form-section" class="btn btn--primary btn--md">Generator öffnen</a>
            <a href="#button-generator-code" class="btn btn--primary-transp btn--md">Zum HTML-Code</a>
        </div>
    </div>
</section>

<section id="button-generator-form-section" class="long-width">
    <div class="container container--text-only container--wide">
        <form id="button-generator-form" class="form form--card">
            <div class="form__grid form__grid--4">
                <div class="form__title form__title--center">
                    <h2>Button konfigurieren</h2>
                    <p>
                        Die Felder nutzen das Formularsystem aus <code>forms.css</code>:
                        <code>.form</code>, <code>.form__grid</code>, <code>.form__field</code>,
                        <code>.form__label</code>, <code>.form__control</code> und <code>.form__actions</code>.
                    </p>
                </div>

                <div class="form__notice form__notice--info">
                    <strong>Hinweis:</strong>
                    Für Navigation nutze <code>&lt;a&gt;</code>. Für echte Aktionen in Formularen oder Interfaces nutze <code>&lt;button&gt;</code>.
                </div>

                <div class="form__section-title form__section-title--primary">
                    <h3>Grunddaten</h3>
                    <p>Lege Text, Elementtyp, Link, Button-Type und ARIA-Beschriftung fest.</p>
                </div>

                <div class="form__field form__field--span-2">
                    <label class="form__label" for="button-label">Text</label>
                    <input class="form__control" type="text" id="button-label" name="label" value="<?= vdb_button_generator_e($prefill['label']) ?>">
                    <p class="form__hint">Bei Icon-only wird dieser Text als <code>aria-label</code> genutzt, falls kein eigener Wert gesetzt wird.</p>
                </div>

                <div class="form__field form__field--span-2">
                    <label class="form__label" for="button-href">Link / href</label>
                    <input class="form__control" type="text" id="button-href" name="href" value="<?= vdb_button_generator_e($prefill['href']) ?>">
                    <p class="form__hint">Wird nur für <code>&lt;a&gt;</code>-Buttons ausgegeben.</p>
                </div>

                <div class="form__field">
                    <label class="form__label" for="button-element">Element</label>
                    <select class="form__control" id="button-element" name="element">
                        <option value="a" <?= $prefill['element'] === 'a' ? 'selected' : '' ?>>Link: &lt;a&gt;</option>
                        <option value="button" <?= $prefill['element'] === 'button' ? 'selected' : '' ?>>Button: &lt;button&gt;</option>
                    </select>
                </div>

                <div class="form__field">
                    <label class="form__label" for="button-type">Button type</label>
                    <select class="form__control" id="button-type" name="type">
                        <?php foreach (['button', 'submit', 'reset'] as $type): ?>
                            <option value="<?= vdb_button_generator_e($type) ?>" <?= $type === $prefill['type'] ? 'selected' : '' ?>><?= vdb_button_generator_e($type) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <p class="form__hint">Wird nur für <code>&lt;button&gt;</code> ausgegeben.</p>
                </div>

                <div class="form__field">
                    <label class="form__label" for="button-disabled">Zustand</label>
                    <select class="form__control" id="button-disabled" name="disabled">
                        <?php foreach ($disabledModes as $value => $label): ?>
                            <option value="<?= vdb_button_generator_e($value) ?>" <?= $value === $prefill['disabled'] ? 'selected' : '' ?>><?= vdb_button_generator_e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form__field">
                    <label class="form__label" for="button-aria-label">Aria-label</label>
                    <input class="form__control" type="text" id="button-aria-label" name="ariaLabel" value="<?= vdb_button_generator_e($prefill['ariaLabel']) ?>">
                    <p class="form__hint">Pflicht bei Icon-only Buttons.</p>
                </div>

                <div class="form__section-title form__section-title--secondary-cta">
                    <h3>Erscheinung</h3>
                    <p>Bestimme Größe, Farbvariante und semantische Button-Klasse.</p>
                </div>

                <div class="form__field form__field--span-2">
                    <label class="form__label" for="button-size">Größe</label>
                    <select class="form__control" id="button-size" name="size">
                        <?php foreach ($sizes as $class => $label): ?>
                            <option value="<?= vdb_button_generator_e($class) ?>" <?= $class === $prefill['size'] ? 'selected' : '' ?>><?= vdb_button_generator_e($label . ' — ' . $class) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form__field form__field--span-2">
                    <label class="form__label" for="button-variant">Farbe / Variante</label>
                    <select class="form__control" id="button-variant" name="variant">
                        <?php foreach ($variants as $groupLabel => $items): ?>
                            <optgroup label="<?= vdb_button_generator_e($groupLabel) ?>">
                                <?php foreach ($items as $class => $label): ?>
                                    <option value="<?= vdb_button_generator_e($class) ?>" <?= $class === $prefill['variant'] ? 'selected' : '' ?>><?= vdb_button_generator_e($label . ' — ' . $class) ?></option>
                                <?php endforeach; ?>
                            </optgroup>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form__section-title form__section-title--secondary-highlight">
                    <h3>Icon</h3>
                    <p>Optional kann ein Icon aus dem VDBS-Sprite in den Button eingebunden werden.</p>
                </div>

                <div class="form__field">
                    <label class="form__label" for="button-icon-mode">Icon-Modus</label>
                    <select class="form__control" id="button-icon-mode" name="iconMode">
                        <?php foreach ($iconModes as $value => $label): ?>
                            <option value="<?= vdb_button_generator_e($value) ?>" <?= $value === $prefill['iconMode'] ? 'selected' : '' ?>><?= vdb_button_generator_e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form__field">
                    <label class="form__label" for="button-icon-gap">Icon-Abstand</label>
                    <select class="form__control" id="button-icon-gap" name="iconGap">
                        <?php foreach ($iconGaps as $class => $label): ?>
                            <option value="<?= vdb_button_generator_e($class) ?>" <?= $class === $prefill['iconGap'] ? 'selected' : '' ?>><?= vdb_button_generator_e($label . ($class !== '' ? ' — ' . $class : '')) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form__field form__field--span-2">
                    <label class="form__label" for="button-icon-filter">Icon suchen</label>
                    <div class="form__input-icon">
                        <svg class="vdb-icon" aria-hidden="true">
                            <use href="/assets/icons/vdb-icons.svg#icon-search"></use>
                        </svg>
                        <input class="form__control" type="search" id="button-icon-filter" placeholder="Icon-Namen filtern..." autocomplete="off">
                    </div>
                    <p class="form__hint">Die Liste wird aus <code>/assets/icons/vdb-icons.svg</code> geladen.</p>
                </div>

                <div class="form__field form__field--span-2">
                    <label class="form__label" for="button-icon-name">Icon auswählen</label>
                    <select class="form__control" id="button-icon-name" name="icon">
                        <option value="<?= vdb_button_generator_e($prefill['icon']) ?>" selected><?= vdb_button_generator_e($prefill['icon']) ?></option>
                    </select>
                </div>

                <div class="form__section-title">
                    <h3>Ausgabe</h3>
                    <p>Erzeuge entweder einen einzelnen Button oder eine Button-Group mit zwei Buttons.</p>
                </div>

                <div class="form__field form__field--span-2">
                    <label class="form__label" for="button-output">Ausgabeart</label>
                    <select class="form__control" id="button-output" name="output">
                        <?php foreach ($outputModes as $value => $label): ?>
                            <option value="<?= vdb_button_generator_e($value) ?>" <?= $value === $prefill['output'] ? 'selected' : '' ?>><?= vdb_button_generator_e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form__field form__field--span-2">
                    <label class="form__label" for="button-second-label">Zweiter Button</label>
                    <input class="form__control" type="text" id="button-second-label" name="secondLabel" value="<?= vdb_button_generator_e($prefill['secondLabel']) ?>">
                    <p class="form__hint">Wird nur bei Button-Group ausgegeben.</p>
                </div>

                <div class="form__field form__field--span-2">
                    <label class="form__label" for="button-second-variant">Variante zweiter Button</label>
                    <select class="form__control" id="button-second-variant" name="secondVariant">
                        <?php foreach ($variants as $groupLabel => $items): ?>
                            <optgroup label="<?= vdb_button_generator_e($groupLabel) ?>">
                                <?php foreach ($items as $class => $label): ?>
                                    <option value="<?= vdb_button_generator_e($class) ?>" <?= $class === $prefill['secondVariant'] ? 'selected' : '' ?>><?= vdb_button_generator_e($label . ' — ' . $class) ?></option>
                                <?php endforeach; ?>
                            </optgroup>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form__field form__field--span-2">
                    <label class="form__label" for="button-group-title">Group-Titel</label>
                    <input class="form__control" type="text" id="button-group-title" name="groupTitle" value="<?= vdb_button_generator_e($prefill['groupTitle']) ?>">
                </div>

                <div class="form__field form__field--span-full">
                    <label class="form__label" for="button-group-subtitle">Group-Untertitel</label>
                    <input class="form__control" type="text" id="button-group-subtitle" name="groupSubtitle" value="<?= vdb_button_generator_e($prefill['groupSubtitle']) ?>">
                </div>

                <fieldset class="form__fieldset">
                    <legend class="form__legend">Button-Group Layout</legend>

                    <div class="form__grid form__grid--4 form__grid--compact">
                        <div class="form__field">
                            <label class="form__label" for="button-group-direction">Richtung</label>
                            <select class="form__control" id="button-group-direction" name="groupDirection">
                                <?php foreach ($groupDirections as $class => $label): ?>
                                    <option value="<?= vdb_button_generator_e($class) ?>" <?= $class === $prefill['groupDirection'] ? 'selected' : '' ?>><?= vdb_button_generator_e($label . ' — ' . $class) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form__field">
                            <label class="form__label" for="button-group-gap">Abstand</label>
                            <select class="form__control" id="button-group-gap" name="groupGap">
                                <?php foreach ($groupGaps as $class => $label): ?>
                                    <option value="<?= vdb_button_generator_e($class) ?>" <?= $class === $prefill['groupGap'] ? 'selected' : '' ?>><?= vdb_button_generator_e($label . ' — ' . $class) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form__field">
                            <label class="form__label" for="button-group-main">Hauptachse</label>
                            <select class="form__control" id="button-group-main" name="groupMain">
                                <?php foreach ($groupMain as $class => $label): ?>
                                    <option value="<?= vdb_button_generator_e($class) ?>" <?= $class === $prefill['groupMain'] ? 'selected' : '' ?>><?= vdb_button_generator_e($label . ' — ' . $class) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form__field">
                            <label class="form__label" for="button-group-sec">Nebenachse</label>
                            <select class="form__control" id="button-group-sec" name="groupSec">
                                <?php foreach ($groupSec as $class => $label): ?>
                                    <option value="<?= vdb_button_generator_e($class) ?>" <?= $class === $prefill['groupSec'] ? 'selected' : '' ?>><?= vdb_button_generator_e($label . ' — ' . $class) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </fieldset>

                <div class="form__actions form__actions--right">
                    <button class="btn btn--primary-light btn--md" type="reset" id="button-generator-reset">Zurücksetzen</button>
                    <button class="btn btn--primary btn--md" type="button" id="button-generator-jump-code">HTML-Code ansehen</button>
                </div>
            </div>
        </form>
    </div>
</section>

<section id="button-generator-preview" class="section--surface long-width">
    <div class="container container--text-only container--wide">
        <div class="form form--card">
            <div class="form__grid form__grid--2">
                <div class="form__title">
                    <h2>Vorschau</h2>
                    <p>Die Vorschau aktualisiert sich automatisch. Klicks auf Vorschau-Links werden verhindert.</p>
                </div>

                <div class="form__notice form__notice--info form__field--span-full">
                    <strong>Live-Vorschau:</strong>
                    Prüfe hier Größe, Farbe, Icon und Button-Group-Wirkung.
                </div>

                <div class="form__field form__field--span-full">
                    <label class="form__label">Darstellung</label>
                    <div id="button-preview" class="form__notice"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="button-generator-code" class="long-width">
    <div class="container container--text-only container--wide">
        <div class="form form--card">
            <div class="form__grid form__grid--1">
                <div class="form__title">
                    <h2>HTML-Code</h2>
                    <p>Kopiere den fertigen Code und füge ihn an der gewünschten Stelle ein.</p>
                </div>

                <div class="form__field">
                    <label class="form__label" for="button-example-code">Generierter Code</label>
                    <textarea class="form__control" id="button-example-code" rows="12" readonly></textarea>
                </div>

                <div class="form__actions form__actions--right">
                    <button class="btn btn--primary btn--md" type="button" id="copy-button-code">Code kopieren</button>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
(function(){
    const labelInput = document.getElementById('button-label');
    const hrefInput = document.getElementById('button-href');
    const elementInput = document.getElementById('button-element');
    const typeInput = document.getElementById('button-type');
    const sizeInput = document.getElementById('button-size');
    const variantInput = document.getElementById('button-variant');
    const disabledInput = document.getElementById('button-disabled');
    const ariaLabelInput = document.getElementById('button-aria-label');
    const iconModeInput = document.getElementById('button-icon-mode');
    const iconInput = document.getElementById('button-icon-name');
    const iconGapInput = document.getElementById('button-icon-gap');
    const iconFilterInput = document.getElementById('button-icon-filter');

    const outputInput = document.getElementById('button-output');
    const secondLabelInput = document.getElementById('button-second-label');
    const secondVariantInput = document.getElementById('button-second-variant');
    const groupDirectionInput = document.getElementById('button-group-direction');
    const groupGapInput = document.getElementById('button-group-gap');
    const groupMainInput = document.getElementById('button-group-main');
    const groupSecInput = document.getElementById('button-group-sec');
    const groupTitleInput = document.getElementById('button-group-title');
    const groupSubtitleInput = document.getElementById('button-group-subtitle');

    const preview = document.getElementById('button-preview');
    const example = document.getElementById('button-example-code');
    const copyBtn = document.getElementById('copy-button-code');
    const jumpBtn = document.getElementById('button-generator-jump-code');
    const form = document.getElementById('button-generator-form');

    let icons = [];

    function escapeHtml(value) {
        return String(value || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function normalizeIconName(name) {
        return String(name || '').trim() || 'icon-arrow-right';
    }

    function indent(code, level = 1) {
        const prefix = '    '.repeat(level);
        return code.split('\n').map((line) => line ? prefix + line : line).join('\n');
    }

    function renderIconOptions(query = '') {
        const current = normalizeIconName(iconInput.value);
        const term = query.trim().toLowerCase();
        const filtered = icons.filter((icon) => !term || icon.toLowerCase().includes(term));
        iconInput.innerHTML = '';

        const list = filtered.length ? filtered : [current];

        list.forEach((icon) => {
            const option = document.createElement('option');
            option.value = icon;
            option.textContent = icon;
            option.selected = icon === current;
            iconInput.appendChild(option);
        });

        if (!filtered.includes(current) && icons.includes(current)) {
            const option = document.createElement('option');
            option.value = current;
            option.textContent = current;
            option.selected = true;
            iconInput.prepend(option);
        }
    }

    function buildIconSvg(icon) {
        const normalized = normalizeIconName(icon);
        return `<svg class="vdb-icon" aria-hidden="true">\n        <use href="/assets/icons/vdb-icons.svg#${escapeHtml(normalized)}"></use>\n    </svg>`;
    }

    function buildButtonHtml(options) {
        const element = options.element || 'a';
        const label = options.label || 'Button';
        const href = options.href || '#';
        const type = options.type || 'button';
        const size = options.size || 'btn--md';
        const variant = options.variant || 'btn--primary';
        const disabled = options.disabled || 'none';
        const ariaLabel = options.ariaLabel || '';
        const iconMode = options.iconMode || 'none';
        const icon = normalizeIconName(options.icon);
        const iconGap = options.iconGap || '';

        const classes = ['btn'];

        if (iconMode !== 'none') {
            classes.push('btn-icon');
        }

        if (iconMode === 'only') {
            classes.push('btn--icon-only');
        }

        if (iconMode === 'right') {
            classes.push('btn--icon-right');
        }

        classes.push(variant, size);

        if (iconMode !== 'none' && iconGap) {
            classes.push(iconGap);
        }

        if (disabled === 'class') {
            classes.push('is-disabled');
        }

        let attrs = `class="${classes.filter(Boolean).join(' ')}"`;

        if (element === 'button') {
            attrs = `type="${escapeHtml(type)}" ` + attrs;
            if (disabled === 'disabled') {
                attrs += ' disabled';
            }
            if (disabled === 'aria') {
                attrs += ' aria-disabled="true"';
            }
        } else {
            attrs = `href="${escapeHtml(href)}" ` + attrs;
            if (disabled === 'disabled' || disabled === 'aria') {
                attrs += ' aria-disabled="true" tabindex="-1"';
            }
        }

        if (iconMode === 'only') {
            attrs += ` aria-label="${escapeHtml(ariaLabel || label || 'Button')}"`;
        } else if (ariaLabel) {
            attrs += ` aria-label="${escapeHtml(ariaLabel)}"`;
        }

        let inner = escapeHtml(label || 'Button');
        const iconSvg = buildIconSvg(icon);

        if (iconMode === 'left') {
            inner = `${iconSvg}\n    ${escapeHtml(label || 'Button')}`;
        }

        if (iconMode === 'right') {
            inner = `${escapeHtml(label || 'Button')}\n    ${iconSvg}`;
        }

        if (iconMode === 'only') {
            inner = iconSvg;
        }

        if (element === 'button') {
            return `<button ${attrs}>\n    ${inner}\n</button>`;
        }

        return `<a ${attrs}>\n    ${inner}\n</a>`;
    }

    function buildGroupHtml(buttonHtml, secondButtonHtml) {
        const groupClasses = [
            'btn-group',
            groupDirectionInput.value,
            groupMainInput.value,
            groupSecInput.value,
            groupGapInput.value,
        ].filter(Boolean);

        const title = groupTitleInput.value.trim();
        const subtitle = groupSubtitleInput.value.trim();

        let html = `<div class="${groupClasses.join(' ')}">`;

        if (title || subtitle) {
            html += `\n    <div class="btn-group__title-container">`;
            if (title) {
                html += `\n        <h3 class="btn-group__title">${escapeHtml(title)}</h3>`;
            }
            if (subtitle) {
                html += `\n        <p class="btn-group__subtitle">${escapeHtml(subtitle)}</p>`;
            }
            html += `\n    </div>`;
        }

        html += `\n${indent(buttonHtml, 1)}`;
        html += `\n\n${indent(secondButtonHtml, 1)}`;
        html += `\n</div>`;

        return html;
    }

    function collectPrimaryOptions() {
        return {
            element: elementInput.value,
            label: labelInput.value,
            href: hrefInput.value,
            type: typeInput.value,
            size: sizeInput.value,
            variant: variantInput.value,
            disabled: disabledInput.value,
            ariaLabel: ariaLabelInput.value,
            iconMode: iconModeInput.value,
            icon: iconInput.value,
            iconGap: iconGapInput.value,
        };
    }

    function collectSecondOptions() {
        return {
            element: elementInput.value,
            label: secondLabelInput.value || 'Weitere Informationen',
            href: hrefInput.value || '#',
            type: typeInput.value,
            size: sizeInput.value,
            variant: secondVariantInput.value,
            disabled: 'none',
            ariaLabel: '',
            iconMode: 'none',
            icon: iconInput.value,
            iconGap: iconGapInput.value,
        };
    }

    function update() {
        const primary = buildButtonHtml(collectPrimaryOptions());
        const second = buildButtonHtml(collectSecondOptions());
        const html = outputInput.value === 'group'
            ? buildGroupHtml(primary, second)
            : primary;

        preview.innerHTML = html;
        example.value = html;
    }

    const inputs = [
        labelInput,
        hrefInput,
        elementInput,
        typeInput,
        sizeInput,
        variantInput,
        disabledInput,
        ariaLabelInput,
        iconModeInput,
        iconInput,
        iconGapInput,
        outputInput,
        secondLabelInput,
        secondVariantInput,
        groupDirectionInput,
        groupGapInput,
        groupMainInput,
        groupSecInput,
        groupTitleInput,
        groupSubtitleInput,
    ];

    inputs.forEach((input) => {
        input.addEventListener('input', update);
        input.addEventListener('change', update);
    });

    iconFilterInput.addEventListener('input', function () {
        renderIconOptions(iconFilterInput.value);
        update();
    });

    preview.addEventListener('click', function (event) {
        event.preventDefault();
    });

    form.addEventListener('reset', function () {
        window.setTimeout(function () {
            renderIconOptions('');
            update();
        }, 0);
    });

    jumpBtn.addEventListener('click', function () {
        document.getElementById('button-generator-code').scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    fetch('/assets/icons/vdb-icons.svg')
        .then(function (response) { return response.text(); })
        .then(function (svgText) {
            const parser = new DOMParser();
            const doc = parser.parseFromString(svgText, 'image/svg+xml');
            icons = Array.from(doc.querySelectorAll('symbol'))
                .map(function (symbol) { return symbol.getAttribute('id') || ''; })
                .filter(Boolean)
                .sort();
            renderIconOptions('');
        })
        .catch(function () {
            icons = [normalizeIconName(iconInput.value)];
            renderIconOptions('');
        })
        .finally(function () {
            update();
        });

    copyBtn.addEventListener('click', function(){
        const text = example.value;
        navigator.clipboard.writeText(text).then(function(){
            copyBtn.textContent = 'Kopiert!';
            setTimeout(function(){
                copyBtn.textContent = 'Code kopieren';
            }, 1500);
        });
    });

    update();
})();
</script>
