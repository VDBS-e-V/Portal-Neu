<?php

declare(strict_types=1);

namespace App\Repository;

use InvalidArgumentException;
use PDO;
use RuntimeException;

final class IdentityAdministrationRepository
{
    public function __construct(private readonly PDO $pdo, private readonly IdentityAuthorizationRepository $identity)
    {
    }

    /** @return array<int,array<string,mixed>> */
    public function dashboardStats(): array
    {
        return [
            'systems' => $this->countTable('ids_systems'),
            'groups' => $this->countTable('ids_groups'),
            'permissions' => $this->countTable('ids_permissions'),
            'subjects' => $this->countTable('ids_subjects'),
            'assignments' => $this->countTable('ids_subject_groups'),
            'expiredAssignments' => $this->countExpiredAssignments(),
        ];
    }

    /** @return array<int,array<string,mixed>> */
    public function systems(): array
    {
        $stmt = $this->pdo->query(
            'SELECT s.*,
                    (SELECT COUNT(*) FROM ids_groups g WHERE g.system_id = s.id) AS group_count,
                    (SELECT COUNT(*) FROM ids_permissions p WHERE p.system_id = s.id) AS permission_count
             FROM ids_systems s
             ORDER BY s.sorting ASC, s.key_name ASC'
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /** @return array<string,mixed> */
    public function system(int $id): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM ids_systems WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return is_array($row) ? $row : [];
    }

    /** @param array<string,mixed> $data */
    public function createSystem(array $data): int
    {
        $data = $this->normalizeSystemData($data);
        $stmt = $this->pdo->prepare(
            'INSERT INTO ids_systems (key_name, name, description, is_active, is_external, sorting)
             VALUES (:key_name, :name, :description, :is_active, :is_external, :sorting)'
        );
        $stmt->execute($data);

        return (int) $this->pdo->lastInsertId();
    }

    /** @param array<string,mixed> $data */
    public function updateSystem(int $id, array $data): void
    {
        $data = $this->normalizeSystemData($data);
        $data['id'] = $id;
        $stmt = $this->pdo->prepare(
            'UPDATE ids_systems
             SET key_name = :key_name,
                 name = :name,
                 description = :description,
                 is_active = :is_active,
                 is_external = :is_external,
                 sorting = :sorting,
                 updated_at = CURRENT_TIMESTAMP
             WHERE id = :id'
        );
        $stmt->execute($data);
    }

    /** @return array<int,array<string,mixed>> */
    public function groups(?int $systemId = null): array
    {
        $params = [];
        $where = '';
        if ($systemId !== null && $systemId > 0) {
            $where = 'WHERE g.system_id = :system_id';
            $params['system_id'] = $systemId;
        }

        $stmt = $this->pdo->prepare(
            'SELECT g.*, s.key_name AS system_key, s.name AS system_name,
                    (SELECT COUNT(*) FROM ids_group_permissions gp WHERE gp.group_id = g.id) AS permission_count,
                    (SELECT COUNT(*) FROM ids_subject_groups sg WHERE sg.group_id = g.id AND (sg.expires_at IS NULL OR sg.expires_at > CURRENT_TIMESTAMP)) AS subject_count
             FROM ids_groups g
             JOIN ids_systems s ON s.id = g.system_id
             ' . $where . '
             ORDER BY s.sorting ASC, s.key_name ASC, g.sorting ASC, g.key_name ASC'
        );
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /** @return array<string,mixed> */
    public function group(int $id): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT g.*, s.key_name AS system_key, s.name AS system_name
             FROM ids_groups g
             JOIN ids_systems s ON s.id = g.system_id
             WHERE g.id = :id
             LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return is_array($row) ? $row : [];
    }

    /** @param array<string,mixed> $data */
    public function createGroup(array $data): int
    {
        $data = $this->normalizeGroupData($data);
        $stmt = $this->pdo->prepare(
            'INSERT INTO ids_groups (system_id, key_name, name, description, sorting, is_active, is_system, is_default, is_assignable)
             VALUES (:system_id, :key_name, :name, :description, :sorting, :is_active, :is_system, :is_default, :is_assignable)'
        );
        $stmt->execute($data);

        return (int) $this->pdo->lastInsertId();
    }

    /** @param array<string,mixed> $data */
    public function updateGroup(int $id, array $data): void
    {
        $data = $this->normalizeGroupData($data);
        $data['id'] = $id;
        $stmt = $this->pdo->prepare(
            'UPDATE ids_groups
             SET system_id = :system_id,
                 key_name = :key_name,
                 name = :name,
                 description = :description,
                 sorting = :sorting,
                 is_active = :is_active,
                 is_system = :is_system,
                 is_default = :is_default,
                 is_assignable = :is_assignable,
                 updated_at = CURRENT_TIMESTAMP
             WHERE id = :id'
        );
        $stmt->execute($data);
        $this->bumpSubjectsForGroup($id);
    }

    public function deleteGroup(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM ids_groups WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    /** @return array<int,array<string,mixed>> */
    public function permissions(?int $systemId = null, bool $includeInactive = true): array
    {
        $conditions = [];
        $params = [];
        if ($systemId !== null && $systemId > 0) {
            $conditions[] = 'p.system_id = :system_id';
            $params['system_id'] = $systemId;
        }
        if (!$includeInactive) {
            $conditions[] = 'p.is_active = 1';
        }
        $where = $conditions === [] ? '' : 'WHERE ' . implode(' AND ', $conditions);

        $stmt = $this->pdo->prepare(
            'SELECT p.*, s.key_name AS system_key, s.name AS system_name,
                    (SELECT COUNT(*) FROM ids_group_permissions gp WHERE gp.permission_id = p.id) AS group_count
             FROM ids_permissions p
             JOIN ids_systems s ON s.id = p.system_id
             ' . $where . '
             ORDER BY s.sorting ASC, s.key_name ASC, p.category ASC, p.key_name ASC'
        );
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /** @return array<string,mixed> */
    public function permission(int $id): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT p.*, s.key_name AS system_key, s.name AS system_name
             FROM ids_permissions p
             JOIN ids_systems s ON s.id = p.system_id
             WHERE p.id = :id
             LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return is_array($row) ? $row : [];
    }

    /** @param array<string,mixed> $data */
    public function createPermission(array $data): int
    {
        $data = $this->normalizePermissionData($data);
        $this->assertPermissionBelongsToSystemKey($data['system_id'], $data['key_name']);
        $stmt = $this->pdo->prepare(
            'INSERT INTO ids_permissions (system_id, key_name, name, description, category, is_active, is_system, deprecated_at, deprecated_reason)
             VALUES (:system_id, :key_name, :name, :description, :category, :is_active, :is_system, :deprecated_at, :deprecated_reason)'
        );
        $stmt->execute($data);

        return (int) $this->pdo->lastInsertId();
    }

    /** @param array<string,mixed> $data */
    public function updatePermission(int $id, array $data): void
    {
        $data = $this->normalizePermissionData($data);
        $data['id'] = $id;
        $this->assertPermissionBelongsToSystemKey($data['system_id'], $data['key_name']);
        $stmt = $this->pdo->prepare(
            'UPDATE ids_permissions
             SET system_id = :system_id,
                 key_name = :key_name,
                 name = :name,
                 description = :description,
                 category = :category,
                 is_active = :is_active,
                 is_system = :is_system,
                 deprecated_at = :deprecated_at,
                 deprecated_reason = :deprecated_reason,
                 updated_at = CURRENT_TIMESTAMP
             WHERE id = :id'
        );
        $stmt->execute($data);
        $this->bumpSubjectsForPermission($id);
    }

    public function deactivatePermission(int $id, ?string $reason = null): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE ids_permissions
             SET is_active = 0,
                 deprecated_at = COALESCE(deprecated_at, CURRENT_TIMESTAMP),
                 deprecated_reason = :reason,
                 updated_at = CURRENT_TIMESTAMP
             WHERE id = :id'
        );
        $stmt->execute(['id' => $id, 'reason' => $reason]);
        $this->bumpSubjectsForPermission($id);
    }

