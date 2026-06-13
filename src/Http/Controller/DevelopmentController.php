<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Http\Request\Request;
use App\Http\Response\Response;
use App\Presentation\Templating\Renderer;
use App\Repository\AreaRepository;
use App\Repository\MenuItemRepository;
use App\Repository\MenuRepository;

final class DevelopmentController extends Controller
{
    public function __construct(
        Renderer $renderer,
        AreaRepository $areas,
        private MenuRepository $menus,
        private MenuItemRepository $menuItemRepository,
    ) {
        parent::__construct($renderer, $areas, $menus, $menuItemRepository);
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

        return $this->page($request, 'pages/development/web-control/index', [
            'title' => 'Web-Control',
            'areaName' => 'Web-Control',
            'pageTitle' => 'Dashboard',
            'areaRootLink' => '/development/web-control',
            'headerAreaKey' => 'development',
            'areaNav' => $this->webControlNav('dashboard'),
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
        ]);
    }

    public function areasIndex(Request $request): Response
    {
        return $this->page($request, 'pages/development/areas/index', [
            'title' => 'Bereiche',
            'areaName' => 'Web-Control',
            'pageTitle' => 'Bereiche',
            'areaRootLink' => '/development/web-control',
            'headerAreaKey' => 'development',
            'areaNav' => $this->webControlNav('areas'),
            'areas' => $this->areas->findAll(),
        ]);
    }

    public function areasCreateForm(Request $request): Response
    {
        return $this->page($request, 'pages/development/areas/form', [
            'title' => 'Neuen Bereich',
            'areaName' => 'Web-Control',
            'pageTitle' => 'Neuen Bereich erstellen',
            'areaRootLink' => '/development/web-control',
            'headerAreaKey' => 'development',
            'areaNav' => $this->webControlNav('areas'),
            'area' => null,
            'errors' => [],
        ]);
    }

    public function areasCreate(Request $request): Response
    {
        $body = $request->body;

        $name = $this->bodyString($body, 'name');
        $areaKey = $this->bodyString($body, 'area_key');
        $icon = $this->bodyString($body, 'icon');
        $description = $this->bodyString($body, 'description');
        $startPath = $this->bodyString($body, 'start_path', '/');
        $isActive = isset($body['is_active']) ? 1 : 0;
        $isExternal = isset($body['is_external']) ? 1 : 0;
        $sortOrder = $this->bodyInt($body, 'sort_order');

        $area = [
            'name' => $name,
            'area_key' => $areaKey,
            'icon' => $icon,
            'description' => $description,
            'start_path' => $startPath,
            'is_active' => $isActive,
            'is_external' => $isExternal,
            'sort_order' => $sortOrder,
        ];

        $errors = $this->validateAreaData($area);

        if ($errors !== []) {
            return $this->areaFormPage(
                $request,
                'Neuen Bereich',
                'Neuen Bereich erstellen',
                $area,
                $errors
            );
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
            return $this->redirect('/development/web-control/areas');
        }

        return $this->areaFormPage(
            $request,
            'Neuen Bereich',
            'Neuen Bereich erstellen',
            $area,
            ['Speichern fehlgeschlagen.']
        );
    }

    public function areasEditForm(Request $request): Response
    {
        $id = $this->queryInt($request, 'id');

        if ($id <= 0) {
            return $this->redirect('/development/web-control/areas');
        }

        $area = $this->areas->find($id);

        if ($area === []) {
            return $this->redirect('/development/web-control/areas');
        }

        return $this->areaFormPage(
            $request,
            'Bereich bearbeiten',
            'Bereich bearbeiten',
            $area
        );
    }

    public function areasEdit(Request $request): Response
    {
        $body = $request->body;
        $id = $this->bodyInt($body, 'id');

        if ($id <= 0) {
            return $this->redirect('/development/web-control/areas');
        }

        $data = [];

        if (isset($body['area_key'])) {
            $data['area_key'] = $this->bodyString($body, 'area_key');
        }

        if (isset($body['icon'])) {
            $data['icon'] = $this->bodyString($body, 'icon');
        }

        if (isset($body['name'])) {
            $data['name'] = $this->bodyString($body, 'name');
        }

        if (isset($body['description'])) {
            $data['description'] = $this->bodyString($body, 'description');
        }

        if (isset($body['start_path'])) {
            $data['start_path'] = $this->bodyString($body, 'start_path');
        }

        $data['is_active'] = isset($body['is_active']) ? 1 : 0;
        $data['is_external'] = isset($body['is_external']) ? 1 : 0;

        if (isset($body['sort_order'])) {
            $data['sort_order'] = $this->bodyInt($body, 'sort_order');
        }

        if ($data === []) {
            return $this->redirect('/development/web-control/areas');
        }

        $errors = $this->validateAreaData($data, false);

        if ($errors !== []) {
            $area = array_merge(
                $this->areas->find($id),
                $data,
                ['id' => $id]
            );

            return $this->areaFormPage(
                $request,
                'Bereich bearbeiten',
                'Bereich bearbeiten',
                $area,
                $errors
            );
        }

        $ok = $this->areas->update($id, $data);

        if (!$ok) {
            $area = array_merge(
                $this->areas->find($id),
                $data,
                ['id' => $id]
            );

            return $this->areaFormPage(
                $request,
                'Bereich bearbeiten',
                'Bereich bearbeiten',
                $area,
                ['Speichern fehlgeschlagen.']
            );
        }

        return $this->redirect('/development/web-control/areas');
    }

