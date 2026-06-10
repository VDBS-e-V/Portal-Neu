<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Http\Request\Request;
use App\Http\Response\Response;
use App\Repository\AreaRepository;

final class StyleGuideController extends Controller
{

    public function __construct(
        \App\Presentation\Templating\Renderer $renderer,
        private AreaRepository $areas
    ) {
        parent::__construct($renderer);
    }

    public function index(Request $request): Response
    {
        $areas = [];
        foreach ($this->areas->findAll() as $area) {
            $areas[] = [
                'name' => (string) ($area['name'] ?? ''),
                'link' => (string) ($area['start_path'] ?? '/'),
            ];
        }

        return $this->html('pages/style-guide/index', [
            'title' => 'Übersicht',
            'areaName' => 'Style Guide',
            'pageTitle' => 'Übersicht',
            'areaRootLink' => '/',
            'areaNav' => [[
                'label' => 'Start',
                'href' => '/',
                'active' => true,
            ]],
            'headerNav' => [],
            'areas' => $areas,
            'path' => $request->path,
            'now' => date('c'),
        ]);
    }

    public function buttons(Request $request): Response
    {
        $areas = [];
        foreach ($this->areas->findAll() as $area) {
            $areas[] = [
                'name' => (string) ($area['name'] ?? ''),
                'link' => (string) ($area['start_path'] ?? '/'),
            ];
        }

        return $this->html('pages/style-guide/buttons', [
            'title' => 'Buttons',
            'areaName' => 'Style Guide',
            'pageTitle' => 'Buttons',
            'areaRootLink' => '/',
            'areaNav' => [[
                'label' => 'Start',
                'href' => '/',
                'active' => true,
            ]],
            'headerNav' => [],
            'areas' => $areas,
            'path' => $request->path,
            'now' => date('c'),
        ]);
    }

    public function buttonGenerator(Request $request): Response
    {
        $areas = [];
        foreach ($this->areas->findAll() as $area) {
            $areas[] = [
                'name' => (string) ($area['name'] ?? ''),
                'link' => (string) ($area['start_path'] ?? '/'),
            ];
        }

        return $this->html('pages/style-guide/buttons-generator', [
            'title' => 'Buttons Generator',
            'areaName' => 'Style Guide',
            'pageTitle' => 'Buttons Generator',
            'areaRootLink' => '/',
            'areaNav' => [[
                'label' => 'Start',
                'href' => '/',
                'active' => true,
            ]],
            'headerNav' => [],
            'areas' => $areas,
            'path' => $request->path,
            'now' => date('c'),
        ]);
    }

    public function icons(Request $request): Response
    {
        $areas = [];
        foreach ($this->areas->findAll() as $area) {
            $areas[] = [
                'name' => (string) ($area['name'] ?? ''),
                'link' => (string) ($area['start_path'] ?? '/'),
            ];
        }

        return $this->html('pages/style-guide/icons', [
            'title' => 'Icons',
            'areaName' => 'Style Guide',
            'pageTitle' => 'Icons',
            'areaRootLink' => '/',
            'areaNav' => [[
                'label' => 'Start',
                'href' => '/',
                'active' => true,
            ]],
            'headerNav' => [],
            'areas' => $areas,
            'path' => $request->path,
            'now' => date('c'),
        ]);
    }

    public function iconsGenerator(Request $request): Response
    {
        $areas = [];
        foreach ($this->areas->findAll() as $area) {
            $areas[] = [
                'name' => (string) ($area['name'] ?? ''),
                'link' => (string) ($area['start_path'] ?? '/'),
            ];
        }

        return $this->html('pages/style-guide/icons-generator', [
            'title' => 'Icons Generator',
            'areaName' => 'Style Guide',
            'pageTitle' => 'Icons Generator',
            'areaRootLink' => '/',
            'areaNav' => [[
                'label' => 'Start',
                'href' => '/',
                'active' => true,
            ]],
            'headerNav' => [],
            'areas' => $areas,
            'path' => $request->path,
            'now' => date('c'),
        ]);
    }

    public function containers(Request $request): Response
    {
        $areas = [];
        foreach ($this->areas->findAll() as $area) {
            $areas[] = [
                'name' => (string) ($area['name'] ?? ''),
                'link' => (string) ($area['start_path'] ?? '/'),
            ];
        }

        return $this->html('pages/style-guide/containers', [
            'title' => 'Containers',
            'areaName' => 'Style Guide',
            'pageTitle' => 'Containers',
            'areaRootLink' => '/',
            'areaNav' => [[
                'label' => 'Start',
                'href' => '/',
                'active' => true,
            ]],
            'headerNav' => [],
            'areas' => $areas,
            'path' => $request->path,
            'now' => date('c'),
        ]);
    }

    public function forms(Request $request): Response
    {
        $areas = [];
        foreach ($this->areas->findAll() as $area) {
            $areas[] = [
                'name' => (string) ($area['name'] ?? ''),
                'link' => (string) ($area['start_path'] ?? '/'),
            ];
        }

        return $this->html('pages/style-guide/forms', [
            'title' => 'Forms',
            'areaName' => 'Style Guide',
            'pageTitle' => 'Forms',
            'areaRootLink' => '/',
            'areaNav' => [[
                'label' => 'Start',
                'href' => '/',
                'active' => true,
            ]],
            'headerNav' => [],
            'areas' => $areas,
            'path' => $request->path,
            'now' => date('c'),
        ]);
    }

