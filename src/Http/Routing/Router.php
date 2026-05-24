<?php

declare(strict_types=1);

namespace App\Http\Routing;

use App\Bootstrap\Container;
use App\Http\Request\Request;
use App\Http\Response\HtmlResponse;
use App\Http\Response\Response;
use App\Presentation\Templating\Renderer;
use App\Infrastructure\Persistence\PDO\AreaPdoRepository;
use PDO;

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

        // Build areaNav for header (best effort)
        $areaNav = [];
        $areaRootLink = '/';
        try {
            $dbConfig = Container::get($this->container, 'config.db');
            $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $dbConfig['host'], (int)$dbConfig['port'], $dbConfig['name'], $dbConfig['charset']);
            $pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['pass'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            $repo = new AreaPdoRepository($pdo);
            $areas = $repo->findAllOrdered();
            foreach ($areas as $a) {
                $slug = $a->slug;
                if (str_starts_with($slug, '/')) {
                    $href = $slug;
                } elseif ($slug === 'start' || $slug === '') {
                    $href = '/';
                } else {
                    $href = '/' . ltrim($slug, '/');
                }

                $active = false;
                if ($href === '/') {
                    $active = $request->path === '/';
                } else {
                    $active = str_starts_with($request->path, $href);
                }

                $areaNav[] = [
                    'label' => $a->name,
                    'href' => $href,
                    'active' => $active,
                    'icon' => $a->icon ?? null,
                ];
            }
            if (!empty($areaNav)) {
                $areaRootLink = $areaNav[0]['href'] ?? '/';
            }
        } catch (\Throwable $e) {
            // ignore
        }

        $html = $renderer->renderPage('pages/errors/404', [
            'title' => '404',
            'pageTitle' => '404 – Nicht gefunden',
            'areaName' => 'Fehler',
            'areaNav' => $areaNav,
            'areaRootLink' => $areaRootLink,
        ]);

        return new HtmlResponse($html, 404);
    }
}