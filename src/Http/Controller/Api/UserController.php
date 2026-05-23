<?php

declare(strict_types=1);

namespace App\Http\Controller\Api;

use App\Http\Request\Request;
use App\Http\Response\JsonResponse;

final class UserController
{
    public function __construct(private array $container) {}

    public function list(Request $request): JsonResponse
    {
        // Dummy – später: Application Query Handler + Repository
        return new JsonResponse([
            'ok' => true,
            'data' => [
                ['id' => 1, 'name' => 'Demo User'],
            ],
        ]);
    }
}