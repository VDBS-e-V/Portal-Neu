<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Http\Request\Request;
use App\Http\Response\Response;
use App\Presentation\Templating\Renderer;
use App\Repository\AreaRepository;
use App\Repository\MenuRepository;
use App\Repository\MenuItemRepository;

final class DevelopmentController extends Controller
{
    public function __construct(
        Renderer $renderer,
        private AreaRepository $areas,
        private MenuRepository $menus,
        private MenuItemRepository $menuItemRepository,
    )
    {
        parent::__construct($renderer);
    }

    public function webControlDashboard(Request $request): Response
    {
        $areas = $this->areas->findAll();
        $menus = $this->menus->findAll();
        $menuItems = $this->menuItemRepository->findAll();

        $activeAreas = 0;
        $externalAreas = 0;

        foreach ($areas as $area) {
            if (!empty($area['is_active'])) {
                $activeAreas++;
            }

            if (!empty($area['is_external'])) {
                $externalAreas++;
            }
        }

        $activeMenuItems = 0;

        foreach ($menuItems as $menuItem) {
            if (!empty($menuItem['is_active'])) {
                $activeMenuItems++;
            }
        }

        $menuItemsByMenuId = [];

        foreach ($menuItems as $menuItem) {
            $menuId = (string) ($menuItem['menu_id'] ?? '');

            if ($menuId === '') {
                continue;
            }

            $menuItemsByMenuId[$menuId][] = $menuItem;
        }

        return $this->html('pages.development.web-control.index', [
            'title' => 'Web-Control',
            'areaName' => 'Web-Control',
            'pageTitle' => 'Dashboard',
            'areaRootLink' => '/development/web-control',
            'areaNav' => [
                [
                    'label' => 'Dashboard',
                    'href' => '/development/web-control',
                    'active' => true,
                ],
                [
                    'label' => 'Bereiche',
                    'href' => '/development/web-control/areas',
                    'active' => false,
                ],
                [
                    'label' => 'Menüs',
                    'href' => '/development/web-control/menus',
                    'active' => false,
                ],
            ],
            'headerNav' => [],
            'areas' => $areas,
            'menus' => $menus,
            'menuItems' => $menuItems,
            'menuItemsByMenuId' => $menuItemsByMenuId,
            'stats' => [
                'areas_total' => count($areas),
                'areas_active' => $activeAreas,
                'areas_external' => $externalAreas,
                'menus_total' => count($menus),
                'menu_items_total' => count($menuItems),
                'menu_items_active' => $activeMenuItems,
            ],
            'path' => $request->path,
            'now' => date('c'),
        ]);
    }

    // Areas
    public function areasIndex(Request $request): Response
    {
        $areas = $this->areas->findAll();

        return $this->html('pages.development.areas.index', [
            'title' => 'Bereiche',
            'areaName' => 'Bereiche',
            'pageTitle' => 'Übersicht',
            'areaRootLink' => '/',
            'areaNav' => [[ 'label' => 'Start', 'href' => '/', 'active' => false ]],
            'headerNav' => [],
            'areas' => $areas,
            'path' => $request->path,
            'now' => date('c'),
        ]);
    }

    public function areasCreateForm(Request $request): Response
    {
        return $this->html('pages.development.areas.form', [
            'title' => 'Neuen Bereich',
            'areaName' => 'Bereiche',
            'pageTitle' => 'Neuen Bereich erstellen',
            'areaRootLink' => '/development/web-control/areas',
            'areaNav' => [[ 'label' => 'Bereiche', 'href' => '/development/web-control/areas', 'active' => true ]],
            'headerNav' => [],
            'area' => null,
            'path' => $request->path,
            'now' => date('c'),
        ]);
    }

