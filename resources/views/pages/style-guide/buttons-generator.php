<?php
declare(strict_types=1);
$prefill = $prefill ?? [
    'label' => 'Button',
    'size' => 'btn--md',
    'variant' => 'btn--primary',
    'icon' => '',
    'iconPosition' => 'left',
    'gap' => 'btn--gap-sm',
    'type' => 'button',
    'iconOnly' => false,
];
?>
<h1>Button Generator</h1>
<p>Wähle Label, Größe, Variante und optional ein Icon. Kopiere den fertigen HTML-Code weiter unten.</p>

<div class="form__layout form__layout--split">
    <div class="form__panel">
        <form id="button-generator-form" class="form">
            <div class="form__field">
                <label class="form__label" for="btn-label">Label</label>
                <input class="input" id="btn-label" name="label" type="text" value="<?= htmlspecialchars($prefill['label'], ENT_QUOTES, 'UTF-8') ?>" />
            </div>

            <div class="form__field-grid">
                <div class="form__field">
                    <label class="form__label" for="btn-size">Größe</label>
                    <select class="input" id="btn-size" name="size">
                        <?php $sizes = ['btn--xs','btn--sm','btn--md','btn--lg','btn--xl']; ?>
                        <?php foreach ($sizes as $s): ?>
                            <option value="<?= $s ?>" <?= $s === $prefill['size'] ? 'selected' : '' ?>><?= $s ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form__field">
                    <label class="form__label" for="btn-variant">Variante</label>
                    <select class="input" id="btn-variant" name="variant">
                        <?php $variants = ['btn--primary','btn--primary-light','btn--primary-transp','btn--secondary-cta','btn--secondary-cta-light','btn--secondary-cta-transp','btn--secondary-highlight','btn--secondary-highlight-light','btn--secondary-highlight-transp','btn--success','btn--error','btn--warning','btn--info']; ?>
                        <?php foreach ($variants as $v): ?>
                            <option value="<?= $v ?>" <?= $v === $prefill['variant'] ? 'selected' : '' ?>><?= $v ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form__field form__picker" data-btn-icon-picker>
                <label class="form__label" for="btn-icon-name">Icon</label>
                <button type="button" class="btn btn--outline btn--md form__picker-toggle" id="btn-picker-toggle" aria-haspopup="listbox" aria-expanded="false">
                    <span class="form__picker-summary">
                        <svg class="vdb-icon vdb-icon--regular vdb-icon--md" aria-hidden="true"><use href="/assets/icons/vdb-icons.svg#<?= htmlspecialchars($prefill['icon'] ?: 'icon-star', ENT_QUOTES, 'UTF-8') ?>"></use></svg>
                        <span>
                            <span class="form__picker-summary-label" data-picker-label><?= htmlspecialchars($prefill['icon'] ?: 'kein Icon', ENT_QUOTES, 'UTF-8') ?></span>
                            <span class="form__picker-summary-meta">Icon auswählen</span>
                        </span>
                    </span>
                    <span aria-hidden="true">▾</span>
                </button>
                <input type="hidden" id="btn-icon-name" name="icon" value="<?= htmlspecialchars($prefill['icon'], ENT_QUOTES, 'UTF-8') ?>" />
                <div class="form__picker-menu" id="btn-picker-menu" role="listbox" aria-label="Icons auswählen">
                    <div class="form__picker-search">
                        <input class="input" type="search" id="btn-picker-search" placeholder="Icons filtern..." autocomplete="off" />
                    </div>
                    <div class="form__picker-list" id="btn-picker-list">
                        <div class="form__picker-empty">Icons werden geladen...</div>
                    </div>
                </div>
                <div class="form__hint">Wähle ein Icon oder lasse das Feld leer.</div>
            </div>

            <div class="form__field-grid">
                <div class="form__field">
                    <label class="form__label" for="btn-icon-position">Icon Position</label>
                    <select class="input" id="btn-icon-position" name="iconPosition">
                        <option value="left" <?= $prefill['iconPosition'] === 'left' ? 'selected' : '' ?>>links</option>
                        <option value="right" <?= $prefill['iconPosition'] === 'right' ? 'selected' : '' ?>>rechts</option>
                    </select>
                </div>

                <div class="form__field">
                    <label class="form__label" for="btn-gap">Gap</label>
                    <select class="input" id="btn-gap" name="gap">
                        <?php $gaps = ['btn--gap-xs','btn--gap-sm','btn--gap-md','btn--gap-lg','btn--gap-xl']; ?>
                        <?php foreach ($gaps as $g): ?>
                            <option value="<?= $g ?>" <?= $g === $prefill['gap'] ? 'selected' : '' ?>><?= $g ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form__field">
                <label class="form__label" for="btn-icon-only">Icon-only</label>
                <input type="checkbox" id="btn-icon-only" name="iconOnly" <?= $prefill['iconOnly'] ? 'checked' : '' ?> />
                <div class="form__hint">Zeige nur das Icon (setze `aria-label` für Zugänglichkeit)</div>
            </div>

            <div class="form__field">
                <label class="form__label" for="btn-type">Type</label>
                <select class="input" id="btn-type" name="type">
                    <option value="button" <?= $prefill['type'] === 'button' ? 'selected' : '' ?>>button</option>
                    <option value="submit" <?= $prefill['type'] === 'submit' ? 'selected' : '' ?>>submit</option>
                    <option value="reset" <?= $prefill['type'] === 'reset' ? 'selected' : '' ?>>reset</option>
                </select>
            </div>

            <div class="form__actions">
                <button class="btn btn--primary btn--md" type="button" id="copy-code">Code kopieren</button>
            </div>
        </form>

        <h3 class="form__panel-title form__panel-title--spaced">Beispiel</h3>
        <pre id="example-code" class="form__code"></pre>
    </div>

    <div class="form__panel form__panel--soft">
        <h3 class="form__panel-title">Vorschau</h3>
        <div class="form__preview">
            <div id="preview-button" class="form__preview-icon"></div>
        </div>
    </div>
