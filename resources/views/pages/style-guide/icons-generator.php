<?php
declare(strict_types=1);
$prefill = $prefill ?? ['icon' => 'icon-star', 'size' => 'vdb-icon--xl', 'color' => 'vdb-icon--primary', 'stroke' => 'vdb-icon--regular'];
?>
<h1>Icon Generator</h1>
<p>Wähle Größe, Farbe und Linienstärke. Kopiere den fertigen HTML-Code weiter unten.</p>
<div class="form__layout form__layout--split">
	<div class="form__panel">
		<form id="icon-generator-form" class="form">
			<div class="form__field form__picker" data-icon-picker>
				<label class="form__label" for="icon-name">Icon</label>
				<button type="button" class="btn btn--outline btn--md form__picker-toggle" id="icon-picker-toggle" aria-haspopup="listbox" aria-expanded="false">
					<span class="form__picker-summary">
						<svg class="vdb-icon vdb-icon--regular vdb-icon--md" aria-hidden="true">
							<use href="/assets/icons/vdb-icons.svg#<?= htmlspecialchars($prefill['icon'], ENT_QUOTES, 'UTF-8') ?>"></use>
						</svg>
						<span>
							<span class="form__picker-summary-label" data-picker-label><?= htmlspecialchars($prefill['icon'], ENT_QUOTES, 'UTF-8') ?></span>
							<span class="form__picker-summary-meta">Icon auswählen</span>
						</span>
					</span>
					<span aria-hidden="true">▾</span>
				</button>
				<input type="hidden" id="icon-name" name="icon" value="<?= htmlspecialchars($prefill['icon'], ENT_QUOTES, 'UTF-8') ?>" />
				<div class="form__picker-menu" id="icon-picker-menu" role="listbox" aria-label="Icons auswählen">
					<div class="form__picker-search">
						<input class="input" type="search" id="icon-picker-search" placeholder="Icons filtern..." autocomplete="off" />
					</div>
					<div class="form__picker-list" id="icon-picker-list">
						<div class="form__picker-empty">Icons werden geladen...</div>
					</div>
				</div>
				<div class="form__hint">Die Auswahl liest automatisch alle Symbole aus der Sprite-Datei.</div>
			</div>

			<div class="form__field-grid">
				<div class="form__field">
					<label class="form__label" for="icon-size">Größe</label>
					<select class="input" id="icon-size" name="size">
						<?php $sizes = ['vdb-icon--2xs','vdb-icon--xs','vdb-icon--sm','vdb-icon--md','vdb-icon--lg','vdb-icon--xl','vdb-icon--2xl','vdb-icon--3xl']; ?>
						<?php foreach ($sizes as $s): ?>
							<option value="<?= $s ?>" <?= $s === $prefill['size'] ? 'selected' : '' ?>><?= $s ?></option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="form__field">
					<label class="form__label" for="icon-color">Farbe</label>
					<select class="input" id="icon-color" name="color">
						<?php $colors = ['vdb-icon--primary','vdb-icon--secondary-cta','vdb-icon--secondary-highlight','vdb-icon--amber','vdb-icon--teal','vdb-icon--berry','vdb-icon--plum','vdb-icon--lime','vdb-icon--sun-yellow']; ?>
						<?php foreach ($colors as $c): ?>
							<option value="<?= $c ?>" <?= $c === $prefill['color'] ? 'selected' : '' ?>><?= $c ?></option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="form__field">
					<label class="form__label" for="icon-stroke">Linienstärke</label>
					<select class="input" id="icon-stroke" name="stroke">
						<?php $strokes = ['vdb-icon--thin','vdb-icon--regular','vdb-icon--medium','vdb-icon--bold','vdb-icon--extrabold']; ?>
						<?php foreach ($strokes as $st): ?>
							<option value="<?= $st ?>" <?= $st === $prefill['stroke'] ? 'selected' : '' ?>><?= $st ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>

			<div class="form__actions">
				<button class="btn btn--primary btn--md" type="button" id="copy-code">Code kopieren</button>
			</div>
		</form>

		<h3 class="form__panel-title form__panel-title--spaced">Beispiel</h3>
		<pre id="example-code" class="form__code">&lt;svg class="vdb-icon <?= htmlspecialchars($prefill['color'], ENT_QUOTES, 'UTF-8') ?> <?= htmlspecialchars($prefill['stroke'], ENT_QUOTES, 'UTF-8') ?> <?= htmlspecialchars($prefill['size'], ENT_QUOTES, 'UTF-8') ?>"&gt;&lt;use href="/assets/icons/vdb-icons.svg#<?= htmlspecialchars($prefill['icon'], ENT_QUOTES, 'UTF-8') ?>"&gt;&lt;/use&gt;&lt;/svg&gt;</pre>
	</div>

	<div class="form__panel form__panel--soft">
		<h3 class="form__panel-title">Vorschau</h3>
		<div class="form__preview">
			<svg id="preview-icon" class="vdb-icon <?= htmlspecialchars($prefill['color'], ENT_QUOTES, 'UTF-8') ?> <?= htmlspecialchars($prefill['stroke'], ENT_QUOTES, 'UTF-8') ?> <?= htmlspecialchars($prefill['size'], ENT_QUOTES, 'UTF-8') ?> form__preview-icon"><use href="/assets/icons/vdb-icons.svg#<?= htmlspecialchars($prefill['icon'], ENT_QUOTES, 'UTF-8') ?>"></use></svg>
		</div>
	</div>
