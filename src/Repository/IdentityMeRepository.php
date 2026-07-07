<?php

declare(strict_types=1);

namespace App\Repository;

use InvalidArgumentException;
use PDO;

final class IdentityMeRepository
{
    public function __construct(
        private readonly PDO $pdo
    ) {
    }

    /**
     * @return array<string,mixed>
     */
    public function meForUserId(int $userId, ?string $systemKey = null): array
    {
        $systemKey = $this->normalizeOptionalSystemKey($systemKey);

        if ($systemKey !== null && !$this->systemExists($systemKey)) {
            throw new InvalidArgumentException('Unbekanntes System: ' . $systemKey);
        }

        $identity = $this->identityForUserId($userId);
        if ($identity === []) {
            return [];
        }

        $subjectId = (int) $identity['subject']['id'];

        if ($systemKey !== null) {
            return [
                'subject' => [
                    'uuid' => $identity['subject']['uuid'],
                    'status' => $identity['subject']['status'],
                    'permission_version' => $identity['subject']['permission_version'],
                ],
                'system' => $systemKey,
                'groups' => $this->groupsForSubjectAndSystem($subjectId, $systemKey),
                'permissions' => $this->permissionsForSubjectAndSystem($subjectId, $systemKey),
                'cache_ttl_seconds' => $this->cacheTtlSeconds(),
            ];
        }

        $systems = [];
        foreach ($this->activeSystemKeys() as $activeSystemKey) {
            $groups = $this->groupsForSubjectAndSystem($subjectId, $activeSystemKey);
            $permissions = $this->permissionsForSubjectAndSystem($subjectId, $activeSystemKey);

            if ($groups === [] && $permissions === []) {
                continue;
            }

            $systems[$activeSystemKey] = [
                'groups' => $groups,
                'permissions' => $permissions,
            ];
        }

        return $identity + [
            'systems' => $systems,
            'cache_ttl_seconds' => $this->cacheTtlSeconds(),
        ];
    }

    /**
     * @return array<string,mixed>
     */
    public function identityForUserId(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT
                s.id AS subject_id,
                s.uuid AS subject_uuid,
                s.status AS subject_status,
                s.permission_version,
                p.id AS person_id,
                p.display_name,
                u.id AS user_id,
                u.email,
                u.status AS user_status
             FROM ids_users u
             JOIN ids_persons p ON p.id = u.person_id
             JOIN ids_subjects s ON s.id = p.subject_id
             WHERE u.id = :user_id
               AND u.status = :user_status
               AND s.status = :subject_status
             LIMIT 1'
        );
        $stmt->execute([
            'user_id' => $userId,
            'user_status' => 'active',
            'subject_status' => 'active',
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!is_array($row)) {
            return [];
        }

        $displayName = trim((string) ($row['display_name'] ?? ''));
        if ($displayName === '') {
            $displayName = (string) $row['email'];
        }

        return [
            'subject' => [
                'id' => (int) $row['subject_id'],
                'uuid' => (string) $row['subject_uuid'],
                'status' => (string) $row['subject_status'],
                'permission_version' => (int) $row['permission_version'],
            ],
            'person' => [
                'id' => (int) $row['person_id'],
                'display_name' => $displayName,
            ],
            'login' => [
                'id' => (int) $row['user_id'],
                'email' => (string) $row['email'],
            ],
        ];
    }

    /**
     * @return string[]
     */
    public function activeSystemKeys(): array
    {
        $stmt = $this->pdo->query(
            'SELECT key_name
             FROM ids_systems
             WHERE is_active = 1
             ORDER BY sorting ASC, key_name ASC'
        );

        $values = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return array_values(array_map('strval', $values));
    }

    /**
     * @return string[]
     */
    public function groupsForSubjectAndSystem(int $subjectId, string $systemKey): array
    {
        $systemKey = $this->normalizeSystemKey($systemKey);

        $stmt = $this->pdo->prepare(
            'SELECT DISTINCT g.key_name
             FROM ids_subject_groups sg
             JOIN ids_groups g ON g.id = sg.group_id
             JOIN ids_systems sys ON sys.id = g.system_id
             WHERE sg.subject_id = :subject_id
               AND sys.key_name = :system_key
               AND sys.is_active = 1
               AND g.is_active = 1
               AND (sg.expires_at IS NULL OR sg.expires_at > CURRENT_TIMESTAMP)
             ORDER BY g.sorting ASC, g.key_name ASC'
        );
        $stmt->execute([
            'subject_id' => $subjectId,
            'system_key' => $systemKey,
        ]);

        return array_values(array_map('strval', $stmt->fetchAll(PDO::FETCH_COLUMN)));
    }

    /**
     * @return string[]
     */
    public function permissionsForSubjectAndSystem(int $subjectId, string $systemKey): array
    {
        $systemKey = $this->normalizeSystemKey($systemKey);

        $stmt = $this->pdo->prepare(
            'SELECT DISTINCT perm.key_name
             FROM ids_subject_groups sg
             JOIN ids_groups g ON g.id = sg.group_id
             JOIN ids_systems sys ON sys.id = g.system_id
             JOIN ids_group_permissions gp ON gp.group_id = g.id
             JOIN ids_permissions perm ON perm.id = gp.permission_id
             WHERE sg.subject_id = :subject_id
               AND sys.key_name = :system_key
               AND sys.is_active = 1
               AND g.is_active = 1
               AND perm.is_active = 1
               AND perm.system_id = sys.id
               AND (sg.expires_at IS NULL OR sg.expires_at > CURRENT_TIMESTAMP)
             ORDER BY perm.key_name ASC'
        );
        $stmt->execute([
            'subject_id' => $subjectId,
            'system_key' => $systemKey,
        ]);

        return array_values(array_map('strval', $stmt->fetchAll(PDO::FETCH_COLUMN)));
    }

    public function systemExists(string $systemKey): bool
    {
        $systemKey = $this->normalizeSystemKey($systemKey);

        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*)
             FROM ids_systems
             WHERE key_name = :system_key
               AND is_active = 1'
        );
        $stmt->execute(['system_key' => $systemKey]);

        return (int) $stmt->fetchColumn() > 0;
    }

    public function cacheTtlSeconds(): int
    {
        $value = getenv('IDENTITY_ME_CACHE_TTL');
        if ($value === false || trim((string) $value) === '') {
            return 300;
        }

        $ttl = (int) $value;
        if ($ttl < 0) {
            return 0;
        }

        if ($ttl > 3600) {
            return 3600;
        }

        return $ttl;
    }

    private function normalizeOptionalSystemKey(?string $systemKey): ?string
    {
        if ($systemKey === null) {
            return null;
        }

        $systemKey = trim($systemKey);
        if ($systemKey === '') {
            return null;
        }

        return $this->normalizeSystemKey($systemKey);
    }

    private function normalizeSystemKey(string $systemKey): string
    {
        $systemKey = strtolower(trim($systemKey));
        if (!preg_match('/^[a-z][a-z0-9-]*$/', $systemKey)) {
            throw new InvalidArgumentException('Ungültiger System-Key: ' . $systemKey);
        }

        return $systemKey;
    }
}
