<?php

declare(strict_types=1);

namespace App\Security;

use App\Identity\IdentityPermissions;
use App\Repository\IdentityAdministrationRepository;
use PDO;
use ReflectionObject;
use RuntimeException;
use Throwable;

/**
 * Safety guard for the central identity administration.
 *
 * The canonical identity schema uses key_name in ids_systems, ids_groups and
 * ids_permissions. The public API deliberately keeps the current controller
 * method names, but all checks are enforced against the new central model:
 * ids_subjects, ids_groups, ids_subject_groups, ids_permissions and
 * ids_group_permissions.
 */
final class IdentityAdminSafetyService
{
    /** @var string[] */
    private array $criticalPermissions = [
        IdentityPermissions::GRUPPEN_PERMISSIONS_MANAGE,
        IdentityPermissions::SUBJECTS_GROUPS_ASSIGN,
    ];

    private ?IdentityAdministrationRepository $administration = null;

    private ?PDO $pdo = null;

    public function __construct(IdentityAdministrationRepository|PDO $administrationOrPdo, ?PDO $pdo = null, mixed ...$ignored)
    {
        if ($administrationOrPdo instanceof IdentityAdministrationRepository) {
            $this->administration = $administrationOrPdo;
            $this->pdo = $pdo ?? $this->extractPdo($administrationOrPdo);
            return;
        }

        $this->pdo = $administrationOrPdo;
    }

    /** @param array<string,mixed> $group */
    public function assertGroupCanBeDeleted(array $group): void
    {
        if ($group === []) {
            throw new RuntimeException('Gruppe nicht gefunden.');
        }

        if ((int)($group['is_system'] ?? 0) === 1) {
            throw new RuntimeException('Systemgruppen dürfen nicht gelöscht werden.');
        }

        $systemKey = (string)($group['system_key'] ?? $group['system'] ?? $group['system_key_name'] ?? '');
        $groupKey = (string)($group['key_name'] ?? $group['group_key'] ?? $group['key'] ?? '');
        if ($systemKey === 'identity' && $groupKey === 'administrator') {
            throw new RuntimeException('Die identity.administrator-Gruppe darf nicht gelöscht werden.');
        }

        if (isset($group['id']) && $this->isIdentityAdministratorGroup((int)$group['id'])) {
            throw new RuntimeException('Die identity.administrator-Gruppe darf nicht gelöscht werden.');
        }
    }

    /** @param array<string,mixed> $permission */
    public function assertPermissionCanBeDeactivated(array $permission): void
    {
        if ($permission === []) {
            throw new RuntimeException('Permission nicht gefunden.');
        }

        $key = (string)($permission['key_name'] ?? $permission['permission_key'] ?? $permission['key'] ?? '');
        if (in_array($key, $this->criticalPermissions, true)) {
            throw new RuntimeException('Kritische Verwaltungspermissions dürfen nicht deaktiviert werden.');
        }

        if (isset($permission['id']) && $this->isCriticalPermissionId((int)$permission['id'])) {
            throw new RuntimeException('Kritische Verwaltungspermissions dürfen nicht deaktiviert werden.');
        }
    }

    /** @param array<string,mixed>|int $group */
    public function assertSubjectGroupCanBeRemoved(int $subjectId, array|int $group): void
    {
        $groupId = is_array($group) ? (int)($group['id'] ?? 0) : (int)$group;
        if ($groupId <= 0) {
            throw new RuntimeException('Gruppe nicht gefunden.');
        }

        $systemKey = is_array($group) ? (string)($group['system_key'] ?? $group['system'] ?? '') : '';
        $groupKey = is_array($group) ? (string)($group['key_name'] ?? $group['group_key'] ?? $group['key'] ?? '') : '';

        if (($systemKey === 'identity' && $groupKey === 'administrator') || $this->isIdentityAdministratorGroup($groupId)) {
            $others = $this->countActiveSubjectsInGroup('identity', 'administrator', $subjectId);
            if ($others < 1) {
                throw new RuntimeException('Der letzte aktive identity.administrator darf nicht entfernt werden.');
            }
        }

        $groupPermissions = $this->permissionKeysForGroup($groupId);
        foreach ($this->criticalPermissions as $permissionKey) {
            if (!in_array($permissionKey, $groupPermissions, true)) {
                continue;
            }

            $others = $this->countActiveSubjectsWithPermission($permissionKey, $subjectId);
            if ($others < 1) {
                throw new RuntimeException('Die letzte aktive Verwaltungsberechtigung darf nicht entfernt werden: ' . $permissionKey);
            }
        }
    }

    /** @param array<int,int|string> $newGroupIds */
    public function assertSubjectGroupsCanBeReplaced(int $subjectId, array $newGroupIds): void
    {
        $newGroupIds = array_values(array_unique(array_map('intval', $newGroupIds)));
        $currentGroupIds = $this->groupIdsForSubject($subjectId);

        foreach ($currentGroupIds as $currentGroupId) {
            if (!in_array($currentGroupId, $newGroupIds, true)) {
                $this->assertSubjectGroupCanBeRemoved($subjectId, $currentGroupId);
            }
        }
    }

