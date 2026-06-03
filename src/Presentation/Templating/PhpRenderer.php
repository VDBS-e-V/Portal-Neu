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

                    // determine selected area id: prefer template params, fall back to first header area
                    $selectedAreaId = null;
                    if (isset($parameters['area']) && is_array($parameters['area']) && isset($parameters['area']['id'])) {
                        $selectedAreaId = (int) $parameters['area']['id'];
                    } elseif (isset($parameters['area_id'])) {
                        $selectedAreaId = (int) $parameters['area_id'];
                    } elseif (isset($parameters['areaId'])) {
                        $selectedAreaId = (int) $parameters['areaId'];
                    } elseif (!empty($global['headerAreas'])) {
                        $first = $global['headerAreas'][0] ?? null;
                        $selectedAreaId = $first && isset($first['id']) ? (int) $first['id'] : null;
                    }

                    if ($selectedAreaId) {
                        $defaultMenu = $menuRepo->findDefaultForArea($selectedAreaId);
                        if (!empty($defaultMenu) && isset($defaultMenu['id'])) {
                            $global['headerMenus'] = $menuItemRepo->findByMenuIdAndParent((int) $defaultMenu['id'], null);
                        } else {
                            $global['headerMenus'] = [];
                        }
                    } else {
                        $global['headerMenus'] = [];
                    }
                }
            } catch (\Throwable $e) {
                // ignore header menus on error
            }
        }

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
}