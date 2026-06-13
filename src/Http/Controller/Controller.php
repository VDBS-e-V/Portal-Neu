<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Http\Response\HtmlResponse;
use App\Http\Response\JsonResponse;
use App\Http\Response\Response;
use App\Presentation\Templating\Renderer;

abstract class Controller
{
    public function __construct(
        protected Renderer $renderer
    ) {
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

    protected function normalizeView(string $view): string
    {
        return str_replace('.', '/', $view);
    }
}