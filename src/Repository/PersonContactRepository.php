<?php

declare(strict_types=1);

namespace App\Repository;

use InvalidArgumentException;
use PDO;

final class PersonContactRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function forPerson(int $personId): array
    {
        if ($this->tableExists('ids_person_contact_details')) {
            $stmt = $this->pdo->prepare(
                'SELECT *
                 FROM ids_person_contact_details
                 WHERE person_id = :person_id
                 ORDER BY is_primary DESC, contact_type ASC, label ASC, id ASC'
            );
            $stmt->execute(['person_id' => $personId]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        }

        if ($this->tableExists('ids_user_contact_details')) {
            $stmt = $this->pdo->prepare(
                'SELECT *
                 FROM ids_user_contact_details
                 WHERE user_id = :person_id
                 ORDER BY is_primary DESC, contact_type ASC, label ASC, id ASC'
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
        $table = $this->tableExists('ids_person_contact_details')
            ? 'ids_person_contact_details'
            : 'ids_user_contact_details';

        $foreignColumn = $table === 'ids_person_contact_details' ? 'person_id' : 'user_id';

        $contactType = $this->normalizeContactType((string) ($data['contact_type'] ?? 'email'));
        $value = trim((string) ($data['value'] ?? ''));

        if ($value === '') {
            throw new InvalidArgumentException('Kontaktwert darf nicht leer sein.');
        }

        $this->pdo->beginTransaction();

        try {
            if (!empty($data['is_primary'])) {
                $this->clearPrimary($personId, $contactType);
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
                'is_verified' => !empty($data['is_verified']) ? 1 : 0,
            ]);

            $id = (int) $this->pdo->lastInsertId();

            $this->pdo->commit();

            return $id;
        } catch (\Throwable $throwable) {
            $this->pdo->rollBack();
            throw $throwable;
        }
    }

    public function delete(int $personId, int $contactId): bool
    {
        $table = $this->tableExists('ids_person_contact_details')
            ? 'ids_person_contact_details'
            : 'ids_user_contact_details';

        $foreignColumn = $table === 'ids_person_contact_details' ? 'person_id' : 'user_id';

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

    private function clearPrimary(int $personId, string $contactType): void
    {
        $table = $this->tableExists('ids_person_contact_details')
            ? 'ids_person_contact_details'
            : 'ids_user_contact_details';

        $foreignColumn = $table === 'ids_person_contact_details' ? 'person_id' : 'user_id';

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

    private function normalizeContactType(string $type): string
    {
        $type = mb_strtolower(trim($type));

        $allowed = ['email', 'phone', 'mobile', 'website', 'other'];

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