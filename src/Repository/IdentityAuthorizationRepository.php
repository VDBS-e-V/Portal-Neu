<?php

declare(strict_types=1);

namespace App\Repository;

use InvalidArgumentException;
use PDO;
use RuntimeException;

final class IdentityAuthorizationRepository
{
    private PDO $pdo;

    /** @var array<int,array{version:int,permissions:string[],groups:array<string,string[]>}> */
    private array $subjectCache = [];

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function subjectIdForUserId(int $userId): ?int
    {
        $stmt = $this->pdo->prepare(
            'SELECT p.subject_id
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

        $subjectId = $stmt->fetchColumn();
        return $subjectId === false ? null : (int) $subjectId;
    }

    /** @return array<string,mixed> */
    public function subjectForUserId(int $userId): array
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
                u.email
             FROM ids_users u
             JOIN ids_persons p ON p.id = u.person_id
             JOIN ids_subjects s ON s.id = p.subject_id
             WHERE u.id = :user_id
             LIMIT 1'
        );
        $stmt->execute(['user_id' => $userId]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return is_array($row) ? $row : [];
    }

    public function permissionVersion(int $subjectId): int
    {
        $stmt = $this->pdo->prepare('SELECT permission_version FROM ids_subjects WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $subjectId]);

        $value = $stmt->fetchColumn();
        return $value === false ? 0 : (int) $value;
    }

    public function bumpPermissionVersion(int $subjectId): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE ids_subjects
             SET permission_version = permission_version + 1
             WHERE id = :subject_id'
        );
        $stmt->execute(['subject_id' => $subjectId]);
        unset($this->subjectCache[$subjectId]);
    }

    public function canSubject(int $subjectId, string $permissionKey): bool
    {
        $permissionKey = $this->normalizePermissionKey($permissionKey);
        return in_array($permissionKey, $this->permissionsForSubject($subjectId), true);
    }

    /** @return string[] */
    public function permissionsForSubject(int $subjectId): array
    {
        return $this->loadEffectiveAccess($subjectId)['permissions'];
    }

    /** @return string[] */
    public function permissionsForSubjectAndSystem(int $subjectId, string $systemKey): array
    {
        $systemKey = $this->normalizeKey($systemKey, 'systemKey');
        return array_values(array_filter(
            $this->permissionsForSubject($subjectId),
            static fn (string $permission): bool => str_starts_with($permission, $systemKey . '.')
        ));
    }

    /** @return array<string,string[]> keyed by system key */
    public function groupsForSubject(int $subjectId): array
    {
        return $this->loadEffectiveAccess($subjectId)['groups'];
    }

    /** @return string[] */
    public function groupsForSubjectAndSystem(int $subjectId, string $systemKey): array
    {
        $systemKey = $this->normalizeKey($systemKey, 'systemKey');
        return $this->groupsForSubject($subjectId)[$systemKey] ?? [];
    }

    public function ensureSubjectForPerson(int $personId): int
    {
        $this->pdo->beginTransaction();

        try {
            $stmt = $this->pdo->prepare('SELECT id, person_uuid, status, subject_id FROM ids_persons WHERE id = :person_id FOR UPDATE');
            $stmt->execute(['person_id' => $personId]);
            $person = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!is_array($person)) {
                throw new RuntimeException('Person nicht gefunden.');
            }

            if (!empty($person['subject_id'])) {
                $this->pdo->commit();
                return (int) $person['subject_id'];
            }

            $uuid = $this->uuidFromPersonUuid((string) $person['person_uuid']);
            $status = match ((string) $person['status']) {
                'disabled' => 'disabled',
                'erased', 'erasure_requested' => 'deleted',
                default => 'active',
            };

            $insert = $this->pdo->prepare(
                'INSERT INTO ids_subjects (uuid, status)
                 VALUES (:uuid, :status)
                 ON DUPLICATE KEY UPDATE updated_at = CURRENT_TIMESTAMP'
            );
            $insert->execute(['uuid' => $uuid, 'status' => $status]);

            $subjectId = (int) $this->pdo
                ->query('SELECT id FROM ids_subjects WHERE uuid = ' . $this->pdo->quote($uuid) . ' LIMIT 1')
                ->fetchColumn();

            $update = $this->pdo->prepare('UPDATE ids_persons SET subject_id = :subject_id WHERE id = :person_id');
            $update->execute(['subject_id' => $subjectId, 'person_id' => $personId]);

            $this->assignDefaultGroups($subjectId);
            $this->pdo->commit();

            return $subjectId;
        } catch (\Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }

    public function assignDefaultGroups(int $subjectId): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT IGNORE INTO ids_subject_groups (subject_id, group_id, assigned_at, note)
             SELECT :subject_id, g.id, CURRENT_TIMESTAMP, :note
             FROM ids_groups g
             JOIN ids_systems s ON s.id = g.system_id
             WHERE g.is_active = 1
               AND g.is_default = 1
               AND g.is_assignable = 1
               AND s.is_active = 1'
        );
        $stmt->execute([
            'subject_id' => $subjectId,
            'note' => 'Automatisch über ids_groups.is_default zugewiesen.',
        ]);

        $this->bumpPermissionVersion($subjectId);
    }

    public function assignGroupToSubject(
        int $subjectId,
        string $systemKey,
        string $groupKey,
        ?int $assignedBySubjectId = null,
        ?string $expiresAt = null,
        ?string $note = null
    ): void {
        $groupId = $this->groupId($systemKey, $groupKey);

        if ($groupId === null) {
            throw new RuntimeException(sprintf('Gruppe nicht gefunden: %s.%s', $systemKey, $groupKey));
        }

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
            'assigned_by_subject_id' => $assignedBySubjectId,
            'expires_at' => $expiresAt,
            'note' => $note,
        ]);

        $this->bumpPermissionVersion($subjectId);
    }

    public function removeGroupFromSubject(int $subjectId, string $systemKey, string $groupKey): void
    {
        $groupId = $this->groupId($systemKey, $groupKey);

        if ($groupId === null) {
            return;
        }

        $stmt = $this->pdo->prepare(
            'DELETE FROM ids_subject_groups
             WHERE subject_id = :subject_id
               AND group_id = :group_id'
        );
        $stmt->execute(['subject_id' => $subjectId, 'group_id' => $groupId]);

        $this->bumpPermissionVersion($subjectId);
    }

    public function groupId(string $systemKey, string $groupKey): ?int
    {
        $systemKey = $this->normalizeKey($systemKey, 'systemKey');
        $groupKey = $this->normalizeKey($groupKey, 'groupKey');

        $stmt = $this->pdo->prepare(
            'SELECT g.id
             FROM ids_groups g
             JOIN ids_systems s ON s.id = g.system_id
             WHERE s.key_name = :system_key
               AND g.key_name = :group_key
               AND s.is_active = 1
               AND g.is_active = 1
             LIMIT 1'
        );
        $stmt->execute(['system_key' => $systemKey, 'group_key' => $groupKey]);

        $id = $stmt->fetchColumn();
        return $id === false ? null : (int) $id;
    }

    /** @return array{id:int,key_name:string,name:string}|null */
    public function permissionByKey(string $permissionKey): ?array
    {
        $permissionKey = $this->normalizePermissionKey($permissionKey);

        $stmt = $this->pdo->prepare(
            'SELECT p.id, p.key_name, p.name
             FROM ids_permissions p
             JOIN ids_systems s ON s.id = p.system_id
             WHERE p.key_name = :permission_key
               AND p.is_active = 1
               AND s.is_active = 1
             LIMIT 1'
        );
        $stmt->execute(['permission_key' => $permissionKey]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return is_array($row) ? $row : null;
    }

    /** @return array{version:int,permissions:string[],groups:array<string,string[]>} */
    private function loadEffectiveAccess(int $subjectId): array
    {
        $version = $this->permissionVersion($subjectId);
        $cached = $this->subjectCache[$subjectId] ?? null;

        if (is_array($cached) && $cached['version'] === $version) {
            return $cached;
        }

        $stmt = $this->pdo->prepare(
            'SELECT DISTINCT p.key_name
             FROM ids_subjects sub
             JOIN ids_subject_groups sg ON sg.subject_id = sub.id
             JOIN ids_groups g ON g.id = sg.group_id
             JOIN ids_systems sys ON sys.id = g.system_id
             JOIN ids_group_permissions gp ON gp.group_id = g.id
             JOIN ids_permissions p ON p.id = gp.permission_id AND p.system_id = g.system_id
             WHERE sub.id = :subject_id
               AND sub.status = :subject_status
               AND sys.is_active = 1
               AND g.is_active = 1
               AND p.is_active = 1
               AND (sg.expires_at IS NULL OR sg.expires_at > CURRENT_TIMESTAMP)
             ORDER BY p.key_name ASC'
        );
        $stmt->execute(['subject_id' => $subjectId, 'subject_status' => 'active']);
        $permissions = array_values(array_map('strval', $stmt->fetchAll(PDO::FETCH_COLUMN) ?: []));

        $groupStmt = $this->pdo->prepare(
            'SELECT sys.key_name AS system_key, g.key_name AS group_key
             FROM ids_subjects sub
             JOIN ids_subject_groups sg ON sg.subject_id = sub.id
             JOIN ids_groups g ON g.id = sg.group_id
             JOIN ids_systems sys ON sys.id = g.system_id
             WHERE sub.id = :subject_id
               AND sub.status = :subject_status
               AND sys.is_active = 1
               AND g.is_active = 1
               AND (sg.expires_at IS NULL OR sg.expires_at > CURRENT_TIMESTAMP)
             ORDER BY sys.key_name ASC, g.sorting ASC, g.key_name ASC'
        );
        $groupStmt->execute(['subject_id' => $subjectId, 'subject_status' => 'active']);

        $groups = [];
        foreach ($groupStmt->fetchAll(PDO::FETCH_ASSOC) ?: [] as $row) {
            $systemKey = (string) $row['system_key'];
            $groups[$systemKey] ??= [];
            $groups[$systemKey][] = (string) $row['group_key'];
        }

        $access = [
            'version' => $version,
            'permissions' => $permissions,
            'groups' => $groups,
        ];

        $this->subjectCache[$subjectId] = $access;
        return $access;
    }

    private function normalizeKey(string $key, string $fieldName): string
    {
        $key = trim($key);
        if (!preg_match('/^[a-z0-9][a-z0-9-]*$/', $key)) {
            throw new InvalidArgumentException(sprintf('Ungültiger %s: %s', $fieldName, $key));
        }

        return $key;
    }

    private function normalizePermissionKey(string $permissionKey): string
    {
        $permissionKey = trim($permissionKey);
        if (!preg_match('/^[a-z0-9][a-z0-9-]*(\.[a-z0-9][a-z0-9-]*)+$/', $permissionKey)) {
            throw new InvalidArgumentException(sprintf('Ungültiger Permission-Key: %s', $permissionKey));
        }

        return $permissionKey;
    }

    private function uuidFromPersonUuid(string $binaryUuid): string
    {
        $hex = bin2hex($binaryUuid);

        if (strlen($hex) !== 32) {
            return $this->newUuidV4();
        }

        return sprintf(
            '%s-%s-%s-%s-%s',
            substr($hex, 0, 8),
            substr($hex, 8, 4),
            substr($hex, 12, 4),
            substr($hex, 16, 4),
            substr($hex, 20, 12)
        );
    }

    private function newUuidV4(): string
    {
        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);
        $hex = bin2hex($data);

        return sprintf(
            '%s-%s-%s-%s-%s',
            substr($hex, 0, 8),
            substr($hex, 8, 4),
            substr($hex, 12, 4),
            substr($hex, 16, 4),
            substr($hex, 20, 12)
        );
    }
}
