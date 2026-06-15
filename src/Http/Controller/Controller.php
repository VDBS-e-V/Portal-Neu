<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Http\Request\Request;
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

    protected function routeParam(Request $request, string $key, ?string $default = null): ?string
    {
        if (method_exists($request, 'routeParam')) {
            return $request->routeParam($key, $default);
        }

        if (!property_exists($request, 'routeParams')) {
            return $default;
        }

        if (!array_key_exists($key, $request->routeParams)) {
            return $default;
        }

        return (string) $request->routeParams[$key];
    }

    protected function routeInt(Request $request, string $key, int $default = 0): int
    {
        if (method_exists($request, 'routeInt')) {
            return $request->routeInt($key, $default);
        }

        $value = $this->routeParam($request, $key);

        if ($value === null || $value === '') {
            return $default;
        }

        return (int) $value;
    }

    protected function queryString(Request $request, string $key, string $default = ''): string
    {
        return trim((string) ($request->query[$key] ?? $default));
    }

    protected function queryInt(Request $request, string $key, int $default = 0): int
    {
        $value = $request->query[$key] ?? $default;

        if ($value === '' || $value === null) {
            return $default;
        }

        return (int) $value;
    }

    protected function routeOrQueryInt(Request $request, string $key, int $default = 0): int
    {
        $value = $this->routeInt($request, $key, $default);

        if ($value !== $default) {
            return $value;
        }

        return $this->queryInt($request, $key, $default);
    }

    protected function routeOrBodyInt(
        Request $request,
        string $routeKey,
        string $bodyKey,
        int $default = 0
    ): int {
        $value = $this->routeInt($request, $routeKey, $default);

        if ($value !== $default) {
            return $value;
        }

        return $this->bodyInt($request->body, $bodyKey, $default);
    }

    protected function bodyString(array $body, string $key, string $default = ''): string
    {
        return trim((string) ($body[$key] ?? $default));
    }

    protected function bodyNullableString(array $body, string $key): ?string
    {
        $value = $this->bodyString($body, $key);

        return $value === '' ? null : $value;
    }

    protected function bodyInt(array $body, string $key, int $default = 0): int
    {
        $value = $body[$key] ?? $default;

        if ($value === '' || $value === null) {
            return $default;
        }

        return (int) $value;
    }

    protected function bodyNullableInt(array $body, string $key): ?int
    {
        $value = $body[$key] ?? null;

        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }

    protected function bodyBool(array $body, string $key): bool
    {
        return isset($body[$key]) && (string) $body[$key] === '1';
    }
}