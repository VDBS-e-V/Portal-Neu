<?php

declare(strict_types=1);

namespace App\Http\Routing;

use App\Http\Controller\Controller;
use App\Http\Request\Request;
use App\Http\Response\HtmlResponse;
use App\Http\Response\JsonResponse;
use App\Http\Response\Response;
use App\Presentation\Templating\Renderer;
use RuntimeException;

final class Router
{
    /** @var array<int, Route> */
    private array $routes = [];

    public function __construct(private Renderer $renderer)
    {
    }

    public function add(Route $route): void
    {
        $this->routes[] = $route;
    }

    public function dispatch(Request $request): Response
    {
        foreach ($this->routes as $route) {
            if ($route->method !== $request->method || $route->path !== $request->path) {
                continue;
            }

            $controllerClass = $route->controller;
            $controller = new $controllerClass($this->renderer);

            if (!$controller instanceof Controller) {
                throw new RuntimeException('Route controller must extend ' . Controller::class);
            }

            $action = $route->action;
            $response = $controller->{$action}($request);

            if ($response instanceof Response) {
                return $response;
            }

            if (is_string($response)) {
                return new HtmlResponse($response);
            }

            throw new RuntimeException('Controller action must return a response or string.');
        }

        if ($request->isApi()) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Not Found',
            ], 404);
        }

        return new HtmlResponse($this->renderer->renderPage('pages/errors/404', [
            'title' => '404',
            'areaName' => 'Fehler',
            'pageTitle' => '404 - Nicht gefunden',
            'areaRootLink' => '/',
            'areaNav' => [[
                'label' => 'Start',
                'href' => '/',
                'active' => false,
            ]],
            'headerNav' => [],
            'path' => $request->path,
            'now' => date('c'),
        ]), 404);
    }
}