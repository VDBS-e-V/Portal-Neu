<?php declare(strict_types=1); ?>

<div class="error-page">
	<div class="error-text">
		<h1 class="error-code">403</h1>
		<h2 class="error-title">Zugriff verweigert</h2>
		<p class="error-subtitle">Sie haben keine Berechtigung, auf diese Seite zuzugreifen. Bitte überprüfen Sie Ihre Zugriffsrechte oder kontaktieren Sie den Support, wenn Sie glauben, dass dies ein Fehler ist.</p>

		<div class="btn-group btn-group--vertical">
			<button class="btn btn--secondary-cta btn-icon" type="button"  onclick="window.location.href='/'">
				<svg class="vdb-icon vdb-icon--current vdb-icon--regular vdb-icon--md"><use href="/assets/icons/vdb-icons.svg#icon-home"></use></svg>
				zur Startseite
			</button>
			<button class="btn btn--secondary-cta-transp btn-icon" type="button" onclick="window.location.href='/kontakt?form=service&title=Zugriff%20verweigert%20403&referrer=' + encodeURIComponent(window.location.href)">
				<svg class="vdb-icon vdb-icon--current vdb-icon--regular vdb-icon--md"><use href="/assets/icons/vdb-icons.svg#icon-contact-form"></use></svg>
				Kontakt aufnehmen
			</button>
		</div>
	</div>
	<div class="error-image">
		<img src="/assets/images/errors/zugriff-verweigert.png" alt="Illustration: Zugriff verweigert" />< 
	</div>
</div>