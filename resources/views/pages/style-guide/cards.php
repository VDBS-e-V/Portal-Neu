<?php declare(strict_types=1); ?>

<h1><?php echo htmlspecialchars($pageTitle ?? 'Styleguide', ENT_QUOTES, 'UTF-8'); ?></h1>

<p>Diese Seite zeigt die Kachelvarianten als Vorlage. Default‑Hintergrund ist leichtes Grau; Hover verwendet ein intensiveres Grau. Bildquellen werden als kleines Badge im Bild angezeigt. Beispielbilder werden von <a href="https://picsum.photos/600/900" target="_blank" rel="noopener">picsum.photos</a> geladen.</p>

<section>
    <div class="card-grid">
        <article class="card">
            <div class="card__image">
                <img src="https://picsum.photos/600/900" alt="Beispielbild">
                <span class="card__badge">Bildquelle: dpa</span>
            </div>
            <div class="card__content">
                <h3 class="card__title">„Es gibt immer einen nächsten Text zu schreiben, ein nächstes Buch”</h3>
                <p class="card__text">Rhetorikforscherin, Erstakademikerin, feministische Wissenschaftlerin: Interview mit der Romanistin Anita Traninger, Berliner Wissenschaftspreisträgerin 2025</p>
                <h6 class="card__posttype">Blogartikel</h6>
            </div>
            <div class="card__icon" aria-hidden="true">
                <svg class="card__icon-svg" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
                    <path d="M5 12h12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
        </article>
    </div>
</section>