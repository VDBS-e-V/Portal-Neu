<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Http\Response\JsonResponse;

abstract class ApiController extends Controller
{
    protected function success(array $data = [], int $status = 200, array $meta = []): JsonResponse
    {
        return $this->json(array_merge([
            'ok' => true,
            'data' => $data,
        ], $meta), $status);
    }

    protected function error(string $message, int $status = 400, array $meta = []): JsonResponse
    {
        return $this->json(array_merge([
            'ok' => false,
            'error' => $message,
        ], $meta), $status);
    }
}