<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Http\Request\Request;
use App\Http\Response\Response;

final class HealthController extends ApiController
{
    public function health(Request $request): Response
    {
        return $this->text('ok');
    }

    public function apiHealth(Request $request): Response
    {
        return $this->json(['status' => 'ok']);
    }
}