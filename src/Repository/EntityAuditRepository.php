<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class EntityAuditRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function forEntity(string $entityType, int $entityId, int $limit = 100): array
    {
        if (!$this->tableExists('pt_audit_log')) {
            return [];
        }

        $stmt = $this->pdo->prepare(
            'SELECT *
             FROM pt_audit_log
             WHERE entity_type = :entity_type
               AND entity_id = :entity_id
             ORDER BY occurred_at DESC, id DESC
             LIMIT :limit'
        );
        $stmt->bindValue('entity_type', $entityType);
        $stmt->bindValue('entity_id', $entityId, PDO::PARAM_INT);
        $stmt->bindValue('limit', max(1, min(500, $limit)), PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function forPerson(int $personId, int $limit = 150): array
    {
        if (!$this->tableExists('pt_audit_log')) {
            return [];
        }

        $entityFilters = [
            ['ids_persons', $personId],
        ];

        $userId = $this->loginUserIdForPerson($personId);

        if ($userId > 0) {
            $entityFilters[] = ['ids_users', $userId];
        }

        return $this->forEntityList($entityFilters, $limit);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function forGroup(int $groupId, int $limit = 150): array
    {
        return $this->forEntityList([
            ['ids_permission_groups', $groupId],
            ['ids_person_permission_groups', $groupId],
            ['ids_user_permission_groups', $groupId],
            ['pt_permission_group_page_group_access', $groupId],
        ], $limit);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function forErasureRequest(int $requestId, int $limit = 150): array
    {
        if (!$this->tableExists('ids_person_erasure_requests')) {
            return $this->forEntity('ids_person_erasure_requests', $requestId, $limit);
        }

        $stmt = $this->pdo->prepare(
            'SELECT person_id
             FROM ids_person_erasure_requests
             WHERE id = :id
             LIMIT 1'
        );
        $stmt->execute(['id' => $requestId]);

        $personId = (int) ($stmt->fetchColumn() ?: 0);

        $entityFilters = [
            ['ids_person_erasure_requests', $requestId],
        ];

        if ($personId > 0) {
            $entityFilters[] = ['ids_persons', $personId];
        }

        return $this->forEntityList($entityFilters, $limit);
    }

    /**
     * @param array<int, array{0:string,1:int}> $entityFilters
     * @return array<int, array<string, mixed>>
     */
    private function forEntityList(array $entityFilters, int $limit): array
    {
        if (!$this->tableExists('pt_audit_log') || $entityFilters === []) {
            return [];
        }

        $where = [];
        $params = [];

        foreach ($entityFilters as $index => $filter) {
            $typeKey = 'entity_type_' . $index;
            $idKey = 'entity_id_' . $index;

            $where[] = '(entity_type = :' . $typeKey . ' AND entity_id = :' . $idKey . ')';
            $params[$typeKey] = (string) $filter[0];
            $params[$idKey] = (int) $filter[1];
        }

        $stmt = $this->pdo->prepare(
            'SELECT *
             FROM pt_audit_log
             WHERE ' . implode(' OR ', $where) . '
             ORDER BY occurred_at DESC, id DESC
             LIMIT :limit'
        );

        foreach ($params as $key => $value) {
            if (str_starts_with($key, 'entity_id_')) {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
                continue;
            }

            $stmt->bindValue($key, $value);
        }

        $stmt->bindValue('limit', max(1, min(500, $limit)), PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    private function loginUserIdForPerson(int $personId): int
    {
        if (!$this->tableExists('ids_users') || !$this->columnExists('ids_users', 'person_id')) {
            return 0;
        }

        $stmt = $this->pdo->prepare(
            'SELECT id
             FROM ids_users
             WHERE person_id = :person_id
             ORDER BY id ASC
             LIMIT 1'
        );
        $stmt->execute(['person_id' => $personId]);

        return (int) ($stmt->fetchColumn() ?: 0);
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
