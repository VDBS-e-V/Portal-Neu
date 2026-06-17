<?php

declare(strict_types=1);

namespace App\Presentation\Navigation;

use App\Repository\AreaRepository;
use App\Repository\MenuItemRepository;
use App\Repository\MenuRepository;
use App\Repository\UserRepository;
use App\Security\SessionAuth;

final class HeaderDataProvider
{
    public function __construct(
        private readonly AreaRepository $areas,
        private readonly MenuRepository $menus,
        private readonly MenuItemRepository $menuItems,
        private readonly UserRepository $users,
        private readonly SessionAuth $auth
    ) {
    }

    /**
     * @param array<string, mixed> $parameters
     *
     * @return array<string, mixed>
     */
    public function build(array $parameters): array
    {
        $headerAreas = $this->areas->findAll();
        $selectedAreaId = $this->resolveAreaId($parameters, $headerAreas);
        $path = $this->normalizePath((string) ($parameters['path'] ?? ($_SERVER['REQUEST_URI'] ?? '/')));

        $headerMenus = [];

        if ($selectedAreaId !== null && $selectedAreaId > 0) {
            $areaMenu = $this->menus->findForArea($selectedAreaId);

            if (!empty($areaMenu) && isset($areaMenu['id'])) {
                /*
                 * Der Header ist bereits auf parent_id/children vorbereitet.
                 *
                 * Deshalb müssen hier alle aktiven Menüeinträge geladen werden,
                 * nicht nur parent_id IS NULL.
                 *
                 * resources/views/parts/header.php baut daraus den Baum und
                 * rendert:
                 *
                 * - header-bottom-nav-list-item has-submenu
                 * - header-bottom-submenu
                 * - header-bottom-submenu--nested
                 */
                $headerMenus = $this->menuItems->findByMenuId((int) $areaMenu['id'], true);
                $headerMenus = $this->applyActiveState($headerMenus, $path);
            }
        }

        $currentUser = $this->currentUser();

        return [
            'headerAreas' => $this->applyAreaActiveState($headerAreas, $selectedAreaId),
            'headerMenus' => $headerMenus,
            'isLoggedIn' => $currentUser !== [],
            'currentUser' => $currentUser,
            'csrfToken' => $this->csrfToken(),
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $items
     *
     * @return array<int, array<string, mixed>>
     */
    private function applyActiveState(array $items, string $path): array
    {
        $itemsById = [];
        $indexById = [];
        $activeIds = [];

        foreach ($items as $index => $item) {
            if (!is_array($item) || !isset($item['id'])) {
                continue;
            }

            $id = (int) $item['id'];
            $itemsById[$id] = $item;
            $indexById[$id] = $index;

            $url = $this->normalizePath((string) ($item['url'] ?? ''));

            if ($url !== '' && $this->pathMatches($path, $url)) {
                $activeIds[$id] = true;
            }
        }

        foreach (array_keys($activeIds) as $activeId) {
            $currentId = (int) $activeId;

            while (isset($itemsById[$currentId])) {
                $activeIds[$currentId] = true;

                $parentId = (int) ($itemsById[$currentId]['parent_id'] ?? 0);

                if ($parentId <= 0 || $parentId === $currentId) {
                    break;
                }

                $currentId = $parentId;
            }
        }

        foreach ($activeIds as $id => $_) {
            if (!isset($indexById[$id])) {
                continue;
            }

            $items[$indexById[$id]]['active'] = true;
            $items[$indexById[$id]]['current'] = true;
            $items[$indexById[$id]]['is_current'] = true;
        }

        return $items;
    }

    /**
     * @param array<int, array<string, mixed>> $areas
     *
     * @return array<int, array<string, mixed>>
     */
    private function applyAreaActiveState(array $areas, ?int $selectedAreaId): array
    {
        if ($selectedAreaId === null || $selectedAreaId <= 0) {
            return $areas;
        }

        foreach ($areas as $index => $area) {
            if (!is_array($area) || !isset($area['id'])) {
                continue;
            }

            if ((int) $area['id'] === $selectedAreaId) {
                $areas[$index]['active'] = true;
                $areas[$index]['current'] = true;
                $areas[$index]['is_current'] = true;
            }
        }

        return $areas;
    }

    private function currentUser(): array
    {
        $userId = $this->currentUserId();

        if ($userId === null || $userId <= 0) {
            return [];
        }

        $user = $this->users->headerProfileForUser($userId);

        if ($user === [] || ($user['status'] ?? '') !== 'active') {
            return [];
        }

        return $user;
    }

    private function currentUserId(): ?int
    {
        if (method_exists($this->auth, 'id')) {
            $id = $this->auth->id();

            return $id === null ? null : (int) $id;
        }

        if (method_exists($this->auth, 'userId')) {
            $id = $this->auth->userId();

            return $id === null ? null : (int) $id;
        }

        return null;
    }

    private function csrfToken(): string
    {
        if (method_exists($this->auth, 'csrfToken')) {
            return (string) $this->auth->csrfToken();
        }

        if (method_exists($this->auth, 'token')) {
            return (string) $this->auth->token();
        }

        return '';
    }

    private function normalizePath(string $path): string
    {
        $path = trim($path);

        if ($path === '') {
            return '/';
        }

        $questionMarkPosition = strpos($path, '?');

        if ($questionMarkPosition !== false) {
            $path = substr($path, 0, $questionMarkPosition);
        }

        $path = '/' . ltrim($path, '/');
        $path = rtrim($path, '/');

        return $path === '' ? '/' : $path;
    }

    private function pathMatches(string $currentPath, string $itemPath): bool
    {
        if ($itemPath === '/') {
            return $currentPath === '/';
        }

        return $currentPath === $itemPath
            || str_starts_with($currentPath, $itemPath . '/');
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

        $path = $this->normalizePath((string) ($parameters['path'] ?? ($_SERVER['REQUEST_URI'] ?? '/')));

        $bestMatch = null;
        $bestLength = -1;

        foreach ($headerAreas as $area) {
            if (!isset($area['id'])) {
                continue;
            }

            $startPath = $this->normalizePath((string) ($area['start_path'] ?? '/'));

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
