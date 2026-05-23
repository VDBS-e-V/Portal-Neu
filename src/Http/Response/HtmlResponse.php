<?php

declare(strict_types=1);

namespace App\Http\Response;

final class HtmlResponse extends Response
{
    public function __construct(string $html, int $status = 200, array $headers = [])
    {
        parent::__construct(
            status: $status,
            headers: array_merge(['Content-Type' => 'text/html; charset=utf-8'], $headers),
            body: $html
        );
    }
}