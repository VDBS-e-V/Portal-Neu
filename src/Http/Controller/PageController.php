<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Http\Request\Request;
use App\Http\Response\HtmlResponse;
use App\Presentation\Templating\Renderer;
use App\Repository\AreaRepository;
use App\Repository\MenuItemRepository;
use App\Repository\MenuRepository;

abstract class PageController extends Controller
{
    public function __construct(
        Renderer $renderer,
        protected ?AreaRepository $areas = null,
        protected ?MenuRepository $headerMenuRepository = null,
        protected ?MenuItemRepository $headerMenuItemRepository = null
    ) {
        parent::__construct($renderer);
    }

    protected function renderPage(
        Request $request,
        string $view,
        array $parameters = [],
        int $status = 200
    ): HtmlResponse {
        $parameters = array_replace($this->pageDefaults($request), $parameters);
        $parameters = $this->withHeaderData($request, $parameters);

        return $this->html($view, $parameters, $status);
    }

    protected function pageDefaults(Request $request): array
    {
        return [
            'title' => 'VDBS Portal',
            'areaName' => 'VDBS Portal',
            'pageTitle' => 'VDBS Portal',
            'areaRootLink' => '/',
            'areaNav' => $this->startNav(),
            'headerAreas' => [],
            'headerMenus' => [],
            'headerNav' => [],
            'areas' => [],
            'path' => $request->path,
            'now' => date('c'),
        ];
    }

    protected function areaLinks(): array
    {
        return $this->headerAreas($this->allAreas(), []);
    }

    protected function startNav(bool $active = true): array
    {
        return [
            [
                'label' => 'Start',
                'href' => '/',
                'active' => $active,
            ],
        ];
    }

    protected function webControlNav(string $activeKey = 'dashboard'): array
    {
        return [
            [
                'label' => 'Dashboard',
                'href' => '/development/web-control',
                'active' => $activeKey === 'dashboard',
            ],
            [
                'label' => 'Bereiche',
                'href' => '/development/web-control/areas',
                'active' => $activeKey === 'areas',
            ],
            [
                'label' => 'Menüs',
                'href' => '/development/web-control/menus',
                'active' => $activeKey === 'menus',
            ],
        ];
    }

    private function withHeaderData(Request $request, array $parameters): array
    {
        $areas = $this->allAreas();
        $currentArea = $this->currentArea($request, $areas, $parameters);
        $headerAreas = $this->headerAreas($areas, $currentArea);
        $headerMenus = $this->headerMenus($request, $currentArea);

        $parameters['headerAreas'] = $headerAreas;
        $parameters['headerMenus'] = $headerMenus;
        $parameters['areas'] = $headerAreas;
        $parameters['headerNav'] = $headerMenus;

        if ($currentArea !== []) {
            $parameters['currentArea'] = $currentArea;
        }

        return $parameters;
    }

    private function allAreas(): array
    {
        if ($this->areas === null) {
            return [];
        }

        return $this->areas->findAll();
    }

    private function headerAreas(array $areas, array $currentArea): array
    {
        $links = [];
        $currentAreaId = (string) ($currentArea['id'] ?? '');

        foreach ($areas as $area) {
            if (isset($area['is_active']) && (int) $area['is_active'] !== 1) {
                continue;
            }

            $areaId = (string) ($area['id'] ?? '');
            $startPath = (string) ($area['start_path'] ?? '/');
            if ($startPath === '') {
                $startPath = '/';
            }

            $links[] = [
                'id' => $area['id'] ?? null,
                'area_key' => (string) ($area['area_key'] ?? ''),
                'name' => (string) ($area['name'] ?? ''),
                'start_path' => $startPath,
                'link' => $startPath,
                'active' => $currentAreaId !== '' && $areaId === $currentAreaId,
            ];
        }

        return $links;
    }

    private function currentArea(Request $request, array $areas, array $parameters): array
    {
        $headerAreaKey = trim((string) ($parameters['headerAreaKey'] ?? ''));
        if ($headerAreaKey !== '') {
            foreach ($areas as $area) {
                if ((string) ($area['area_key'] ?? '') === $headerAreaKey) {
                    return $area;
                }
            }
        }

        $areaRootLink = trim((string) ($parameters['areaRootLink'] ?? ''));
        if ($areaRootLink !== '') {
            foreach ($areas as $area) {
                if ((string) ($area['start_path'] ?? '') === $areaRootLink) {
                    return $area;
                }
            }
        }

        $path = $this->normalizePathForNavigation($request->path);
        $bestArea = [];
        $bestLength = -1;

        foreach ($areas as $area) {
            if (isset($area['is_active']) && (int) $area['is_active'] !== 1) {
                continue;
            }

            $startPath = $this->normalizePathForNavigation((string) ($area['start_path'] ?? '/'));
            if (!$this->pathStartsWith($path, $startPath)) {
                continue;
            }

            $length = strlen($startPath);
            if ($startPath === '/') {
                $length = 0;
            }

            if ($length > $bestLength) {
                $bestArea = $area;
                $bestLength = $length;
            }
        }

        if ($bestArea !== []) {
            return $bestArea;
        }

        foreach ($areas as $area) {
            if ((string) ($area['area_key'] ?? '') === 'portal') {
                return $area;
            }
        }

        return $areas[0] ?? [];
    }

