<?php

declare(strict_types=1);

namespace App\Security;

use App\Repository\IdentityAuthorizationRepository;
use App\Repository\UserRepository;

final class AuthorizationService
{
    private SessionAuth $auth;
    private UserRepository $users;
    private IdentityAuthorizationRepository $identity;

    /** @var array<int,array<string,mixed>> */
    private array $currentUserCache = [];

    public function __construct(
        SessionAuth $auth,
        UserRepository $users,
        IdentityAuthorizationRepository $identity
    ) {
        $this->auth = $auth;
        $this->users = $users;
        $this->identity = $identity;
    }

    public function currentUserId(): ?int
    {
        return $this->auth->id();
    }

    /** @return array<string,mixed> */
    public function currentUser(): array
    {
        $userId = $this->currentUserId();
        if ($userId === null) {
            return [];
        }

        if (isset($this->currentUserCache[$userId])) {
            return $this->currentUserCache[$userId];
        }

        $user = $this->users->find($userId);
        if ($user === [] || !$this->isActiveUser($user)) {
            return [];
        }

        $this->currentUserCache[$userId] = $user;
        return $user;
    }

    public function currentSubjectId(): ?int
    {
        $userId = $this->currentUserId();
        if ($userId === null) {
            return null;
        }

        return $this->identity->subjectIdForUserId($userId);
    }

    public function isLoggedIn(): bool
    {
        return $this->currentUser() !== [] && $this->currentSubjectId() !== null;
    }

    /** @return array<string,mixed> */
    public function requireLogin(): array
    {
        $user = $this->currentUser();
        if ($user === [] || $this->currentSubjectId() === null) {
            throw new AuthorizationException('Bitte zuerst anmelden.', 401);
        }

        return $user;
    }

    public function can(string $permissionKey): bool
    {
        $subjectId = $this->currentSubjectId();
        if ($subjectId === null) {
            return false;
        }

        return $this->identity->canSubject($subjectId, $permissionKey);
    }

    /** @param string[] $permissionKeys */
    public function hasAny(array $permissionKeys): bool
    {
        foreach ($permissionKeys as $permissionKey) {
            if ($this->can((string) $permissionKey)) {
                return true;
            }
        }

        return false;
    }

    /** @param string[] $permissionKeys */
    public function hasAll(array $permissionKeys): bool
    {
        foreach ($permissionKeys as $permissionKey) {
            if (!$this->can((string) $permissionKey)) {
                return false;
            }
        }

        return true;
    }

    /** @return array<string,mixed> */
    public function requirePermission(string $permissionKey): array
    {
        $user = $this->requireLogin();

        if (!$this->can($permissionKey)) {
            throw new AuthorizationException('Kein Zugriff: ' . $permissionKey, 403);
        }

        return $user;
    }

    /**
     * @param string[] $permissionKeys
     * @return array<string,mixed>
     */
    public function requireAny(array $permissionKeys): array
    {
        $user = $this->requireLogin();

        if (!$this->hasAny($permissionKeys)) {
            throw new AuthorizationException('Kein Zugriff: passende Permission fehlt.', 403);
        }

        return $user;
    }

    /**
     * @param string[] $permissionKeys
     * @return array<string,mixed>
     */
    public function requireAll(array $permissionKeys): array
    {
        $user = $this->requireLogin();

        if (!$this->hasAll($permissionKeys)) {
            throw new AuthorizationException('Kein Zugriff: erforderliche Permissions fehlen.', 403);
        }

        return $user;
    }

    /** @return string[] */
    public function permissionsForCurrentSubject(): array
    {
        $subjectId = $this->currentSubjectId();
        if ($subjectId === null) {
            return [];
        }

        return $this->identity->permissionsForSubject($subjectId);
    }

    /** @return string[] */
    public function permissionsForSystem(string $systemKey): array
    {
        $subjectId = $this->currentSubjectId();
        if ($subjectId === null) {
            return [];
        }

        return $this->identity->permissionsForSubjectAndSystem($subjectId, $systemKey);
    }

    /** @return array<string,string[]> */
    public function groupsForCurrentSubject(): array
    {
        $subjectId = $this->currentSubjectId();
        if ($subjectId === null) {
            return [];
        }

        return $this->identity->groupsForSubject($subjectId);
    }

    /** @return string[] */
    public function groupsForSystem(string $systemKey): array
    {
        $subjectId = $this->currentSubjectId();
        if ($subjectId === null) {
            return [];
        }

        return $this->identity->groupsForSubjectAndSystem($subjectId, $systemKey);
    }

    public function currentPersonId(): ?int
    {
        $user = $this->currentUser();
        if ($user === []) {
            return null;
        }

        $personId = $user['person_id'] ?? null;
        if ($personId === null || $personId === '') {
            return null;
        }

        return (int) $personId;
    }

    /**
     * Temporäre Kompatibilitätsbrücke für alte Verwaltung-Controller.
     *
     * Neue Implementierungen müssen requirePermission() verwenden. Diese Methode
     * verhindert aber, dass noch aktive Legacy-Routen mit einem Fatal Error
     * abbrechen, solange die alten Controller schrittweise entfernt werden.
     *
     * @return array<string,mixed>
     */
    private function legacyPageGroupPermissionKey(string $areaKey, string $pageGroupKey): string
    {
        $areaKey = $this->normalizeLegacyKey($areaKey);
        $pageGroupKey = $this->normalizeLegacyKey($pageGroupKey);
        $compound = $areaKey . '.' . $pageGroupKey;

        $map = [
            'verwaltung.dashboard' => 'portal.verwaltung.dashboard.view',
            'verwaltung.personen' => 'portal.verwaltung.personen.view',
            'verwaltung.gruppen' => 'identity.gruppen.view',
            'verwaltung.berechtigungen' => 'identity.permissions.view',
            'verwaltung.permissions' => 'identity.permissions.view',
            'verwaltung.audit' => 'portal.verwaltung.audit.view',
            'verwaltung.entity-audit' => 'portal.verwaltung.entity-audit.view',
            'verwaltung.datenschutz' => 'portal.verwaltung.datenschutz.view',
            'verwaltung.einladungen' => 'portal.verwaltung.einladungen.view',
            'verwaltung.schulverzeichnis' => 'portal.verwaltung.schulverzeichnis.view',
            'verwaltung.schulen' => 'portal.verwaltung.schulverzeichnis.view',
        ];

        if (isset($map[$compound])) {
            return $map[$compound];
        }

        if ($areaKey === 'verwaltung') {
            return 'portal.verwaltung.' . $pageGroupKey . '.view';
        }

        if ($areaKey === 'portal') {
            return 'portal.' . $pageGroupKey . '.view';
        }

        return $areaKey . '.' . $pageGroupKey . '.view';
    }

    private function normalizeLegacyKey(string $key): string
    {
        $key = strtolower(trim($key));
        $key = str_replace('_', '-', $key);
        $key = preg_replace('/[^a-z0-9.-]+/', '-', $key) ?? $key;
        $key = preg_replace('/-+/', '-', $key) ?? $key;
        return trim($key, '-.');
    }
    /** @param array<string,mixed> $user */
    private function isActiveUser(array $user): bool
    {
        $status = (string) ($user['status'] ?? '');
        return $status === 'active' || $status === '1' || $status === '';
    }
}
