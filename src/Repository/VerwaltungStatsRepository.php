<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class VerwaltungStatsRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    /**
     * @return array<string, int>
     */
    public function overview(): array
    {
        return [
            'persons_total' => $this->personsTotal(),
            'persons_active' => $this->personsByStatus('active'),
            'persons_disabled' => $this->personsByStatus('disabled'),
            'persons_erasure_requested' => $this->personsByStatus('erasure_requested'),
            'persons_erased' => $this->personsByStatus('erased'),

            'logins_total' => $this->countTable('ids_users'),
            'logins_active' => $this->countWhere('ids_users', 'status = :status', ['status' => 'active']),
            'logins_disabled' => $this->countWhere('ids_users', 'status = :status', ['status' => 'disabled']),
            'logins_invited' => $this->countWhere('ids_users', 'status = :status', ['status' => 'invited']),

            'groups_total' => $this->countTable('ids_permission_groups'),
            'groups_system' => $this->countWhere('ids_permission_groups', 'is_system = 1'),

            'page_groups_total' => $this->countTable('pt_page_groups'),
            'page_groups_active' => $this->countWhere('pt_page_groups', 'is_active = 1'),

            'pending_invitations' => $this->countWhere('ids_user_invitations', 'status = :status', ['status' => 'pending']),
            'expired_invitations' => $this->countWhere('ids_user_invitations', 'status = :status', ['status' => 'expired']),

            'open_erasure_requests' => $this->countWhere(
                'ids_person_erasure_requests',
                'status IN (\'requested\', \'approved\')'
            ),
            'requested_erasure_requests' => $this->countWhere(
                'ids_person_erasure_requests',
                'status = :status',
                ['status' => 'requested']
            ),
            'approved_erasure_requests' => $this->countWhere(
                'ids_person_erasure_requests',
                'status = :status',
                ['status' => 'approved']
            ),

            'audit_entries_total' => $this->countTable('pt_audit_log'),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function latestPersons(int $limit = 5): array
    {
        $limit = max(1, min(25, $limit));

        if ($this->tableExists('ids_persons')) {
            $stmt = $this->pdo->prepare(
                'SELECT
                    p.id,
                    p.display_name,
                    p.status,
                    p.created_at,
                    u.email AS login_email
                 FROM ids_persons p
                 LEFT JOIN ids_users u ON u.person_id = p.id
                 ORDER BY p.created_at DESC, p.id DESC
                 LIMIT :limit'
            );
            $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        }

        if ($this->tableExists('ids_users')) {
            $stmt = $this->pdo->prepare(
                'SELECT
                    u.id,
                    u.display_name,
                    u.status,
                    u.created_at,
                    u.email AS login_email
                 FROM ids_users u
                 ORDER BY u.created_at DESC, u.id DESC
                 LIMIT :limit'
            );
            $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        }

        return [];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function latestAuditEntries(int $limit = 5): array
    {
        $limit = max(1, min(25, $limit));

        if (!$this->tableExists('pt_audit_log')) {
            return [];
        }

        $stmt = $this->pdo->prepare(
            'SELECT
                id,
                occurred_at,
                actor_user_id,
                action,
                entity_type,
                entity_id,
                entity_label,
                request_method,
                request_uri
             FROM pt_audit_log
             ORDER BY occurred_at DESC, id DESC
             LIMIT :limit'
        );
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function openTasks(int $limit = 10): array
    {
        $limit = max(1, min(50, $limit));
        $tasks = [];

        if ($this->tableExists('ids_user_invitations')) {
            $stmt = $this->pdo->prepare(
                'SELECT
                    invitation.id,
                    invitation.status,
                    invitation.expires_at,
                    invitation.created_at,
                    u.email AS login_email,
                    p.display_name AS person_label
                 FROM ids_user_invitations invitation
                 INNER JOIN ids_users u ON u.id = invitation.user_id
                 LEFT JOIN ids_persons p ON p.id = u.person_id
                 WHERE invitation.status = :status
                 ORDER BY invitation.expires_at ASC, invitation.id ASC
                 LIMIT :limit'
            );
            $stmt->bindValue('status', 'pending');
            $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
            $stmt->execute();

            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) ?: [] as $row) {
                $tasks[] = [
                    'type' => 'invitation',
                    'label' => 'Offene Einladung',
                    'title' => $row['person_label'] ?: $row['login_email'],
                    'subtitle' => 'läuft ab: ' . (string) ($row['expires_at'] ?? ''),
                    'href' => '/verwaltung/einladungen',
                    'priority' => 20,
                ];
            }
        }

        if ($this->tableExists('ids_person_erasure_requests')) {
            $stmt = $this->pdo->prepare(
                'SELECT
                    er.id,
                    er.status,
                    er.requested_at,
                    p.display_name,
                    u.email AS login_email
                 FROM ids_person_erasure_requests er
                 INNER JOIN ids_persons p ON p.id = er.person_id
                 LEFT JOIN ids_users u ON u.person_id = p.id
                 WHERE er.status IN (\'requested\', \'approved\')
                 ORDER BY
                    CASE er.status
                        WHEN \'approved\' THEN 1
                        WHEN \'requested\' THEN 2
                        ELSE 9
                    END,
                    er.requested_at ASC,
                    er.id ASC
                 LIMIT :limit'
            );
            $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
            $stmt->execute();

            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) ?: [] as $row) {
                $tasks[] = [
                    'type' => 'erasure',
                    'label' => $row['status'] === 'approved' ? 'DSGVO freigegeben' : 'DSGVO beantragt',
                    'title' => $row['display_name'] ?: $row['login_email'],
                    'subtitle' => 'beantragt: ' . (string) ($row['requested_at'] ?? ''),
                    'href' => '/verwaltung/datenschutz/' . (int) ($row['id'] ?? 0),
                    'priority' => $row['status'] === 'approved' ? 5 : 10,
                ];
            }
        }

        usort(
            $tasks,
            static fn (array $a, array $b): int => ((int) $a['priority'] <=> (int) $b['priority'])
        );

        return array_slice($tasks, 0, $limit);
    }

    private function personsTotal(): int
    {
        if ($this->tableExists('ids_persons')) {
            return $this->countTable('ids_persons');
        }

        return $this->countTable('ids_users');
    }

    private function personsByStatus(string $status): int
    {
        if ($this->tableExists('ids_persons')) {
            return $this->countWhere('ids_persons', 'status = :status', ['status' => $status]);
        }

        if (in_array($status, ['active', 'disabled'], true)) {
            return $this->countWhere('ids_users', 'status = :status', ['status' => $status]);
        }

        return 0;
    }

    private function countTable(string $table): int
    {
        if (!$this->tableExists($table)) {
            return 0;
        }

        $stmt = $this->pdo->query('SELECT COUNT(*) FROM `' . $table . '`');

        return (int) $stmt->fetchColumn();
    }

    /**
     * @param array<string, mixed> $params
     */
    private function countWhere(string $table, string $where, array $params = []): int
    {
        if (!$this->tableExists($table)) {
            return 0;
        }

        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM `' . $table . '` WHERE ' . $where);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
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