    private function headerMenus(Request $request, array $currentArea): array
    {
        if (
            $currentArea === []
            || $this->headerMenuRepository === null
            || $this->headerMenuItemRepository === null
        ) {
            return [];
        }

        $menu = $this->menuForArea($currentArea);
        if ($menu === []) {
            return [];
        }

        $menuId = (int) ($menu['id'] ?? 0);
        if ($menuId <= 0) {
            return [];
        }

        return $this->buildMenuTree(
            $this->itemsForMenu($menuId, $request->path)
        );
    }

    private function menuForArea(array $area): array
    {
        $areaId = (int) ($area['id'] ?? 0);
        if ($areaId <= 0 || $this->headerMenuRepository === null) {
            return [];
        }

        $fallback = [];

        foreach ($this->headerMenuRepository->findAll() as $menu) {
            if ((int) ($menu['area_id'] ?? 0) !== $areaId) {
                continue;
            }

            if ($fallback === []) {
                $fallback = $menu;
            }

            if (!empty($menu['is_default'])) {
                return $menu;
            }
        }

        return $fallback;
    }

    private function itemsForMenu(int $menuId, string $path): array
    {
        if ($this->headerMenuItemRepository === null) {
            return [];
        }

        $items = [];

        foreach ($this->headerMenuItemRepository->findAll() as $item) {
            if ((int) ($item['menu_id'] ?? 0) !== $menuId) {
                continue;
            }

            if (isset($item['is_active']) && (int) $item['is_active'] !== 1) {
                continue;
            }

            $items[] = $this->normalizeMenuItem($item, $path);
        }

        usort($items, static function (array $a, array $b): int {
            return [
                (int) ($a['level'] ?? 1),
                (int) ($a['order_index'] ?? 0),
                (string) ($a['title'] ?? ''),
            ] <=> [
                (int) ($b['level'] ?? 1),
                (int) ($b['order_index'] ?? 0),
                (string) ($b['title'] ?? ''),
            ];
        });

        return $items;
    }

    private function normalizeMenuItem(array $item, string $path): array
    {
        $url = (string) ($item['url'] ?? '');

        if ($url === '') {
            $slug = trim((string) ($item['slug'] ?? ''), '/');
            $url = $slug !== '' ? '/' . $slug : '#';
        }

        $title = (string) ($item['title'] ?? ($item['name'] ?? ''));

        return array_replace($item, [
            'id' => (int) ($item['id'] ?? 0),
            'menu_id' => (int) ($item['menu_id'] ?? 0),
            'parent_id' => $item['parent_id'] === null || $item['parent_id'] === '' ? null : (int) $item['parent_id'],
            'title' => $title,
            'label' => $title,
            'url' => $url,
            'href' => $url,
            'level' => (int) ($item['level'] ?? 1),
            'order_index' => (int) ($item['order_index'] ?? 0),
            'target' => $item['target'] ?? null,
            'children' => [],
            'active' => $this->isActiveMenuUrl($path, $url),
        ]);
    }

    private function buildMenuTree(array $items): array
    {
        $ids = [];
        foreach ($items as $item) {
            $id = (int) ($item['id'] ?? 0);
            if ($id > 0) {
                $ids[$id] = true;
            }
        }

        $byParent = [];

        foreach ($items as $item) {
            $id = (int) ($item['id'] ?? 0);
            if ($id <= 0) {
                continue;
            }

            $parentId = $item['parent_id'] ?? null;
            if ($parentId === null || !isset($ids[(int) $parentId])) {
                $parentKey = 'root';
            } else {
                $parentKey = (string) ((int) $parentId);
            }

            $byParent[$parentKey][] = $item;
        }

        $build = function (string $parentKey) use (&$build, &$byParent): array {
            $nodes = $byParent[$parentKey] ?? [];

            usort($nodes, static function (array $a, array $b): int {
                return [
                    (int) ($a['order_index'] ?? 0),
                    (string) ($a['title'] ?? ''),
                ] <=> [
                    (int) ($b['order_index'] ?? 0),
                    (string) ($b['title'] ?? ''),
                ];
            });

            foreach ($nodes as $index => $node) {
                $children = $build((string) ((int) ($node['id'] ?? 0)));
                $node['children'] = $children;

                foreach ($children as $child) {
                    if (!empty($child['active'])) {
                        $node['active'] = true;
                        break;
                    }
                }

                $nodes[$index] = $node;
            }

            return $nodes;
        };

        return $build('root');
    }

    private function isActiveMenuUrl(string $path, string $url): bool
    {
        if ($url === '' || $url === '#') {
            return false;
        }

        if (preg_match('/^https?:\/\//i', $url) === 1) {
            return false;
        }

        $urlPath = parse_url($url, PHP_URL_PATH);
        if (!is_string($urlPath) || $urlPath === '') {
            return false;
        }

        $path = $this->normalizePathForNavigation($path);
        $urlPath = $this->normalizePathForNavigation($urlPath);

        if ($urlPath === '/') {
            return $path === '/';
        }

        return $path === $urlPath || str_starts_with($path, rtrim($urlPath, '/') . '/');
    }

    private function pathStartsWith(string $path, string $prefix): bool
    {
        $path = $this->normalizePathForNavigation($path);
        $prefix = $this->normalizePathForNavigation($prefix);

        if ($prefix === '/') {
            return true;
        }

        return $path === $prefix || str_starts_with($path, rtrim($prefix, '/') . '/');
    }

    private function normalizePathForNavigation(string $path): string
    {
        $path = parse_url($path, PHP_URL_PATH) ?: '/';
        $path = '/' . ltrim($path, '/');

        if ($path !== '/') {
            $path = rtrim($path, '/');
        }

        return $path;
    }
}
