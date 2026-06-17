<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class PersonPermissionGroupRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function personIdForUserId(int $userId): ?int
    {
        if (!$this->columnExists('ids_users', 'person_id')) {
            return null;
        }

        $stmt = $this->pdo->prepare('SELECT person_id FROM ids_users WHERE id = :user_id LIMIT 1');
        $stmt->execute(['user_id' => $userId]);

        $value = $stmt->fetchColumn();

        if ($value === false || $value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }

    /** @return array<int, array<string, mixed>> */
    public function groupsForUser(int $userId): array
    {
        $personId = $this->personIdForUserId($userId);

        if ($personId !== null && $this->tableExists('ids_person_permission_groups')) {
            return $this->groupsForPerson($personId);
        }

        if (!$this->tableExists('ids_user_permission_groups')) {
            return [];
        }

        $stmt = $this->pdo->prepare(
            'SELECT pg.*, upg.assigned_at, upg.assigned_by_user_id
             FROM ids_user_permission_groups upg
             INNER JOIN ids_permission_groups pg ON pg.id = upg.permission_group_id
             WHERE upg.user_id = :user_id
             ORDER BY pg.group_key ASC'
        );
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /** @return array<int, array<string, mixed>> */
    public function groupsForPerson(int $personId): array
    {
        if (!$this->tableExists('ids_person_permission_groups')) {
            return [];
        }

        $stmt = $this->pdo->prepare(
            'SELECT pg.*, ppg.assigned_at, ppg.assigned_by_person_id
             FROM ids_person_permission_groups ppg
             INNER JOIN ids_permission_groups pg ON pg.id = ppg.permission_group_id
             WHERE ppg.person_id = :person_id
             ORDER BY pg.group_key ASC'
        );
        $stmt->execute(['person_id' => $personId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /** @return array<int, array<string, mixed>> */
    public function personsForGroup(int $permissionGroupId): array
    {
        if ($this->tableExists('ids_person_permission_groups') && $this->tableExists('ids_persons')) {
            $stmt = $this->pdo->prepare(
                'SELECT p.*, ppg.assigned_at, ppg.assigned_by_person_id
                 FROM ids_person_permission_groups ppg
                 INNER JOIN ids_persons p ON p.id = ppg.person_id
                 WHERE ppg.permission_group_id = :group_id
                 ORDER BY p.display_name ASC, p.id ASC'
            );
            $stmt->execute(['group_id' => $permissionGroupId]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        }

        if ($this->tableExists('ids_user_permission_groups')) {
            $stmt = $this->pdo->prepare(
                'SELECT u.*, upg.assigned_at, upg.assigned_by_user_id
                 FROM ids_user_permission_groups upg
                 INNER JOIN ids_users u ON u.id = upg.user_id
                 WHERE upg.permission_group_id = :group_id
                 ORDER BY u.display_name ASC, u.email ASC, u.id ASC'
            );
            $stmt->execute(['group_id' => $permissionGroupId]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        }

        return [];
    }

    public function userHasGroup(int $userId, string $groupKey): bool
    {
        $groupKey = mb_strtolower(trim($groupKey));

        foreach ($this->groupsForUser($userId) as $group) {
            if ((string) ($group['group_key'] ?? '') === $groupKey) {
                return true;
            }
        }

        return false;
    }

    /** @param array<int, string> $groupKeys */
    public function userHasAnyGroup(int $userId, array $groupKeys): bool
    {
        $allowed = array_flip(array_map(
            static fn (string $key): string => mb_strtolower(trim($key)),
            $groupKeys
        ));

        foreach ($this->groupsForUser($userId) as $group) {
            if (isset($allowed[(string) ($group['group_key'] ?? '')])) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array<int, int> $permissionGroupIds
     */
    public function syncGroupsForPerson(int $personId, array $permissionGroupIds, ?int $assignedByPersonId): void
    {
        if (!$this->tableExists('ids_person_permission_groups')) {
            return;
        }

        $permissionGroupIds = array_values(array_unique(array_map('intval', $permissionGroupIds)));

        $this->pdo->beginTransaction();

        try {
            if ($permissionGroupIds === []) {
                $stmt = $this->pdo->prepare(
                    'DELETE FROM ids_person_permission_groups
                     WHERE person_id = :person_id'
                );
                $stmt->execute(['person_id' => $personId]);
            } else {
                $placeholders = [];
                $params = ['person_id' => $personId];

                foreach ($permissionGroupIds as $index => $groupId) {
                    $key = 'group_id_' . $index;
                    $placeholders[] = ':' . $key;
                    $params[$key] = $groupId;
                }

                $stmt = $this->pdo->prepare(
                    'DELETE FROM ids_person_permission_groups
                     WHERE person_id = :person_id
                       AND permission_group_id NOT IN (' . implode(', ', $placeholders) . ')'
                );
                $stmt->execute($params);

                $stmt = $this->pdo->prepare(
                    'INSERT IGNORE INTO ids_person_permission_groups
                       (person_id, permission_group_id, assigned_by_person_id)
                     VALUES (:person_id, :group_id, :assigned_by_person_id)'
                );

                foreach ($permissionGroupIds as $groupId) {
                    $stmt->execute([
                        'person_id' => $personId,
                        'group_id' => $groupId,
                        'assigned_by_person_id' => $assignedByPersonId,
                    ]);
                }
            }

            $this->pdo->commit();
        } catch (\Throwable $throwable) {
            $this->pdo->rollBack();
            throw $throwable;
        }
    }

    public function addGroupToPerson(int $personId, int $permissionGroupId, ?int $assignedByPersonId): void
    {
        if (!$this->tableExists('ids_person_permission_groups')) {
            return;
        }

        $stmt = $this->pdo->prepare(
            'INSERT IGNORE INTO ids_person_permission_groups
               (person_id, permission_group_id, assigned_by_person_id)
             VALUES (:person_id, :group_id, :assigned_by_person_id)'
        );
        $stmt->execute([
            'person_id' => $personId,
            'group_id' => $permissionGroupId,
            'assigned_by_person_id' => $assignedByPersonId,
        ]);
    }

    public function removeGroupFromPerson(int $personId, int $permissionGroupId): void
    {
        if (!$this->tableExists('ids_person_permission_groups')) {
            return;
        }

        $stmt = $this->pdo->prepare(
            'DELETE FROM ids_person_permission_groups
             WHERE person_id = :person_id
               AND permission_group_id = :group_id'
        );
        $stmt->execute([
            'person_id' => $personId,
            'group_id' => $permissionGroupId,
        ]);
    }

    public function countActivePrincipalsWithGroupKey(string $groupKey): int
    {
        $groupKey = mb_strtolower(trim($groupKey));

        if ($this->tableExists('ids_person_permission_groups') && $this->tableExists('ids_persons')) {
            $stmt = $this->pdo->prepare(
                'SELECT COUNT(DISTINCT ppg.person_id)
                 FROM ids_person_permission_groups ppg
                 INNER JOIN ids_permission_groups pg ON pg.id = ppg.permission_group_id
                 INNER JOIN ids_persons p ON p.id = ppg.person_id
                 WHERE pg.group_key = :group_key
                   AND p.status IN (\'active\')'
            );
            $stmt->execute(['group_key' => $groupKey]);

            return (int) $stmt->fetchColumn();
        }

        if ($this->tableExists('ids_user_permission_groups')) {
            $stmt = $this->pdo->prepare(
                'SELECT COUNT(DISTINCT upg.user_id)
                 FROM ids_user_permission_groups upg
                 INNER JOIN ids_permission_groups pg ON pg.id = upg.permission_group_id
                 INNER JOIN ids_users u ON u.id = upg.user_id
                 WHERE pg.group_key = :group_key
                   AND u.status IN (\'active\', \'1\')'
            );
            $stmt->execute(['group_key' => $groupKey]);

            return (int) $stmt->fetchColumn();
        }

        return 0;
    }

    private function tableExists(string $table): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*)
             FROM information_schema.TABLES
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = :table_name'
        );
        $stmt->execute(['table_name' => $table]);

        return (int) $stmt->fetchColumn() > 0;
    }

    private function columnExists(string $table, string $column): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*)
             FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = :table_name
               AND COLUMN_NAME = :column_name'
        );
        $stmt->execute([
            'table_name' => $table,
            'column_name' => $column,
        ]);

        return (int) $stmt->fetchColumn() > 0;
    }
}