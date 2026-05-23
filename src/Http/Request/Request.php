<?php

declare(strict_types=1);

namespace App\Http\Request;

final class Request
{
    public function __construct(
        public readonly string $method,
        public readonly string $path,
        public readonly array $query,
        public readonly array $post,
        public readonly array $server,
    ) {}

    public static function fromGlobals(): self
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';

        // optional: wenn du unter /vdbs_portal/public läufst, ist path evtl. /vdbs_portal/public/...
        // Das bereinigst du sauber über APP_BASE_URL oder Webserver-Config. Für jetzt: so lassen.

        return new self(
            method: $method,
            path: $path,
            query: $_GET ?? [],
            post: $_POST ?? [],
            server: $_SERVER ?? [],
        );
    }
}