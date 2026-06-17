<?php

declare(strict_types=1);

namespace App\Security;

use App\Repository\PageGroupAccessRepository;
use App\Repository\PersonPermissionGroupRepository;
use App\Repository\UserRepository;

final class AuthorizationService
{
    public function __construct(
        private readonly SessionAuth $auth,
        private readonly UserRepository $users,
        private readonly PersonPermissionGroupRepository $personGroups,
        private readonly PageGroupAccessRepository $pageGroupAccess
    ) {
    }

    public function currentUserId(): ?int
    {
        return $this->auth->id();
    }

    /** @return array<string, mixed> */
    public function currentUser(): array
    {
        $userId = $this->currentUserId();

        if ($userId === null) {
            return [];
        }

        $user = $this->users->find($userId);

        if ($user === [] || !$this->isActiveUser($user)) {
            return [];
        }

        return $user;
    }

    public function currentPersonId(): ?int
    {
        $userId = $this->currentUserId();

        if ($userId === null) {
            return null;
        }

        return $this->personGroups->personIdForUserId($userId);
    }

    public function isLoggedIn(): bool
    {
        return $this->currentUser() !== [];
    }

    /** @return array<string, mixed> */
    public function requireLogin(): array
    {
        $user = $this->currentUser();

        if ($user === []) {
            throw new AuthorizationException('Bitte zuerst anmelden.', 401);
        }

        return $user;
    }

    public function userHasGroup(int $userId, string $groupKey): bool
    {
        return $this->personGroups->userHasGroup($userId, $groupKey);
    }

    /** @param array<int, string> $groupKeys */
    public function userHasAnyGroup(int $userId, array $groupKeys): bool
    {
        return $this->personGroups->userHasAnyGroup($userId, $groupKeys);
    }

    public function currentUserHasGroup(string $groupKey): bool
    {
        $user = $this->currentUser();

        if ($user === []) {
            return false;
        }

        return $this->userHasGroup((int) $user['id'], $groupKey);
    }

    /** @param array<int, string> $groupKeys */
    public function currentUserHasAnyGroup(array $groupKeys): bool
    {
        $user = $this->currentUser();

        if ($user === []) {
            return false;
        }

        return $this->userHasAnyGroup((int) $user['id'], $groupKeys);
    }

    public function canAccessPageGroup(int $userId, string $areaKey, string $pageGroupKey): bool
    {
        return $this->pageGroupAccess->userHasPageGroupAccess($userId, $areaKey, $pageGroupKey);
    }

    public function currentUserCanAccessPageGroup(string $areaKey, string $pageGroupKey): bool
    {
        $user = $this->currentUser();

        if ($user === []) {
            return false;
        }

        return $this->canAccessPageGroup((int) $user['id'], $areaKey, $pageGroupKey);
    }

    /** @return array<string, mixed> */
    public function requireGroup(string $groupKey): array
    {
        $user = $this->requireLogin();

        if (!$this->userHasGroup((int) $user['id'], $groupKey)) {
            throw new AuthorizationException('Diese Aktion erfordert die Gruppe: ' . $groupKey, 403);
        }

        return $user;
    }

    /**
     * @param array<int, string> $groupKeys
     * @return array<string, mixed>
     */
    public function requireAnyGroup(array $groupKeys): array
    {
        $user = $this->requireLogin();

        if (!$this->userHasAnyGroup((int) $user['id'], $groupKeys)) {
            throw new AuthorizationException('Diese Aktion erfordert eine passende Berechtigungsgruppe.', 403);
        }

        return $user;
    }

    /** @return array<string, mixed> */
    public function requirePageGroupAccess(string $areaKey, string $pageGroupKey): array
    {
        $user = $this->requireLogin();

        if (!$this->canAccessPageGroup((int) $user['id'], $areaKey, $pageGroupKey)) {
            throw new AuthorizationException(
                sprintf('Kein Zugriff auf %s.%s.', $areaKey, $pageGroupKey),
                403
            );
        }

        return $user;
    }

    /** @return array<string, mixed> */
    public function requireVerwaltungAdmin(): array
    {
        return $this->requireGroup(VerwaltungAccess::ADMIN_GROUP);
    }

    /** @param array<string, mixed> $user */
    private function isActiveUser(array $user): bool
    {
        $status = (string) ($user['status'] ?? '');

        return $status === 'active' || $status === '1' || $status === '';
    }
}