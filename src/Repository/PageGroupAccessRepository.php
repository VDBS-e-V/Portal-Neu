<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class PageGroupAccessRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function userHasPageGroupAccess(int $userId, string $areaKey, string $pageGroupKey): bool
    {
        $personId = $this->personIdForUserId($userId);

        if ($personId !== null && $this->personHasPageGroupAccess($personId, $areaKey, $pageGroupKey)) {
            return true;
        }

        if ($this->legacyUserHasPageGroupAccess($userId, $areaKey, $pageGroupKey)) {
            return true;
        }

        return $this->userHasAreaAccess($userId, $areaKey);
    }

    public function personHasPageGroupAccess(int $personId, string $areaKey, string $pageGroupKey): bool
    {
        if (!$this->tableExists('pt_page_groups') || !$this->tableExists('pt_permission_group_page_group_access')) {
            return false;
        }

        if (!$this->tableExists('ids_person_permission_groups')) {
            return false;
        }

        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*)
             FROM ids_person_permission_groups ppg
             INNER JOIN ids_permission_groups g ON g.id = ppg.permission_group_id
             INNER JOIN pt_permission_group_page_group_access access ON access.permission_group_id = g.id
             INNER JOIN pt_page_groups page_group ON page_group.id = access.page_group_id
             INNER JOIN pt_areas area ON area.id = page_group.area_id
             WHERE ppg.person_id = :person_id
               AND area.area_key = :area_key
               AND page_group.page_group_key = :page_group_key
               AND page_group.is_active = 1'
        );
        $stmt->execute([
            'person_id' => $personId,
            'area_key' => $this->normalizeKey($areaKey),
            'page_group_key' => $this->normalizeKey($pageGroupKey),
        ]);

        return (int) $stmt->fetchColumn() > 0;
    }

    public function userHasAreaAccess(int $userId, string $areaKey): bool
    {
        $personId = $this->personIdForUserId($userId);

        if ($personId !== null && $this->tableExists('ids_person_permission_groups')) {
            $stmt = $this->pdo->prepare(
                'SELECT COUNT(*)
                 FROM ids_person_permission_groups ppg
                 INNER JOIN ids_permission_groups g ON g.id = ppg.permission_group_id
                 INNER JOIN pt_permission_group_area_access access ON access.permission_group_id = g.id
                 INNER JOIN pt_areas area ON area.id = access.area_id
                 WHERE ppg.person_id = :person_id
                   AND area.area_key = :area_key'
            );
            $stmt->execute([
                'person_id' => $personId,
                'area_key' => $this->normalizeKey($areaKey),
            ]);

            if ((int) $stmt->fetchColumn() > 0) {
                return true;
            }
        }

        if (!$this->tableExists('ids_user_permission_groups')) {
            return false;
        }

        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*)
             FROM ids_user_permission_groups upg
             INNER JOIN ids_permission_groups g ON g.id = upg.permission_group_id
             INNER JOIN pt_permission_group_area_access access ON access.permission_group_id = g.id
             INNER JOIN pt_areas area ON area.id = access.area_id
             WHERE upg.user_id = :user_id
               AND area.area_key = :area_key'
        );
        $stmt->execute([
            'user_id' => $userId,
            'area_key' => $this->normalizeKey($areaKey),
        ]);

        return (int) $stmt->fetchColumn() > 0;
    }

    /** @return array<int, array<string, mixed>> */
    public function pageGroupsForPermissionGroup(int $permissionGroupId): array
    {
        if (!$this->tableExists('pt_permission_group_page_group_access')) {
            return [];
        }

        $stmt = $this->pdo->prepare(
            'SELECT pg.*, a.area_key, a.name AS area_name
             FROM pt_permission_group_page_group_access access
             INNER JOIN pt_page_groups pg ON pg.id = access.page_group_id
             INNER JOIN pt_areas a ON a.id = pg.area_id
             WHERE access.permission_group_id = :permission_group_id
             ORDER BY a.sort_order ASC, pg.sort_order ASC, pg.name ASC'
        );
        $stmt->execute(['permission_group_id' => $permissionGroupId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /** @param array<int, int> $pageGroupIds */
    public function syncPageGroupsForPermissionGroup(int $permissionGroupId, array $pageGroupIds): void
    {
        if (!$this->tableExists('pt_permission_group_page_group_access')) {
            return;
        }

        $pageGroupIds = array_values(array_unique(array_map('intval', $pageGroupIds)));

        $this->pdo->beginTransaction();

        try {
            if ($pageGroupIds === []) {
                $stmt = $this->pdo->prepare(
                    'DELETE FROM pt_permission_group_page_group_access
                     WHERE permission_group_id = :permission_group_id'
                );
                $stmt->execute(['permission_group_id' => $permissionGroupId]);
            } else {
                $placeholders = [];
                $params = ['permission_group_id' => $permissionGroupId];

                foreach ($pageGroupIds as $index => $pageGroupId) {
                    $key = 'page_group_id_' . $index;
                    $placeholders[] = ':' . $key;
                    $params[$key] = $pageGroupId;
                }

                $stmt = $this->pdo->prepare(
                    'DELETE FROM pt_permission_group_page_group_access
                     WHERE permission_group_id = :permission_group_id
                       AND page_group_id NOT IN (' . implode(', ', $placeholders) . ')'
                );
                $stmt->execute($params);

                $stmt = $this->pdo->prepare(
                    'INSERT IGNORE INTO pt_permission_group_page_group_access
                       (permission_group_id, page_group_id)
                     VALUES (:permission_group_id, :page_group_id)'
                );

                foreach ($pageGroupIds as $pageGroupId) {
                    $stmt->execute([
                        'permission_group_id' => $permissionGroupId,
                        'page_group_id' => $pageGroupId,
                    ]);
                }
            }

            $this->pdo->commit();
        } catch (\Throwable $throwable) {
            $this->pdo->rollBack();
            throw $throwable;
        }
    }

    private function legacyUserHasPageGroupAccess(int $userId, string $areaKey, string $pageGroupKey): bool
    {
        if (!$this->tableExists('ids_user_permission_groups')) {
            return false;
        }

        if (!$this->tableExists('pt_page_groups') || !$this->tableExists('pt_permission_group_page_group_access')) {
            return false;
        }

        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*)
             FROM ids_user_permission_groups upg
             INNER JOIN ids_permission_groups g ON g.id = upg.permission_group_id
             INNER JOIN pt_permission_group_page_group_access access ON access.permission_group_id = g.id
             INNER JOIN pt_page_groups page_group ON page_group.id = access.page_group_id
             INNER JOIN pt_areas area ON area.id = page_group.area_id
             WHERE upg.user_id = :user_id
               AND area.area_key = :area_key
               AND page_group.page_group_key = :page_group_key
               AND page_group.is_active = 1'
        );
        $stmt->execute([
            'user_id' => $userId,
            'area_key' => $this->normalizeKey($areaKey),
            'page_group_key' => $this->normalizeKey($pageGroupKey),
        ]);

        return (int) $stmt->fetchColumn() > 0;
    }

    private function personIdForUserId(int $userId): ?int
    {
        if (!$this->columnExists('ids_users', 'person_id')) {
            return null;
        }

        $stmt = $this->pdo->prepare('SELECT person_id FROM ids_users WHERE id = :user_id LIMIT 1');
        $stmt->execute(['user_id' => $userId]);

        $personId = $stmt->fetchColumn();

        if ($personId === false || $personId === null || $personId === '') {
            return null;
        }

        return (int) $personId;
    }

    private function normalizeKey(string $key): string
    {
        return mb_strtolower(trim($key));
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