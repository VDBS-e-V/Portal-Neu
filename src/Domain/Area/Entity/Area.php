<?php

declare(strict_types=1);

namespace App\Domain\Area\Entity;

final class Area
{
    public function __construct(
        public readonly int $id,
        public readonly string $uuid,
        public readonly string $name,
        public readonly string $slug,
        public readonly ?string $description,
        public readonly ?string $icon,
        public readonly int $sorting,
        /** @var string[] */
        public readonly array $allowedUserGroups,
        public readonly bool $visibilityHeader,
        public readonly bool $visibilityListing,
        public readonly ?string $createdAt = null,
        public readonly ?string $updatedAt = null,
    ) {}
}
