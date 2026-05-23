<?php

declare(strict_types=1);

namespace App\Http\Routing;

final class Route
{
    public function __construct(
        public readonly string $method,
        public readonly string $path,
        /** @var array{0: class-string, 1: string} */
        public readonly array $handler
    ) {}
}