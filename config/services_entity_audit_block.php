<?php

declare(strict_types=1);

use App\Bootstrap\Container;
use App\Http\Controller\Verwaltung\EntityAuditController;
use App\Navigation\VerwaltungNavigation;
use App\Presentation\Templating\Renderer;
use App\Repository\AreaRepository;
use App\Repository\EntityAuditRepository;
use App\Repository\MenuItemRepository;
use App\Repository\MenuRepository;
use App\Repository\PermissionGroupRepository;
use App\Repository\PersonErasureRepository;
use App\Repository\PersonRepository;
use App\Security\AuthorizationService;

return [
    EntityAuditRepository::class => static function (Container $container): EntityAuditRepository {
        return new EntityAuditRepository($container->get(PDO::class));
    },

    EntityAuditController::class => static function (Container $container): EntityAuditController {
        return new EntityAuditController(
            $container->get(Renderer::class),
            $container->get(AreaRepository::class),
            $container->get(MenuRepository::class),
            $container->get(MenuItemRepository::class),
            $container->get(EntityAuditRepository::class),
            $container->get(PersonRepository::class),
            $container->get(PermissionGroupRepository::class),
            $container->get(PersonErasureRepository::class),
            $container->get(VerwaltungNavigation::class),
            $container->get(AuthorizationService::class)
        );
    },
];
