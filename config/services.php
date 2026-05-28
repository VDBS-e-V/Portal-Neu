<?php

declare(strict_types=1);

use App\Bootstrap\Container;
use App\Http\Routing\Router;
use App\Presentation\Templating\PhpRenderer;
use App\Presentation\Templating\Renderer;
use PDO;
use PDOException;
use RuntimeException;
use App\Http\Controller\AreaController;
use App\Http\Controller\MenuController;
use App\Repository\AreaRepository;
use App\Repository\MenuRepository;
use App\Repository\MenuItemRepository;
use App\Repository\UserRepository;

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
    MenuRepository::class => static function (Container $container): MenuRepository {
        return new MenuRepository($container->get(PDO::class));
    },
    MenuItemRepository::class => static function (Container $container): MenuItemRepository {
        return new MenuItemRepository($container->get(PDO::class));
    },
    UserRepository::class => static function (Container $container): UserRepository {
        return new UserRepository($container->get(PDO::class));
    },
    AreaController::class => static function (Container $container): AreaController {
        return new AreaController($container->get(Renderer::class), $container->get(AreaRepository::class));
    },
    MenuController::class => static function (Container $container): MenuController {
        return new MenuController(
            $container->get(Renderer::class),
            $container->get(MenuRepository::class),
            $container->get(AreaRepository::class)
        );
    },
];