    public function areasDelete(Request $request): Response
    {
        $id = $this->bodyInt($request->body, 'id');

        if ($id <= 0) {
            return $this->redirect('/development/web-control/areas');
        }

        $this->areas->delete($id);

        return $this->redirect('/development/web-control/areas');
    }

    public function menusIndex(Request $request): Response
    {
        $menus = $this->menus->findAll();
        $areas = $this->areas->findAll();

        $areasMap = [];

        foreach ($areas as $area) {
            $areasMap[(string) ($area['id'] ?? '')] = (string) ($area['name'] ?? '');
        }

        $menuItemsByMenuId = [];
        $menuItemCounts = [];

        foreach ($this->menuItemRepository->findAll() as $item) {
            $menuId = (string) ($item['menu_id'] ?? '');

            if ($menuId === '') {
                continue;
            }

            $menuItemsByMenuId[$menuId][] = $item;
            $menuItemCounts[$menuId] = ($menuItemCounts[$menuId] ?? 0) + 1;
        }

        return $this->page($request, 'pages/development/menus/index', [
            'title' => 'Menüs',
            'areaName' => 'Web-Control',
            'pageTitle' => 'Menüs',
            'areaRootLink' => '/development/web-control',
            'headerAreaKey' => 'development',
            'areaNav' => $this->webControlNav('menus'),
            'menus' => $menus,
            'areasMap' => $areasMap,
            'menuItemsByMenuId' => $menuItemsByMenuId,
            'menuItemCounts' => $menuItemCounts,
        ]);
    }

    public function menusCreateForm(Request $request): Response
    {
        return $this->menuFormPage(
            $request,
            'Neues Menü',
            'Neues Menü erstellen',
            null,
            $this->availableAreasForMenuCreation()
        );
    }

    public function menusCreate(Request $request): Response
    {
        $body = $request->body;

        $name = $this->bodyString($body, 'name');
        $slug = $this->bodyString($body, 'slug');
        $areaId = $this->bodyInt($body, 'area_id');

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

        if ($areaId > 0 && $this->menus->findForArea($areaId) !== []) {
            $errors[] = 'Für diese Area existiert bereits ein Menü.';
        }

        $menu = [
            'name' => $name,
            'slug' => $slug,
            'area_id' => $areaId,
            'is_default' => 1,
        ];

        if ($errors !== []) {
            return $this->menuFormPage(
                $request,
                'Neues Menü',
                'Neues Menü erstellen',
                $menu,
                $this->availableAreasForMenuCreation(),
                $errors
            );
        }

        $id = $this->menus->create([
            'area_id' => $areaId,
            'name' => $name,
            'slug' => $slug,
            'is_default' => 1,
        ]);

        if ($id > 0) {
            return $this->redirect('/development/web-control/menus');
        }

        return $this->menuFormPage(
            $request,
            'Neues Menü',
            'Neues Menü erstellen',
            $menu,
            $this->availableAreasForMenuCreation(),
            ['Speichern fehlgeschlagen.']
        );
    }

    public function menusEditForm(Request $request): Response
    {
        $id = $this->queryInt($request, 'id');

        if ($id <= 0) {
            return $this->redirect('/development/web-control/menus');
        }

        $menu = $this->menus->find($id);

        if ($menu === []) {
            return $this->redirect('/development/web-control/menus');
        }

        return $this->menuFormPage(
            $request,
            'Menü bearbeiten',
            'Menü bearbeiten',
            $menu,
            [],
            [],
            $this->areaLabelForMenu($menu),
            $this->menuItemRepository->findByMenuId($id)
        );
    }

    public function menusEdit(Request $request): Response
    {
        $body = $request->body;
        $id = $this->bodyInt($body, 'id');

        if ($id <= 0) {
            return $this->redirect('/development/web-control/menus');
        }

        $menu = $this->menus->find($id);

        if ($menu === []) {
            return $this->redirect('/development/web-control/menus');
        }

        $data = [];

        if (isset($body['name'])) {
            $data['name'] = $this->bodyString($body, 'name');
        }

        if (isset($body['slug'])) {
            $data['slug'] = $this->bodyString($body, 'slug');
        }

        if ($data === []) {
            return $this->redirect('/development/web-control/menus');
        }

        $errors = [];

        if (($data['name'] ?? '') === '') {
            $errors[] = 'Name ist erforderlich.';
        }

        if (($data['slug'] ?? '') === '') {
            $errors[] = 'Slug ist erforderlich.';
        }

        if ($errors !== []) {
            return $this->menuFormPage(
                $request,
                'Menü bearbeiten',
                'Menü bearbeiten',
                array_merge($menu, $data),
                [],
                $errors,
                $this->areaLabelForMenu($menu),
                $this->menuItemRepository->findByMenuId($id)
            );
        }

        $ok = $this->menus->update($id, $data);

        if (!$ok) {
            return $this->menuFormPage(
                $request,
                'Menü bearbeiten',
                'Menü bearbeiten',
                array_merge($menu, $data),
                [],
                ['Speichern fehlgeschlagen.'],
                $this->areaLabelForMenu($menu),
                $this->menuItemRepository->findByMenuId($id)
            );
        }

        return $this->redirect('/development/web-control/menus');
    }

