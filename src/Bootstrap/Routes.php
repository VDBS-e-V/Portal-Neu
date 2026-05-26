<?php

declare(strict_types=1);

namespace App\Bootstrap;

final class Routes
{
    /**
     * @return array<int, object>
     */
    public static function load(): array
    {
        return require dirname(__DIR__, 2) . '/config/routes.php';
    }
}