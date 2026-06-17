<?php

declare(strict_types=1);

use App\Audit\AuditLogger;
use App\Bootstrap\Container;
use App\Http\Controller\Verwaltung\GruppenController;
use App\Presentation\Templating\Renderer;
use App\Repository\AreaRepository;
use App\Repository\MenuItemRepository;
use App\Repository\MenuRepository;
use App\Repository\PermissionGroupRepository;
use App\Repository\PersonPermissionGroupRepository;
use App\Security\AdminSafetyService;
use App\Security\AuthorizationService;

return [
    GruppenController::class => static function (Container $container): GruppenController {
        return new GruppenController(
            $container->get(Renderer::class),
            $container->get(AreaRepository::class),
            $container->get(MenuRepository::class),
            $container->get(MenuItemRepository::class),
            $container->get(PermissionGroupRepository::class),
            $container->get(PersonPermissionGroupRepository::class),
            $container->get(AuthorizationService::class),
            $container->get(AdminSafetyService::class),
            $container->get(AuditLogger::class)
        );
    },
];