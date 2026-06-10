<?php declare(strict_types=1); ?>

<section id="hero" class=" no-padding">
    <div
        class="hero hero--image hero--portal hero--center"
        role="banner"
        aria-label="Hero - Willkommen"
    >
        <picture class="hero__background">
            <img
                src="/assets/images/heros/portal-home.png"
                alt=""
                aria-hidden="true"
            >
        </picture>

        <div class="hero__inner">
            <div class="hero__copy">
                <h1 class="hero__title">
                    VDBS Portal
                </h1>

                <p class="hero__lead">
                    <span class="hero__lead__badge">
                        Zentrale Services, Zugang zu Ihrem Konto und Hilfestellungen —
                        schnell und übersichtlich.
                    </span>
                </p>
            </div>
        </div>
    </div>

    <?php
    $areas = [
        [
            "name" => "Zugang zum Portal",
            "link" => "/zugang-zum-portal"
        ],
        [
            "name" => "Mein Konto",
            "link" => "/mein-konto"
        ],
        [
            "name" => "Hilfe",
            "link" => "/hilfe"
        ],
        [
            "name" => "Über das Portal",
            "link" => "/ueber-das-portal"
        ]
    ];

    // Farbzuweisungen für die Kacheln (Zufall)
    $colors = ["a", "b", "c", "d", "e"];
    shuffle($colors);
    ?>

    <nav class="hero__tiles" aria-label="Portal Schnellzugriff">
        <?php
        foreach ($areas as $index => $area) {
            $colorClass = "hero__tile--" . $colors[$index % count($colors)];
            ?>
            <a class="hero__tile <?php echo $colorClass ?>" href="<?php echo $area["link"] ?>">
                <?php echo $area["name"] ?>
            </a>
            <?php
        }
        ?>
    </nav>
</section>

<section id="uebersicht-portal" class="small">
	<div class="btn-group btn-group--horizontal btn-group--main-center btn-group--gap-md" role="group" aria-label="Optionen Übersicht Portal">
		<div class="btn-group__title-container">
			<h1 class="btn-group__title">Das VDBS Serviceportal</h1>
			<h2 class="btn-group__subtitle">
				Das VDBS Serviceportal ist Ihre zentrale Anlaufstelle für alle Informationen und Dienstleistungen rund um den VDBS. Hier finden Sie alle Bereiche des VDBS Serviceportals.
			</h2>
		</div>

        <?php        
        foreach ($areas as $area) {
            ?>
            <a href="<?php echo $area["link"] ?>" class="link--no-style"><button class="btn btn--secondary-cta-light btn--md"><?php echo $area["name"] ?></button></a>
            <?php
        }    
        ?>

        <a href="/vorstand" class="link--no-style"><button class="btn btn--secondary-cta-light btn--md">Vorstand</button></a>
        <a href="/development" class="link--no-style"><button class="btn btn--secondary-cta-light btn--md">Development</button></a>
        <a href="/styleguide" class="link--no-style"><button class="btn btn--secondary-cta-light btn--md">StyleGuide</button></a>
        <a href="/teamende" class="link--no-style"><button class="btn btn--secondary-cta-light btn--md">Teamende</button></a>
        <a href="/mitglieder" class="link--no-style"><button class="btn btn--secondary-cta-light btn--md">Mitglieder</button></a>
    </div>
</section>

