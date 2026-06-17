<?php

declare(strict_types=1);

namespace App\Navigation;

use App\Repository\AuthorizedMenuRepository;

final class AuthorizedMainMenu
{
    public function __construct(private readonly AuthorizedMenuRepository $menus)
    {
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function portalMain(): array
    {
        return $this->menus->tree('portal.main');
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function verwaltungMain(): array
    {
        return $this->menus->tree('verwaltung.main');
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function bySlug(string $menuSlug): array
    {
        return $this->menus->tree($menuSlug);
    }
}
