<?php

declare(strict_types=1);

use App\Audit\AuditLogger;
use App\Bootstrap\Container;
use App\Http\Controller\ProfileController;
use App\Presentation\Templating\Renderer;
use App\Repository\AccountProfileRepository;
use App\Repository\LoginEventRepository;
use App\Security\AccountSessionContext;
use App\Security\CsrfGuard;

return [
    AccountProfileRepository::class => static function (Container $container): AccountProfileRepository {
        return new AccountProfileRepository($container->get(PDO::class));
    },

    LoginEventRepository::class => static function (Container $container): LoginEventRepository {
        return new LoginEventRepository($container->get(PDO::class));
    },

    ProfileController::class => static function (Container $container): ProfileController {
        return new ProfileController(
            $container->get(Renderer::class),
            $container->get(AccountSessionContext::class),
            $container->get(AccountProfileRepository::class),
            $container->get(LoginEventRepository::class),
            $container->get(CsrfGuard::class),
            $container->get(AuditLogger::class)
        );
    },
];
