<?php

declare(strict_types=1);

use App\Bootstrap\Container;
use App\Http\Routing\Router;
use App\Presentation\Templating\PhpRenderer;
use App\Presentation\Templating\Renderer;

return [
    Renderer::class => static function (Container $container): Renderer {
        return new PhpRenderer((string) $container->get('paths.views'));
    },
    Router::class => static function (Container $container): Router {
        return new Router($container->get(Renderer::class));
    },
];