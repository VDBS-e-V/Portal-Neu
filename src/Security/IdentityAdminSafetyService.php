<?php

declare(strict_types=1);

namespace App\Security;

use App\Identity\IdentityPermissions;
use App\Repository\IdentityAdministrationRepository;
use RuntimeException;

final class IdentityAdminSafetyService
{
    /** @var string[] */
    private array $criticalPermissions = [
        IdentityPermissions::GRUPPEN_PERMISSIONS_MANAGE,
        IdentityPermissions::SUBJECTS_GROUPS_ASSIGN,
    ];

    public function __construct(private readonly IdentityAdministrationRepository $administration)
    {
    }

    /** @param array<string,mixed> $group */
    public function assertGroupCanBeDeleted(array $group): void
    {
        if ($group === []) {
            throw new RuntimeException('Gruppe nicht gefunden.');
        }

        if ((int) ($group['is_system'] ?? 0) === 1) {
            throw new RuntimeException('Systemgruppen dürfen nicht gelöscht werden.');
        }

        if (($group['system_key'] ?? '') === 'identity' && ($group['key_name'] ?? '') === 'administrator') {
            throw new RuntimeException('Die identity.administrator-Gruppe darf nicht gelöscht werden.');
        }
    }

    /** @param array<string,mixed> $permission */
    public function assertPermissionCanBeDeactivated(array $permission): void
    {
        if ($permission === []) {
            throw new RuntimeException('Permission nicht gefunden.');
        }

        $key = (string) ($permission['key_name'] ?? '');
        if (in_array($key, $this->criticalPermissions, true)) {
            throw new RuntimeException('Kritische Verwaltungspermissions dürfen nicht deaktiviert werden.');
        }
    }

    /** @param array<string,mixed> $group */
    public function assertSubjectGroupCanBeRemoved(int $subjectId, array $group): void
    {
        if ($group === []) {
            throw new RuntimeException('Gruppe nicht gefunden.');
        }

        $systemKey = (string) ($group['system_key'] ?? '');
        $groupKey = (string) ($group['key_name'] ?? '');

        if ($systemKey === 'identity' && $groupKey === 'administrator') {
            $others = $this->administration->countActiveSubjectsInGroup('identity', 'administrator', $subjectId);
            if ($others < 1) {
                throw new RuntimeException('Der letzte aktive identity.administrator darf nicht entfernt werden.');
            }
        }

        $groupPermissions = $this->administration->permissionKeysForGroup((int) $group['id']);
        foreach ($this->criticalPermissions as $permissionKey) {
            if (!in_array($permissionKey, $groupPermissions, true)) {
                continue;
            }

            $others = $this->administration->countActiveSubjectsWithPermission($permissionKey, $subjectId);
            if ($others < 1) {
                throw new RuntimeException('Die letzte aktive Verwaltungsberechtigung darf nicht entfernt werden: ' . $permissionKey);
            }
        }
    }
}
