<?php

declare(strict_types=1);

namespace App\Presentation\Templating;

use App\Bootstrap\Container;
use RuntimeException;

final class PhpRenderer implements Renderer
{
    private ?Container $container;

    public function __construct(private string $basePath, ?Container $container = null)
    {
        $this->container = $container;
    }

    public function render(string $view, array $parameters = []): string
    {
        $file = rtrim($this->basePath, '/\\') . '/' . str_replace('.', '/', $view) . '.php';

        if (!is_file($file)) {
            throw new RuntimeException('View not found: ' . $file);
        }

        return $this->includeFile($file, $parameters);
    }

    public function renderPage(string $view, array $parameters = [], string $layout = 'layouts/base'): string
    {
        $global = [];

        if ($this->container !== null) {
            try {
                if ($this->container->has(\App\Repository\AreaRepository::class)) {
                    $global['headerAreas'] = $this->container->get(\App\Repository\AreaRepository::class)->findAll();
                }
            } catch (\Throwable $e) {
                // ignore header areas on error
            }

            try {
                if ($this->container->has(\App\Repository\MenuRepository::class)
                    && $this->container->has(\App\Repository\MenuItemRepository::class)) {
                    $menuRepo = $this->container->get(\App\Repository\MenuRepository::class);
                    $menuItemRepo = $this->container->get(\App\Repository\MenuItemRepository::class);

                    $selectedAreaId = $this->resolveAreaIdForHeader($parameters, $global['headerAreas'] ?? []);

                    if ($selectedAreaId !== null && $selectedAreaId > 0) {
                        $areaMenu = $menuRepo->findForArea($selectedAreaId);
                        if (!empty($areaMenu) && isset($areaMenu['id'])) {
                            $global['headerMenus'] = $menuItemRepo->findByMenuIdAndParent((int) $areaMenu['id'], null);
                        }
                    }
                }
            } catch (\Throwable $e) {
                // ignore header menus on error
            }
        }

        $global['headerMenus'] = $global['headerMenus'] ?? [];

        $parameters = array_merge($global, $parameters);

        $content = $this->render($view, $parameters);

        return $this->render($layout, array_merge($parameters, [
            'content' => $content,
        ]));
    }

    /**
     * @param array<string, mixed> $parameters
     */
    private function includeFile(string $file, array $parameters): string
    {
        ob_start();

        (static function (string $__file, array $__parameters): void {
            extract($__parameters, EXTR_SKIP);
            require $__file;
        })($file, $parameters);

        return (string) ob_get_clean();
    }

    /**
     * @param array<string, mixed> $parameters
     * @param array<int, array<string, mixed>> $headerAreas
     */
    private function resolveAreaIdForHeader(array $parameters, array $headerAreas): ?int
    {
        if (isset($parameters['area']) && is_array($parameters['area']) && isset($parameters['area']['id'])) {
            return (int) $parameters['area']['id'];
        }

        if (isset($parameters['area_id'])) {
            return (int) $parameters['area_id'];
        }

        if (isset($parameters['areaId'])) {
            return (int) $parameters['areaId'];
        }

        $path = (string) ($parameters['path'] ?? '/');
        $bestMatch = null;
        $bestLength = -1;

        foreach ($headerAreas as $area) {
            if (!isset($area['id'])) {
                continue;
            }

            $startPath = trim((string) ($area['start_path'] ?? '/'));
            if ($startPath === '') {
                $startPath = '/';
            }

            $matches = $startPath === '/'
                ? true
                : $path === $startPath || str_starts_with($path, rtrim($startPath, '/') . '/');

            if (!$matches) {
                continue;
            }

            $length = strlen($startPath);
            if ($length > $bestLength) {
                $bestLength = $length;
                $bestMatch = (int) $area['id'];
            }
        }

        if ($bestMatch !== null) {
            return $bestMatch;
        }

        if (!empty($headerAreas)) {
            $firstArea = $headerAreas[0] ?? null;
            if (is_array($firstArea) && isset($firstArea['id'])) {
                return (int) $firstArea['id'];
            }
        }

        return null;
    }
}
