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
            'title' => 'VDBS Portal',
            'now' => date('c'),
            'path' => $request->path,
        ]);

        return new HtmlResponse($html);
    }
}