    public function areasCreate(Request $request): Response
    {
        $body = $request->body;
        $name = trim((string) ($body['name'] ?? ''));
        $areaKey = trim((string) ($body['area_key'] ?? ''));
        $icon = trim((string) ($body['icon'] ?? ''));
        $description = trim((string) ($body['description'] ?? ''));
        $startPath = trim((string) ($body['start_path'] ?? '/'));
        $isActive = isset($body['is_active']) ? 1 : 0;
        $isExternal = isset($body['is_external']) ? 1 : 0;
        $sortOrder = isset($body['sort_order']) ? (int) $body['sort_order'] : 0;

        $errors = [];
        if ($name === '') {
            $errors[] = 'Name ist erforderlich.';
        }
        if ($areaKey === '') {
            $errors[] = 'Area Key ist erforderlich.';
        }
        if (!preg_match('/^[a-z0-9_]+$/', $areaKey)) {
            $errors[] = 'Area Key darf nur a-z, 0-9 und _ enthalten.';
        }
        if ($startPath === '') {
            $errors[] = 'Startpfad ist erforderlich.';
        }
        if ($startPath !== '' && $startPath[0] !== '/') {
            $errors[] = 'Startpfad muss mit / beginnen.';
        }

        if (!empty($errors)) {
            return $this->html('pages.development.areas.form', [
                'title' => 'Neuen Bereich',
                'area' => [
                    'name' => $name,
                    'area_key' => $areaKey,
                    'icon' => $icon,
                    'description' => $description,
                    'start_path' => $startPath,
                    'is_active' => $isActive,
                    'is_external' => $isExternal,
                    'sort_order' => $sortOrder,
                ],
                'errors' => $errors,
                'path' => $request->path,
                'now' => date('c'),
            ]);
        }

        $id = $this->areas->create([
            'area_key' => $areaKey,
            'icon' => $icon,
            'name' => $name,
            'description' => $description,
            'start_path' => $startPath,
            'is_active' => $isActive,
            'is_external' => $isExternal,
            'sort_order' => $sortOrder,
        ]);

        if ($id > 0) {
            return new Response(302, ['Location' => '/development/web-control/areas'], '');
        }

        return $this->html('pages.development.areas.form', [
            'title' => 'Neuen Bereich',
            'errors' => ['Speichern fehlgeschlagen.'],
            'area' => [
                'name' => $name,
                'area_key' => $areaKey,
                'icon' => $icon,
                'description' => $description,
                'start_path' => $startPath,
                'is_active' => $isActive,
                'is_external' => $isExternal,
                'sort_order' => $sortOrder,
            ],
            'path' => $request->path,
            'now' => date('c'),
        ]);
    }

    public function areasEditForm(Request $request): Response
    {
        $id = isset($request->query['id']) ? (int) $request->query['id'] : 0;
        if ($id <= 0) {
            return new Response(302, ['Location' => '/development/web-control/areas'], '');
        }

        $area = $this->areas->find($id);

        if (empty($area)) {
            return new Response(302, ['Location' => '/development/web-control/areas'], '');
        }

        return $this->html('pages.development.areas.form', [
            'title' => 'Bereich bearbeiten',
            'area' => $area,
            'path' => $request->path,
            'now' => date('c'),
        ]);
    }

    public function areasEdit(Request $request): Response
    {
        $body = $request->body;
        $id = isset($body['id']) ? (int) $body['id'] : 0;
        if ($id <= 0) {
            return new Response(302, ['Location' => '/development/web-control/areas'], '');
        }

        $data = [];
        if (isset($body['area_key'])) {
            $data['area_key'] = trim((string) $body['area_key']);
        }
        if (isset($body['icon'])) {
            $data['icon'] = trim((string) $body['icon']);
        }
        if (isset($body['name'])) {
            $data['name'] = trim((string) $body['name']);
        }
        if (isset($body['description'])) {
            $data['description'] = trim((string) $body['description']);
        }
        if (isset($body['start_path'])) {
            $data['start_path'] = trim((string) $body['start_path']);
        }
        $data['is_active'] = isset($body['is_active']) ? 1 : 0;
        $data['is_external'] = isset($body['is_external']) ? 1 : 0;
        if (isset($body['sort_order'])) {
            $data['sort_order'] = (int) $body['sort_order'];
        }

        if (empty($data)) {
            return new Response(302, ['Location' => '/development/web-control/areas'], '');
        }

        $ok = $this->areas->update($id, $data);

        return new Response($ok ? 302 : 500, ['Location' => '/development/web-control/areas'], '');
    }

    public function areasDelete(Request $request): Response
    {
        $body = $request->body;
        $id = isset($body['id']) ? (int) $body['id'] : 0;
        if ($id <= 0) {
            return new Response(302, ['Location' => '/development/web-control/areas'], '');
        }

        $ok = $this->areas->delete($id);

        return new Response($ok ? 302 : 500, ['Location' => '/development/web-control/areas'], '');
    }

