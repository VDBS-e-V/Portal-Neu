<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class AuditLogRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function create(array $data): int
    {
        if (!$this->tableExists('pt_audit_log')) {
            return 0;
        }

        $allowed = [
            'occurred_at',
            'actor_person_id',
            'actor_user_id',
            'action',
            'entity_type',
            'entity_id',
            'entity_uuid',
            'entity_label',
            'request_id',
            'request_method',
            'request_uri',
            'ip_address',
            'user_agent',
            'old_values',
            'new_values',
            'metadata',
        ];

        $payload = [];

        foreach ($allowed as $column) {
            if (array_key_exists($column, $data)) {
                $payload[$column] = $data[$column];
            }
        }

        if (($payload['action'] ?? '') === '' || ($payload['entity_type'] ?? '') === '') {
            return 0;
        }

        $columns = array_keys($payload);

        $sql = sprintf(
            'INSERT INTO pt_audit_log (%s) VALUES (%s)',
            implode(', ', array_map(static fn (string $column): string => '`' . $column . '`', $columns)),
            implode(', ', array_map(static fn (string $column): string => ':' . $column, $columns))
        );

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($payload);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * @param array<string, mixed> $filters
     * @return array<int, array<string, mixed>>
     */
    public function search(array $filters = []): array
    {
        if (!$this->tableExists('pt_audit_log')) {
            return [];
        }

        $where = [];
        $params = [];

        $q = trim((string) ($filters['q'] ?? ''));

        if ($q !== '') {
            $where[] = '(
                al.action LIKE :q
                OR al.entity_type LIKE :q
                OR al.entity_label LIKE :q
                OR al.request_uri LIKE :q
                OR u.email LIKE :q
                OR p.display_name LIKE :q
            )';
            $params['q'] = '%' . $q . '%';
        }

        $action = trim((string) ($filters['action'] ?? ''));

        if ($action !== '') {
            $where[] = 'al.action = :action';
            $params['action'] = $action;
        }

        $entityType = trim((string) ($filters['entity_type'] ?? ''));

        if ($entityType !== '') {
            $where[] = 'al.entity_type = :entity_type';
            $params['entity_type'] = $entityType;
        }

        $actorUserId = (int) ($filters['actor_user_id'] ?? 0);

        if ($actorUserId > 0) {
            $where[] = 'al.actor_user_id = :actor_user_id';
            $params['actor_user_id'] = $actorUserId;
        }

        $actorPersonId = (int) ($filters['actor_person_id'] ?? 0);

        if ($actorPersonId > 0) {
            $where[] = 'al.actor_person_id = :actor_person_id';
            $params['actor_person_id'] = $actorPersonId;
        }

        $from = trim((string) ($filters['from'] ?? ''));

        if ($from !== '') {
            $where[] = 'al.occurred_at >= :from_date';
            $params['from_date'] = $from . ' 00:00:00';
        }

        $to = trim((string) ($filters['to'] ?? ''));

        if ($to !== '') {
            $where[] = 'al.occurred_at <= :to_date';
            $params['to_date'] = $to . ' 23:59:59';
        }

        $limit = max(1, min(500, (int) ($filters['limit'] ?? 100)));
        $offset = max(0, (int) ($filters['offset'] ?? 0));

        $joins = [];

        if ($this->tableExists('ids_users')) {
            $joins[] = 'LEFT JOIN ids_users u ON u.id = al.actor_user_id';
        } else {
            $joins[] = 'LEFT JOIN (SELECT NULL AS id, NULL AS email) u ON 1 = 0';
        }

        if ($this->tableExists('ids_persons')) {
            $joins[] = 'LEFT JOIN ids_persons p ON p.id = al.actor_person_id';
        } else {
            $joins[] = 'LEFT JOIN (SELECT NULL AS id, NULL AS display_name) p ON 1 = 0';
        }

        $sql = 'SELECT
                    al.*,
                    u.email AS actor_email,
                    p.display_name AS actor_display_name
                FROM pt_audit_log al
                ' . implode("\n", $joins);

        if ($where !== []) {
            $sql .= "\nWHERE " . implode("\n  AND ", $where);
        }

        $sql .= '
                ORDER BY al.occurred_at DESC, al.id DESC
                LIMIT :limit OFFSET :offset';

        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $key => $value) {
            if (is_int($value)) {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key, $value);
            }
        }

        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * @return array<string, mixed>
     */
    public function find(int $id): array
    {
        if (!$this->tableExists('pt_audit_log')) {
            return [];
        }

        $joins = [];

        if ($this->tableExists('ids_users')) {
            $joins[] = 'LEFT JOIN ids_users u ON u.id = al.actor_user_id';
        } else {
            $joins[] = 'LEFT JOIN (SELECT NULL AS id, NULL AS email) u ON 1 = 0';
        }

        if ($this->tableExists('ids_persons')) {
            $joins[] = 'LEFT JOIN ids_persons p ON p.id = al.actor_person_id';
        } else {
            $joins[] = 'LEFT JOIN (SELECT NULL AS id, NULL AS display_name) p ON 1 = 0';
        }

        $stmt = $this->pdo->prepare(
            'SELECT
                al.*,
                u.email AS actor_email,
                p.display_name AS actor_display_name
             FROM pt_audit_log al
             ' . implode("\n", $joins) . '
             WHERE al.id = :id
             LIMIT 1'
        );
        $stmt->execute(['id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * @return array<int, string>
     */
    public function distinctActions(): array
    {
        if (!$this->tableExists('pt_audit_log')) {
            return [];
        }

        $stmt = $this->pdo->query(
            'SELECT DISTINCT action
             FROM pt_audit_log
             WHERE action IS NOT NULL
               AND action <> \'\'
             ORDER BY action ASC'
        );

        return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    }

    /**
     * @return array<int, string>
     */
    public function distinctEntityTypes(): array
    {
        if (!$this->tableExists('pt_audit_log')) {
            return [];
        }

        $stmt = $this->pdo->query(
            'SELECT DISTINCT entity_type
             FROM pt_audit_log
             WHERE entity_type IS NOT NULL
               AND entity_type <> \'\'
             ORDER BY entity_type ASC'
        );

        return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
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
    public function forActorPerson(int $personId, int $limit = 100): array
    {
        if (!$this->tableExists('pt_audit_log')) {
            return [];
        }

        $stmt = $this->pdo->prepare(
            'SELECT *
             FROM pt_audit_log
             WHERE actor_person_id = :person_id
             ORDER BY occurred_at DESC, id DESC
             LIMIT :limit'
        );
        $stmt->bindValue('person_id', $personId, PDO::PARAM_INT);
        $stmt->bindValue('limit', max(1, min(500, $limit)), PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
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
}
