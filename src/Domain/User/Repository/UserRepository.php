<?php

declare(strict_types=1);

namespace App\Domain\User\Repository;

use App\Domain\User\Entity\User;

interface UserRepository
{
    /** @return User[] */
    public function all(): array;
}