    public function assertSubjectStatusCanBeChanged(int $subjectId, string $newStatus): void
    {
        $newStatus = strtolower(trim($newStatus));
        if (!in_array($newStatus, ['disabled', 'deleted', 'merged'], true)) {
            return;
        }

        if ($this->subjectHasGroup($subjectId, 'identity', 'administrator')) {
            $others = $this->countActiveSubjectsInGroup('identity', 'administrator', $subjectId);
            if ($others < 1) {
                throw new RuntimeException('Der letzte aktive identity.administrator darf nicht deaktiviert, gelöscht oder zusammengeführt werden.');
            }
        }
    }

    public function assertPermissionCanBeAssignedToGroup(int $groupId, int $permissionId): void
    {
        $pdo = $this->requirePdo();
        $stmt = $pdo->prepare('SELECT system_id FROM ids_groups WHERE id = :id');
        $stmt->execute(['id' => $groupId]);
        $groupSystemId = (int)$stmt->fetchColumn();

        $stmt = $pdo->prepare('SELECT system_id FROM ids_permissions WHERE id = :id');
        $stmt->execute(['id' => $permissionId]);
        $permissionSystemId = (int)$stmt->fetchColumn();

        if ($groupSystemId <= 0 || $permissionSystemId <= 0) {
            throw new RuntimeException('Gruppe oder Permission nicht gefunden.');
        }

        if ($groupSystemId !== $permissionSystemId) {
            throw new RuntimeException('Gruppen dürfen nur Permissions ihres eigenen Systems erhalten.');
        }
    }

    /** @param array<int,int|string> $permissionIds */
    public function assertGroupPermissionsCanBeReplaced(int $groupId, array $permissionIds): void
    {
        $permissionIds = array_values(array_unique(array_map('intval', $permissionIds)));
        foreach ($permissionIds as $permissionId) {
            if ($permissionId > 0) {
                $this->assertPermissionCanBeAssignedToGroup($groupId, $permissionId);
            }
        }

        if (!$this->isIdentityAdministratorGroup($groupId)) {
            return;
        }

        $activeIdentityPermissionIds = $this->activePermissionIdsForSystem('identity');
        $missing = array_values(array_diff($activeIdentityPermissionIds, $permissionIds));
        if ($missing !== []) {
            throw new RuntimeException('Die identity.administrator-Gruppe muss alle aktiven Identity-Permissions behalten.');
        }
    }

    private function extractPdo(object $object): ?PDO
    {
        try {
            $reflection = new ReflectionObject($object);
            foreach ($reflection->getProperties() as $property) {
                $property->setAccessible(true);
                $value = $property->getValue($object);
                if ($value instanceof PDO) {
                    return $value;
                }
            }
        } catch (Throwable) {
        }

        return null;
    }

    private function requirePdo(): PDO
    {
        if ($this->pdo instanceof PDO) {
            return $this->pdo;
        }
        throw new RuntimeException('IdentityAdminSafetyService benötigt eine PDO-Verbindung für diesen Safety-Check.');
    }

    /** @return int[] */
    private function groupIdsForSubject(int $subjectId): array
    {
        $pdo = $this->requirePdo();
        $stmt = $pdo->prepare('SELECT group_id FROM ids_subject_groups WHERE subject_id = :subject_id AND (expires_at IS NULL OR expires_at > CURRENT_TIMESTAMP)');
        $stmt->execute(['subject_id' => $subjectId]);
        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }

    private function subjectHasGroup(int $subjectId, string $systemKey, string $groupKey): bool
    {
        $pdo = $this->requirePdo();
        $stmt = $pdo->prepare(
            'SELECT COUNT(*)
             FROM ids_subject_groups sg
             INNER JOIN ids_groups g ON g.id = sg.group_id
             INNER JOIN ids_systems s ON s.id = g.system_id
             WHERE sg.subject_id = :subject_id
               AND s.key_name = :system_key
               AND g.key_name = :group_key
               AND (sg.expires_at IS NULL OR sg.expires_at > CURRENT_TIMESTAMP)'
        );
        $stmt->execute([
            'subject_id' => $subjectId,
            'system_key' => $systemKey,
            'group_key' => $groupKey,
        ]);
        return (int)$stmt->fetchColumn() > 0;
    }

    private function isIdentityAdministratorGroup(int $groupId): bool
    {
        if ($groupId <= 0) {
            return false;
        }

        $pdo = $this->requirePdo();
        $stmt = $pdo->prepare(
            'SELECT COUNT(*)
             FROM ids_groups g
             INNER JOIN ids_systems s ON s.id = g.system_id
             WHERE g.id = :group_id
               AND s.key_name = :system_key
               AND g.key_name = :group_key'
        );
        $stmt->execute([
            'group_id' => $groupId,
            'system_key' => 'identity',
            'group_key' => 'administrator',
        ]);
        return (int)$stmt->fetchColumn() > 0;
    }

