<?php

declare(strict_types=1);

namespace App\Http\Response;

final class JsonResponse extends Response
{
    public function __construct(array $data, int $status = 200, array $headers = [])
    {
        parent::__construct(
            status: $status,
            headers: array_merge(['Content-Type' => 'application/json; charset=utf-8'], $headers),
            body: json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '{}'
        );
    }
}