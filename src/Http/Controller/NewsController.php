<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Http\Request\Request;
use App\Http\Response\Response;
use App\Repository\AreaRepository;

final class NewsController extends Controller
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

        return $this->html('pages/news/index', [
            'title' => 'News',
            'areaName' => 'VDBS Portal',
            'pageTitle' => 'News',
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

    public function health(Request $request): Response
    {
        return $this->text('ok');
    }

    public function apiHealth(Request $request): Response
    {
        return $this->json(['status' => 'ok']);
    }
}
