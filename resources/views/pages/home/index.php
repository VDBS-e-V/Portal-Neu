<?php declare(strict_types=1); ?>

<style>
/* Page-specific Hero styles */
.hero {
	position: relative;
	overflow: hidden;
	/* make the hero fill the viewport height minus the header spacing token */
	height: calc(60vh);
	min-height: 360px;
	display: flex;
	flex-direction: column;
	justify-content: center;
	align-items: stretch;
	color: var(--color-white);
	background-image: url('/assets/images/heros/sitehero.jpg');
	background-size: cover;
	background-position: center center;
}

.hero::before {
	content: "";
	position: absolute;
	inset: 0;
	background: linear-gradient(180deg, rgba(0,0,0,0.30) 0%, rgba(0,0,0,0.60) 100%); /* subtle dark overlay for better text contrast */
	z-index: 1;
}

.hero__inner {
	position: relative;
	z-index: 2;
	max-width: var(--max-width);
	margin: 0 auto;
	padding: calc(var(--spacing-header) / 2) var(--spacing-container) var(--spacing-3);
	text-align: center;
}

.hero__title {
	font-size: clamp(28px, 4.4vw, 48px);
	line-height: 1.05;
	font-weight: var(--font-weight-extrabold);
	margin: 0 0 var(--spacing-3);
}

.hero__lead {
	margin: 0 auto;
	max-width: 700px;
	opacity: 0.95;
}

@media (max-width: 720px) {
	.hero { min-height: 360px; }
	.hero__tiles { flex-direction: column; width: auto; margin-left: 0; }
}
.page-meta { padding: 24px 0; }
</style>

<div class="hero" role="banner" aria-label="Hero - Willkommen">
	<div class="hero__inner">
		<div class="hero__copy">
			<h1 class="hero__title"><?= htmlspecialchars($title ?? 'Willkommen im VDBS Serviceportal', ENT_QUOTES, 'UTF-8') ?></h1>
			<p class="hero__lead">Zentrale Services, Zugang zu Ihrem Konto und Hilfestellungen — schnell und übersichtlich.</p>
		</div>
	</div>
</div>

<div class="hero__tiles" role="navigation" aria-label="Portal Schnellzugriff">
	<a class="hero__tile hero__tile--a link--no-style" href="#">Über das Portal</a>
	<a class="hero__tile hero__tile--b link--no-style" href="#">Zugang zum Portal</a>
	<a class="hero__tile hero__tile--c link--no-style" href="#">Mein Konto</a>
	<a class="hero__tile hero__tile--d link--no-style" href="#">Hilfe</a>
</div>

<section class="page-meta">
	<p>OK – Seite rendert. <br>
    <a href="">sdfds</a></p>
	<ul>
		<li>Path: <code><?= htmlspecialchars($path ?? '', ENT_QUOTES, 'UTF-8') ?></code></li>
		<li>Now: <code><?= htmlspecialchars($now ?? '', ENT_QUOTES, 'UTF-8') ?></code></li>
	</ul>
</section>