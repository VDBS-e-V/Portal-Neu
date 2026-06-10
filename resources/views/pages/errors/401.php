<?php declare(strict_types=1); ?>

<section>
    <div class="error-page">
        <div class="error-text">
            <h1 class="error-code">401</h1>
            <h2 class="error-title">Nicht autorisiert</h2>
            <p class="error-subtitle">Sie sind nicht autorisiert, auf diese Seite zuzugreifen. Bitte melden Sie sich an oder kontaktieren Sie den Support, wenn Sie glauben, dass dies ein Fehler ist.</p>

            <div class="btn-group btn-group--vertical">
                <button class="btn btn--secondary-cta btn-icon" type="button"  onclick="window.location.href='/'">
                    <svg class="vdb-icon vdb-icon--current vdb-icon--regular vdb-icon--md"><use href="/assets/icons/vdb-icons.svg#icon-home"></use></svg>
                    zur Startseite
                </button>
                <button class="btn btn--secondary-cta-transp btn-icon" type="button" onclick="window.location.href='/kontakt?form=service&title=Nicht%20autorisiert%20401&referrer=' + encodeURIComponent(window.location.href)">
                    <svg class="vdb-icon vdb-icon--current vdb-icon--regular vdb-icon--md"><use href="/assets/icons/vdb-icons.svg#icon-contact-form"></use></svg>
                    Kontakt aufnehmen
                </button>
            </div>
        </div>
        <div class="error-image">
            <img src="/assets/images/errors/nicht-autorisiert.png" alt="Illustration: Nicht autorisiert" />< 
        </div>
    </div>
</section>
