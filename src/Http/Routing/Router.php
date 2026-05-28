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
use App\Repository\AreaRepository;
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
        // bind the Request into the container for services and views
        try {
            $this->container->set(Request::class, $request);
        } catch (\Throwable $e) {
            // ignore container binding errors
        }

        // try to resolve current area and store it in the container as 'current_area'
        try {
            if ($this->container->has(AreaRepository::class)) {
                $areaRepo = $this->container->get(AreaRepository::class);
                $currentArea = null;

                // Admin paths: allow explicit query or session selection
                if (str_starts_with($request->path, '/development/web-control')) {
                    $areaId = isset($request->query['area_id']) ? (int) $request->query['area_id'] : 0;
                    if ($areaId > 0) {
                        $currentArea = $areaRepo->find($areaId);
                    } else {
                        if (session_status() === PHP_SESSION_NONE) {
                            @session_start();
                        }
                        $sessId = isset($_SESSION['selected_area_id']) ? (int) $_SESSION['selected_area_id'] : 0;
                        if ($sessId > 0) {
                            $currentArea = $areaRepo->find($sessId);
                        } else {
                            $currentArea = $areaRepo->findBySlug('main');
                            if (empty($currentArea)) {
                                $areas = $areaRepo->findAll();
                                $currentArea = $areas[0] ?? [];
                            }
                        }
                    }
                } else {
                    // Public pages: determine by first path segment
                    $path = ltrim($request->path, '/');
                    $segment = $path === '' ? '' : (explode('/', $path)[0] ?? '');
                    if ($segment === '' || $segment === '/') {
                        $currentArea = $areaRepo->findBySlug('main');
                        if (empty($currentArea)) {
                            $areas = $areaRepo->findAll();
                            $currentArea = $areas[0] ?? [];
                        }
                    } else {
                        $currentArea = $areaRepo->findBySlug($segment);
                        if (empty($currentArea)) {
                            $currentArea = $areaRepo->findBySlug('main');
                            if (empty($currentArea)) {
                                $areas = $areaRepo->findAll();
                                $currentArea = $areas[0] ?? [];
                            }
                        }
                    }
                }

                $this->container->set('current_area', $currentArea ?: null);
            }
        } catch (\Throwable $e) {
            // ignore area resolution errors to avoid breaking requests
        }
        foreach ($this->routes as $route) {
            if ($route->method !== $request->method || $route->path !== $request->path) {
                continue;
            }

            $controllerClass = $route->controller;

            if ($this->container->has($controllerClass)) {
                $controller = $this->container->get($controllerClass);
            } else {
                /** @var Renderer $renderer */
                $renderer = $this->container->get(Renderer::class);
                $controller = new $controllerClass($renderer);
            }

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
}