<?php

declare(strict_types=1);

namespace App\Domain\Menu\Repository;

use App\Domain\Menu\Entity\Menu;
use App\Domain\Menu\Entity\MenuItem;

interface MenuRepository
{
    /** @return Menu[] */
    public function all(): array;

    public function findByAreaId(int $areaId): ?Menu;

    /** @return MenuItem[] */
    public function findItemsByMenuId(int $menuId): array;

    /**
     * Returns a nested tree of menu items as arrays:
     * [ ['item' => MenuItem, 'children' => [...]], ... ]
     *
     * @return array
     */
    public function findItemsTreeByMenuId(int $menuId): array;
}
