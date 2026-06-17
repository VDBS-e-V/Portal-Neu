<?php

declare(strict_types=1);

use App\Bootstrap\Container;
use App\Navigation\AuthorizedMainMenu;
use App\Navigation\VerwaltungNavigation;
use App\Repository\AuthorizedMenuRepository;
use App\Security\AuthorizationService;

return [
    AuthorizedMenuRepository::class => static function (Container $container): AuthorizedMenuRepository {
        return new AuthorizedMenuRepository(
            $container->get(PDO::class),
            $container->get(AuthorizationService::class)
        );
    },

    AuthorizedMainMenu::class => static function (Container $container): AuthorizedMainMenu {
        return new AuthorizedMainMenu(
            $container->get(AuthorizedMenuRepository::class)
        );
    },

    VerwaltungNavigation::class => static function (Container $container): VerwaltungNavigation {
        return new VerwaltungNavigation(
            $container->get(AuthorizationService::class)
        );
    },
];