    // Menus
    public function menusIndex(Request $request): Response
    {
        $menus = $this->menus->findAll();
        $areas = $this->areas->findAll();

        $areasMap = [];
        foreach ($areas as $area) {
            $areasMap[(string) ($area['id'] ?? '')] = (string) ($area['name'] ?? '');
        }

        /*
        * Menu Items für die Übersicht laden.
        * Das Template index.php erwartet:
        * - $menuItemsByMenuId
        * - $menuItemCounts
        */
        $menuItemsByMenuId = [];
        $menuItemCounts = [];

        foreach ($this->menuItemRepository->findAll() as $item) {
            $menuId = (string) ($item['menu_id'] ?? '');

            if ($menuId === '') {
                continue;
            }

            if (!isset($menuItemsByMenuId[$menuId])) {
                $menuItemsByMenuId[$menuId] = [];
            }

            $menuItemsByMenuId[$menuId][] = $item;
            $menuItemCounts[$menuId] = ($menuItemCounts[$menuId] ?? 0) + 1;
        }

        return $this->html('pages.development.menus.index', [
            'title' => 'Menüs',
            'areaName' => 'Menüs',
            'pageTitle' => 'Übersicht',
            'areaRootLink' => '/development/web-control/menus',
            'areaNav' => [
                [
                    'label' => 'Menüs',
                    'href' => '/development/web-control/menus',
                    'active' => true,
                ],
            ],
            'headerNav' => [],
            'menus' => $menus,
            'areasMap' => $areasMap,
            'menuItemsByMenuId' => $menuItemsByMenuId,
            'menuItemCounts' => $menuItemCounts,
            'path' => $request->path,
            'now' => date('c'),
        ]);
    }

    public function menusEditForm(Request $request): Response
    {
        $id = isset($request->query['id']) ? (int) $request->query['id'] : 0;

        if ($id <= 0) {
            return new Response(302, ['Location' => '/development/web-control/menus'], '');
        }

        $menu = $this->menus->find($id);

        if (empty($menu)) {
            return new Response(302, ['Location' => '/development/web-control/menus'], '');
        }

        $areaLabel = '';
        foreach ($this->areas->findAll() as $area) {
            if ((string) ($area['id'] ?? '') === (string) ($menu['area_id'] ?? '')) {
                $areaLabel = (string) ($area['name'] ?? '');
                break;
            }
        }

        /*
        * Wichtig:
        * Ohne diese Zeile bekommt das Template keine Items.
        * Dann greift in form.php nur:
        * $menuItems = $menuItems ?? [];
        */
        $menuItems = $this->menuItemRepository->findByMenuId($id);

        return $this->html('pages.development.menus.form', [
            'title' => 'Menü bearbeiten',
            'areaName' => 'Menüs',
            'pageTitle' => 'Menü bearbeiten',
            'areaRootLink' => '/development/web-control/menus',
            'areaNav' => [
                [
                    'label' => 'Menüs',
                    'href' => '/development/web-control/menus',
                    'active' => true,
                ],
            ],
            'headerNav' => [],
            'areas' => [],
            'areaLabel' => $areaLabel,
            'menu' => $menu,
            'menuItems' => $menuItems,
            'menuItemCreateAction' => '/development/web-control/menu-items/create',
            'menuItemEditAction' => '/development/web-control/menu-items/edit',
            'menuItemDeleteAction' => '/development/web-control/menu-items/delete',
            'path' => $request->path,
            'now' => date('c'),
        ]);
    }

    public function menusCreateForm(Request $request): Response
    {
        $availableAreas = $this->areas->findAll();
        $usedAreaIds = [];
        foreach ($this->menus->findAll() as $menu) {
            $usedAreaIds[(string) ($menu['area_id'] ?? '')] = true;
        }
        $availableAreas = array_values(array_filter(
            $availableAreas,
            static fn (array $area): bool => !isset($usedAreaIds[(string) ($area['id'] ?? '')])
        ));

        return $this->html('pages.development.menus.form', [
            'title' => 'Neues Menü',
            'areaName' => 'Menüs',
            'pageTitle' => 'Neues Menü erstellen',
            'areaRootLink' => '/development/web-control/menus',
            'areaNav' => [[ 'label' => 'Menüs', 'href' => '/development/web-control/menus', 'active' => true ]],
            'headerNav' => [],
            'areas' => $availableAreas,
            'menu' => null,
            'path' => $request->path,
            'now' => date('c'),
        ]);
    }

