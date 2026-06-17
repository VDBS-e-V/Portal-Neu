<?php

declare(strict_types=1);

namespace App\Security;

use App\Repository\PageGroupAccessRepository;
use App\Repository\PermissionGroupRepository;
use App\Repository\PersonPermissionGroupRepository;

final class AdminSafetyService
{
    /** @var array<int, string> */
    private array $criticalAdminGroups = [
        VerwaltungAccess::ADMIN_GROUP,
    ];

    public function __construct(
        private readonly PermissionGroupRepository $groups,
        private readonly PersonPermissionGroupRepository $personGroups,
        private readonly PageGroupAccessRepository $pageGroupAccess
    ) {
    }

    public function assertActorDoesNotTargetSelf(?int $targetPersonId, ?int $actorPersonId): void
    {
        if ($targetPersonId !== null && $actorPersonId !== null && $targetPersonId === $actorPersonId) {
            throw new AuthorizationException(
                'Du kannst diese Aktion nicht an deinem eigenen Administrationskonto durchführen.',
                422
            );
        }
    }

    public function assertPersonCanBeDisabled(int $targetPersonId, ?int $actorPersonId): void
    {
        $this->assertActorDoesNotTargetSelf($targetPersonId, $actorPersonId);
        $this->assertAtLeastOneAdministrationPrincipalRemains($targetPersonId, null);
    }

    public function assertGroupCanBeRemovedFromPerson(
        int $targetPersonId,
        int $permissionGroupId,
        ?int $actorPersonId
    ): void {
        $this->assertActorDoesNotTargetSelf($targetPersonId, $actorPersonId);
        $this->assertAtLeastOneAdministrationPrincipalRemains($targetPersonId, $permissionGroupId);
    }

    public function assertSystemGroupCanBeDeleted(int $permissionGroupId): void
    {
        $group = $this->groups->find($permissionGroupId);

        if ($group === []) {
            return;
        }

        if ((int) ($group['is_system'] ?? 0) === 1) {
            throw new AuthorizationException('Systemgruppen dürfen nicht gelöscht werden.', 422);
        }
    }

    private function assertAtLeastOneAdministrationPrincipalRemains(
        ?int $removedPersonId,
        ?int $removedPermissionGroupId
    ): void {
        foreach ($this->criticalAdminGroups as $groupKey) {
            $group = $this->groups->findByKey($groupKey);

            if ($group === []) {
                continue;
            }

            $activeCount = $this->personGroups->countActivePrincipalsWithGroupKey($groupKey);

            if ($activeCount <= 0) {
                throw new AuthorizationException(
                    'Es muss mindestens eine aktive Person mit Verwaltungsrechten existieren.',
                    422
                );
            }

            $groupId = (int) $group['id'];

            if ($removedPermissionGroupId !== null && $removedPermissionGroupId !== $groupId) {
                continue;
            }

            if ($removedPersonId === null && $removedPermissionGroupId === null) {
                continue;
            }

            if ($activeCount <= 1) {
                throw new AuthorizationException(
                    'Die letzte Person mit Verwaltung.Admin-Rechten darf nicht entfernt oder deaktiviert werden.',
                    422
                );
            }
        }
    }
}