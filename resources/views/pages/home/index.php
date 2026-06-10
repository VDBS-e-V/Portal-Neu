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
                    <?= htmlspecialchars($title ?? 'Willkommen im VDBS Serviceportal', ENT_QUOTES, 'UTF-8') ?>
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

<section id="uebersicht-portal">
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

<section id="zugang-zum-portal">
    <div class="container container--text-media container--balanced">
        <figure class="container__media container__media--contain container__media--ratio-4-3">
            <img src="/assets/images/home/home_zugang.png" alt="Zugang zum Portal">
        </figure>

        <div class="container__text">
            <h3>Zugang zum Portal</h3>

            <p>
                Hier finden Sie alle Informationen, die Sie benötigen, um auf das VDBS Serviceportal zuzugreifen. Wenn Sie bereits ein Konto haben, können Sie sich hier anmelden. Wenn Sie noch kein Konto haben, können Sie sich hier registrieren. 
            </p>
        </div>

        <div class="container__buttons btn-group btn-group--vertical btn-group--sec-start btn-group--gap-sm">
            <a href="/zugang-zum-portal" class="btn btn--primary btn--md">Zugang zum Portal</a>
            <a href="/kontakt?preset=1" class="btn btn--secondary-cta-light btn--md">Hilfe kontaktieren</a>
        </div>
    </div>
</section>

<section id="kontakt" class="section--surface">
    <div class="container container--media-text container--balanced">  
        <figure class="container__media container__media--cover container__media--ratio-4-3">
            <img src="/assets/images/home/home_kontakt.png" alt="Campus der Universität">
        </figure>
        <div class="container__text">          
            <h3>Kontakt</h3>

            <p>
                Sollten Sie Fragen oder Probleme haben, können Sie uns jederzeit kontaktieren. Wir helfen Ihnen gerne weiter. Sie erreichen uns unter den E-Mail-Adressen: <br>
                <b>Support</b> support@portal.vdb.schule <br>
                <b>Kontakt</b> kontakt@vdb.schule 
            </p>
        </div>

        <div class="container__buttons btn-group btn-group--vertical">
            <a href="/kontakt" class="btn btn--primary btn--md">Kontaktformular öffnen</a>
        </div>
    </div>
</section>