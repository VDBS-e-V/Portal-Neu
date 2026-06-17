<?php

declare(strict_types=1);

use App\Bootstrap\Container;
use App\Security\CsrfGuard;
use App\Security\CsrfTokenManager;

return [
    CsrfTokenManager::class => static function (): CsrfTokenManager {
        return new CsrfTokenManager();
    },

    CsrfGuard::class => static function (Container $container): CsrfGuard {
        return new CsrfGuard(
            $container->get(CsrfTokenManager::class)
        );
    },
];
