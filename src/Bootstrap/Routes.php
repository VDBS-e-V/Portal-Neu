<?php

declare(strict_types=1);

namespace App\Bootstrap;

use App\Http\Routing\Router;

final class Routes
{
    public static function register(Router $router): void
    {
        $routes = require dirname(__DIR__, 2) . '/config/routes.php';

        foreach ($routes as $r) {
            [$method, $path, $handler] = $r;
            $router->add($method, $path, $handler);
        }
    }
}