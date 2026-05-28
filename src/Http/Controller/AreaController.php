<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Http\Request\Request;
use App\Http\Response\Response;
use App\Presentation\Templating\Renderer;
use App\Repository\AreaRepository;

final class AreaController extends Controller
{
    public function __construct(Renderer $renderer, private AreaRepository $areas)
    {
        parent::__construct($renderer);
    }

    public function index(Request $request): Response
    {
        $areas = $this->areas->findAll();

        return $this->html('pages.areas.index', [
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

    public function createForm(Request $request): Response
    {
        return $this->html('pages.areas.form', [
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

    public function create(Request $request): Response
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
            return $this->html('pages.areas.form', [
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

        return $this->html('pages.areas.form', [
            'title' => 'Neuen Bereich',
            'errors' => ['Speichern fehlgeschlagen.'],
            'area' => ['name' => $name, 'slug' => $slug, 'description' => $description, 'is_public' => $isPublic],
            'path' => $request->path,
            'now' => date('c'),
        ]);
    }

    public function editForm(Request $request): Response
    {
        $id = isset($request->query['id']) ? (int) $request->query['id'] : 0;
        if ($id <= 0) {
            return new Response(302, ['Location' => '/development/web-control/areas'], '');
        }

        $area = $this->areas->find($id);

        if (empty($area)) {
            return new Response(302, ['Location' => '/development/web-control/areas'], '');
        }

        return $this->html('pages.areas.form', [
            'title' => 'Bereich bearbeiten',
            'area' => $area,
            'path' => $request->path,
            'now' => date('c'),
        ]);
    }

    public function edit(Request $request): Response
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

    public function delete(Request $request): Response
    {
        $body = $request->body;
        $id = isset($body['id']) ? (int) $body['id'] : 0;
        if ($id <= 0) {
            return new Response(302, ['Location' => '/development/web-control/areas'], '');
        }

        $ok = $this->areas->delete($id);

        return new Response($ok ? 302 : 500, ['Location' => '/development/web-control/areas'], '');
    }

    public function select(Request $request): Response
    {
        $id = isset($request->query['area_id']) ? (int) $request->query['area_id'] : 0;
        if ($id > 0) {
            if (session_status() === PHP_SESSION_NONE) {
                @session_start();
            }
            $_SESSION['selected_area_id'] = $id;
        }

        $referer = $request->server['HTTP_REFERER'] ?? '/development/web-control/areas';
        return new Response(302, ['Location' => $referer], '');
    }
}
