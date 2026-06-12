<?php

declare(strict_types=1);

namespace App\Presentation\Navigation;

use App\Repository\AreaRepository;
use App\Repository\MenuItemRepository;
use App\Repository\MenuRepository;

final class HeaderDataProvider
{
    public function __construct(
        private AreaRepository $areas,
        private MenuRepository $menus,
        private MenuItemRepository $menuItems
    ) {
    }

    /**
     * @param array<string, mixed> $parameters
     * @return array{headerAreas: array<int, array<string, mixed>>, headerMenus: array<int, array<string, mixed>>}
     */
    public function build(array $parameters): array
    {
        $headerAreas = $this->areas->findAll();
        $selectedAreaId = $this->resolveAreaId($parameters, $headerAreas);

        $headerMenus = [];
        if ($selectedAreaId !== null && $selectedAreaId > 0) {
            $areaMenu = $this->menus->findForArea($selectedAreaId);
            if (!empty($areaMenu) && isset($areaMenu['id'])) {
                $headerMenus = $this->menuItems->findByMenuIdAndParent((int) $areaMenu['id'], null);
            }
        }

        return [
            'headerAreas' => $headerAreas,
            'headerMenus' => $headerMenus,
        ];
    }

    /**
     * @param array<string, mixed> $parameters
     * @param array<int, array<string, mixed>> $headerAreas
     */
    private function resolveAreaId(array $parameters, array $headerAreas): ?int
    {
        if (isset($parameters['area']) && is_array($parameters['area']) && isset($parameters['area']['id'])) {
            return (int) $parameters['area']['id'];
        }

        if (isset($parameters['area_id'])) {
            return (int) $parameters['area_id'];
        }

        if (isset($parameters['areaId'])) {
            return (int) $parameters['areaId'];
        }

        $path = (string) ($parameters['path'] ?? '/');
        $bestMatch = null;
        $bestLength = -1;

        foreach ($headerAreas as $area) {
            if (!isset($area['id'])) {
                continue;
            }

            $startPath = trim((string) ($area['start_path'] ?? '/'));
            if ($startPath === '') {
                $startPath = '/';
            }

            $matches = $startPath === '/'
                ? true
                : $path === $startPath || str_starts_with($path, rtrim($startPath, '/') . '/');

            if (!$matches) {
                continue;
            }

            $length = strlen($startPath);
            if ($length > $bestLength) {
                $bestLength = $length;
                $bestMatch = (int) $area['id'];
            }
        }

        if ($bestMatch !== null) {
            return $bestMatch;
        }

        if (!empty($headerAreas)) {
            $firstArea = $headerAreas[0] ?? null;
            if (is_array($firstArea) && isset($firstArea['id'])) {
                return (int) $firstArea['id'];
            }
        }

        return null;
    }
}
