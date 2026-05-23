<?php

declare(strict_types=1);

namespace App\Http\Routing;

use App\Bootstrap\Container;
use App\Http\Request\Request;
use App\Http\Response\HtmlResponse;
use App\Http\Response\Response;
use App\Presentation\Templating\Renderer;

final class Router
{
    /** @var Route[] */
    private array $routes = [];

    public function __construct(private array $container) {}

    /** @param array{0: class-string, 1: string} $handler */
    public function add(string $method, string $path, array $handler): void
    {
        $this->routes[] = new Route(strtoupper($method), $path, $handler);
    }

    public function dispatch(Request $request): Response
    {
        foreach ($this->routes as $route) {
            if ($route->method !== $request->method) continue;
            if ($route->path !== $request->path) continue;

            [$class, $method] = $route->handler;

            $controller = new $class($this->container);
            $result = $controller->$method($request);

            if ($result instanceof Response) return $result;
            if (is_string($result)) return new HtmlResponse($result);

            return new HtmlResponse('Invalid controller response', 500);
        }

        // 404 als View
        /** @var Renderer $renderer */
        $renderer = Container::get($this->container, Renderer::class);

        $html = $renderer->render('pages/errors/404', [
            'title' => '404',
        ]);

        return new HtmlResponse($html, 404);
    }
}