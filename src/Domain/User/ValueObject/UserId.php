<?php

declare(strict_types=1);

namespace App\Domain\User\ValueObject;

final class UserId
{
    public function __construct(public readonly int $value) {}
}