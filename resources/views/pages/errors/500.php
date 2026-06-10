<?php declare(strict_types=1); ?>

<section>
    <div class="error-page">
        <div class="error-text">
            <h1 class="error-code">500</h1>
            <h2 class="error-title">Serverfehler aufgetreten</h2>
            <p class="error-subtitle">
                Es ist ein unerwarteter Fehler auf dem Server aufgetreten. Bitte versuchen Sie es später erneut oder kontaktieren Sie den Support, wenn das Problem weiterhin besteht. Wir entschuldigen uns für die Unannehmlichkeiten und arbeiten daran, das Problem so schnell wie möglich zu beheben.
            </p>

            <div class="btn-group btn-group--vertical">
                <button class="btn btn--secondary-cta btn-icon" type="button" onclick="window.location.href='/'">
                    <svg class="vdb-icon vdb-icon--current vdb-icon--regular vdb-icon--md"><use href="/assets/icons/vdb-icons.svg#icon-home"></use></svg>
                    zur Startseite
                </button>
                <button class="btn btn--secondary-cta-transp btn-icon" type="button" onclick="window.location.href='/kontakt?form=it-support&title=Serverfehler%20500&referrer=' + encodeURIComponent(window.location.href)">
                    <svg class="vdb-icon vdb-icon--current vdb-icon--regular vdb-icon--md"><use href="/assets/icons/vdb-icons.svg#icon-contact-form"></use></svg>
                    Kontakt aufnehmen
                </button>
            </div>
        </div>
        <div class="error-image">
            <img src="/assets/images/errors/server-fehler.png" alt="Illustration: Serverfehler" />< 
        </div>
    </div>
</section>