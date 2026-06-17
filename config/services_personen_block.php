<?php

declare(strict_types=1);

use App\Audit\AuditLogger;
use App\Bootstrap\Container;
use App\Http\Controller\Verwaltung\PersonenController;
use App\Presentation\Templating\Renderer;
use App\Repository\AreaRepository;
use App\Repository\MenuItemRepository;
use App\Repository\MenuRepository;
use App\Repository\PermissionGroupRepository;
use App\Repository\PersonAddressRepository;
use App\Repository\PersonContactRepository;
use App\Repository\PersonPermissionGroupRepository;
use App\Repository\PersonRepository;
use App\Security\AdminSafetyService;
use App\Security\AuthorizationService;

return [
    PersonRepository::class => static function (Container $container): PersonRepository {
        return new PersonRepository($container->get(PDO::class));
    },

    PersonContactRepository::class => static function (Container $container): PersonContactRepository {
        return new PersonContactRepository($container->get(PDO::class));
    },

    PersonAddressRepository::class => static function (Container $container): PersonAddressRepository {
        return new PersonAddressRepository($container->get(PDO::class));
    },

    PersonenController::class => static function (Container $container): PersonenController {
        return new PersonenController(
            $container->get(Renderer::class),
            $container->get(AreaRepository::class),
            $container->get(MenuRepository::class),
            $container->get(MenuItemRepository::class),
            $container->get(PersonRepository::class),
            $container->get(PersonContactRepository::class),
            $container->get(PersonAddressRepository::class),
            $container->get(PermissionGroupRepository::class),
            $container->get(PersonPermissionGroupRepository::class),
            $container->get(AuthorizationService::class),
            $container->get(AdminSafetyService::class),
            $container->get(AuditLogger::class)
        );
    },
];