<?php

declare(strict_types=1);

namespace App\Repository;

use InvalidArgumentException;
use PDO;
use RuntimeException;

final class AccountProfileRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function profileForUser(int $userId): array
    {
        if ($userId <= 0) {
            return [];
        }

        if ($this->tableExists('ids_persons') && $this->columnExists('ids_users', 'person_id')) {
            $stmt = $this->pdo->prepare(
                'SELECT
                    u.id AS user_id,
                    u.email AS login_email,
                    u.status AS login_status,
                    u.last_login_at,
                    u.created_at AS user_created_at,
                    p.id AS person_id,
                    p.display_name,
                    p.status AS person_status,
                    p.created_at AS person_created_at,
                    n.salutation,
                    n.title,
                    n.first_name,
                    n.middle_name,
                    n.last_name,
                    n.preferred_name,
                    n.pronouns
                 FROM ids_users u
                 LEFT JOIN ids_persons p ON p.id = u.person_id
                 LEFT JOIN ids_person_names n ON n.person_id = p.id
                 WHERE u.id = :user_id
                 LIMIT 1'
            );
            $stmt->execute(['user_id' => $userId]);

            return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
        }

        $stmt = $this->pdo->prepare(
            'SELECT
                u.id AS user_id,
                u.id AS person_id,
                u.email AS login_email,
                u.status AS login_status,
                u.last_login_at,
                u.created_at AS user_created_at,
                u.display_name,
                u.status AS person_status,
                n.salutation,
                n.title,
                n.first_name,
                n.middle_name,
                n.last_name,
                n.preferred_name,
                n.pronouns
             FROM ids_users u
             LEFT JOIN ids_user_names n ON n.user_id = u.id
             WHERE u.id = :user_id
             LIMIT 1'
        );
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * @param array<string, mixed> $data
     */
    public function updateProfileForUser(int $userId, array $data): bool
    {
        $profile = $this->profileForUser($userId);

        if ($profile === []) {
            throw new InvalidArgumentException('Account nicht gefunden.');
        }

        $personId = (int) ($profile['person_id'] ?? 0);

        if ($personId <= 0) {
            throw new InvalidArgumentException('Keine Person zum Account gefunden.');
        }

        $this->pdo->beginTransaction();

        try {
            if ($this->tableExists('ids_persons')) {
                $stmt = $this->pdo->prepare(
                    'UPDATE ids_persons
                     SET display_name = :display_name
                     WHERE id = :id'
                );
                $stmt->execute([
                    'id' => $personId,
                    'display_name' => $this->nullableString($data['display_name'] ?? null),
                ]);

                $this->savePersonName($personId, $data);
            } else {
                $stmt = $this->pdo->prepare(
                    'UPDATE ids_users
                     SET display_name = :display_name
                     WHERE id = :id'
                );
                $stmt->execute([
                    'id' => $userId,
                    'display_name' => $this->nullableString($data['display_name'] ?? null),
                ]);

                $this->saveLegacyUserName($userId, $data);
            }

            $this->pdo->commit();

            return true;
        } catch (\Throwable $throwable) {
            $this->pdo->rollBack();
            throw $throwable;
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function contactsForUser(int $userId): array
    {
        $profile = $this->profileForUser($userId);
        $personId = (int) ($profile['person_id'] ?? 0);

        if ($personId <= 0) {
            return [];
        }

        [$table, $foreignColumn] = $this->contactTable();

        $stmt = $this->pdo->prepare(
            sprintf(
                'SELECT *
                 FROM %s
                 WHERE %s = :person_id
                 ORDER BY is_primary DESC, contact_type ASC, label ASC, id ASC',
                $table,
                $foreignColumn
            )
        );
        $stmt->execute(['person_id' => $personId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * @param array<string, mixed> $data
     */
    public function createContactForUser(int $userId, array $data): int
    {
        $profile = $this->profileForUser($userId);
        $personId = (int) ($profile['person_id'] ?? 0);

        if ($personId <= 0) {
            throw new InvalidArgumentException('Keine Person zum Account gefunden.');
        }

        [$table, $foreignColumn] = $this->contactTable();

        $contactType = $this->normalizeContactType((string) ($data['contact_type'] ?? 'email'));
        $value = trim((string) ($data['value'] ?? ''));

        if ($value === '') {
            throw new InvalidArgumentException('Kontaktwert darf nicht leer sein.');
        }

        $this->pdo->beginTransaction();

        try {
            if (!empty($data['is_primary'])) {
                $stmt = $this->pdo->prepare(
                    sprintf(
                        'UPDATE %s
                         SET is_primary = 0
                         WHERE %s = :person_id
                           AND contact_type = :contact_type',
                        $table,
                        $foreignColumn
                    )
                );
                $stmt->execute([
                    'person_id' => $personId,
                    'contact_type' => $contactType,
                ]);
            }

            $stmt = $this->pdo->prepare(
                sprintf(
                    'INSERT INTO %s
                        (%s, contact_type, label, value, is_primary, is_verified)
                     VALUES
                        (:person_id, :contact_type, :label, :value, :is_primary, :is_verified)',
                    $table,
                    $foreignColumn
                )
            );
            $stmt->execute([
                'person_id' => $personId,
                'contact_type' => $contactType,
                'label' => $this->nullableString($data['label'] ?? null),
                'value' => $value,
                'is_primary' => !empty($data['is_primary']) ? 1 : 0,
                'is_verified' => 0,
            ]);

            $id = (int) $this->pdo->lastInsertId();

            $this->pdo->commit();

            return $id;
        } catch (\Throwable $throwable) {
            $this->pdo->rollBack();
            throw $throwable;
        }
    }

    public function deleteContactForUser(int $userId, int $contactId): bool
    {
        $profile = $this->profileForUser($userId);
        $personId = (int) ($profile['person_id'] ?? 0);

        if ($personId <= 0 || $contactId <= 0) {
            return false;
        }

        [$table, $foreignColumn] = $this->contactTable();

        $stmt = $this->pdo->prepare(
            sprintf(
                'DELETE FROM %s
                 WHERE id = :id
                   AND %s = :person_id',
                $table,
                $foreignColumn
            )
        );

        return (bool) $stmt->execute([
            'id' => $contactId,
            'person_id' => $personId,
        ]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function addressesForUser(int $userId): array
    {
        $profile = $this->profileForUser($userId);
        $personId = (int) ($profile['person_id'] ?? 0);

        if ($personId <= 0) {
            return [];
        }

        [$table, $foreignColumn] = $this->addressTable();

        $stmt = $this->pdo->prepare(
            sprintf(
                'SELECT *
                 FROM %s
                 WHERE %s = :person_id
                 ORDER BY is_primary DESC, address_type ASC, id ASC',
                $table,
                $foreignColumn
            )
        );
        $stmt->execute(['person_id' => $personId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * @param array<string, mixed> $data
     */
    public function createAddressForUser(int $userId, array $data): int
    {
        $profile = $this->profileForUser($userId);
        $personId = (int) ($profile['person_id'] ?? 0);

        if ($personId <= 0) {
            throw new InvalidArgumentException('Keine Person zum Account gefunden.');
        }

        [$table, $foreignColumn] = $this->addressTable();
        $addressType = $this->normalizeAddressType((string) ($data['address_type'] ?? 'private'));

        $this->pdo->beginTransaction();

        try {
            if (!empty($data['is_primary'])) {
                $stmt = $this->pdo->prepare(
                    sprintf(
                        'UPDATE %s
                         SET is_primary = 0
                         WHERE %s = :person_id
                           AND address_type = :address_type',
                        $table,
                        $foreignColumn
                    )
                );
                $stmt->execute([
                    'person_id' => $personId,
                    'address_type' => $addressType,
                ]);
            }

            $columns = [
                $foreignColumn => $personId,
                'address_type' => $addressType,
                'recipient_name' => $this->nullableString($data['recipient_name'] ?? null),
                'organization' => $this->nullableString($data['organization'] ?? null),
                'street' => $this->nullableString($data['street'] ?? null),
                'house_number' => $this->nullableString($data['house_number'] ?? null),
                'address_addition' => $this->nullableString($data['address_addition'] ?? null),
                'postal_code' => $this->nullableString($data['postal_code'] ?? null),
                'city' => $this->nullableString($data['city'] ?? null),
                'state' => $this->nullableString($data['state'] ?? null),
                'country' => strtoupper(trim((string) ($data['country'] ?? 'DE'))) ?: 'DE',
                'is_primary' => !empty($data['is_primary']) ? 1 : 0,
            ];

            foreach (array_keys($columns) as $column) {
                if ($column !== $foreignColumn && !$this->columnExists($table, $column)) {
                    unset($columns[$column]);
                }
            }

            $stmt = $this->pdo->prepare(
                sprintf(
                    'INSERT INTO %s (%s) VALUES (%s)',
                    $table,
                    implode(', ', array_map(static fn (string $column): string => '`' . $column . '`', array_keys($columns))),
                    implode(', ', array_map(static fn (string $column): string => ':' . $column, array_keys($columns)))
                )
            );
            $stmt->execute($columns);

            $id = (int) $this->pdo->lastInsertId();

            $this->pdo->commit();

            return $id;
        } catch (\Throwable $throwable) {
            $this->pdo->rollBack();
            throw $throwable;
        }
    }

    public function deleteAddressForUser(int $userId, int $addressId): bool
    {
        $profile = $this->profileForUser($userId);
        $personId = (int) ($profile['person_id'] ?? 0);

        if ($personId <= 0 || $addressId <= 0) {
            return false;
        }

        [$table, $foreignColumn] = $this->addressTable();

        $stmt = $this->pdo->prepare(
            sprintf(
                'DELETE FROM %s
                 WHERE id = :id
                   AND %s = :person_id',
                $table,
                $foreignColumn
            )
        );

        return (bool) $stmt->execute([
            'id' => $addressId,
            'person_id' => $personId,
        ]);
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
            'salutation' => $this->nullableString($data['salutation'] ?? null),
            'title' => $this->nullableString($data['title'] ?? null),
            'first_name' => $this->nullableString($data['first_name'] ?? null),
            'middle_name' => $this->nullableString($data['middle_name'] ?? null),
            'last_name' => $this->nullableString($data['last_name'] ?? null),
            'preferred_name' => $this->nullableString($data['preferred_name'] ?? null),
            'pronouns' => $this->nullableString($data['pronouns'] ?? null),
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
            'salutation' => $this->nullableString($data['salutation'] ?? null),
            'title' => $this->nullableString($data['title'] ?? null),
            'first_name' => $this->nullableString($data['first_name'] ?? null),
            'middle_name' => $this->nullableString($data['middle_name'] ?? null),
            'last_name' => $this->nullableString($data['last_name'] ?? null),
            'preferred_name' => $this->nullableString($data['preferred_name'] ?? null),
            'pronouns' => $this->nullableString($data['pronouns'] ?? null),
        ]);
    }

    /**
     * @return array{0:string,1:string}
     */
    private function contactTable(): array
    {
        if ($this->tableExists('ids_person_contact_details')) {
            return ['ids_person_contact_details', 'person_id'];
        }

        if ($this->tableExists('ids_user_contact_details')) {
            return ['ids_user_contact_details', 'user_id'];
        }

        throw new RuntimeException('Keine Kontakt-Tabelle gefunden.');
    }

    /**
     * @return array{0:string,1:string}
     */
    private function addressTable(): array
    {
        if ($this->tableExists('ids_person_addresses')) {
            return ['ids_person_addresses', 'person_id'];
        }

        if ($this->tableExists('ids_user_addresses')) {
            return ['ids_user_addresses', 'user_id'];
        }

        throw new RuntimeException('Keine Adress-Tabelle gefunden.');
    }

    private function normalizeContactType(string $type): string
    {
        $type = mb_strtolower(trim($type));

        $allowed = ['email', 'phone', 'mobile', 'website', 'other'];

        return in_array($type, $allowed, true) ? $type : 'other';
    }

    private function normalizeAddressType(string $type): string
    {
        $type = mb_strtolower(trim($type));

        $allowed = ['private', 'work', 'billing', 'shipping', 'other'];

        return in_array($type, $allowed, true) ? $type : 'other';
    }

    private function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
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