</div>

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
	const sizeInput = document.getElementById('icon-size');
	const colorInput = document.getElementById('icon-color');
	const strokeInput = document.getElementById('icon-stroke');
	const preview = document.getElementById('preview-icon');
	const example = document.getElementById('example-code');
	const copyBtn = document.getElementById('copy-code');
	let icons = [];

	function setPickerOpen(isOpen) {
		pickerMenu.classList.toggle('is-open', isOpen);
		pickerToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
		if (isOpen) {
			pickerSearch.focus();
		}
	}

	function normalizeIconName(name) {
		return String(name || '').trim();
	}

	function updatePickerSelection(icon) {
		const normalized = normalizeIconName(icon) || 'icon-star';
		iconInput.value = normalized;
		pickerLabel.textContent = normalized;
		const use = pickerToggle.querySelector('use');
		if (use) {
			use.setAttribute('href', `/assets/icons/vdb-icons.svg#${normalized}`);
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

		if (!filtered.length) {
			pickerList.innerHTML = '<div class="form__picker-empty">Keine Icons gefunden.</div>';
			return;
		}

		filtered.forEach((icon) => {
			const button = document.createElement('button');
			button.type = 'button';
			button.className = 'form__picker-option';
			button.setAttribute('role', 'option');
			button.setAttribute('data-icon-option', '');
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

	function update() {
		const icon = iconInput.value || 'icon-star';
		const size = sizeInput.value;
		const color = colorInput.value;
		const stroke = strokeInput.value;

		preview.setAttribute('class', `vdb-icon ${color} ${stroke} ${size}`);
		preview.querySelector('use').setAttribute('href', `/assets/icons/vdb-icons.svg#${icon}`);

		example.textContent = `<svg class="vdb-icon ${color} ${stroke} ${size}"><use href="/assets/icons/vdb-icons.svg#${icon}"></use></svg>`;
	}

	pickerToggle.addEventListener('click', function () {
		setPickerOpen(!pickerMenu.classList.contains('is-open'));
	});

	document.addEventListener('click', function (event) {
		if (!picker.contains(event.target)) {
			setPickerOpen(false);
		}
	});

	pickerSearch.addEventListener('input', function () {
		renderIconOptions(pickerSearch.value);
	});

	pickerSearch.addEventListener('keydown', function (event) {
		if (event.key === 'Escape') {
			setPickerOpen(false);
			pickerToggle.focus();
		}
	});

	iconInput.addEventListener('input', update);
	sizeInput.addEventListener('change', update);
	colorInput.addEventListener('change', update);
	strokeInput.addEventListener('change', update);

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
