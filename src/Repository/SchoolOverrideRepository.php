<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class SchoolOverrideRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function findBySchoolIndexed(int $schoolId): array
    {
        $statement = $this->pdo->prepare("
            SELECT o.*, p.display_name AS created_by_name
            FROM cod_school_overrides o
            LEFT JOIN ids_persons p ON p.id = o.created_by_person_id
            WHERE o.school_id = :school_id
            ORDER BY o.field_key ASC
        ");
        $statement->execute(['school_id' => $schoolId]);

        $indexed = [];
        foreach ($statement->fetchAll() as $row) {
            $indexed[(string) $row['field_key']] = $row;
        }

        return $indexed;
    }

    public function upsert(
        int $schoolId,
        string $fieldKey,
        ?string $overrideValue,
        ?string $reason,
        ?int $personId
    ): void {
        $statement = $this->pdo->prepare("
            INSERT INTO cod_school_overrides
                (school_id, field_key, override_value, reason, created_by_person_id)
            VALUES
                (:school_id, :field_key, :override_value, :reason, :created_by_person_id)
            ON DUPLICATE KEY UPDATE
                override_value = VALUES(override_value),
                reason = VALUES(reason),
                created_by_person_id = VALUES(created_by_person_id),
                updated_at = CURRENT_TIMESTAMP
        ");

        $statement->execute([
            'school_id' => $schoolId,
            'field_key' => $fieldKey,
            'override_value' => $overrideValue,
            'reason' => $reason,
            'created_by_person_id' => $personId,
        ]);
    }

    public function deleteByField(int $schoolId, string $fieldKey): void
    {
        $statement = $this->pdo->prepare("
            DELETE FROM cod_school_overrides
            WHERE school_id = :school_id AND field_key = :field_key
        ");
        $statement->execute([
            'school_id' => $schoolId,
            'field_key' => $fieldKey,
        ]);
    }
}