    /** @return int[] */
    public function permissionIdsForGroup(int $groupId): array
    {
        $stmt = $this->pdo->prepare('SELECT permission_id FROM ids_group_permissions WHERE group_id = :group_id ORDER BY permission_id ASC');
        $stmt->execute(['group_id' => $groupId]);

        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN) ?: []);
    }

    /** @param int[] $permissionIds */
    public function syncGroupPermissions(int $groupId, array $permissionIds): void
    {
        $group = $this->group($groupId);
        if ($group === []) {
            throw new RuntimeException('Gruppe nicht gefunden.');
        }

        $permissionIds = array_values(array_unique(array_map('intval', $permissionIds)));
        $this->assertPermissionsBelongToSystem((int) $group['system_id'], $permissionIds);

        $this->pdo->beginTransaction();
        try {
            $delete = $this->pdo->prepare('DELETE FROM ids_group_permissions WHERE group_id = :group_id');
            $delete->execute(['group_id' => $groupId]);

            if ($permissionIds !== []) {
                $insert = $this->pdo->prepare(
                    'INSERT INTO ids_group_permissions (group_id, permission_id)
                     VALUES (:group_id, :permission_id)'
                );
                foreach ($permissionIds as $permissionId) {
                    $insert->execute(['group_id' => $groupId, 'permission_id' => $permissionId]);
                }
            }

            $this->bumpSubjectsForGroup($groupId);
            $this->pdo->commit();
        } catch (\Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }

    /** @return array<int,array<string,mixed>> */
    public function persons(string $query = '', int $limit = 100): array
    {
        $query = trim($query);
        $limit = max(1, min(250, $limit));
        $where = '';
        $params = [];
        if ($query !== '') {
            $where = 'WHERE p.display_name LIKE :q OR u.email LIKE :q OR CAST(p.id AS CHAR) = :id_exact';
            $params = ['q' => '%' . $query . '%', 'id_exact' => $query];
        }

        $stmt = $this->pdo->prepare(
            'SELECT p.id, p.subject_id, p.display_name, p.status, u.email,
                    s.uuid AS subject_uuid, s.status AS subject_status,
                    (SELECT COUNT(*) FROM ids_subject_groups sg WHERE sg.subject_id = p.subject_id AND (sg.expires_at IS NULL OR sg.expires_at > CURRENT_TIMESTAMP)) AS active_group_count
             FROM ids_persons p
             LEFT JOIN ids_users u ON u.person_id = p.id
             LEFT JOIN ids_subjects s ON s.id = p.subject_id
             ' . $where . '
             ORDER BY p.updated_at DESC, p.id DESC
             LIMIT ' . $limit
        );
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /** @return array<string,mixed> */
    public function personWithSubject(int $personId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT p.id AS person_id, p.display_name, p.status AS person_status, p.subject_id,
                    u.id AS user_id, u.email, u.status AS user_status,
                    s.uuid AS subject_uuid, s.status AS subject_status, s.permission_version
             FROM ids_persons p
             LEFT JOIN ids_users u ON u.person_id = p.id
             LEFT JOIN ids_subjects s ON s.id = p.subject_id
             WHERE p.id = :person_id
             LIMIT 1'
        );
        $stmt->execute(['person_id' => $personId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return is_array($row) ? $row : [];
    }

    /** @return array<string,mixed> */
    public function subject(int $subjectId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT s.*, p.id AS person_id, p.display_name, p.status AS person_status, u.email, u.status AS user_status
             FROM ids_subjects s
             LEFT JOIN ids_persons p ON p.subject_id = s.id
             LEFT JOIN ids_users u ON u.person_id = p.id
             WHERE s.id = :subject_id
             LIMIT 1'
        );
        $stmt->execute(['subject_id' => $subjectId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return is_array($row) ? $row : [];
    }

    /** @return array<int,array<string,mixed>> */
    public function subjectGroups(int $subjectId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT sg.*, g.key_name AS group_key, g.name AS group_name,
                    g.is_system, g.is_active AS group_is_active,
                    s.key_name AS system_key, s.name AS system_name,
                    assigner.uuid AS assigned_by_subject_uuid,
                    CASE WHEN sg.expires_at IS NOT NULL AND sg.expires_at <= CURRENT_TIMESTAMP THEN 1 ELSE 0 END AS is_expired
             FROM ids_subject_groups sg
             JOIN ids_groups g ON g.id = sg.group_id
             JOIN ids_systems s ON s.id = g.system_id
             LEFT JOIN ids_subjects assigner ON assigner.id = sg.assigned_by_subject_id
             WHERE sg.subject_id = :subject_id
             ORDER BY s.sorting ASC, s.key_name ASC, g.sorting ASC, g.key_name ASC'
        );
        $stmt->execute(['subject_id' => $subjectId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function assignGroupToPerson(int $personId, int $groupId, ?int $actorSubjectId, ?string $expiresAt, ?string $note): void
    {
        $subjectId = $this->identity->ensureSubjectForPerson($personId);
        $group = $this->group($groupId);
        if ($group === [] || (int) $group['is_active'] !== 1 || (int) $group['is_assignable'] !== 1) {
            throw new RuntimeException('Diese Gruppe kann nicht zugewiesen werden.');
        }

        $expiresAt = $this->normalizeNullableDateTime($expiresAt);
        $stmt = $this->pdo->prepare(
            'INSERT INTO ids_subject_groups (subject_id, group_id, assigned_by_subject_id, assigned_at, expires_at, note)
             VALUES (:subject_id, :group_id, :assigned_by_subject_id, CURRENT_TIMESTAMP, :expires_at, :note)
             ON DUPLICATE KEY UPDATE
                assigned_by_subject_id = VALUES(assigned_by_subject_id),
                expires_at = VALUES(expires_at),
                note = VALUES(note),
                updated_at = CURRENT_TIMESTAMP'
        );
        $stmt->execute([
            'subject_id' => $subjectId,
            'group_id' => $groupId,
            'assigned_by_subject_id' => $actorSubjectId,
            'expires_at' => $expiresAt,
            'note' => $note,
        ]);

        $this->identity->bumpPermissionVersion($subjectId);
    }

    public function removeGroupFromSubject(int $subjectId, int $groupId): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM ids_subject_groups WHERE subject_id = :subject_id AND group_id = :group_id');
        $stmt->execute(['subject_id' => $subjectId, 'group_id' => $groupId]);
        $this->identity->bumpPermissionVersion($subjectId);
    }

    public function countActiveSubjectsInGroup(string $systemKey, string $groupKey, ?int $excludeSubjectId = null): int
    {
        $params = ['system_key' => $systemKey, 'group_key' => $groupKey];
        $exclude = '';
        if ($excludeSubjectId !== null) {
            $exclude = 'AND sub.id <> :exclude_subject_id';
            $params['exclude_subject_id'] = $excludeSubjectId;
        }

        $stmt = $this->pdo->prepare(
            'SELECT COUNT(DISTINCT sub.id)
             FROM ids_subjects sub
             JOIN ids_subject_groups sg ON sg.subject_id = sub.id
             JOIN ids_groups g ON g.id = sg.group_id
             JOIN ids_systems sys ON sys.id = g.system_id
             WHERE sub.status = "active"
               AND sys.key_name = :system_key
               AND g.key_name = :group_key
               AND sys.is_active = 1
               AND g.is_active = 1
               AND (sg.expires_at IS NULL OR sg.expires_at > CURRENT_TIMESTAMP)
               ' . $exclude
        );
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    public function countActiveSubjectsWithPermission(string $permissionKey, ?int $excludeSubjectId = null): int
    {
        $params = ['permission_key' => $permissionKey];
        $exclude = '';
        if ($excludeSubjectId !== null) {
            $exclude = 'AND sub.id <> :exclude_subject_id';
            $params['exclude_subject_id'] = $excludeSubjectId;
        }

        $stmt = $this->pdo->prepare(
            'SELECT COUNT(DISTINCT sub.id)
             FROM ids_subjects sub
             JOIN ids_subject_groups sg ON sg.subject_id = sub.id
             JOIN ids_groups g ON g.id = sg.group_id
             JOIN ids_systems sys ON sys.id = g.system_id
             JOIN ids_group_permissions gp ON gp.group_id = g.id
             JOIN ids_permissions p ON p.id = gp.permission_id AND p.system_id = g.system_id
             WHERE sub.status = "active"
               AND sys.is_active = 1
               AND g.is_active = 1
               AND p.is_active = 1
               AND p.key_name = :permission_key
               AND (sg.expires_at IS NULL OR sg.expires_at > CURRENT_TIMESTAMP)
               ' . $exclude
        );
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    /** @return string[] */
    public function permissionKeysForGroup(int $groupId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT p.key_name
             FROM ids_group_permissions gp
             JOIN ids_permissions p ON p.id = gp.permission_id
             WHERE gp.group_id = :group_id
             ORDER BY p.key_name ASC'
        );
        $stmt->execute(['group_id' => $groupId]);

        return array_map('strval', $stmt->fetchAll(PDO::FETCH_COLUMN) ?: []);
    }

    public function bumpSubjectsForGroup(int $groupId): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE ids_subjects sub
             JOIN ids_subject_groups sg ON sg.subject_id = sub.id
             SET sub.permission_version = sub.permission_version + 1
             WHERE sg.group_id = :group_id'
        );
        $stmt->execute(['group_id' => $groupId]);
    }

    private function bumpSubjectsForPermission(int $permissionId): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE ids_subjects sub
             JOIN ids_subject_groups sg ON sg.subject_id = sub.id
             JOIN ids_group_permissions gp ON gp.group_id = sg.group_id
             SET sub.permission_version = sub.permission_version + 1
             WHERE gp.permission_id = :permission_id'
        );
        $stmt->execute(['permission_id' => $permissionId]);
    }

    private function countTable(string $table): int
    {
        $stmt = $this->pdo->query('SELECT COUNT(*) FROM ' . $table);
        return (int) $stmt->fetchColumn();
    }

    private function countExpiredAssignments(): int
    {
        $stmt = $this->pdo->query('SELECT COUNT(*) FROM ids_subject_groups WHERE expires_at IS NOT NULL AND expires_at <= CURRENT_TIMESTAMP');
        return (int) $stmt->fetchColumn();
    }

    /** @param array<string,mixed> $data @return array<string,mixed> */
    private function normalizeSystemData(array $data): array
    {
        $key = $this->normalizeKey((string) ($data['key_name'] ?? ''));
        $name = trim((string) ($data['name'] ?? ''));
        if ($name === '') {
            throw new InvalidArgumentException('Name ist erforderlich.');
        }

        return [
            'key_name' => $key,
            'name' => $name,
            'description' => $this->nullableString($data['description'] ?? null),
            'is_active' => $this->boolInt($data['is_active'] ?? 0),
            'is_external' => $this->boolInt($data['is_external'] ?? 0),
            'sorting' => (int) ($data['sorting'] ?? 100),
        ];
    }

    /** @param array<string,mixed> $data @return array<string,mixed> */
    private function normalizeGroupData(array $data): array
    {
        $systemId = (int) ($data['system_id'] ?? 0);
        if ($systemId <= 0 || $this->system($systemId) === []) {
            throw new InvalidArgumentException('System ist erforderlich.');
        }

        $key = $this->normalizeKey((string) ($data['key_name'] ?? ''));
        $name = trim((string) ($data['name'] ?? ''));
        if ($name === '') {
            throw new InvalidArgumentException('Name ist erforderlich.');
        }

        return [
            'system_id' => $systemId,
            'key_name' => $key,
            'name' => $name,
            'description' => $this->nullableString($data['description'] ?? null),
            'sorting' => (int) ($data['sorting'] ?? 100),
            'is_active' => $this->boolInt($data['is_active'] ?? 0),
            'is_system' => $this->boolInt($data['is_system'] ?? 0),
            'is_default' => $this->boolInt($data['is_default'] ?? 0),
            'is_assignable' => $this->boolInt($data['is_assignable'] ?? 0),
        ];
    }

    /** @param array<string,mixed> $data @return array<string,mixed> */
    private function normalizePermissionData(array $data): array
    {
        $systemId = (int) ($data['system_id'] ?? 0);
        if ($systemId <= 0 || $this->system($systemId) === []) {
            throw new InvalidArgumentException('System ist erforderlich.');
        }

        $key = trim((string) ($data['key_name'] ?? ''));
        if (!preg_match('/^[a-z0-9][a-z0-9-]*(\.[a-z0-9][a-z0-9-]*)+$/', $key)) {
            throw new InvalidArgumentException('Ungültiger Permission-Key.');
        }
        $name = trim((string) ($data['name'] ?? ''));
        if ($name === '') {
            throw new InvalidArgumentException('Name ist erforderlich.');
        }

        return [
            'system_id' => $systemId,
            'key_name' => $key,
            'name' => $name,
            'description' => $this->nullableString($data['description'] ?? null),
            'category' => $this->nullableString($data['category'] ?? null),
            'is_active' => $this->boolInt($data['is_active'] ?? 0),
            'is_system' => $this->boolInt($data['is_system'] ?? 0),
            'deprecated_at' => $this->normalizeNullableDateTime($data['deprecated_at'] ?? null),
            'deprecated_reason' => $this->nullableString($data['deprecated_reason'] ?? null),
        ];
    }

    /** @param int[] $permissionIds */
    private function assertPermissionsBelongToSystem(int $systemId, array $permissionIds): void
    {
        if ($permissionIds === []) {
            return;
        }

        $placeholders = implode(',', array_fill(0, count($permissionIds), '?'));
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM ids_permissions
             WHERE system_id = ?
               AND is_active = 1
               AND deprecated_at IS NULL
               AND id IN (' . $placeholders . ')'
        );
        $stmt->execute(array_merge([$systemId], $permissionIds));

        if ((int) $stmt->fetchColumn() !== count($permissionIds)) {
            throw new InvalidArgumentException('Gruppen dürfen nur aktive, nicht abgekündigte Permissions ihres eigenen Systems erhalten.');
        }
    }

    private function assertPermissionBelongsToSystemKey(int $systemId, string $permissionKey): void
    {
        $system = $this->system($systemId);
        $systemKey = (string) ($system['key_name'] ?? '');
        if ($systemKey === '' || !str_starts_with($permissionKey, $systemKey . '.')) {
            throw new InvalidArgumentException('Permission-Key muss mit dem System-Key beginnen.');
        }
    }

    private function normalizeKey(string $key): string
    {
        $key = trim($key);
        if (!preg_match('/^[a-z0-9][a-z0-9-]*$/', $key)) {
            throw new InvalidArgumentException('Ungültiger technischer Key.');
        }

        return $key;
    }

    private function nullableString(mixed $value): ?string
    {
        $value = trim((string) ($value ?? ''));
        return $value === '' ? null : $value;
    }

    private function boolInt(mixed $value): int
    {
        return in_array((string) $value, ['1', 'true', 'yes', 'on'], true) ? 1 : 0;
    }

    private function normalizeNullableDateTime(mixed $value): ?string
    {
        $value = trim((string) ($value ?? ''));
        if ($value === '') {
            return null;
        }

        $value = str_replace('T', ' ', $value);
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) === 1) {
            return $value . ' 23:59:59';
        }
        if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/', $value) === 1) {
            return $value . ':00';
        }
        if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $value) !== 1) {
            throw new InvalidArgumentException('Ungültiges Datum/Zeit-Format.');
        }

        return $value;
    }
}
