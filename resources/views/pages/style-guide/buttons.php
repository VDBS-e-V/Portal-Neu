<?php declare(strict_types=1); ?>

<h1>Buttons</h1>
<p>Beispiele für Größen, Farben, Icons und Gruppen. Nutze die Klassen aus <code>public/assets/css/components/buttons.css</code>.</p>

<h2>Größen</h2>
<div class="row">
	<button type="button" class="btn btn--xs btn--primary">XS</button>
	<button type="button" class="btn btn--sm btn--primary">SM</button>
	<button type="button" class="btn btn--md btn--primary">MD</button>

	<button type="button" class="btn btn--lg btn--primary">LG</button>
	<button type="button" class="btn btn--xl btn--primary">XL</button>
</div>

<h2>Farben / Varianten</h2>
<div class="row">
	<button type="button" class="btn btn--md btn--primary">Primary</button>
	<button type="button" class="btn btn--md btn--secondary-cta">Secondary CTA</button>
	<button type="button" class="btn btn--md btn--secondary-highlight">Secondary Highlight</button>
</div>
<br>
<div class="row">
    <button type="button" class="btn btn--md btn--primary-light">Primary</button>
	<button type="button" class="btn btn--md btn--secondary-cta-light">Secondary CTA</button>
	<button type="button" class="btn btn--md btn--secondary-highlight-light">Secondary Highlight</button>
</div>
<br>
<div class="row">
    <button type="button" class="btn btn--md btn--primary-transp">Primary</button>
	<button type="button" class="btn btn--md btn--secondary-cta-transp">Secondary CTA</button>
	<button type="button" class="btn btn--md btn--secondary-highlight-transp">Secondary Highlight</button>
</div>

<h2>Status Farben</h2>
<div class="row">
	<button type="button" class="btn btn--md btn--success">Success</button>
	<button type="button" class="btn btn--md btn--error">Error</button>
	<button type="button" class="btn btn--md btn--warning">Warning</button>
	<button type="button" class="btn btn--md btn--info">Info</button>
</div>

<h2>Icon + Text</h2>
<div class="row">
	<button type="button" class="btn btn--md btn--primary btn--gap-sm btn-icon">
		<svg class="vdb-icon vdb-icon--md" aria-hidden="true"><use href="/assets/icons/vdb-icons.svg#icon-save"></use></svg>
		Speichern
	</button>
	<button type="button" class="btn btn--md btn--secondary-cta btn--gap-sm btn-icon">
		<svg class="vdb-icon vdb-icon--md" aria-hidden="true"><use href="/assets/icons/vdb-icons.svg#icon-edit"></use></svg>
		Bearbeiten

	</button>
	<button type="button" class="btn btn--md btn--success btn--gap-sm btn-icon">
		<svg class="vdb-icon vdb-icon--md" aria-hidden="true"><use href="/assets/icons/vdb-icons.svg#icon-check"></use></svg>
		Fertig
	</button>
</div>

<h2>Icon-only</h2>
<div class="row">
	<button type="button" class="btn btn--md btn--primary btn-icon btn--icon-only" aria-label="Speichern">
		<svg class="vdb-icon vdb-icon--md" aria-hidden="true"><use href="/assets/icons/vdb-icons.svg#icon-save"></use></svg>
	</button>
	<button type="button" class="btn btn--md btn--secondary-cta btn-icon btn--icon-only" aria-label="Schließen">
		<svg class="vdb-icon vdb-icon--md" aria-hidden="true"><use href="/assets/icons/vdb-icons.svg#icon-close"></use></svg>

	</button>
</div>

<h2>Gap Utilities</h2>
<div class="row">
	<button type="button" class="btn btn--md btn--primary btn--gap-xs btn-icon"><svg class="vdb-icon vdb-icon--md" aria-hidden="true"><use href="/assets/icons/vdb-icons.svg#icon-save"></use></svg>XS gap</button>
	<button type="button" class="btn btn--md btn--primary btn--gap-sm btn-icon"><svg class="vdb-icon vdb-icon--md" aria-hidden="true"><use href="/assets/icons/vdb-icons.svg#icon-save"></use></svg>SM gap</button>
	<button type="button" class="btn btn--md btn--primary btn--gap-md btn-icon"><svg class="vdb-icon vdb-icon--md" aria-hidden="true"><use href="/assets/icons/vdb-icons.svg#icon-save"></use></svg>MD gap</button>
	<button type="button" class="btn btn--md btn--primary btn--gap-lg btn-icon"><svg class="vdb-icon vdb-icon--md" aria-hidden="true"><use href="/assets/icons/vdb-icons.svg#icon-save"></use></svg>LG gap</button>
	<button type="button" class="btn btn--md btn--primary btn--gap-xl btn-icon"><svg class="vdb-icon vdb-icon--md" aria-hidden="true"><use href="/assets/icons/vdb-icons.svg#icon-save"></use></svg>XL gap</button>
</div>

<h2>Gruppen</h2>
<div class="row">
	<div class="btn-group btn-group--horizontal btn-group--gap-md" role="group" aria-label="Gruppenbeispiel">
		<button type="button" class="btn btn--md btn--secondary-cta">Option 1</button>
		<button type="button" class="btn btn--md btn--secondary-cta">Option 2</button>
		<button type="button" class="btn btn--md btn--secondary-cta">Option 3</button>
	</div>
</div>

<h2>Disabled</h2>
<div class="row">
	<button type="button" class="btn btn--md is-disabled" disabled>Disabled</button>
</div>

