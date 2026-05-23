<?php

declare(strict_types=1);

namespace App\Support;

final class Arr
{
    public static function get(array $arr, string $key, mixed $default = null): mixed
    {
        return array_key_exists($key, $arr) ? $arr[$key] : $default;
    }
}