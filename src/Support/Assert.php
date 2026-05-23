<?php

declare(strict_types=1);

namespace App\Support;

final class Assert
{
    public static function notEmpty(string $value, string $message = 'Value must not be empty'): void
    {
        if (trim($value) === '') {
            throw new \InvalidArgumentException($message);
        }
    }
}