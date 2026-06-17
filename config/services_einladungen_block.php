<?php

declare(strict_types=1);

use App\Audit\AuditLogger;
use App\Bootstrap\Container;
use App\Http\Controller\InvitationController;
use App\Http\Controller\Verwaltung\EinladungenController;
use App\Presentation\Templating\Renderer;
use App\Repository\AreaRepository;
use App\Repository\MenuItemRepository;
use App\Repository\MenuRepository;
use App\Repository\PersonRepository;
use App\Repository\UserInvitationRepository;
use App\Security\AuthorizationService;

return [
    UserInvitationRepository::class => static function (Container $container): UserInvitationRepository {
        return new UserInvitationRepository($container->get(PDO::class));
    },

    InvitationController::class => static function (Container $container): InvitationController {
        return new InvitationController(
            $container->get(Renderer::class),
            $container->get(UserInvitationRepository::class),
            $container->get(AuditLogger::class)
        );
    },

    EinladungenController::class => static function (Container $container): EinladungenController {
        return new EinladungenController(
            $container->get(Renderer::class),
            $container->get(AreaRepository::class),
            $container->get(MenuRepository::class),
            $container->get(MenuItemRepository::class),
            $container->get(UserInvitationRepository::class),
            $container->get(PersonRepository::class),
            $container->get(AuthorizationService::class),
            $container->get(AuditLogger::class)
        );
    },
];
