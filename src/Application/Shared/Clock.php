<?php

declare(strict_types=1);

namespace App\Application\Shared;

interface Clock
{
    public function now(): \DateTimeImmutable;
}