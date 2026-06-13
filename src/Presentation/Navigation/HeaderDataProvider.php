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
        private readonly SessionAuth $auth,
    ) {
    }

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

        $currentUser = $this->currentUser();

        return [
            'headerAreas' => $headerAreas,
            'headerMenus' => $headerMenus,
            'isLoggedIn' => $currentUser !== [],
            'currentUser' => $currentUser,
            'csrfToken' => $this->csrfToken(),
        ];
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