<section id="empfohlene-artikel">
    <div class="grid grid--3col grid--stretch">
        <a class="tile-card tile-card--link" href="/news/1">
            <figure class="tile-card__media tile-card__media--ratio-4-3">
                <img src="/assets/images/blog/mehr_sicherheit.jpg" alt="">
                <figcaption class="tile-card__credit">Bildquelle: Plexels</figcaption>
            </figure>

            <div class="tile-card__body">
                <p class="tile-card__meta">Themenartikel</p>
                <h3 class="tile-card__title">
                    Mehr Sicherheit durch eigene Infrastruktur
                </h3>
                <p class="tile-card__text">
                    Öffentliche Veranstaltung am 18. Juni von 18 bis 20 Uhr.
                </p>
            </div>

            <span class="tile-card__action" aria-hidden="true">
                <svg class="vdb-icon">
                    <use href="/assets/icons/vdb-icons.svg#icon-arrow-right"></use>
                </svg>
            </span>
        </a>

        <a class="tile-card tile-card--link" href="/news/2">
            <figure class="tile-card__media tile-card__media--ratio-4-3">
                <img src="/assets/images/blog/digitalisierte_verwaltung.jpg" alt="">
                <figcaption class="tile-card__credit">Bildquelle: Plexels</figcaption>
            </figure>

            <div class="tile-card__body">
                <p class="tile-card__meta">Themenartikel</p>
                <h3 class="tile-card__title">
                    Wir digitalisieren unsere Arbeitsabläufe
                </h3>
                <p class="tile-card__text">
                    Kurzbeschreibung der Meldung.
                </p>
            </div>

            <span class="tile-card__action" aria-hidden="true">
                <svg class="vdb-icon">
                    <use href="/assets/icons/vdb-icons.svg#icon-arrow-right"></use>
                </svg>
            </span>
        </a>

        <a class="tile-card tile-card--link" href="/news/3">
            <figure class="tile-card__media tile-card__media--ratio-4-3">
                <img src="/assets/images/blog/eigene_cloud.jpg" alt="">
                <figcaption class="tile-card__credit">Bildquelle: Plexels</figcaption>
            </figure>

            <div class="tile-card__body">
                <p class="tile-card__meta">Themenartikel</p>
                <h3 class="tile-card__title">
                    Eine Eigene Cloud - bald kein Traum mehr
                </h3>
                <p class="tile-card__text">
                    Beschreibungstext der Meldung.
                </p>
            </div>

            <span class="tile-card__action" aria-hidden="true">
                <svg class="vdb-icon">
                    <use href="/assets/icons/vdb-icons.svg#icon-arrow-right"></use>
                </svg>
            </span>
        </a>
    </div>
</section>

<section id="zugang-zum-portal" class="">
    <div class="container container--text-media container--balanced">
        <div class="container__text">
            <h3>Zugang zum Portal</h3>

            <p>
                Hier finden Sie alle Informationen, die Sie benötigen, um auf das VDBS Serviceportal zuzugreifen. Wenn Sie bereits ein Konto haben, können Sie sich hier anmelden. Wenn Sie noch kein Konto haben, können Sie sich hier registrieren. 
            </p>
        </div>

        <div class="container__buttons btn-group btn-group--vertical btn-group--gap-sm">
            <a href="/zugang-zum-portal" class="btn btn--primary btn--md">Zugang zum Portal</a>
            <a href="/kontakt?preset=1" class="btn btn--secondary-cta-light btn--md">Hilfe kontaktieren</a>
        </div>

        <figure class="container__media media">
            <div class="media__frame media__frame--contain media__frame--ratio-4-3">
                <img
                    class="media__image"
                    src="/assets/images/home/home_zugang.png"
                    alt="Zugang zum Portal"
                >
            </div>

            <figcaption class="media__caption">
                <span class="media__caption-title">
                    Zugang zum VDBS Serviceportal
                </span>

                <span class="media__credit media__credit--below">
                    Bildquelle: VDBS / Max Mustermann
                </span>
            </figcaption>
        </figure>
    </div>
</section>

<section id="kontakt" class="section--surface">
    <div class="container container--media-text container--balanced">
        <div class="container__text">
            <h3>Kontakt</h3>

            <p>
                Sollten Sie Fragen oder Probleme haben, können Sie uns jederzeit kontaktieren. Wir helfen Ihnen gerne weiter. Sie erreichen uns unter den E-Mail-Adressen: <br>
                <b>Support</b> support@portal.vdb.schule <br>
                <b>Kontakt</b> kontakt@vdb.schule 
            </p>
        </div>

        <div class="container__buttons btn-group btn-group--vertical btn-group--gap-sm">
            <a href="/kontakt" class="btn btn--primary btn--md">Kontaktformular öffnen</a>
        </div>

        <figure class="container__media media">
            <div class="media__frame media__frame--contain media__frame--ratio-4-3">
                <img
                    class="media__image"
                    src="/assets/images/home/home_kontakt.png"
                    alt="Kontakt"
                >
            </div>

            <figcaption class="media__caption">
                <span class="media__caption-title">
                    Kontakt und Unterstützung im VDBS Serviceportal
                </span>

                <span class="media__credit media__credit--below">
                    Bildquelle: VDBS / Max Mustermann
                </span>
            </figcaption>
        </figure>
    </div>
</section>