    public function menusCreate(Request $request): Response
    {
        $body = $request->body;
        $name = trim((string) ($body['name'] ?? ''));
        $slug = trim((string) ($body['slug'] ?? ''));
        $areaId = isset($body['area_id']) ? (int) $body['area_id'] : 0;

        $errors = [];
        if ($name === '') {
            $errors[] = 'Name ist erforderlich.';
        }
        if ($slug === '') {
            $errors[] = 'Slug ist erforderlich.';
        }
        if ($areaId <= 0) {
            $errors[] = 'Area ist erforderlich.';
        }
        if ($areaId > 0 && !empty($this->menus->findForArea($areaId))) {
            $errors[] = 'Für diese Area existiert bereits ein Menü.';
        }

        if (!empty($errors)) {
            return $this->html('pages.development.menus.form', [
                'title' => 'Neues Menü',
                'areaName' => 'Menüs',
                'pageTitle' => 'Neues Menü erstellen',
                'areaRootLink' => '/development/web-control/menus',
                'areaNav' => [[ 'label' => 'Menüs', 'href' => '/development/web-control/menus', 'active' => true ]],
                'headerNav' => [],
                'areas' => $this->areas->findAll(),
                'errors' => $errors,
                'menu' => [
                    'name' => $name,
                    'slug' => $slug,
                    'area_id' => $areaId,
                ],
                'path' => $request->path,
                'now' => date('c'),
            ]);
        }

        $id = $this->menus->create([
            'area_id' => $areaId,
            'name' => $name,
            'slug' => $slug,
            'is_default' => 1,
        ]);

        if ($id > 0) {
            return new Response(302, ['Location' => '/development/web-control/menus'], '');
        }

        return $this->html('pages.development.menus.form', [
            'title' => 'Neues Menü',
            'areaName' => 'Menüs',
            'pageTitle' => 'Neues Menü erstellen',
            'areaRootLink' => '/development/web-control/menus',
            'areaNav' => [[ 'label' => 'Menüs', 'href' => '/development/web-control/menus', 'active' => true ]],
            'headerNav' => [],
            'areas' => $this->areas->findAll(),
            'errors' => ['Speichern fehlgeschlagen.'],
            'menu' => [
                'name' => $name,
                'slug' => $slug,
                'area_id' => $areaId,
                'is_default' => 1,
            ],
            'path' => $request->path,
            'now' => date('c'),
        ]);
    }

    public function menusEdit(Request $request): Response
    {
        $body = $request->body;
        $id = isset($body['id']) ? (int) $body['id'] : 0;
        if ($id <= 0) {
            return new Response(302, ['Location' => '/development/web-control/menus'], '');
        }

        $data = [];
        if (isset($body['name'])) {
            $data['name'] = trim((string) $body['name']);
        }
        if (isset($body['slug'])) {
            $data['slug'] = trim((string) $body['slug']);
        }

        if (empty($data)) {
            return new Response(302, ['Location' => '/development/web-control/menus'], '');
        }

        $ok = $this->menus->update($id, $data);

        return new Response($ok ? 302 : 500, ['Location' => '/development/web-control/menus'], '');
    }

    public function menusDelete(Request $request): Response
    {
        $body = $request->body;
        $id = isset($body['id']) ? (int) $body['id'] : 0;
        if ($id <= 0) {
            return new Response(302, ['Location' => '/development/web-control/menus'], '');
        }

        $ok = $this->menus->delete($id);

        return new Response($ok ? 302 : 500, ['Location' => '/development/web-control/menus'], '');
    }

    public function menuItemsCreate(Request $request): Response
    {
        $body = $request->body;

        $menuId = $this->bodyInt($body, 'menu_id');

        if ($menuId <= 0 || $this->menus->find($menuId) === []) {
            return new Response(302, ['Location' => '/development/web-control/menus'], '');
        }

        $data = $this->menuItemDataFromBody($body, $menuId);

        if ($data['title'] === '') {
            return $this->redirectToMenuItems($menuId);
        }

        if (
            $data['parent_id'] !== null
            && !$this->menuItemRepository->belongsToMenu($data['parent_id'], $menuId)
        ) {
            $data['parent_id'] = null;
        }

        $this->menuItemRepository->create($data);

        return $this->redirectToMenuItems($menuId);
    }

    public function menuItemsEdit(Request $request): Response
    {
        $body = $request->body;

        $menuId = $this->bodyInt($body, 'menu_id');
        $itemId = $this->bodyInt($body, 'id');

        if ($menuId <= 0 || $itemId <= 0 || $this->menus->find($menuId) === []) {
            return new Response(302, ['Location' => '/development/web-control/menus'], '');
        }

        if (!$this->menuItemRepository->belongsToMenu($itemId, $menuId)) {
            return $this->redirectToMenuItems($menuId);
        }

        $data = $this->menuItemDataFromBody($body, $menuId);

        if ($data['title'] === '') {
            return $this->redirectToMenuItems($menuId);
        }

        if ($data['parent_id'] === $itemId) {
            $data['parent_id'] = null;
        }

        if (
            $data['parent_id'] !== null
            && !$this->menuItemRepository->belongsToMenu($data['parent_id'], $menuId)
        ) {
            $data['parent_id'] = null;
        }

        unset($data['menu_id']);

        $this->menuItemRepository->update($itemId, $data);

        return $this->redirectToMenuItems($menuId);
    }

