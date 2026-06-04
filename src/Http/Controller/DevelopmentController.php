<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Http\Request\Request;
use App\Http\Response\Response;
use App\Presentation\Templating\Renderer;
use App\Repository\AreaRepository;
use App\Repository\MenuRepository;

final class DevelopmentController extends Controller
{
    public function __construct(
        Renderer $renderer,
        private AreaRepository $areas,
        private MenuRepository $menus
    )
    {
        parent::__construct($renderer);
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

        return $this->html('pages.development.menus.index', [
            'title' => 'Menüs',
            'areaName' => 'Menüs',
            'pageTitle' => 'Übersicht',
            'areaRootLink' => '/development/web-control/menus',
            'areaNav' => [[ 'label' => 'Menüs', 'href' => '/development/web-control/menus', 'active' => true ]],
            'headerNav' => [],
            'menus' => $menus,
            'areasMap' => $areasMap,
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

        return $this->html('pages.development.menus.form', [
            'title' => 'Menü bearbeiten',
            'areaName' => 'Menüs',
            'pageTitle' => 'Menü bearbeiten',
            'areaRootLink' => '/development/web-control/menus',
            'areaNav' => [[ 'label' => 'Menüs', 'href' => '/development/web-control/menus', 'active' => true ]],
            'headerNav' => [],
            'areas' => [],
            'areaLabel' => $areaLabel,
            'menu' => $menu,
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
}
