<?php

declare(strict_types=1);

/*
Manuell in config/routes.php übernehmen:

1. Bei den use-Statements ergänzen:

use App\Http\Controller\Identity\IdentityMeController;

2. Im return [...]-Array ergänzen:

new Route('GET', '/identity/me', IdentityMeController::class, 'index'),
*/
