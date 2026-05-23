<?php

declare(strict_types=1);

namespace App\Http\Request;

final class Validator
{
    public static function requireString(array $input, string $key): string
    {
        $v = $input[$key] ?? null;
        if (!is_string($v) || trim($v) === '') {
            throw new \InvalidArgumentException("Missing or invalid field: {$key}");
        }
        return $v;
    }
}