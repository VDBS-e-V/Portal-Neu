<?php

declare(strict_types=1);

use App\Bootstrap\Container;
use App\Http\Controller\Verwaltung\SchulverzeichnisController;
use App\Navigation\VerwaltungNavigation;
use App\Presentation\Templating\Renderer;
use App\Repository\AreaRepository;
use App\Repository\MenuItemRepository;
use App\Repository\MenuRepository;
use App\Repository\SchoolDirectoryRepository;
use App\Repository\SchoolOverrideRepository;
use App\Security\AuthorizationService;
use App\Service\SchoolDirectoryReadService;
use App\Service\SchoolDirectorySimpleEditService;

return [
    SchoolDirectoryRepository::class => static function (Container $container): SchoolDirectoryRepository {
        return new SchoolDirectoryRepository($container->get(PDO::class));
    },

    SchoolOverrideRepository::class => static function (Container $container): SchoolOverrideRepository {
        return new SchoolOverrideRepository($container->get(PDO::class));
    },

    SchoolDirectoryReadService::class => static function (Container $container): SchoolDirectoryReadService {
        return new SchoolDirectoryReadService(
            $container->get(SchoolDirectoryRepository::class),
            $container->get(SchoolOverrideRepository::class)
        );
    },

    SchoolDirectorySimpleEditService::class => static function (Container $container): SchoolDirectorySimpleEditService {
        return new SchoolDirectorySimpleEditService(
            $container->get(SchoolDirectoryRepository::class),
            $container->get(SchoolOverrideRepository::class)
        );
    },

    SchulverzeichnisController::class => static function (Container $container): SchulverzeichnisController {
        return new SchulverzeichnisController(
            $container->get(Renderer::class),
            $container->get(AreaRepository::class),
            $container->get(MenuRepository::class),
            $container->get(MenuItemRepository::class),
            $container->get(VerwaltungNavigation::class),
            $container->get(AuthorizationService::class),
            $container->get(SchoolDirectoryRepository::class),
            $container->get(SchoolDirectoryReadService::class),
            $container->get(SchoolDirectorySimpleEditService::class)
        );
    },
];
