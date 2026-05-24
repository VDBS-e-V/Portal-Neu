<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Bootstrap\Container;
use App\Http\Request\Request;
use App\Http\Response\HtmlResponse;
use App\Presentation\Templating\Renderer;

final class StyleGuideController
{
    public function __construct(private array $container) {}

    public function index(Request $request): HtmlResponse
    {
        /** @var Renderer $renderer */
        $renderer = Container::get($this->container, Renderer::class);

        $html = $renderer->renderPage('pages/styleguide/index', [
            'areaName' => 'Styleguide',
            'pageTitle' => 'Übersicht',
            'areaRootLink' => '/styleguide',
            'areaNav' => [
                ['label' => 'Start', 'href' => '/', 'active' => false],
                ['label' => 'Styleguide', 'href' => '/styleguide', 'active' => true],
                ['label' => 'Area 3', 'href' => '/areas/area3', 'active' => false],
            ],
            'headerNav' => [
                ['label' => 'Übersicht', 'href' => '/styleguide', 'active' => true],
                ['label' => 'Kacheln', 'href' => '/styleguide/cards', 'active' => false],
            ],
        ]);

        return new HtmlResponse($html);
    }

    public function cards(Request $request): HtmlResponse
    {
        /** @var Renderer $renderer */
        $renderer = Container::get($this->container, Renderer::class);

        $html = $renderer->renderPage('pages/styleguide/cards', [
            'areaName' => 'Styleguide',
            'pageTitle' => 'Kacheln',
            'areaRootLink' => '/styleguide',
            'areaNav' => [
                ['label' => 'Start', 'href' => '/', 'active' => false],
                ['label' => 'Styleguide', 'href' => '/styleguide', 'active' => true],
                ['label' => 'Area 3', 'href' => '/areas/area3', 'active' => false],
            ],
            'headerNav' => [
                ['label' => 'Übersicht', 'href' => '/styleguide', 'active' => false],
                ['label' => 'Kacheln', 'href' => '/styleguide/cards', 'active' => true],
            ],
        ]);

        return new HtmlResponse($html);
    }
}