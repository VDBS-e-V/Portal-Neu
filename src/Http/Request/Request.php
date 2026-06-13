<?php

declare(strict_types=1);

namespace App\Http\Request;

final class Request
{
    /**
     * @param array<string, mixed> $query
     * @param array<string, mixed> $body
     * @param array<string, mixed> $server
     * @param array<string, string> $routeParams
     */
    public function __construct(
        public readonly string $method,
        public readonly string $path,
        public readonly array $query = [],
        public readonly array $body = [],
        public readonly array $server = [],
        public readonly array $routeParams = []
    ) {
    }

    public static function fromGlobals(string $baseUrl = ''): self
    {
        $method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
        $uri = (string) ($_SERVER['REQUEST_URI'] ?? '/');

        $path = parse_url($uri, PHP_URL_PATH);
        $path = is_string($path) && $path !== '' ? $path : '/';

        $baseUrl = rtrim($baseUrl, '/');

        if ($baseUrl !== '' && str_starts_with($path, $baseUrl)) {
            $path = substr($path, strlen($baseUrl));
            $path = $path === '' ? '/' : $path;
        }

        return new self(
            $method,
            $path,
            $_GET ?? [],
            $_POST ?? [],
            $_SERVER ?? []
        );
    }

    /**
     * @param array<string, string> $routeParams
     */
    public function withRouteParams(array $routeParams): self
    {
        return new self(
            $this->method,
            $this->path,
            $this->query,
            $this->body,
            $this->server,
            $routeParams
        );
    }

    public function routeParam(string $key, ?string $default = null): ?string
    {
        if (!array_key_exists($key, $this->routeParams)) {
            return $default;
        }

        return (string) $this->routeParams[$key];
    }

    public function routeInt(string $key, int $default = 0): int
    {
        $value = $this->routeParam($key);

        if ($value === null || $value === '') {
            return $default;
        }

        return (int) $value;
    }

    public function isApi(): bool
    {
        return str_starts_with($this->path, '/api');
    }
}