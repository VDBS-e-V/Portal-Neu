<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class PersonAddressRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function forPerson(int $personId): array
    {
        if ($this->tableExists('ids_person_addresses')) {
            $stmt = $this->pdo->prepare(
                'SELECT *
                 FROM ids_person_addresses
                 WHERE person_id = :person_id
                 ORDER BY is_primary DESC, address_type ASC, id ASC'
            );
            $stmt->execute(['person_id' => $personId]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        }

        if ($this->tableExists('ids_user_addresses')) {
            $stmt = $this->pdo->prepare(
                'SELECT *
                 FROM ids_user_addresses
                 WHERE user_id = :person_id
                 ORDER BY is_primary DESC, address_type ASC, id ASC'
            );
            $stmt->execute(['person_id' => $personId]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        }

        return [];
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(int $personId, array $data): int
    {
        $table = $this->tableExists('ids_person_addresses')
            ? 'ids_person_addresses'
            : 'ids_user_addresses';

        $foreignColumn = $table === 'ids_person_addresses' ? 'person_id' : 'user_id';
        $addressType = $this->normalizeAddressType((string) ($data['address_type'] ?? 'private'));

        $this->pdo->beginTransaction();

        try {
            if (!empty($data['is_primary'])) {
                $this->clearPrimary($personId, $addressType);
            }

            $stmt = $this->pdo->prepare(
                sprintf(
                    'INSERT INTO %s
                        (%s, address_type, recipient_name, organization, street, house_number, address_addition, postal_code, city, state, country, is_primary)
                     VALUES
                        (:person_id, :address_type, :recipient_name, :organization, :street, :house_number, :address_addition, :postal_code, :city, :state, :country, :is_primary)',
                    $table,
                    $foreignColumn
                )
            );
            $stmt->execute([
                'person_id' => $personId,
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
            ]);

            $id = (int) $this->pdo->lastInsertId();

            $this->pdo->commit();

            return $id;
        } catch (\Throwable $throwable) {
            $this->pdo->rollBack();
            throw $throwable;
        }
    }

    public function delete(int $personId, int $addressId): bool
    {
        $table = $this->tableExists('ids_person_addresses')
            ? 'ids_person_addresses'
            : 'ids_user_addresses';

        $foreignColumn = $table === 'ids_person_addresses' ? 'person_id' : 'user_id';

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

    private function clearPrimary(int $personId, string $addressType): void
    {
        $table = $this->tableExists('ids_person_addresses')
            ? 'ids_person_addresses'
            : 'ids_user_addresses';

        $foreignColumn = $table === 'ids_person_addresses' ? 'person_id' : 'user_id';

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

    private function normalizeAddressType(string $type): string
    {
        $type = mb_strtolower(trim($type));

        $allowed = ['private', 'work', 'billing', 'shipping', 'other'];

        if (!in_array($type, $allowed, true)) {
            return 'other';
        }

        return $type;
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
}