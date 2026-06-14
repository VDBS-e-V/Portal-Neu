<?php
declare(strict_types=1);

$defaultPrefill = [
    'icon' => 'icon-star',
    'size' => 'vdb-icon--xl',
    'color' => 'vdb-icon--primary',
    'stroke' => 'vdb-icon--regular',
    'output' => 'decorative',
    'ariaLabel' => 'Information',
    'buttonLabel' => 'Mehr erfahren',
    'buttonHref' => '#',
    'buttonVariant' => 'btn--primary',
    'buttonSize' => 'btn--md',
];

$prefill = array_merge($defaultPrefill, $prefill ?? []);

if (isset($_GET['icon']) && is_string($_GET['icon']) && preg_match('/^icon-[a-z0-9_-]+$/i', $_GET['icon'])) {
    $prefill['icon'] = $_GET['icon'];
}

if (!function_exists('vdb_icon_generator_e')) {
    function vdb_icon_generator_e($value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('vdb_icon_generator_selected')) {
    function vdb_icon_generator_selected($value, $selectedValue): string
    {
        return (string) $value === (string) $selectedValue ? 'selected' : '';
    }
}

$sizes = [
    'vdb-icon--2xs' => '2XS',
    'vdb-icon--xs' => 'XS',
    'vdb-icon--sm' => 'Small',
    'vdb-icon--md' => 'Medium',
    'vdb-icon--lg' => 'Large',
    'vdb-icon--xl' => 'XL',
    'vdb-icon--2xl' => '2XL',
    'vdb-icon--3xl' => '3XL',
];

$colors = [
    'vdb-icon--primary' => 'Primary',
    'vdb-icon--secondary-cta' => 'Secondary CTA',
    'vdb-icon--secondary-highlight' => 'Secondary Highlight',
    'vdb-icon--amber' => 'Amber',
    'vdb-icon--teal' => 'Teal',
    'vdb-icon--berry' => 'Berry',
    'vdb-icon--plum' => 'Plum',
    'vdb-icon--sun-yellow' => 'Sun Yellow',
    'vdb-icon--lime' => 'Lime',
];

$strokes = [
    'vdb-icon--thin' => 'Thin',
    'vdb-icon--regular' => 'Regular',
    'vdb-icon--medium' => 'Medium',
    'vdb-icon--bold' => 'Bold',
    'vdb-icon--extrabold' => 'Extra Bold',
];

$outputModes = [
    'decorative' => 'Dekoratives Icon — aria-hidden',
    'labelled' => 'Bedeutungstragendes Icon — role/aria-label',
    'buttonIcon' => 'Icon im Button mit Text',
    'iconOnlyButton' => 'Icon-only Button mit aria-label',
];

$buttonVariants = [
    'btn--primary' => 'Primary',
    'btn--primary-light' => 'Primary Light',
    'btn--primary-transp' => 'Primary Transparent',
    'btn--secondary-cta' => 'Secondary CTA',
    'btn--secondary-cta-light' => 'Secondary CTA Light',
    'btn--outline' => 'Outline',
    'btn--ghost' => 'Ghost',
];

$buttonSizes = [
    'btn--xs' => 'Extra small',
    'btn--sm' => 'Small',
    'btn--md' => 'Medium',
    'btn--lg' => 'Large',
    'btn--xl' => 'Extra large',
];

$fallbackIcons = [
    'icon-home',
    'icon-menu',
    'icon-close',
    'icon-arrow-left',
    'icon-arrow-right',
    'icon-arrow-down',
    'icon-arrow-up',
    'icon-breadcrumb',
    'icon-search',
    'icon-filter',
    'icon-sort',
    'icon-more',
    'icon-mail',
    'icon-phone',
    'icon-fax',
    'icon-web',
    'icon-link',
    'icon-location',
    'icon-message',
    'icon-user',
    'icon-login',
    'icon-logout',
    'icon-lock',
    'icon-settings',
    'icon-bell',
    'icon-edit',
    'icon-save',
    'icon-trash',
    'icon-upload',
    'icon-download',
    'icon-share',
    'icon-print',
    'icon-copy',
    'icon-external-link',
    'icon-plus',
    'icon-minus',
    'icon-check',
    'icon-document',
    'icon-image',
    'icon-calendar',
    'icon-time',
    'icon-folder',
    'icon-book',
    'icon-list',
    'icon-grid',
    'icon-dashboard',
    'icon-portal',
    'icon-members',
    'icon-member-card',
    'icon-organization',
    'icon-board',
    'icon-meeting',
    'icon-minutes',
    'icon-task',
    'icon-announcement',
    'icon-inbox',
    'icon-form',
    'icon-invoice',
    'icon-finance',
    'icon-roles',
    'icon-school',
    'icon-course',
    'icon-workshop',
    'icon-certificate',
    'icon-feedback',
    'icon-barcode',
    'icon-isbn',
    'icon-catalog',
    'icon-scan',
    'icon-borrow',
    'icon-return',
    'icon-due-date',
    'icon-reservation',
    'icon-media',
    'icon-shelf',
    'icon-category',
    'icon-author',
    'icon-publisher',
    'icon-cover',
    'icon-qr',
    'icon-inventory',
    'icon-statistics',
    'icon-open-book',
    'icon-book-stack',
    'icon-bookshelf',
    'icon-laptop',
    'icon-bookmark',
    'icon-plant',
    'icon-method',
    'icon-materials',
    'icon-tag',
    'icon-duration',
    'icon-phase',
    'icon-group-size',
    'icon-age',
    'icon-moderation',
    'icon-approve',
    'icon-reject',
    'icon-version',
    'icon-report',
    'icon-comment',
    'icon-rating',
    'icon-visibility',
    'icon-vote-box',
    'icon-megaphone',
    'icon-group',
    'icon-flag',
    'icon-parliament',
    'icon-star',
    'icon-shield',
    'icon-cookie',
    'icon-accessibility',
    'icon-eye',
    'icon-server',
    'icon-api',
    'icon-database',
    'icon-backup',
    'icon-sync',
    'icon-export',
    'icon-import',
    'icon-template',
    'icon-layout',
    'icon-permission-key',
    'icon-heart',
    'icon-like',
    'icon-project',
    'icon-access-denied',
    'icon-account',
    'icon-archive',
    'icon-association',
    'icon-audio',
    'icon-ballot',
    'icon-book-search',
    'icon-cancel',
    'icon-chat',
    'icon-checklist',
    'icon-committee',
    'icon-community',
    'icon-consent',
    'icon-contact-form',
    'icon-data',
    'icon-delete',
    'icon-desk-lamp',
    'icon-discussion',
    'icon-donate',
    'icon-election',
    'icon-error',
    'icon-event',
    'icon-external-service',
    'icon-globe',
    'icon-hand-up',
    'icon-help',
    'icon-imprint',
    'icon-info',
    'icon-library',
    'icon-library-card',
    'icon-library-software',
    'icon-loan',
    'icon-maintenance',
    'icon-material',
    'icon-media-list',
    'icon-member',
    'icon-method-admin',
    'icon-methods',
    'icon-moderate',
    'icon-newsletter',
    'icon-no-results',
    'icon-not-found',
    'icon-offline',
    'icon-password',
    'icon-pdf',
    'icon-permission',
    'icon-permissions',
    'icon-pin',
    'icon-podium',
    'icon-profile',
    'icon-publication',
    'icon-register',
    'icon-reports',
    'icon-reservations',
    'icon-returns',
    'icon-reviews',
    'icon-role',
    'icon-school-library',
    'icon-server-problem',
    'icon-spinner',
    'icon-student-council',
    'icon-success',
    'icon-sv',
    'icon-tags',
    'icon-team',
    'icon-tile-view',
    'icon-verein',
    'icon-video',
    'icon-volunteer',
    'icon-vote-check',
    'icon-warning',
    'icon-website',
];
?>

<section id="icon-generator-intro" class="section--surface">
    <div class="container container--text-only container--narrow container--center">
        <div class="container__text">
            <h1>Icon Generator</h1>
            <p>Wähle Icon, Größe, Farbe, Linienstärke und Ausgabeart. Der fertige HTML-Code wird live erzeugt und kann direkt kopiert werden.</p>
        </div>
        <div class="container__buttons btn-group btn-group--vertical btn-group--main-center btn-group--gap-sm">
            <a href="#icon-generator-form-section" class="btn btn--primary btn--md">Generator öffnen</a>
            <a href="#icon-generator-code" class="btn btn--primary-transp btn--md">Zum HTML-Code</a>
        </div>
    </div>
</section>

<section id="icon-generator-form-section" class="long-width">
    <div class="container container--text-only container--wide">
        <form id="icon-generator-form" class="form form--card">
            <div class="form__grid form__grid--4">
                <div class="form__title form__title--center">
                    <h2>Icon konfigurieren</h2>
                    <p>Die Felder nutzen das Formularsystem aus <code>forms.css</code>. Die Ausgabe nutzt <code>.vdb-icon</code> und optional Button-Klassen aus <code>buttons.css</code>.</p>
                </div>
                <div class="form__notice form__notice--info form__field--span-full">
                    <strong>Hinweis:</strong> Dekorative Icons erhalten <code>aria-hidden="true"</code>. Wenn ein Icon ohne sichtbaren Text eine Bedeutung trägt, nutze eine zugängliche Beschriftung über <code>aria-label</code>.
                </div>

                <div class="form__section-title form__section-title--primary">
                    <h3>Icon</h3>
                    <p>Wähle ein Symbol aus der Sprite-Datei oder filtere nach Namen.</p>
                </div>

                <div class="form__field form__field--span-full form__picker" data-icon-picker>
                    <label class="form__label" for="icon-name">Icon</label>
                    <button type="button" class="btn btn--outline btn--md form__picker-toggle" id="icon-picker-toggle" aria-haspopup="listbox" aria-expanded="false">
                        <span class="form__picker-summary">
                            <svg class="vdb-icon vdb-icon--primary vdb-icon--regular vdb-icon--md" aria-hidden="true"><use href="/assets/icons/vdb-icons.svg#<?= vdb_icon_generator_e($prefill['icon']) ?>"></use></svg>
                            <span><span class="form__picker-summary-label" data-picker-label><?= vdb_icon_generator_e($prefill['icon']) ?></span><span class="form__picker-summary-meta">Icon auswählen</span></span>
                        </span>
                        <span aria-hidden="true">▾</span>
                    </button>
                    <input type="hidden" id="icon-name" name="icon" value="<?= vdb_icon_generator_e($prefill['icon']) ?>">
                    <div class="form__picker-menu" id="icon-picker-menu" role="listbox" aria-label="Icons auswählen">
                        <div class="form__picker-search"><input class="form__control" type="search" id="icon-picker-search" placeholder="Icons filtern..." autocomplete="off"></div>
                        <div class="form__picker-list" id="icon-picker-list"><div class="form__picker-empty">Icons werden geladen...</div></div>
                    </div>
                    <p class="form__hint">Die Auswahl lädt alle Symbole aus <code>/assets/icons/vdb-icons.svg</code>; falls das fehlschlägt, nutzt sie die serverseitige Fallback-Liste.</p>
                </div>

                <div class="form__section-title form__section-title--secondary-cta"><h3>Erscheinung</h3><p>Bestimme Größe, Farbe und Linienstärke.</p></div>

                <div class="form__field">
                    <label class="form__label" for="icon-size">Größe</label>
                    <select class="form__control" id="icon-size" name="size"><?php foreach ($sizes as $class => $label): ?><option value="<?= vdb_icon_generator_e($class) ?>" <?= vdb_icon_generator_selected($class, $prefill['size']) ?>><?= vdb_icon_generator_e($label . ' — ' . $class) ?></option><?php endforeach; ?></select>
                </div>
                <div class="form__field">
                    <label class="form__label" for="icon-color">Farbe</label>
                    <select class="form__control" id="icon-color" name="color"><?php foreach ($colors as $class => $label): ?><option value="<?= vdb_icon_generator_e($class) ?>" <?= vdb_icon_generator_selected($class, $prefill['color']) ?>><?= vdb_icon_generator_e($label . ' — ' . $class) ?></option><?php endforeach; ?></select>
                </div>
                <div class="form__field">
                    <label class="form__label" for="icon-stroke">Linienstärke</label>
                    <select class="form__control" id="icon-stroke" name="stroke"><?php foreach ($strokes as $class => $label): ?><option value="<?= vdb_icon_generator_e($class) ?>" <?= vdb_icon_generator_selected($class, $prefill['stroke']) ?>><?= vdb_icon_generator_e($label . ' — ' . $class) ?></option><?php endforeach; ?></select>
                </div>
                <div class="form__field">
                    <label class="form__label" for="icon-output">Ausgabeart</label>
                    <select class="form__control" id="icon-output" name="output"><?php foreach ($outputModes as $value => $label): ?><option value="<?= vdb_icon_generator_e($value) ?>" <?= vdb_icon_generator_selected($value, $prefill['output']) ?>><?= vdb_icon_generator_e($label) ?></option><?php endforeach; ?></select>
                </div>

                <div class="form__section-title form__section-title--secondary-highlight"><h3>Beschriftung und Button</h3><p>Diese Felder werden je nach Ausgabeart genutzt.</p></div>

                <div class="form__field form__field--span-2"><label class="form__label" for="icon-aria-label">ARIA-Label</label><input class="form__control" type="text" id="icon-aria-label" name="ariaLabel" value="<?= vdb_icon_generator_e($prefill['ariaLabel']) ?>"><p class="form__hint">Pflicht bei bedeutungstragenden Icons und Icon-only Buttons.</p></div>
                <div class="form__field form__field--span-2"><label class="form__label" for="icon-button-label">Button-Text</label><input class="form__control" type="text" id="icon-button-label" name="buttonLabel" value="<?= vdb_icon_generator_e($prefill['buttonLabel']) ?>"></div>
                <div class="form__field form__field--span-2"><label class="form__label" for="icon-button-href">Button href</label><input class="form__control" type="text" id="icon-button-href" name="buttonHref" value="<?= vdb_icon_generator_e($prefill['buttonHref']) ?>"></div>
                <div class="form__field"><label class="form__label" for="icon-button-variant">Button-Variante</label><select class="form__control" id="icon-button-variant" name="buttonVariant"><?php foreach ($buttonVariants as $class => $label): ?><option value="<?= vdb_icon_generator_e($class) ?>" <?= vdb_icon_generator_selected($class, $prefill['buttonVariant']) ?>><?= vdb_icon_generator_e($label . ' — ' . $class) ?></option><?php endforeach; ?></select></div>
                <div class="form__field"><label class="form__label" for="icon-button-size">Button-Größe</label><select class="form__control" id="icon-button-size" name="buttonSize"><?php foreach ($buttonSizes as $class => $label): ?><option value="<?= vdb_icon_generator_e($class) ?>" <?= vdb_icon_generator_selected($class, $prefill['buttonSize']) ?>><?= vdb_icon_generator_e($label . ' — ' . $class) ?></option><?php endforeach; ?></select></div>

                <div class="form__actions form__actions--right"><button class="btn btn--primary-light btn--md" type="reset" id="icon-generator-reset">Zurücksetzen</button><button class="btn btn--primary btn--md" type="button" id="icon-generator-jump-code">HTML-Code ansehen</button></div>
            </div>
        </form>
    </div>
</section>

<section id="icon-generator-preview" class="section--surface long-width"><div class="container container--text-only container--wide"><div class="form form--card"><div class="form__grid form__grid--2"><div class="form__title"><h2>Vorschau</h2><p>Die Vorschau aktualisiert sich automatisch. Klicks auf Vorschau-Links werden verhindert.</p></div><div class="form__notice form__notice--info form__field--span-full"><strong>Live-Vorschau:</strong> Prüfe hier Symbol, Größe, Farbe, Linienstärke und Ausgabeart.</div><div class="form__field form__field--span-full"><label class="form__label">Darstellung</label><div id="icon-preview" class="form__notice"></div></div></div></div></div></section>

<section id="icon-generator-code" class="long-width"><div class="container container--text-only container--wide"><div class="form form--card"><div class="form__grid form__grid--1"><div class="form__title"><h2>HTML-Code</h2><p>Kopiere den fertigen Code und füge ihn an der gewünschten Stelle ein.</p></div><div class="form__field"><label class="form__label" for="icon-example-code">Generierter Code</label><textarea class="form__control" id="icon-example-code" rows="10" readonly></textarea></div><div class="form__actions form__actions--right"><button class="btn btn--primary btn--md" type="button" id="copy-icon-code">Code kopieren</button></div></div></div></div></section>

<script>
(function(){
    const form = document.getElementById('icon-generator-form');
    const picker = document.querySelector('[data-icon-picker]');
    const pickerToggle = document.getElementById('icon-picker-toggle');
    const pickerMenu = document.getElementById('icon-picker-menu');
    const pickerList = document.getElementById('icon-picker-list');
    const pickerSearch = document.getElementById('icon-picker-search');
    const pickerLabel = document.querySelector('[data-picker-label]');
    const iconInput = document.getElementById('icon-name');
    const preview = document.getElementById('icon-preview');
    const example = document.getElementById('icon-example-code');
    const copyBtn = document.getElementById('copy-icon-code');
    const jumpBtn = document.getElementById('icon-generator-jump-code');
    const initialIcon = <?= json_encode((string) $prefill['icon'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    const fields = {
        size: document.getElementById('icon-size'),
        color: document.getElementById('icon-color'),
        stroke: document.getElementById('icon-stroke'),
        output: document.getElementById('icon-output'),
        ariaLabel: document.getElementById('icon-aria-label'),
        buttonLabel: document.getElementById('icon-button-label'),
        buttonHref: document.getElementById('icon-button-href'),
        buttonVariant: document.getElementById('icon-button-variant'),
        buttonSize: document.getElementById('icon-button-size')
    };
    let icons = <?= json_encode($fallbackIcons, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    function escapeHtml(value) { return String(value || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;'); }
    function attr(value) { return escapeHtml(value || ''); }
    function normalizeIconName(name) { const icon = String(name || '').trim(); return /^icon-[a-z0-9_-]+$/i.test(icon) ? icon : 'icon-star'; }
    function getValue(name) { return fields[name] ? fields[name].value : ''; }
    function indent(code, level) { const prefix = '    '.repeat(level || 1); return code.split('\n').map((line) => line ? prefix + line : line).join('\n'); }
    function iconSvg(options) {
        const outputOptions = options || {};
        const icon = normalizeIconName(iconInput.value);
        const classes = ['vdb-icon', getValue('color'), getValue('stroke'), getValue('size')].filter(Boolean).join(' ');
        const label = getValue('ariaLabel') || 'Icon';
        const accessibility = outputOptions.labelled ? ` role="img" aria-label="${attr(label)}"` : ' aria-hidden="true"';
        return `<svg class="${attr(classes)}"${accessibility}>\n    <use href="/assets/icons/vdb-icons.svg#${attr(icon)}"></use>\n</svg>`;
    }
    function buildCode() {
        const output = getValue('output');
        const icon = iconSvg({ labelled: output === 'labelled' });
        if (output === 'buttonIcon') {
            return `<a href="${attr(getValue('buttonHref') || '#')}" class="btn btn-icon ${attr(getValue('buttonVariant'))} ${attr(getValue('buttonSize'))}">\n${indent(icon, 1)}\n    ${escapeHtml(getValue('buttonLabel') || 'Mehr erfahren')}\n</a>`;
        }
        if (output === 'iconOnlyButton') {
            const innerIcon = iconSvg({ labelled: false });
            return `<button type="button" class="btn btn-icon btn--icon-only ${attr(getValue('buttonVariant'))} ${attr(getValue('buttonSize'))}" aria-label="${attr(getValue('ariaLabel') || getValue('buttonLabel') || 'Aktion')}">\n${indent(innerIcon, 1)}\n</button>`;
        }
        return icon;
    }
    function updatePreview() { const html = buildCode(); preview.innerHTML = html; example.value = html; }
    function setPickerOpen(isOpen) { pickerMenu.classList.toggle('is-open', isOpen); pickerToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false'); if (isOpen) pickerSearch.focus(); }
    function updatePickerSelection(icon) {
        const normalized = normalizeIconName(icon);
        iconInput.value = normalized;
        pickerLabel.textContent = normalized;
        const use = pickerToggle.querySelector('use');
        if (use) use.setAttribute('href', `/assets/icons/vdb-icons.svg#${normalized}`);
        pickerList.querySelectorAll('[data-icon-option]').forEach((button) => {
            const active = button.getAttribute('data-icon-name') === normalized;
            button.classList.toggle('is-selected', active);
            button.setAttribute('aria-selected', active ? 'true' : 'false');
        });
    }
    function renderIconOptions(query) {
        const term = String(query || '').trim().toLowerCase();
        const filtered = icons.filter((icon) => !term || icon.toLowerCase().includes(term));
        pickerList.innerHTML = '';
        if (!filtered.length) { pickerList.innerHTML = '<div class="form__picker-empty">Keine Icons gefunden.</div>'; return; }
        filtered.forEach((icon) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'form__picker-option';
            button.setAttribute('role', 'option');
            button.setAttribute('data-icon-option', '');
            button.setAttribute('data-icon-name', icon);
            button.setAttribute('aria-selected', icon === iconInput.value ? 'true' : 'false');
            button.innerHTML = `<svg class="form__picker-option-icon vdb-icon vdb-icon--primary vdb-icon--regular vdb-icon--md" aria-hidden="true"><use href="/assets/icons/vdb-icons.svg#${icon}"></use></svg><span class="form__picker-option-text"><span class="form__picker-option-name">${icon}</span><span class="form__picker-option-code">#${icon}</span></span>`;
            button.addEventListener('click', function () { updatePickerSelection(icon); setPickerOpen(false); updatePreview(); });
            pickerList.appendChild(button);
        });
        updatePickerSelection(iconInput.value);
    }
    pickerToggle.addEventListener('click', function () { setPickerOpen(!pickerMenu.classList.contains('is-open')); });
    document.addEventListener('click', function (event) { if (!picker.contains(event.target)) setPickerOpen(false); });
    pickerSearch.addEventListener('input', function () { renderIconOptions(pickerSearch.value); });
    pickerSearch.addEventListener('keydown', function (event) { if (event.key === 'Escape') { setPickerOpen(false); pickerToggle.focus(); } });
    Object.keys(fields).forEach(function(key) { fields[key].addEventListener('input', updatePreview); fields[key].addEventListener('change', updatePreview); });
    form.addEventListener('reset', function() { window.setTimeout(function() { updatePickerSelection(initialIcon); updatePreview(); }, 0); });
    preview.addEventListener('click', function(event) { event.preventDefault(); });
    jumpBtn.addEventListener('click', function() { document.getElementById('icon-generator-code').scrollIntoView({ behavior: 'smooth', block: 'start' }); });
    copyBtn.addEventListener('click', function() {
        const text = example.value;
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(function() { copyBtn.textContent = 'Kopiert!'; window.setTimeout(function() { copyBtn.textContent = 'Code kopieren'; }, 1500); });
            return;
        }
        example.select(); document.execCommand('copy'); copyBtn.textContent = 'Kopiert!'; window.setTimeout(function() { copyBtn.textContent = 'Code kopieren'; }, 1500);
    });
    fetch('/assets/icons/vdb-icons.svg')
        .then(function (response) { return response.ok ? response.text() : Promise.reject(); })
        .then(function (svgText) {
            const parser = new DOMParser();
            const doc = parser.parseFromString(svgText, 'image/svg+xml');
            const loadedIcons = Array.from(doc.querySelectorAll('symbol')).map(function (symbol) { return symbol.getAttribute('id') || ''; }).filter(function (icon) { return /^icon-[a-z0-9_-]+$/i.test(icon); }).sort();
            if (loadedIcons.length) icons = loadedIcons;
        })
        .catch(function () {})
        .finally(function () { renderIconOptions(''); updatePickerSelection(iconInput.value); updatePreview(); });
    renderIconOptions(''); updatePickerSelection(iconInput.value); updatePreview();
})();
</script>
