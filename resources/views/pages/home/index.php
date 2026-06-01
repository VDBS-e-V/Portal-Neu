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
	color: var(--color-ink);
	background-image: url('/assets/images/heros/portal-home.png');
	background-size: cover;
	background-position: center center;
}

.hero::before {
	content: "";
	position: absolute;
	inset: 0;
	background: linear-gradient(180deg, rgba(255,255,255,0.08) 0%, rgba(255,255,255,0.22) 100%); /* subtle dark overlay for better text contrast */
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
	font-size: clamp(40px, 6vw, 72px);
	line-height: 1.05;
	font-weight: var(--font-weight-extrabold);
	margin: 0 0 var(--spacing-8);
}

.hero__lead {
	margin: 0 auto;
	max-width: 500px;
	opacity: 1;
	font-size: 1.125rem;
	font-weight: var(--font-weight-light);
	color: var(--color-white);
	text-align: center;
	hyphens: auto;
}

.hero__lead__badge {
	background: var(--color-primary);
	padding: 4px 10px;
	display: inline;
	-webkit-box-decoration-break: clone;
	box-decoration-break: clone;
	text-decoration: none;
	hyphens: auto;
	box-shadow: 0 8px 24px rgba(0,0,0,0.16);
}

@media (max-width: 720px) {
	.hero { min-height: 360px; }
	.hero__tiles { flex-direction: column; width: auto; margin-left: 0; }
	.hero__lead { text-align: center; }
}
.page-meta { padding: 24px 0; }
</style>

<div class="hero" role="banner" aria-label="Hero - Willkommen">
	<div class="hero__inner">
		<div class="hero__copy">
			<h1 class="hero__title"><?= htmlspecialchars($title ?? 'Willkommen im VDBS Serviceportal', ENT_QUOTES, 'UTF-8') ?></h1>
			<p class="hero__lead"><span class="hero__lead__badge">Zentrale Services, Zugang zu Ihrem Konto und Hilfestellungen — schnell und übersichtlich.</span></p>
		</div>
	</div>
</div>

<div class="hero__tiles" role="navigation" aria-label="Portal Schnellzugriff">
    <?php
		// Erzeuge eine zufällige Reihenfolge der Buchstaben a-e
		$letters = range('a', 'e');
		shuffle($letters);

        // Links
        $links = [
            1 => ["/ueber-das-portal", "Über das Portal"],
            2 => ["/zugang-zum-portal", "Zugang zum Portal"],
            3 => ["/mein-konto", "Mein Konto"],
            4 => ["/hilfe", "Hilfe"]
        ];

        $i = 0;
        while ($i < 4) {
            echo '<a class="hero__tile hero__tile--' . $letters[$i] . ' link--no-style" href="' . htmlspecialchars($links[$i + 1][0], ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($links[$i + 1][1], ENT_QUOTES, 'UTF-8') . '</a>';
            $i++;
        }
	?>        
</div>

<section id="uebersicht-portal">
	<div class="container_uebersicht">
		<div class="btn-group" aria-label="Schnellzugriff">
			<a class="btn btn--primary btn--md" href="/">Start</a>
			<a class="btn btn--outline btn--md" href="/development">Development</a>
			<a class="btn btn--ghost btn--md" href="/styleguide">Styleguide</a>
		</div>
	</div>
</section>
