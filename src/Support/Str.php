<?php

declare(strict_types=1);

namespace App\Support;

final class Str
{
    public static function startsWith(string $s, string $prefix): bool
    {
        return str_starts_with($s, $prefix);
    }
}