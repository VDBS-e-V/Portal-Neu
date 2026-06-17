<?php

declare(strict_types=1);

use App\Audit\AuditLogger;
use App\Bootstrap\Container;
use App\Http\Controller\Verwaltung\BerechtigungenController;
use App\Presentation\Templating\Renderer;
use App\Repository\AreaRepository;
use App\Repository\MenuItemRepository;
use App\Repository\MenuRepository;
use App\Repository\PageGroupAccessRepository;
use App\Repository\PageGroupRepository;
use App\Repository\PermissionGroupRepository;
use App\Security\AuthorizationService;

return [
    BerechtigungenController::class => static function (Container $container): BerechtigungenController {
        return new BerechtigungenController(
            $container->get(Renderer::class),
            $container->get(AreaRepository::class),
            $container->get(MenuRepository::class),
            $container->get(MenuItemRepository::class),
            $container->get(PermissionGroupRepository::class),
            $container->get(PageGroupRepository::class),
            $container->get(PageGroupAccessRepository::class),
            $container->get(AuthorizationService::class),
            $container->get(AuditLogger::class)
        );
    },
];
