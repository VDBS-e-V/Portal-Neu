<?php

declare(strict_types=1);

use App\Bootstrap\Container;
use App\Http\Controller\Verwaltung\VerwaltungController;
use App\Navigation\VerwaltungNavigation;
use App\Presentation\Templating\Renderer;
use App\Repository\AreaRepository;
use App\Repository\MenuItemRepository;
use App\Repository\MenuRepository;
use App\Repository\VerwaltungStatsRepository;
use App\Security\AuthorizationService;

return [
    VerwaltungNavigation::class => static function (): VerwaltungNavigation {
        return new VerwaltungNavigation();
    },

    VerwaltungStatsRepository::class => static function (Container $container): VerwaltungStatsRepository {
        return new VerwaltungStatsRepository($container->get(PDO::class));
    },

    VerwaltungController::class => static function (Container $container): VerwaltungController {
        return new VerwaltungController(
            $container->get(Renderer::class),
            $container->get(AreaRepository::class),
            $container->get(MenuRepository::class),
            $container->get(MenuItemRepository::class),
            $container->get(VerwaltungStatsRepository::class),
            $container->get(VerwaltungNavigation::class),
            $container->get(AuthorizationService::class)
        );
    },
];