    public function menuItemsDelete(Request $request): Response
    {
        $body = $request->body;

        $menuId = $this->bodyInt($body, 'menu_id');
        $itemId = $this->bodyInt($body, 'id');

        if ($menuId <= 0 || $itemId <= 0 || $this->menus->find($menuId) === []) {
            return new Response(302, ['Location' => '/development/web-control/menus'], '');
        }

        $this->menuItemRepository->deleteFromMenu($itemId, $menuId);

        return $this->redirectToMenuItems($menuId);
    }

    private function menuItemDataFromBody(array $body, int $menuId): array
    {
        $level = $this->bodyInt($body, 'level', 1);

        if ($level < 1) {
            $level = 1;
        }

        if ($level > 3) {
            $level = 3;
        }

        $target = $this->bodyString($body, 'target');
        $target = $target === '_blank' ? '_blank' : null;

        return [
            'menu_id' => $menuId,
            'parent_id' => $this->bodyNullableInt($body, 'parent_id'),
            'title' => $this->bodyString($body, 'title'),
            'slug' => $this->bodyNullableString($body, 'slug'),
            'url' => $this->bodyNullableString($body, 'url'),
            'route_name' => $this->bodyNullableString($body, 'route_name'),
            'icon' => $this->bodyNullableString($body, 'icon'),
            'target' => $target,
            'order_index' => $this->bodyNullableInt($body, 'order_index'),
            'level' => $level,
            'is_active' => $this->bodyBool($body, 'is_active') ? 1 : 0,
        ];
    }

    private function bodyString(array $body, string $key, string $default = ''): string
    {
        return trim((string) ($body[$key] ?? $default));
    }

    private function bodyNullableString(array $body, string $key): ?string
    {
        $value = $this->bodyString($body, $key);

        return $value === '' ? null : $value;
    }

    private function bodyInt(array $body, string $key, int $default = 0): int
    {
        $value = $body[$key] ?? $default;

        if ($value === '' || $value === null) {
            return $default;
        }

        return (int) $value;
    }

    private function bodyNullableInt(array $body, string $key): ?int
    {
        $value = $body[$key] ?? null;

        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }

    private function bodyBool(array $body, string $key): bool
    {
        return isset($body[$key]) && (string) $body[$key] === '1';
    }

    private function redirectToMenuItems(int $menuId): Response
    {
        return new Response(
            302,
            ['Location' => '/development/web-control/menus/edit?id=' . urlencode((string) $menuId) . '#menu-items'],
            ''
        );
    }

    private function menuItemDataFromPost(int $menuId): array
    {
        $level = $this->postInt('level', 1);

        if ($level < 1) {
            $level = 1;
        }

        if ($level > 3) {
            $level = 3;
        }

        $target = $this->postString('target');
        $target = $target === '_blank' ? '_blank' : null;

        return [
            'menu_id' => $menuId,
            'parent_id' => $this->postNullableInt('parent_id'),
            'title' => $this->postString('title'),
            'slug' => $this->postNullableString('slug'),
            'url' => $this->postNullableString('url'),
            'route_name' => $this->postNullableString('route_name'),
            'icon' => $this->postNullableString('icon'),
            'target' => $target,
            'order_index' => $this->postNullableInt('order_index'),
            'level' => $level,
            'is_active' => $this->postBool('is_active') ? 1 : 0,
        ];
    }

    private function postString(string $key, string $default = ''): string
    {
        return trim((string) ($_POST[$key] ?? $default));
    }

    private function postNullableString(string $key): ?string
    {
        $value = $this->postString($key);

        return $value === '' ? null : $value;
    }

    private function postInt(string $key, int $default = 0): int
    {
        $value = $_POST[$key] ?? $default;

        if ($value === '' || $value === null) {
            return $default;
        }

        return (int) $value;
    }

    private function postNullableInt(string $key): ?int
    {
        $value = $_POST[$key] ?? null;

        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }

    private function postBool(string $key): bool
    {
        return isset($_POST[$key]) && (string) $_POST[$key] === '1';
    }
}
