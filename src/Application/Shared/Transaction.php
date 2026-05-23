<?php

declare(strict_types=1);

namespace App\Application\Shared;

interface Transaction
{
    public function run(callable $fn): mixed;
}