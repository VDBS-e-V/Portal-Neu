<?php

declare(strict_types=1);

namespace App\Repository;

use InvalidArgumentException;
use PDO;
use RuntimeException;

final class PersonRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    /**
     * @param array{
     *   q?: string,
     *   status?: string,
     *   group_id?: int|string|null,
     *   limit?: int|string|null,
     *   offset?: int|string|null
     * } $filters
     *
     * @return array<int, array<string, mixed>>
     */
    public function search(array $filters = []): array
    {
        if ($this->tableExists('ids_persons')) {
            return $this->searchPersons($filters);
        }

        return $this->searchLegacyUsers($filters);
    }

    /**
     * @return array<string, mixed>
     */
    public function find(int $personId): array
    {
        if ($personId <= 0) {
            return [];
        }

        if ($this->tableExists('ids_persons')) {
            $stmt = $this->pdo->prepare(
                'SELECT
                    p.*,
                    HEX(p.person_uuid) AS person_uuid_hex,
                    n.salutation,
                    n.title,
                    n.first_name,
                    n.middle_name,
                    n.last_name,
                    n.preferred_name,
                    n.pronouns,
                    u.id AS user_id,
                    u.email AS login_email,
                    u.status AS login_status,
                    u.email_verified_at,
                    u.last_login_at
                 FROM ids_persons p
                 LEFT JOIN ids_person_names n ON n.person_id = p.id
                 LEFT JOIN ids_users u ON u.person_id = p.id
                 WHERE p.id = :id
                 LIMIT 1'
            );
            $stmt->execute(['id' => $personId]);

            return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
        }

        $stmt = $this->pdo->prepare(
            'SELECT
                u.id,
                u.id AS user_id,
                u.user_uuid,
                HEX(u.user_uuid) AS user_uuid_hex,
                u.display_name,
                u.email AS login_email,
                u.status,
                u.status AS login_status,
                u.created_at,
                u.updated_at,
                n.salutation,
                n.title,
                n.first_name,
                n.middle_name,
                n.last_name,
                n.preferred_name,
                n.pronouns
             FROM ids_users u
             LEFT JOIN ids_user_names n ON n.user_id = u.id
             WHERE u.id = :id
             LIMIT 1'
        );
        $stmt->execute(['id' => $personId]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * @return array<string, mixed>
     */
    public function findByLoginEmail(string $email): array
    {
        $email = mb_strtolower(trim($email));

        if ($email === '') {
            return [];
        }

        if ($this->tableExists('ids_persons') && $this->columnExists('ids_users', 'person_id')) {
            $stmt = $this->pdo->prepare(
                'SELECT
                    p.*,
                    HEX(p.person_uuid) AS person_uuid_hex,
                    u.id AS user_id,
                    u.email AS login_email,
                    u.status AS login_status
                 FROM ids_users u
                 INNER JOIN ids_persons p ON p.id = u.person_id
                 WHERE u.email = :email
                 LIMIT 1'
            );
            $stmt->execute(['email' => $email]);

            return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
        }

        $stmt = $this->pdo->prepare(
            'SELECT
                u.*,
                u.id AS user_id,
                u.email AS login_email,
                u.status AS login_status
             FROM ids_users u
             WHERE u.email = :email
             LIMIT 1'
        );
        $stmt->execute(['email' => $email]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): int
    {
        $this->pdo->beginTransaction();

        try {
            if ($this->tableExists('ids_persons')) {
                $personId = $this->createPerson($data);
            } else {
                $personId = $this->createLegacyUser($data);
            }

            $this->pdo->commit();

            return $personId;
        } catch (\Throwable $throwable) {
            $this->pdo->rollBack();
            throw $throwable;
        }
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(int $personId, array $data): bool
    {
        if ($personId <= 0) {
            return false;
        }

        $this->pdo->beginTransaction();

        try {
            if ($this->tableExists('ids_persons')) {
                $result = $this->updatePerson($personId, $data);
            } else {
                $result = $this->updateLegacyUser($personId, $data);
            }

            $this->pdo->commit();

            return $result;
        } catch (\Throwable $throwable) {
            $this->pdo->rollBack();
            throw $throwable;
        }
    }

    public function setStatus(int $personId, string $status): bool
    {
        $status = mb_strtolower(trim($status));

        if ($this->tableExists('ids_persons')) {
            $allowed = ['active', 'disabled', 'erasure_requested', 'erased'];

            if (!in_array($status, $allowed, true)) {
                throw new InvalidArgumentException('Ungültiger Personenstatus.');
            }

            $fields = [
                'status' => $status,
                'id' => $personId,
            ];

            $disabledSql = '';

            if ($status === 'disabled') {
                $disabledSql = ', disabled_at = COALESCE(disabled_at, CURRENT_TIMESTAMP)';
            }

            if ($status === 'erasure_requested') {
                $disabledSql = ', erasure_requested_at = COALESCE(erasure_requested_at, CURRENT_TIMESTAMP)';
            }

            if ($status === 'erased') {
                $disabledSql = ', erased_at = COALESCE(erased_at, CURRENT_TIMESTAMP)';
            }

            $stmt = $this->pdo->prepare(
                'UPDATE ids_persons
                 SET status = :status' . $disabledSql . '
                 WHERE id = :id'
            );

            return (bool) $stmt->execute($fields);
        }

        $allowed = ['active', 'disabled'];

        if (!in_array($status, $allowed, true)) {
            throw new InvalidArgumentException('Ungültiger Nutzerstatus.');
        }

        $stmt = $this->pdo->prepare(
            'UPDATE ids_users
             SET status = :status
             WHERE id = :id'
        );

        return (bool) $stmt->execute([
            'id' => $personId,
            'status' => $status,
        ]);
    }

    public function createLoginForPerson(int $personId, string $email, ?string $plainPassword = null): int
    {
        if (!$this->tableExists('ids_persons') || !$this->columnExists('ids_users', 'person_id')) {
            throw new RuntimeException('Login-Anlage benötigt ids_persons und ids_users.person_id.');
        }

        $email = mb_strtolower(trim($email));

        if ($email === '') {
            throw new InvalidArgumentException('Login-E-Mail ist erforderlich.');
        }

        if ($this->findByLoginEmail($email) !== []) {
            throw new InvalidArgumentException('Diese Login-E-Mail ist bereits vergeben.');
        }

        $passwordHash = $plainPassword !== null && trim($plainPassword) !== ''
            ? password_hash($plainPassword, PASSWORD_DEFAULT)
            : null;

        $status = $passwordHash === null ? 'invited' : 'active';

        $stmt = $this->pdo->prepare(
            'INSERT INTO ids_users
                (person_id, user_uuid, identity_subject, email, password_hash, status)
             VALUES
                (:person_id, :user_uuid, NULL, :email, :password_hash, :status)'
        );
        $stmt->execute([
            'person_id' => $personId,
            'user_uuid' => random_bytes(16),
            'email' => $email,
            'password_hash' => $passwordHash,
            'status' => $this->userStatusSupportsInvited() ? $status : ($status === 'invited' ? 'disabled' : 'active'),
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function disableLoginForPerson(int $personId): bool
    {
        if (!$this->columnExists('ids_users', 'person_id')) {
            return false;
        }

        $stmt = $this->pdo->prepare(
            'UPDATE ids_users
             SET status = :status
             WHERE person_id = :person_id'
        );

        return (bool) $stmt->execute([
            'person_id' => $personId,
            'status' => 'disabled',
        ]);
    }

    /**
     * @param array<string, mixed> $filters
     * @return array<int, array<string, mixed>>
     */
    private function searchPersons(array $filters): array
    {
        $joins = [
            'LEFT JOIN ids_person_names n ON n.person_id = p.id',
            'LEFT JOIN ids_users u ON u.person_id = p.id',
        ];
        $where = [];
        $params = [];

        $hasIdentityGroups = $this->tableExists('ids_subject_groups')
            && $this->tableExists('ids_groups')
            && $this->columnExists('ids_persons', 'subject_id');

        $groupSelect = 'NULL AS group_keys';
        if ($hasIdentityGroups) {
            $joins[] = 'LEFT JOIN ids_subject_groups sg ON sg.subject_id = p.subject_id AND (sg.expires_at IS NULL OR sg.expires_at > CURRENT_TIMESTAMP)';
            $joins[] = 'LEFT JOIN ids_groups g ON g.id = sg.group_id';
            $groupSelect = 'GROUP_CONCAT(DISTINCT g.group_key ORDER BY g.group_key SEPARATOR ", ") AS group_keys';
        }

        $q = trim((string) ($filters['q'] ?? ''));
        if ($q !== '') {
            $where[] = '(
                p.display_name LIKE :q
                OR n.first_name LIKE :q
                OR n.last_name LIKE :q
                OR n.preferred_name LIKE :q
                OR u.email LIKE :q
            )';
            $params['q'] = '%' . $q . '%';
        }

        $status = trim((string) ($filters['status'] ?? ''));
        if ($status !== '') {
            $where[] = 'p.status = :status';
            $params['status'] = $status;
        }

        $groupId = (int) ($filters['group_id'] ?? 0);
        if ($groupId > 0 && $hasIdentityGroups) {
            $where[] = 'sg.group_id = :group_id';
            $params['group_id'] = $groupId;
        }

        $limit = max(1, min(250, (int) ($filters['limit'] ?? 100)));
        $offset = max(0, (int) ($filters['offset'] ?? 0));

        $sql = 'SELECT p.*, HEX(p.person_uuid) AS person_uuid_hex,
                n.first_name, n.last_name, n.preferred_name,
                u.id AS user_id, u.email AS login_email, u.status AS login_status,
                ' . $groupSelect . '
            FROM ids_persons p '
            . implode("\n", $joins);

        if ($where !== []) {
            $sql .= "\nWHERE " . implode("\n AND ", $where);
        }

        $sql .= ' GROUP BY p.id, n.id, u.id
            ORDER BY p.display_name ASC, n.last_name ASC, n.first_name ASC, p.id ASC
            LIMIT :limit OFFSET :offset';

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * @param array<string, mixed> $filters
     * @return array<int, array<string, mixed>>
     */
    private function searchLegacyUsers(array $filters): array
    {
        $where = [];
        $params = [];

        $q = trim((string) ($filters['q'] ?? ''));
        if ($q !== '') {
            $where[] = '(
                u.display_name LIKE :q
                OR u.email LIKE :q
                OR n.first_name LIKE :q
                OR n.last_name LIKE :q
                OR n.preferred_name LIKE :q
            )';
            $params['q'] = '%' . $q . '%';
        }

        $status = trim((string) ($filters['status'] ?? ''));
        if ($status !== '') {
            $where[] = 'u.status = :status';
            $params['status'] = $status;
        }

        $limit = max(1, min(250, (int) ($filters['limit'] ?? 100)));
        $offset = max(0, (int) ($filters['offset'] ?? 0));

        $sql = 'SELECT u.id, u.id AS user_id, u.display_name, u.email AS login_email,
                u.status, u.status AS login_status, u.created_at, u.updated_at,
                n.first_name, n.last_name, n.preferred_name,
                NULL AS group_keys
            FROM ids_users u
            LEFT JOIN ids_user_names n ON n.user_id = u.id';

        if ($where !== []) {
            $sql .= "\nWHERE " . implode("\n AND ", $where);
        }

        $sql .= ' GROUP BY u.id, n.id
            ORDER BY u.display_name ASC, u.email ASC, u.id ASC
            LIMIT :limit OFFSET :offset';

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * @param array<string, mixed> $data
     */
    private function createPerson(array $data): int
    {
        $displayName = $this->normalizeNullableString($data['display_name'] ?? null);
        $status = $this->normalizePersonStatus((string) ($data['status'] ?? 'active'));

        $stmt = $this->pdo->prepare(
            'INSERT INTO ids_persons
                (person_uuid, display_name, status)
             VALUES
                (:person_uuid, :display_name, :status)'
        );
        $stmt->execute([
            'person_uuid' => random_bytes(16),
            'display_name' => $displayName,
            'status' => $status,
        ]);

        $personId = (int) $this->pdo->lastInsertId();

        $this->savePersonName($personId, $data);

        $loginEmail = $this->normalizeNullableEmail($data['login_email'] ?? null);

        if ($loginEmail !== null) {
            $this->createLoginForPerson(
                $personId,
                $loginEmail,
                $this->normalizeNullableString($data['login_password'] ?? null)
            );
        }

        return $personId;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function updatePerson(int $personId, array $data): bool
    {
        $displayName = $this->normalizeNullableString($data['display_name'] ?? null);
        $status = $this->normalizePersonStatus((string) ($data['status'] ?? 'active'));

        $stmt = $this->pdo->prepare(
            'UPDATE ids_persons
             SET display_name = :display_name,
                 status = :status
             WHERE id = :id'
        );
        $stmt->execute([
            'id' => $personId,
            'display_name' => $displayName,
            'status' => $status,
        ]);

        $this->savePersonName($personId, $data);

        $loginEmail = $this->normalizeNullableEmail($data['login_email'] ?? null);
        $existing = $this->find($personId);
        $existingUserId = (int) ($existing['user_id'] ?? 0);

        if ($loginEmail !== null) {
            if ($existingUserId > 0) {
                $stmt = $this->pdo->prepare(
                    'UPDATE ids_users
                     SET email = :email
                     WHERE id = :id'
                );
                $stmt->execute([
                    'id' => $existingUserId,
                    'email' => $loginEmail,
                ]);
            } else {
                $this->createLoginForPerson(
                    $personId,
                    $loginEmail,
                    $this->normalizeNullableString($data['login_password'] ?? null)
                );
            }
        }

        return true;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function createLegacyUser(array $data): int
    {
        $loginEmail = $this->normalizeNullableEmail($data['login_email'] ?? null);

        if ($loginEmail === null) {
            throw new InvalidArgumentException('In der alten DB-Struktur ist eine Login-E-Mail erforderlich.');
        }

        if ($this->findByLoginEmail($loginEmail) !== []) {
            throw new InvalidArgumentException('Diese Login-E-Mail ist bereits vergeben.');
        }

        $displayName = $this->normalizeNullableString($data['display_name'] ?? null);
        $password = $this->normalizeNullableString($data['login_password'] ?? null);

        $stmt = $this->pdo->prepare(
            'INSERT INTO ids_users
                (user_uuid, identity_subject, email, display_name, password_hash, status)
             VALUES
                (:user_uuid, NULL, :email, :display_name, :password_hash, :status)'
        );
        $stmt->execute([
            'user_uuid' => random_bytes(16),
            'email' => $loginEmail,
            'display_name' => $displayName,
            'password_hash' => $password !== null ? password_hash($password, PASSWORD_DEFAULT) : null,
            'status' => $this->normalizeUserStatus((string) ($data['status'] ?? 'active')),
        ]);

        $userId = (int) $this->pdo->lastInsertId();

        $this->saveLegacyUserName($userId, $data);

        return $userId;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function updateLegacyUser(int $userId, array $data): bool
    {
        $loginEmail = $this->normalizeNullableEmail($data['login_email'] ?? null);

        if ($loginEmail === null) {
            throw new InvalidArgumentException('In der alten DB-Struktur ist eine Login-E-Mail erforderlich.');
        }

        $stmt = $this->pdo->prepare(
            'UPDATE ids_users
             SET email = :email,
                 display_name = :display_name,
                 status = :status
             WHERE id = :id'
        );
        $stmt->execute([
            'id' => $userId,
            'email' => $loginEmail,
            'display_name' => $this->normalizeNullableString($data['display_name'] ?? null),
            'status' => $this->normalizeUserStatus((string) ($data['status'] ?? 'active')),
        ]);

        $this->saveLegacyUserName($userId, $data);

        return true;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function savePersonName(int $personId, array $data): void
    {
        if (!$this->tableExists('ids_person_names')) {
            return;
        }

        $stmt = $this->pdo->prepare(
            'INSERT INTO ids_person_names
                (person_id, salutation, title, first_name, middle_name, last_name, preferred_name, pronouns)
             VALUES
                (:person_id, :salutation, :title, :first_name, :middle_name, :last_name, :preferred_name, :pronouns)
             ON DUPLICATE KEY UPDATE
                salutation = VALUES(salutation),
                title = VALUES(title),
                first_name = VALUES(first_name),
                middle_name = VALUES(middle_name),
                last_name = VALUES(last_name),
                preferred_name = VALUES(preferred_name),
                pronouns = VALUES(pronouns)'
        );
        $stmt->execute([
            'person_id' => $personId,
            'salutation' => $this->normalizeNullableString($data['salutation'] ?? null),
            'title' => $this->normalizeNullableString($data['title'] ?? null),
            'first_name' => $this->normalizeNullableString($data['first_name'] ?? null),
            'middle_name' => $this->normalizeNullableString($data['middle_name'] ?? null),
            'last_name' => $this->normalizeNullableString($data['last_name'] ?? null),
            'preferred_name' => $this->normalizeNullableString($data['preferred_name'] ?? null),
            'pronouns' => $this->normalizeNullableString($data['pronouns'] ?? null),
        ]);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function saveLegacyUserName(int $userId, array $data): void
    {
        if (!$this->tableExists('ids_user_names')) {
            return;
        }

        $stmt = $this->pdo->prepare(
            'INSERT INTO ids_user_names
                (user_id, salutation, title, first_name, middle_name, last_name, preferred_name, pronouns)
             VALUES
                (:user_id, :salutation, :title, :first_name, :middle_name, :last_name, :preferred_name, :pronouns)
             ON DUPLICATE KEY UPDATE
                salutation = VALUES(salutation),
                title = VALUES(title),
                first_name = VALUES(first_name),
                middle_name = VALUES(middle_name),
                last_name = VALUES(last_name),
                preferred_name = VALUES(preferred_name),
                pronouns = VALUES(pronouns)'
        );
        $stmt->execute([
            'user_id' => $userId,
            'salutation' => $this->normalizeNullableString($data['salutation'] ?? null),
            'title' => $this->normalizeNullableString($data['title'] ?? null),
            'first_name' => $this->normalizeNullableString($data['first_name'] ?? null),
            'middle_name' => $this->normalizeNullableString($data['middle_name'] ?? null),
            'last_name' => $this->normalizeNullableString($data['last_name'] ?? null),
            'preferred_name' => $this->normalizeNullableString($data['preferred_name'] ?? null),
            'pronouns' => $this->normalizeNullableString($data['pronouns'] ?? null),
        ]);
    }

    private function normalizeNullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function normalizeNullableEmail(mixed $value): ?string
    {
        $value = $this->normalizeNullableString($value);

        if ($value === null) {
            return null;
        }

        $value = mb_strtolower($value);

        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Ungültige E-Mail-Adresse.');
        }

        return $value;
    }

    private function normalizePersonStatus(string $status): string
    {
        $status = mb_strtolower(trim($status));

        $allowed = ['active', 'disabled', 'erasure_requested', 'erased'];

        if (!in_array($status, $allowed, true)) {
            return 'active';
        }

        return $status;
    }

    private function normalizeUserStatus(string $status): string
    {
        $status = mb_strtolower(trim($status));

        $allowed = ['active', 'disabled'];

        if ($this->userStatusSupportsInvited()) {
            $allowed[] = 'invited';
        }

        if (!in_array($status, $allowed, true)) {
            return 'active';
        }

        return $status;
    }

    private function userStatusSupportsInvited(): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT COLUMN_TYPE
             FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = :table_name
               AND COLUMN_NAME = :column_name
             LIMIT 1'
        );
        $stmt->execute([
            'table_name' => 'ids_users',
            'column_name' => 'status',
        ]);

        $columnType = (string) ($stmt->fetchColumn() ?: '');

        return str_contains($columnType, 'invited');
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