    private function isCriticalPermissionId(int $permissionId): bool
    {
        if ($permissionId <= 0) {
            return false;
        }

        $pdo = $this->requirePdo();
        $stmt = $pdo->prepare('SELECT key_name FROM ids_permissions WHERE id = :id');
        $stmt->execute(['id' => $permissionId]);
        $key = (string)$stmt->fetchColumn();
        return in_array($key, $this->criticalPermissions, true);
    }

    /** @return string[] */
    private function permissionKeysForGroup(int $groupId): array
    {
        if ($this->administration instanceof IdentityAdministrationRepository && method_exists($this->administration, 'permissionKeysForGroup')) {
            /** @var string[] $keys */
            $keys = $this->administration->permissionKeysForGroup($groupId);
            return $keys;
        }

        $pdo = $this->requirePdo();
        $stmt = $pdo->prepare(
            'SELECT p.key_name
             FROM ids_group_permissions gp
             INNER JOIN ids_permissions p ON p.id = gp.permission_id
             WHERE gp.group_id = :group_id'
        );
        $stmt->execute(['group_id' => $groupId]);
        return array_values(array_map('strval', $stmt->fetchAll(PDO::FETCH_COLUMN)));
    }

    private function countActiveSubjectsInGroup(string $systemKey, string $groupKey, ?int $excludeSubjectId = null): int
    {
        if ($this->administration instanceof IdentityAdministrationRepository && method_exists($this->administration, 'countActiveSubjectsInGroup')) {
            return (int)$this->administration->countActiveSubjectsInGroup($systemKey, $groupKey, $excludeSubjectId);
        }

        $pdo = $this->requirePdo();
        $params = [
            'system_key' => $systemKey,
            'group_key' => $groupKey,
        ];
        $excludeSql = '';
        if ($excludeSubjectId !== null) {
            $excludeSql = ' AND sub.id <> :exclude_subject_id';
            $params['exclude_subject_id'] = $excludeSubjectId;
        }

        $stmt = $pdo->prepare(
            'SELECT COUNT(DISTINCT sub.id)
             FROM ids_subjects sub
             INNER JOIN ids_subject_groups sg ON sg.subject_id = sub.id
             INNER JOIN ids_groups g ON g.id = sg.group_id
             INNER JOIN ids_systems s ON s.id = g.system_id
             WHERE sub.status = \'active\'
               AND s.key_name = :system_key
               AND g.key_name = :group_key
               AND (sg.expires_at IS NULL OR sg.expires_at > CURRENT_TIMESTAMP)' . $excludeSql
        );
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    private function countActiveSubjectsWithPermission(string $permissionKey, ?int $excludeSubjectId = null): int
    {
        if ($this->administration instanceof IdentityAdministrationRepository && method_exists($this->administration, 'countActiveSubjectsWithPermission')) {
            return (int)$this->administration->countActiveSubjectsWithPermission($permissionKey, $excludeSubjectId);
        }

        $pdo = $this->requirePdo();
        $params = ['permission_key' => $permissionKey];
        $excludeSql = '';
        if ($excludeSubjectId !== null) {
            $excludeSql = ' AND sub.id <> :exclude_subject_id';
            $params['exclude_subject_id'] = $excludeSubjectId;
        }

        $stmt = $pdo->prepare(
            'SELECT COUNT(DISTINCT sub.id)
             FROM ids_subjects sub
             INNER JOIN ids_subject_groups sg ON sg.subject_id = sub.id
             INNER JOIN ids_groups g ON g.id = sg.group_id
             INNER JOIN ids_group_permissions gp ON gp.group_id = g.id
             INNER JOIN ids_permissions p ON p.id = gp.permission_id
             WHERE sub.status = \'active\'
               AND p.key_name = :permission_key
               AND (sg.expires_at IS NULL OR sg.expires_at > CURRENT_TIMESTAMP)' . $excludeSql
        );
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    /** @return int[] */
    private function activePermissionIdsForSystem(string $systemKey): array
    {
        $pdo = $this->requirePdo();
        $activeSql = $this->permissionActiveCondition('p');
        $stmt = $pdo->prepare(
            'SELECT p.id
             FROM ids_permissions p
             INNER JOIN ids_systems s ON s.id = p.system_id
             WHERE s.key_name = :system_key AND ' . $activeSql
        );
        $stmt->execute(['system_key' => $systemKey]);
        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }

    private function permissionActiveCondition(string $alias): string
    {
        if ($this->columnExists('ids_permissions', 'is_active')) {
            return "({$alias}.is_active IS NULL OR {$alias}.is_active = 1)";
        }
        if ($this->columnExists('ids_permissions', 'active')) {
            return "({$alias}.active IS NULL OR {$alias}.active = 1)";
        }
        if ($this->columnExists('ids_permissions', 'status')) {
            return "({$alias}.status IS NULL OR {$alias}.status = '' OR {$alias}.status = 'active')";
        }
        return '1=1';
    }

    private function columnExists(string $table, string $column): bool
    {
        $pdo = $this->requirePdo();
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = :table AND column_name = :column');
        $stmt->execute(['table' => $table, 'column' => $column]);
        return (int)$stmt->fetchColumn() > 0;
    }
}
