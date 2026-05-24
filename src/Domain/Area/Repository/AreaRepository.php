<?php

declare(strict_types=1);

namespace App\Domain\Area\Repository;

use App\Domain\Area\Entity\Area;
use App\Domain\User\Entity\User;

interface AreaRepository
{
    /** @return Area[] */
    public function all(): array;

    /** @return Area[] */
    public function findAllOrdered(): array;

    /** @return Area[] */
    public function findVisibleForUser(User $user): array;
}
