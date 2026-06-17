<?php

declare(strict_types=1);

use App\Audit\AuditLogger;
use App\Bootstrap\Container;
use App\Http\Controller\Verwaltung\DatenschutzController;
use App\Presentation\Templating\Renderer;
use App\Repository\AreaRepository;
use App\Repository\MenuItemRepository;
use App\Repository\MenuRepository;
use App\Repository\PersonErasureRepository;
use App\Repository\PersonRepository;
use App\Security\AdminSafetyService;
use App\Security\AuthorizationService;

return [
    PersonErasureRepository::class => static function (Container $container): PersonErasureRepository {
        return new PersonErasureRepository($container->get(PDO::class));
    },

    DatenschutzController::class => static function (Container $container): DatenschutzController {
        return new DatenschutzController(
            $container->get(Renderer::class),
            $container->get(AreaRepository::class),
            $container->get(MenuRepository::class),
            $container->get(MenuItemRepository::class),
            $container->get(PersonErasureRepository::class),
            $container->get(PersonRepository::class),
            $container->get(AuthorizationService::class),
            $container->get(AdminSafetyService::class),
            $container->get(AuditLogger::class)
        );
    },
];
