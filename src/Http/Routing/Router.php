<?php

declare(strict_types=1);

namespace App\Http\Routing;

use App\Bootstrap\Container;
use App\Http\Controller\Controller;
use App\Http\Request\Request;
use App\Http\Response\HtmlResponse;
use App\Http\Response\JsonResponse;
use App\Http\Response\Response;
use App\Presentation\Templating\Renderer;
use ReflectionClass;
use ReflectionNamedType;
use RuntimeException;

final class Router
{
    /** @var array<int, Route> */
    private array $routes = [];

    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
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

            $controller = $this->makeController($controllerClass);

            if (!$controller instanceof Controller) {
                throw new RuntimeException('Route controller must extend ' . Controller::class);
            }

            $action = $route->action;
            $response = $controller->{$action}($request);

            if ($response instanceof Response) {
                return $response;
            }

            if (is_string($response)) {
                /** @var Renderer $renderer */
                $renderer = $this->container->get(Renderer::class);

                return new HtmlResponse($renderer->renderPage($response));
            }

            throw new RuntimeException('Controller action must return a response or string.');
        }

        if ($request->isApi()) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Not Found',
            ], 404);
        }

        /** @var Renderer $renderer */
        $renderer = $this->container->get(Renderer::class);

        return new HtmlResponse($renderer->renderPage('pages/errors/404', [
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

    private function makeController(string $controllerClass): object
    {
        if ($this->container->has($controllerClass)) {
            return $this->container->get($controllerClass);
        }

        $reflection = new ReflectionClass($controllerClass);
        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            return new $controllerClass();
        }

        $arguments = [];

        foreach ($constructor->getParameters() as $parameter) {
            $type = $parameter->getType();

            if (!$type instanceof ReflectionNamedType || $type->isBuiltin()) {
                throw new RuntimeException(
                    'Cannot resolve constructor parameter $' . $parameter->getName() .
                    ' for controller ' . $controllerClass
                );
            }

            $dependencyClass = $type->getName();

            if (!$this->container->has($dependencyClass)) {
                throw new RuntimeException(
                    'Missing container entry for ' . $dependencyClass .
                    ' required by controller ' . $controllerClass
                );
            }

            $arguments[] = $this->container->get($dependencyClass);
        }

        return $reflection->newInstanceArgs($arguments);
    }
}