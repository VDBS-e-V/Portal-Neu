<?php

declare(strict_types=1);

use App\Bootstrap\Container;
use App\Http\Controller\Verwaltung\AuditLogController;
use App\Presentation\Templating\Renderer;
use App\Repository\AreaRepository;
use App\Repository\AuditLogRepository;
use App\Repository\MenuItemRepository;
use App\Repository\MenuRepository;
use App\Security\AuthorizationService;

return [
    AuditLogController::class => static function (Container $container): AuditLogController {
        return new AuditLogController(
            $container->get(Renderer::class),
            $container->get(AreaRepository::class),
            $container->get(MenuRepository::class),
            $container->get(MenuItemRepository::class),
            $container->get(AuditLogRepository::class),
            $container->get(AuthorizationService::class)
        );
    },
];
