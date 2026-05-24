<?php

declare(strict_types=1);

namespace App\Bootstrap;

use App\Http\Request\Request;
use App\Http\Routing\Router;
use App\Presentation\Templating\Renderer;
use App\Infrastructure\Persistence\PDO\AreaPdoRepository;
use PDO;

final class App
{
    public static function run(): void
    {
        self::loadEnv(dirname(__DIR__, 2) . '/.env');

        $config = require dirname(__DIR__, 2) . '/config/app.php';

        // Container zuerst bauen (für 500-Rendering)
        $container = Container::build();

        ErrorHandling::register(
            (bool)($config['debug'] ?? false),
            function () use (&$container): string {
                /** @var Renderer $renderer */
                $renderer = Container::get($container, Renderer::class);

                // Build areaNav for header (best effort)
                $areaNav = [];
                $areaRootLink = '/';
                try {
                    $dbConfig = Container::get($container, 'config.db');
                    $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $dbConfig['host'], (int)$dbConfig['port'], $dbConfig['name'], $dbConfig['charset']);
                    $pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['pass'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
                    $repo = new AreaPdoRepository($pdo);
                    $areas = $repo->findAllOrdered();
                    $request = Request::fromGlobals();
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
                    // ignore - fallback to empty nav
                }

                // optional Daten reinreichen (nicht zu viel leaken)
                return $renderer->renderPage('pages/errors/500', [
                    'title' => '500',
                    'pageTitle' => '500 – Serverfehler',
                    'areaName' => 'Fehler',
                    'areaNav' => $areaNav,
                    'areaRootLink' => $areaRootLink,
                ]);
            }
        );

        $router = new Router($container);
        Routes::register($router);

        $request = Request::fromGlobals();
        $response = $router->dispatch($request);
        $response->send();
    }

    private static function loadEnv(string $envFile): void
    {
        if (!is_file($envFile)) return;

        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) return;

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) continue;

            $pos = strpos($line, '=');
            if ($pos === false) continue;

            $key = trim(substr($line, 0, $pos));
            $val = trim(substr($line, $pos + 1));

            if ((str_starts_with($val, '"') && str_ends_with($val, '"')) ||
                (str_starts_with($val, "'") && str_ends_with($val, "'"))) {
                $val = substr($val, 1, -1);
            }

            if (getenv($key) === false) {
                putenv($key . '=' . $val);
                $_ENV[$key] = $val;
            }
        }
    }
}