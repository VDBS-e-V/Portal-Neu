<?php

declare(strict_types=1);

/*
Manuell in config/services.php übernehmen:

1. Bei den use-Statements ergänzen:

use App\Repository\IdentityMeRepository;

2. Im Service-Array ergänzen:

IdentityMeRepository::class => static function (Container $container): IdentityMeRepository {
    return new IdentityMeRepository($container->get(PDO::class));
},
*/
