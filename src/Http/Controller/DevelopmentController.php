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
    public function __construct(Renderer $renderer, private AreaRepository $areas, private MenuRepository $menus)
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
        $slug = trim((string) ($body['slug'] ?? ''));
        $description = trim((string) ($body['description'] ?? ''));
        $isPublic = isset($body['is_public']) ? (int) $body['is_public'] : 0;

        $errors = [];
        if ($name === '') {
            $errors[] = 'Name ist erforderlich.';
        }
        if ($slug === '') {
            $errors[] = 'Slug ist erforderlich.';
        }

        if (!empty($errors)) {
            return $this->html('pages.development.areas.form', [
                'title' => 'Neuen Bereich',
                'area' => ['name' => $name, 'slug' => $slug, 'description' => $description, 'is_public' => $isPublic],
                'errors' => $errors,
                'path' => $request->path,
                'now' => date('c'),
            ]);
        }

        $id = $this->areas->create([
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
            'is_public' => $isPublic,
        ]);

        if ($id > 0) {
            return new Response(302, ['Location' => '/development/web-control/areas'], '');
        }

        return $this->html('pages.development.areas.form', [
            'title' => 'Neuen Bereich',
            'errors' => ['Speichern fehlgeschlagen.'],
            'area' => ['name' => $name, 'slug' => $slug, 'description' => $description, 'is_public' => $isPublic],
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
        if (isset($body['name'])) {
            $data['name'] = trim((string) $body['name']);
        }
        if (isset($body['slug'])) {
            $data['slug'] = trim((string) $body['slug']);
        }
        if (isset($body['description'])) {
            $data['description'] = trim((string) $body['description']);
        }
        if (isset($body['is_public'])) {
            $data['is_public'] = (int) $body['is_public'];
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
        foreach ($areas as $a) {
            $areasMap[$a['id']] = $a['name'];
        }

        return $this->html('pages.development.menus.index', [
            'title' => 'Menüs',
            'areaName' => 'Menüs',
            'pageTitle' => 'Übersicht',
            'areaRootLink' => '/',
            'areaNav' => [[ 'label' => 'Start', 'href' => '/', 'active' => false ]],
            'headerNav' => [],
            'menus' => $menus,
            'areasMap' => $areasMap,
            'path' => $request->path,
            'now' => date('c'),
        ]);
    }

    public function menusCreateForm(Request $request): Response
    {
        $areas = $this->areas->findAll();

        return $this->html('pages.development.menus.form', [
            'title' => 'Neues Menü',
            'areas' => $areas,
            'areaName' => 'Menüs',
            'pageTitle' => 'Neues Menü erstellen',
            'areaRootLink' => '/development/web-control/menus',
            'areaNav' => [[ 'label' => 'Menüs', 'href' => '/development/web-control/menus', 'active' => true ]],
            'headerNav' => [],
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
        $isDefault = isset($body['is_default']) ? (int) $body['is_default'] : 0;

        $errors = [];
        if ($name === '') {
            $errors[] = 'Name ist erforderlich.';
        }

        if (!empty($errors)) {
            $areas = $this->areas->findAll();
            return $this->html('pages.development.menus.form', [
                'title' => 'Neues Menü',
                'areas' => $areas,
                'errors' => $errors,
                'menu' => ['name' => $name, 'slug' => $slug, 'area_id' => $areaId, 'is_default' => $isDefault],
                'path' => $request->path,
                'now' => date('c'),
            ]);
        }

        $id = $this->menus->create([
            'area_id' => $areaId,
            'name' => $name,
            'slug' => $slug,
            'is_default' => $isDefault,
        ]);

        if ($id > 0) {
            return new Response(302, ['Location' => '/development/web-control/menus'], '');
        }

        $areas = $this->areas->findAll();
        return $this->html('pages.development.menus.form', [
            'title' => 'Neues Menü',
            'areas' => $areas,
            'errors' => ['Speichern fehlgeschlagen.'],
            'menu' => ['name' => $name, 'slug' => $slug, 'area_id' => $areaId, 'is_default' => $isDefault],
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

        $areas = $this->areas->findAll();

        return $this->html('pages.development.menus.form', [
            'title' => 'Menü bearbeiten',
            'menu' => $menu,
            'areas' => $areas,
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
        if (isset($body['area_id'])) {
            $data['area_id'] = (int) $body['area_id'];
        }
        if (isset($body['is_default'])) {
            $data['is_default'] = (int) $body['is_default'];
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
