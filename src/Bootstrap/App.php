<?php

declare(strict_types=1);

namespace App\Bootstrap;

use App\Http\Request\Request;
use App\Http\Routing\Router;
use App\Presentation\Templating\Renderer;

final class App
{
    public static function run(): void
    {
        $root = dirname(__DIR__, 2);

        self::loadEnv($root . '/.env');

        $config = require $root . '/config/app.php';
        $container = new Container(
            [
                'paths.root' => $root,
                'paths.views' => $root . '/resources/views',
                'config.app' => $config,
            ],
            require $root . '/config/services.php'
        );

        ErrorHandling::register((bool) $config['debug'], static function () use ($container): string {
            /** @var Renderer $renderer */
            $renderer = $container->get(Renderer::class);

            return $renderer->renderPage('pages/errors/500', [
                'title' => '500',
                'areaName' => 'Fehler',
                'pageTitle' => '500 - Serverfehler',
                'areaRootLink' => '/',
                'areaNav' => [[
                    'label' => 'Start',
                    'href' => '/',
                    'active' => false,
                ]],
                'headerNav' => [],
                'path' => parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/',
                'now' => date('c'),
            ]);
        });

        /** @var Router $router */
        $router = $container->get(Router::class);

        foreach (Routes::load() as $route) {
            $router->add($route);
        }

        $request = Request::fromGlobals((string) ($config['base_url'] ?? ''));
        $router->dispatch($request)->send();
    }

    private static function loadEnv(string $envFile): void
    {
        if (!is_file($envFile)) {
            return;
        }

        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if ($lines === false) {
            return;
        }

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            $position = strpos($line, '=');

            if ($position === false) {
                continue;
            }

            $key = trim(substr($line, 0, $position));
            $value = trim(substr($line, $position + 1));

            if (
                (str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                (str_starts_with($value, "'") && str_ends_with($value, "'"))
            ) {
                $value = substr($value, 1, -1);
            }

            if (getenv($key) === false) {
                putenv($key . '=' . $value);
                $_ENV[$key] = $value;
            }
        }
    }
}
