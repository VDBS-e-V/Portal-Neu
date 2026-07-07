<?php

declare(strict_types=1);

namespace App\Repository;

use InvalidArgumentException;
use PDO;
use RuntimeException;

final class PersonErasureRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function createRequest(int $personId, ?int $requestedByUserId, ?string $reason): int
    {
        $this->assertTableExists();

        if ($this->openRequestForPerson($personId) !== []) {
            throw new InvalidArgumentException('Für diese Person existiert bereits ein offener DSGVO-Vorgang.');
        }

        $stmt = $this->pdo->prepare(
            'INSERT INTO ids_person_erasure_requests
                (person_id, status, requested_by_user_id, reason)
             VALUES
                (:person_id, :status, :requested_by_user_id, :reason)'
        );
        $stmt->execute([
            'person_id' => $personId,
            'status' => 'requested',
            'requested_by_user_id' => $requestedByUserId,
            'reason' => $this->nullableString($reason),
        ]);

        $id = (int) $this->pdo->lastInsertId();

        if ($this->tableExists('ids_persons') && $this->columnExists('ids_persons', 'status')) {
            $stmt = $this->pdo->prepare(
                'UPDATE ids_persons
                 SET status = :status
                 WHERE id = :id'
            );
            $stmt->execute([
                'id' => $personId,
                'status' => 'erasure_requested',
            ]);
        }

        return $id;
    }

    public function approve(int $requestId, int $approvedByUserId, ?string $reviewNote): bool
    {
        $this->assertTableExists();

        $stmt = $this->pdo->prepare(
            'UPDATE ids_person_erasure_requests
             SET status = :status,
                 approved_by_user_id = :approved_by_user_id,
                 approved_at = CURRENT_TIMESTAMP,
                 review_note = :review_note
             WHERE id = :id
               AND status = :old_status'
        );

        return (bool) $stmt->execute([
            'id' => $requestId,
            'status' => 'approved',
            'old_status' => 'requested',
            'approved_by_user_id' => $approvedByUserId,
            'review_note' => $this->nullableString($reviewNote),
        ]);
    }

    public function reject(int $requestId, int $rejectedByUserId, ?string $reviewNote): bool
    {
        $this->assertTableExists();

        $request = $this->find($requestId);

        if ($request === []) {
            return false;
        }

        $stmt = $this->pdo->prepare(
            'UPDATE ids_person_erasure_requests
             SET status = :status,
                 rejected_at = CURRENT_TIMESTAMP,
                 review_note = :review_note
             WHERE id = :id
               AND status IN (\'requested\', \'approved\')'
        );
        $result = (bool) $stmt->execute([
            'id' => $requestId,
            'status' => 'rejected',
            'review_note' => $this->nullableString($reviewNote),
        ]);

        if ($result && $this->tableExists('ids_persons') && $this->columnExists('ids_persons', 'status')) {
            $stmt = $this->pdo->prepare(
                'UPDATE ids_persons
                 SET status = :status
                 WHERE id = :id
                   AND status = :old_status'
            );
            $stmt->execute([
                'id' => (int) $request['person_id'],
                'status' => 'disabled',
                'old_status' => 'erasure_requested',
            ]);
        }

        return $result;
    }

    public function cancel(int $requestId, int $cancelledByUserId, ?string $reviewNote): bool
    {
        $this->assertTableExists();

        $request = $this->find($requestId);

        if ($request === []) {
            return false;
        }

        $stmt = $this->pdo->prepare(
            'UPDATE ids_person_erasure_requests
             SET status = :status,
                 cancelled_at = CURRENT_TIMESTAMP,
                 review_note = :review_note
             WHERE id = :id
               AND status IN (\'requested\', \'approved\')'
        );
        $result = (bool) $stmt->execute([
            'id' => $requestId,
            'status' => 'cancelled',
            'review_note' => $this->nullableString($reviewNote),
        ]);

        if ($result && $this->tableExists('ids_persons') && $this->columnExists('ids_persons', 'status')) {
            $stmt = $this->pdo->prepare(
                'UPDATE ids_persons
                 SET status = :status
                 WHERE id = :id
                   AND status = :old_status'
            );
            $stmt->execute([
                'id' => (int) $request['person_id'],
                'status' => 'disabled',
                'old_status' => 'erasure_requested',
            ]);
        }

        return $result;
    }

    public function completeAnonymization(int $requestId, int $completedByUserId): bool
    {
        $this->assertTableExists();

        $request = $this->find($requestId);

        if ($request === []) {
            throw new InvalidArgumentException('DSGVO-Vorgang nicht gefunden.');
        }

        if ((string) ($request['status'] ?? '') !== 'approved') {
            throw new InvalidArgumentException('DSGVO-Vorgang muss vor der Umsetzung freigegeben sein.');
        }

        $personId = (int) ($request['person_id'] ?? 0);

        if ($personId <= 0) {
            throw new InvalidArgumentException('Ungültige Person.');
        }

        $this->pdo->beginTransaction();

        try {
            $this->anonymizePerson($personId);

            $stmt = $this->pdo->prepare(
                'UPDATE ids_person_erasure_requests
                 SET status = :status,
                     completed_by_user_id = :completed_by_user_id,
                     completed_at = CURRENT_TIMESTAMP
                 WHERE id = :id'
            );
            $stmt->execute([
                'id' => $requestId,
                'status' => 'completed',
                'completed_by_user_id' => $completedByUserId,
            ]);

            $this->pdo->commit();

            return true;
        } catch (\Throwable $throwable) {
            $this->pdo->rollBack();
            throw $throwable;
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function find(int $requestId): array
    {
        if (!$this->tableExists('ids_person_erasure_requests')) {
            return [];
        }

        $stmt = $this->pdo->prepare(
            'SELECT
                er.*,
                p.display_name,
                p.status AS person_status,
                u.email AS login_email,
                requester.email AS requested_by_email,
                approver.email AS approved_by_email,
                completer.email AS completed_by_email
             FROM ids_person_erasure_requests er
             INNER JOIN ids_persons p ON p.id = er.person_id
             LEFT JOIN ids_users u ON u.person_id = p.id
             LEFT JOIN ids_users requester ON requester.id = er.requested_by_user_id
             LEFT JOIN ids_users approver ON approver.id = er.approved_by_user_id
             LEFT JOIN ids_users completer ON completer.id = er.completed_by_user_id
             WHERE er.id = :id
             LIMIT 1'
        );
        $stmt->execute(['id' => $requestId]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * @return array<string, mixed>
     */
    public function openRequestForPerson(int $personId): array
    {
        if (!$this->tableExists('ids_person_erasure_requests')) {
            return [];
        }

        $stmt = $this->pdo->prepare(
            'SELECT *
             FROM ids_person_erasure_requests
             WHERE person_id = :person_id
               AND status IN (\'requested\', \'approved\')
             ORDER BY id DESC
             LIMIT 1'
        );
        $stmt->execute(['person_id' => $personId]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * @param array<string, mixed> $filters
     * @return array<int, array<string, mixed>>
     */
    public function search(array $filters = []): array
    {
        if (!$this->tableExists('ids_person_erasure_requests')) {
            return [];
        }

        $where = [];
        $params = [];

        $status = trim((string) ($filters['status'] ?? ''));

        if ($status !== '') {
            $where[] = 'er.status = :status';
            $params['status'] = $status;
        }

        $q = trim((string) ($filters['q'] ?? ''));

        if ($q !== '') {
            $where[] = '(
                p.display_name LIKE :q
                OR u.email LIKE :q
                OR er.reason LIKE :q
                OR er.review_note LIKE :q
            )';
            $params['q'] = '%' . $q . '%';
        }

        $limit = max(1, min(250, (int) ($filters['limit'] ?? 100)));

        $sql = 'SELECT
                    er.*,
                    p.display_name,
                    p.status AS person_status,
                    u.email AS login_email,
                    requester.email AS requested_by_email,
                    approver.email AS approved_by_email,
                    completer.email AS completed_by_email
                FROM ids_person_erasure_requests er
                INNER JOIN ids_persons p ON p.id = er.person_id
                LEFT JOIN ids_users u ON u.person_id = p.id
                LEFT JOIN ids_users requester ON requester.id = er.requested_by_user_id
                LEFT JOIN ids_users approver ON approver.id = er.approved_by_user_id
                LEFT JOIN ids_users completer ON completer.id = er.completed_by_user_id';

        if ($where !== []) {
            $sql .= "\nWHERE " . implode("\n  AND ", $where);
        }

        $sql .= '
                ORDER BY er.created_at DESC, er.id DESC
                LIMIT :limit';

        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    private function anonymizePerson(int $personId): void
    {
        $anonymousLabel = 'Gelöschte Person #' . $personId;
        $anonymousEmail = 'deleted-person-' . $personId . '@invalid.local';

        if ($this->tableExists('ids_person_contact_details')) {
            $stmt = $this->pdo->prepare(
                'DELETE FROM ids_person_contact_details
                 WHERE person_id = :person_id'
            );
            $stmt->execute(['person_id' => $personId]);
        }

        if ($this->tableExists('ids_person_addresses')) {
            $stmt = $this->pdo->prepare(
                'DELETE FROM ids_person_addresses
                 WHERE person_id = :person_id'
            );
            $stmt->execute(['person_id' => $personId]);
        }

        if ($this->tableExists('ids_person_names')) {
            $stmt = $this->pdo->prepare(
                'UPDATE ids_person_names
                 SET salutation = NULL,
                     title = NULL,
                     first_name = NULL,
                     middle_name = NULL,
                     last_name = NULL,
                     preferred_name = NULL,
                     pronouns = NULL
                 WHERE person_id = :person_id'
            );
            $stmt->execute(['person_id' => $personId]);
        }

        if ($this->tableExists('ids_person_permission_groups')) {
            $stmt = $this->pdo->prepare(
                'DELETE FROM ids_person_permission_groups
                 WHERE person_id = :person_id'
            );
            $stmt->execute(['person_id' => $personId]);
        }

        if ($this->tableExists('ids_user_invitations')) {
            $stmt = $this->pdo->prepare(
                'UPDATE ids_user_invitations invitation
                 INNER JOIN ids_users u ON u.id = invitation.user_id
                 SET invitation.status = CASE
                         WHEN invitation.status = \'pending\' THEN \'revoked\'
                         ELSE invitation.status
                     END,
                     invitation.revoked_at = CASE
                         WHEN invitation.status = \'pending\' THEN CURRENT_TIMESTAMP
                         ELSE invitation.revoked_at
                     END
                 WHERE u.person_id = :person_id'
            );
            $stmt->execute(['person_id' => $personId]);
        }

        if ($this->tableExists('ids_users') && $this->columnExists('ids_users', 'person_id')) {
            $columns = [
                'email = :email',
                'password_hash = NULL',
                'status = :status',
            ];

            if ($this->columnExists('ids_users', 'identity_subject')) {
                $columns[] = 'identity_subject = NULL';
            }

            if ($this->columnExists('ids_users', 'email_verified_at')) {
                $columns[] = 'email_verified_at = NULL';
            }

            $stmt = $this->pdo->prepare(
                'UPDATE ids_users
                 SET ' . implode(', ', $columns) . '
                 WHERE person_id = :person_id'
            );
            $stmt->execute([
                'person_id' => $personId,
                'email' => $anonymousEmail,
                'status' => 'disabled',
            ]);
        }

        $set = [
            'display_name = :display_name',
            'status = :status',
        ];

        if ($this->columnExists('ids_persons', 'erased_at')) {
            $set[] = 'erased_at = CURRENT_TIMESTAMP';
        }

        if ($this->columnExists('ids_persons', 'disabled_at')) {
            $set[] = 'disabled_at = COALESCE(disabled_at, CURRENT_TIMESTAMP)';
        }

        $stmt = $this->pdo->prepare(
            'UPDATE ids_persons
             SET ' . implode(', ', $set) . '
             WHERE id = :id'
        );
        $stmt->execute([
            'id' => $personId,
            'display_name' => $anonymousLabel,
            'status' => 'erased',
        ]);
    }

    private function nullableString(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }

    private function assertTableExists(): void
    {
        if (!$this->tableExists('ids_person_erasure_requests')) {
            throw new RuntimeException('Tabelle ids_person_erasure_requests fehlt. Migration 0027 ausführen.');
        }
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