    public function grids(Request $request): Response
    {
        $areas = [];
        foreach ($this->areas->findAll() as $area) {
            $areas[] = [
                'name' => (string) ($area['name'] ?? ''),
                'link' => (string) ($area['start_path'] ?? '/'),
            ];
        }

        return $this->html('pages/style-guide/grids', [
            'title' => 'Grids',
            'areaName' => 'Style Guide',
            'pageTitle' => 'Grids',
            'areaRootLink' => '/',
            'areaNav' => [[
                'label' => 'Start',
                'href' => '/',
                'active' => true,
            ]],
            'headerNav' => [],
            'areas' => $areas,
            'path' => $request->path,
            'now' => date('c'),
        ]);
    }

    public function heros(Request $request): Response
    {
        $areas = [];
        foreach ($this->areas->findAll() as $area) {
            $areas[] = [
                'name' => (string) ($area['name'] ?? ''),
                'link' => (string) ($area['start_path'] ?? '/'),
            ];
        }

        return $this->html('pages/style-guide/heros', [
            'title' => 'Heros',
            'areaName' => 'Style Guide',
            'pageTitle' => 'Heros',
            'areaRootLink' => '/',
            'areaNav' => [[
                'label' => 'Start',
                'href' => '/',
                'active' => true,
            ]],
            'headerNav' => [],
            'areas' => $areas,
            'path' => $request->path,
            'now' => date('c'),
        ]);
    }

    public function links(Request $request): Response
    {
        $areas = [];
        foreach ($this->areas->findAll() as $area) {
            $areas[] = [
                'name' => (string) ($area['name'] ?? ''),
                'link' => (string) ($area['start_path'] ?? '/'),
            ];
        }

        return $this->html('pages/style-guide/links', [
            'title' => 'Links',
            'areaName' => 'Style Guide',
            'pageTitle' => 'Links',
            'areaRootLink' => '/',
            'areaNav' => [[
                'label' => 'Start',
                'href' => '/',
                'active' => true,
            ]],
            'headerNav' => [],
            'areas' => $areas,
            'path' => $request->path,
            'now' => date('c'),
        ]);
    }

    public function media(Request $request): Response
    {
        $areas = [];
        foreach ($this->areas->findAll() as $area) {
            $areas[] = [
                'name' => (string) ($area['name'] ?? ''),
                'link' => (string) ($area['start_path'] ?? '/'),
            ];
        }

        return $this->html('pages/style-guide/media', [
            'title' => 'Media',
            'areaName' => 'Style Guide',
            'pageTitle' => 'Media',
            'areaRootLink' => '/',
            'areaNav' => [[
                'label' => 'Start',
                'href' => '/',
                'active' => true,
            ]],
            'headerNav' => [],
            'areas' => $areas,
            'path' => $request->path,
            'now' => date('c'),
        ]);
    }

    public function popovers(Request $request): Response
    {
        $areas = [];
        foreach ($this->areas->findAll() as $area) {
            $areas[] = [
                'name' => (string) ($area['name'] ?? ''),
                'link' => (string) ($area['start_path'] ?? '/'),
            ];
        }

        return $this->html('pages/style-guide/popovers', [
            'title' => 'Popovers',
            'areaName' => 'Style Guide',
            'pageTitle' => 'Popovers',
            'areaRootLink' => '/',
            'areaNav' => [[
                'label' => 'Start',
                'href' => '/',
                'active' => true,
            ]],
            'headerNav' => [],
            'areas' => $areas,
            'path' => $request->path,
            'now' => date('c'),
        ]);
    }

    public function summaries(Request $request): Response
    {
        $areas = [];
        foreach ($this->areas->findAll() as $area) {
            $areas[] = [
                'name' => (string) ($area['name'] ?? ''),
                'link' => (string) ($area['start_path'] ?? '/'),
            ];
        }

        return $this->html('pages/style-guide/summaries', [
            'title' => 'Summaries',
            'areaName' => 'Style Guide',
            'pageTitle' => 'Summaries',
            'areaRootLink' => '/',
            'areaNav' => [[
                'label' => 'Start',
                'href' => '/',
                'active' => true,
            ]],
            'headerNav' => [],
            'areas' => $areas,
            'path' => $request->path,
            'now' => date('c'),
        ]);
    }

    public function tables(Request $request): Response
    {
        $areas = [];
        foreach ($this->areas->findAll() as $area) {
            $areas[] = [
                'name' => (string) ($area['name'] ?? ''),
                'link' => (string) ($area['start_path'] ?? '/'),
            ];
        }

        return $this->html('pages/style-guide/tables', [
            'title' => 'Tables',
            'areaName' => 'Style Guide',
            'pageTitle' => 'Tables',
            'areaRootLink' => '/',
            'areaNav' => [[
                'label' => 'Start',
                'href' => '/',
                'active' => true,
            ]],
            'headerNav' => [],
            'areas' => $areas,
            'path' => $request->path,
            'now' => date('c'),
        ]);
    }
}