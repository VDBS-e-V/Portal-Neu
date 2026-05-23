<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\PDO;

use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepository;

final class UserPdoRepository implements UserRepository
{
    public function __construct(private \PDO $pdo) {}

    public function all(): array
    {
        // Dummy (später SQL)
        return [new User(1, 'Demo User')];
    }
}