    public function menusDelete(Request $request): Response
    {
        $id = $this->bodyInt($request->body, 'id');

        if ($id <= 0) {
            return $this->redirect('/development/web-control/menus');
        }

        $this->menus->delete($id);

        return $this->redirect('/development/web-control/menus');
    }

    public function menuItemsCreate(Request $request): Response
    {
        $body = $request->body;
        $menuId = $this->bodyInt($body, 'menu_id');

        if ($menuId <= 0 || $this->menus->find($menuId) === []) {
            return $this->redirect('/development/web-control/menus');
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
            return $this->redirect('/development/web-control/menus');
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
            return $this->redirect('/development/web-control/menus');
        }

        $this->menuItemRepository->deleteFromMenu($itemId, $menuId);

        return $this->redirectToMenuItems($menuId);
    }

    private function areaFormPage(
        Request $request,
        string $title,
        string $pageTitle,
        ?array $area,
        array $errors = []
    ): Response {
        return $this->page($request, 'pages/development/areas/form', [
            'title' => $title,
            'areaName' => 'Web-Control',
            'pageTitle' => $pageTitle,
            'areaRootLink' => '/development/web-control',
            'headerAreaKey' => 'development',
            'areaNav' => $this->webControlNav('areas'),
            'area' => $area,
            'errors' => $errors,
        ]);
    }

    private function menuFormPage(
        Request $request,
        string $title,
        string $pageTitle,
        ?array $menu,
        array $areas = [],
        array $errors = [],
        string $areaLabel = '',
        array $menuItems = []
    ): Response {
        return $this->page($request, 'pages/development/menus/form', [
            'title' => $title,
            'areaName' => 'Web-Control',
            'pageTitle' => $pageTitle,
            'areaRootLink' => '/development/web-control',
            'headerAreaKey' => 'development',
            'areaNav' => $this->webControlNav('menus'),
            'areas' => $areas,
            'areaLabel' => $areaLabel,
            'menu' => $menu,
            'menuItems' => $menuItems,
            'menuItemCreateAction' => '/development/web-control/menu-items/create',
            'menuItemEditAction' => '/development/web-control/menu-items/edit',
            'menuItemDeleteAction' => '/development/web-control/menu-items/delete',
            'errors' => $errors,
        ]);
    }

    private function availableAreasForMenuCreation(): array
    {
        $availableAreas = $this->areas->findAll();
        $usedAreaIds = [];

        foreach ($this->menus->findAll() as $menu) {
            $areaId = (string) ($menu['area_id'] ?? '');

            if ($areaId !== '') {
                $usedAreaIds[$areaId] = true;
            }
        }

        return array_values(array_filter(
            $availableAreas,
            static fn (array $area): bool => !isset($usedAreaIds[(string) ($area['id'] ?? '')])
        ));
    }

    private function areaLabelForMenu(array $menu): string
    {
        $areaId = (string) ($menu['area_id'] ?? '');

        if ($areaId === '') {
            return '';
        }

        foreach ($this->areas->findAll() as $area) {
            if ((string) ($area['id'] ?? '') === $areaId) {
                return (string) ($area['name'] ?? '');
            }
        }

        return '';
    }

    private function validateAreaData(array $data, bool $requireAll = true): array
    {
        $errors = [];

        $name = isset($data['name']) ? trim((string) $data['name']) : null;
        $areaKey = isset($data['area_key']) ? trim((string) $data['area_key']) : null;
        $startPath = isset($data['start_path']) ? trim((string) $data['start_path']) : null;

        if (($requireAll || $name !== null) && $name === '') {
            $errors[] = 'Name ist erforderlich.';
        }

        if (($requireAll || $areaKey !== null) && $areaKey === '') {
            $errors[] = 'Area Key ist erforderlich.';
        }

        if ($areaKey !== null && $areaKey !== '' && !preg_match('/^[a-z0-9_]+$/', $areaKey)) {
            $errors[] = 'Area Key darf nur a-z, 0-9 und _ enthalten.';
        }

        if (($requireAll || $startPath !== null) && $startPath === '') {
            $errors[] = 'Startpfad ist erforderlich.';
        }

        if ($startPath !== null && $startPath !== '' && $startPath[0] !== '/') {
            $errors[] = 'Startpfad muss mit / beginnen.';
        }

        return $errors;
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

    private function queryInt(Request $request, string $key, int $default = 0): int
    {
        $value = $request->query[$key] ?? $default;

        if ($value === '' || $value === null) {
            return $default;
        }

        return (int) $value;
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
        return $this->redirect(
            '/development/web-control/menus/edit?id=' . urlencode((string) $menuId) . '#menu-items'
        );
    }
}