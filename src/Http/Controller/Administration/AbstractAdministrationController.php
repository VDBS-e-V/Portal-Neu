<?php

declare(strict_types=1);

namespace App\Http\Controller\Administration;

use App\Http\Controller\Controller;
use App\Http\Request\Request;
use App\Http\Response\Response;
use App\Presentation\Templating\Renderer;

abstract class AbstractAdministrationController extends Controller
{
    public function __construct(
        protected readonly Renderer $administrationRenderer
    ) {
        parent::__construct($administrationRenderer);
    }

    /**
     * @param array<string,mixed> $parameters
     */
    protected function renderPage(
        Request $request,
        string $view,
        array $parameters = [],
        int $status = 200
    ): Response {
        unset($request);

        return new Response(
            $status,
            ['Content-Type' => 'text/html; charset=utf-8'],
            $this->administrationRenderer->renderPage(
                $this->normalizeView($view),
                $parameters
            )
        );
    }

    protected function text(string $body, int $status = 200): Response
    {
        return new Response(
            $status,
            ['Content-Type' => 'text/plain; charset=utf-8'],
            $body
        );
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

    /**
     * @param array<string,mixed> $body
     */
    protected function bodyString(array $body, string $key, string $default = ''): string
    {
        return trim((string) ($body[$key] ?? $default));
    }

    /**
     * @param array<string,mixed> $body
     */
    protected function bodyNullableString(array $body, string $key): ?string
    {
        $value = $this->bodyString($body, $key);
        return $value === '' ? null : $value;
    }

    /**
     * @param array<string,mixed> $body
     */
    protected function bodyInt(array $body, string $key, int $default = 0): int
    {
        $value = $body[$key] ?? $default;
        if ($value === '' || $value === null) {
            return $default;
        }

        return (int) $value;
    }

    /**
     * @param array<string,mixed> $body
     */
    protected function bodyNullableInt(array $body, string $key): ?int
    {
        $value = $body[$key] ?? null;
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }

    /**
     * @param array<string,mixed> $body
     */
    protected function bodyBool(array $body, string $key): bool
    {
        return isset($body[$key]) && (string) $body[$key] === '1';
    }
}
