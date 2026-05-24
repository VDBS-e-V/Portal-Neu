<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Bootstrap\Container;
use App\Http\Request\Request;
use App\Http\Response\HtmlResponse;
use App\Presentation\Templating\Renderer;

final class HomeController
{
    public function __construct(private array $container) {}

    public function index(Request $request): HtmlResponse
    {
        /** @var Renderer $renderer */
        $renderer = Container::get($this->container, Renderer::class);

        $html = $renderer->renderPage('pages/home/index', [
            'areaName' => 'VDBS Portal',
            'pageTitle' => 'Startseite',
            'areaRootLink' => '/',
            'areaNav' => [
                ['label' => 'Start', 'href' => '/', 'active' => true],
                ['label' => 'Styleguide', 'href' => '/styleguide', 'active' => false],
                ['label' => 'Area 3', 'href' => '/areas/area3', 'active' => false],
            ],
            'headerNav' => [
                ['label' => 'Über das Portal', 'href' => '/ueber-das-portal', 'active' => true],
                ['label' => 'Zugang zum Portal', 'href' => '/zugang-zum-portal', 'active' => false],
                ['label' => 'FAQ', 'href' => '/faq', 'active' => false],
                ['label' => 'Kontakt', 'href' => '/kontakt', 'active' => false],
            ],
            'now' => date('c'),
            'path' => $request->path,
        ]);

        return new HtmlResponse($html);
    }
}