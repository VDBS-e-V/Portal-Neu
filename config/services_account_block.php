<?php

declare(strict_types=1);

use App\Audit\AuditLogger;
use App\Bootstrap\Container;
use App\Http\Controller\AccountController;
use App\Http\Controller\PasswordResetController;
use App\Presentation\Templating\Renderer;
use App\Repository\PasswordResetRepository;
use App\Repository\UserPasswordRepository;
use App\Security\AccountSessionContext;
use App\Security\CsrfGuard;

return [
    AccountSessionContext::class => static function (Container $container): AccountSessionContext {
        return new AccountSessionContext($container->get(PDO::class));
    },

    UserPasswordRepository::class => static function (Container $container): UserPasswordRepository {
        return new UserPasswordRepository($container->get(PDO::class));
    },

    PasswordResetRepository::class => static function (Container $container): PasswordResetRepository {
        return new PasswordResetRepository(
            $container->get(PDO::class),
            $container->get(UserPasswordRepository::class)
        );
    },

    AccountController::class => static function (Container $container): AccountController {
        return new AccountController(
            $container->get(Renderer::class),
            $container->get(AccountSessionContext::class),
            $container->get(UserPasswordRepository::class),
            $container->get(CsrfGuard::class),
            $container->get(AuditLogger::class)
        );
    },

    PasswordResetController::class => static function (Container $container): PasswordResetController {
        return new PasswordResetController(
            $container->get(Renderer::class),
            $container->get(PasswordResetRepository::class),
            $container->get(CsrfGuard::class),
            $container->get(AuditLogger::class)
        );
    },
];
