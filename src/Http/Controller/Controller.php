<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Http\Request\Request;
use App\Http\Response\HtmlResponse;
use App\Http\Response\JsonResponse;
use App\Http\Response\Response;
use App\Presentation\Templating\Renderer;
use App\Repository\AreaRepository;

abstract class Controller
{
    public function __construct(
        protected Renderer $renderer,
        protected ?AreaRepository $areas = null
    ) {
    }

    protected function page(
        Request $request,
        string $view,
        array $parameters = [],
        int $status = 200
    ): HtmlResponse {
        return $this->html(
            $view,
            array_replace($this->pageDefaults($request), $parameters),
            $status
        );
    }

    protected function html(string $view, array $parameters = [], int $status = 200): HtmlResponse
    {
        return new HtmlResponse(
            $this->renderer->renderPage($this->normalizeView($view), $parameters),
            $status
        );
    }

    protected function json(array $payload, int $status = 200): JsonResponse
    {
        return new JsonResponse($payload, $status);
    }

    protected function text(string $body, int $status = 200): Response
    {
        return new Response($status, ['Content-Type' => 'text/plain; charset=utf-8'], $body);
    }

    protected function redirect(string $location, int $status = 302): Response
    {
        return new Response($status, ['Location' => $location], '');
    }

    protected function pageDefaults(Request $request): array
    {
        return [
            'title' => 'VDBS Portal',
            'areaName' => 'VDBS Portal',
            'pageTitle' => 'VDBS Portal',
            'areaRootLink' => '/',
            'areaNav' => $this->startNav(),
            'headerNav' => [],
            'areas' => $this->areaLinks(),
            'path' => $request->path,
            'now' => date('c'),
        ];
    }

    protected function areaLinks(): array
    {
        if ($this->areas === null) {
            return [];
        }

        $areas = [];

        foreach ($this->areas->findAll() as $area) {
            $areas[] = [
                'name' => (string) ($area['name'] ?? ''),
                'link' => (string) ($area['start_path'] ?? '/'),
            ];
        }

        return $areas;
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

    private function normalizeView(string $view): string
    {
        return str_replace('.', '/', $view);
    }
}