</div>

<script>
(function(){
    const picker = document.querySelector('[data-btn-icon-picker]');
    const pickerToggle = document.getElementById('btn-picker-toggle');
    const pickerMenu = document.getElementById('btn-picker-menu');
    const pickerList = document.getElementById('btn-picker-list');
    const pickerSearch = document.getElementById('btn-picker-search');
    const pickerLabel = document.querySelector('[data-picker-label]');
    const iconInput = document.getElementById('btn-icon-name');
    const labelInput = document.getElementById('btn-label');
    const sizeInput = document.getElementById('btn-size');
    const variantInput = document.getElementById('btn-variant');
    const gapInput = document.getElementById('btn-gap');
    const positionInput = document.getElementById('btn-icon-position');
    const iconOnlyInput = document.getElementById('btn-icon-only');
    const typeInput = document.getElementById('btn-type');
    const previewWrap = document.getElementById('preview-button');
    const example = document.getElementById('example-code');
    const copyBtn = document.getElementById('copy-code');

    let icons = [];

    function setPickerOpen(isOpen) {
        pickerMenu.classList.toggle('is-open', isOpen);
        pickerToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        if (isOpen) pickerSearch.focus();
    }

    function normalizeIconName(name) {
        return String(name || '').trim();
    }

    function updatePickerSelection(icon) {
        const normalized = normalizeIconName(icon) || '';
        iconInput.value = normalized;
        pickerLabel.textContent = normalized || 'kein Icon';
        const use = pickerToggle.querySelector('use');
        if (use) {
            use.setAttribute('href', `/assets/icons/vdb-icons.svg#${normalized || 'icon-star'}`);
        }
        pickerList.querySelectorAll('[data-icon-option]').forEach((button) => {
            const active = button.getAttribute('data-icon-name') === normalized;
            button.classList.toggle('is-selected', active);
            button.setAttribute('aria-selected', active ? 'true' : 'false');
        });
    }

    function renderIconOptions(query = '') {
        const term = query.trim().toLowerCase();
        const filtered = icons.filter((icon) => !term || icon.toLowerCase().includes(term));
        pickerList.innerHTML = '';

        // "No icon" option
        const noneBtn = document.createElement('button');
        noneBtn.type = 'button';
        noneBtn.className = 'form__picker-option';
        noneBtn.setAttribute('role','option');
        noneBtn.setAttribute('data-icon-option','');
        noneBtn.setAttribute('data-icon-name','');
        noneBtn.innerHTML = `<span class="form__picker-option-text"><span class="form__picker-option-name">kein Icon</span></span>`;
        noneBtn.addEventListener('click', function(){
            updatePickerSelection('');
            setPickerOpen(false);
            update();
        });
        pickerList.appendChild(noneBtn);

        if (!filtered.length) {
            if (icons.length === 0) {
                pickerList.innerHTML = '<div class="form__picker-empty">Icons werden geladen...</div>';
            } else {
                pickerList.innerHTML = '<div class="form__picker-empty">Keine Icons gefunden.</div>';
            }
            return;
        }

        filtered.forEach((icon) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'form__picker-option';
            button.setAttribute('role','option');
            button.setAttribute('data-icon-option','');
            button.setAttribute('data-icon-name', icon);
            button.setAttribute('aria-selected', icon === iconInput.value ? 'true' : 'false');
            button.innerHTML = `
                <svg class="form__picker-option-icon vdb-icon vdb-icon--regular vdb-icon--md" aria-hidden="true"><use href="/assets/icons/vdb-icons.svg#${icon}"></use></svg>
                <span class="form__picker-option-text">
                    <span class="form__picker-option-name">${icon}</span>
                    <span class="form__picker-option-code">#${icon}</span>
                </span>
            `;
            button.addEventListener('click', function () {
                updatePickerSelection(icon);
                setPickerOpen(false);
                update();
            });
            pickerList.appendChild(button);
        });

        updatePickerSelection(iconInput.value);
    }

    function escapeHtml(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function update() {
        const label = labelInput.value || '';
        const size = sizeInput.value;
        const variant = variantInput.value;
        const gap = gapInput.value;
        const icon = iconInput.value || '';
        const iconPos = positionInput.value;
        const iconOnly = !!iconOnlyInput.checked;
        const type = typeInput.value || 'button';

        const classes = ['btn', size, variant];
        if (icon) classes.push('btn-icon');
        if (icon && !iconOnly) classes.push(gap);
        if (iconOnly) classes.push('btn--icon-only');

        const classAttr = classes.join(' ').trim();

        let html = '';
        if (iconOnly && icon) {
            html = `<button type="${type}" class="${classAttr}" aria-label="${escapeHtml(label || 'icon')}">`;
            html += `<svg class="vdb-icon vdb-icon--md" aria-hidden="true"><use href="/assets/icons/vdb-icons.svg#${icon}"></use></svg>`;
            html += `</button>`;
        } else if (icon) {
            if (iconPos === 'left') {
                html = `<button type="${type}" class="${classAttr}">`;
                html += `<svg class="vdb-icon vdb-icon--md" aria-hidden="true"><use href="/assets/icons/vdb-icons.svg#${icon}"></use></svg>`;
                html += `${escapeHtml(label)}</button>`;
            } else {
                html = `<button type="${type}" class="${classAttr}">`;
                html += `${escapeHtml(label)}`;
                html += `<svg class="vdb-icon vdb-icon--md" aria-hidden="true"><use href="/assets/icons/vdb-icons.svg#${icon}"></use></svg>`;
                html += `</button>`;
            }
        } else {
            html = `<button type="${type}" class="${classAttr}">${escapeHtml(label)}</button>`;
        }

        previewWrap.innerHTML = html;
        example.textContent = html;
    }

    pickerToggle.addEventListener('click', function () {
        setPickerOpen(!pickerMenu.classList.contains('is-open'));
    });

    document.addEventListener('click', function (event) {
        if (!picker.contains(event.target)) setPickerOpen(false);
    });

    pickerSearch.addEventListener('input', function () { renderIconOptions(pickerSearch.value); });
    pickerSearch.addEventListener('keydown', function (event) { if (event.key === 'Escape') { setPickerOpen(false); pickerToggle.focus(); } });

    iconInput.addEventListener('input', update);
    labelInput.addEventListener('input', update);
    sizeInput.addEventListener('change', update);
    variantInput.addEventListener('change', update);
    gapInput.addEventListener('change', update);
    positionInput.addEventListener('change', update);
    iconOnlyInput.addEventListener('change', update);
    typeInput.addEventListener('change', update);

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
            updatePickerSelection(iconInput.value);
        })
        .catch(function () {
            pickerList.innerHTML = '<div class="form__picker-empty">Icons konnten nicht geladen werden.</div>';
        });

    copyBtn.addEventListener('click', function(){
        navigator.clipboard.writeText(example.textContent).then(function(){
            copyBtn.textContent = 'Kopiert!';
            setTimeout(()=> copyBtn.textContent = 'Code kopieren', 1500);
        });
    });

    update();
})();
</script>
