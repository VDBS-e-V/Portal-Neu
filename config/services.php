<?php

declare(strict_types=1);

use App\Bootstrap\Container;
use App\Http\Controller\AuthController;
use App\Http\Controller\HomeController;
use App\Http\Controller\UserAccountController;
use App\Http\Routing\Router;
use App\Presentation\Navigation\HeaderDataProvider;
use App\Presentation\Templating\PhpRenderer;
use App\Presentation\Templating\Renderer;
// Built-in global classes (PDO, PDOException, RuntimeException) don't need importing
use App\Http\Controller\DevelopmentController;
use App\Repository\AreaRepository;
use App\Repository\MenuItemRepository;
use App\Repository\MenuRepository;
use App\Repository\UserRepository;
use App\Security\SessionAuth;

return [
    Renderer::class => static function (Container $container): Renderer {
        return new PhpRenderer((string) $container->get('paths.views'), $container);
    },
    Router::class => static function (Container $container): Router {
        return new Router($container);
    },
    PDO::class => static function (Container $container): PDO {
        $host = getenv('DB_HOST') ?: '127.0.0.1';
        $port = getenv('DB_PORT') ?: '3306';
        $name = getenv('DB_NAME') ?: 'vdbs';
        $user = getenv('DB_USER') ?: 'root';
        $pass = getenv('DB_PASS') ?: '';
        $charset = getenv('DB_CHARSET') ?: 'utf8mb4';

        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $host, $port, $name, $charset);

        try {
            $pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
            return $pdo;
        } catch (PDOException $e) {
            throw new RuntimeException('Database connection failed: ' . $e->getMessage());
        }
    },
    AreaRepository::class => static function (Container $container): AreaRepository {
        return new AreaRepository($container->get(PDO::class));
    },
    HeaderDataProvider::class => static function (Container $container): HeaderDataProvider {
        return new HeaderDataProvider(
            $container->get(AreaRepository::class),
            $container->get(MenuRepository::class),
            $container->get(MenuItemRepository::class)
        );
    },
    HomeController::class => static function (Container $container): HomeController {
        return new HomeController(
            $container->get(Renderer::class),
            $container->get(AreaRepository::class)
        );
    },
    MenuRepository::class => static function (Container $container): MenuRepository {
        return new MenuRepository($container->get(PDO::class));
    },
    MenuItemRepository::class => static function (Container $container): MenuItemRepository {
        return new MenuItemRepository($container->get(PDO::class));
    },
    UserRepository::class => static function (Container $container): UserRepository {
        return new UserRepository($container->get(PDO::class));
    },
    SessionAuth::class => static function (Container $container): SessionAuth {

        return new SessionAuth();

    },
    UserAccountController::class => static function (Container $container): UserAccountController {
        return new UserAccountController(
            $container->get(Renderer::class),
            $container->get(AreaRepository::class),
            $container->get(MenuRepository::class),
            $container->get(MenuItemRepository::class),
            $container->get(UserRepository::class),
            $container->get(SessionAuth::class)
        );
    },
    AuthController::class => static function (Container $container): AuthController {
        return new AuthController(
            $container->get(Renderer::class),
            $container->get(AreaRepository::class),
            $container->get(UserRepository::class),
            $container->get(SessionAuth::class)
        );
    },
    DevelopmentController::class => static function (Container $container): DevelopmentController {
        return new DevelopmentController(
            $container->get(Renderer::class),
            $container->get(AreaRepository::class),
            $container->get(MenuRepository::class),
            $container->get(MenuItemRepository::class)
        );
    },
];
