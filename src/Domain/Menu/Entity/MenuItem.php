<?php

declare(strict_types=1);

namespace App\Domain\Menu\Entity;

final class MenuItem
{
    public function __construct(
        public readonly int $id,
        public readonly string $uuid,
        public readonly int $menuId,
        public readonly ?int $parentId,
        public readonly string $title,
        public readonly ?string $slug,
        public readonly ?string $href,
        /** @var array<string,mixed> */
        public readonly array $attributes,
        public readonly int $sorting,
        public readonly bool $visible,
        public readonly ?string $createdAt = null,
        public readonly ?string $updatedAt = null,
    ) {}
}
