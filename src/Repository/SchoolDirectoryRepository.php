<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class SchoolDirectoryRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    /**
     * @param array<string, mixed> $filters
     * @return array<int, array<string, mixed>>
     */
    public function search(array $filters = [], int $limit = 50, int $offset = 0): array
    {
        $where = [];
        $params = [];

        $q = trim((string) ($filters['q'] ?? ''));
        if ($q !== '') {
            $where[] = "(
                s.name LIKE :q
                OR s.school_key LIKE :q
                OR si.identifier_value LIKE :q
                OR site.postal_code LIKE :q
                OR site.district LIKE :q
                OR site.locality LIKE :q
                OR site.street LIKE :q
                OR c.value LIKE :q
            )";
            $params['q'] = '%' . $q . '%';
        }

        foreach (['federal_state_code', 'lifecycle_status', 'data_status'] as $field) {
            $value = trim((string) ($filters[$field] ?? ''));
            if ($value !== '') {
                $where[] = "s.`{$field}` = :{$field}";
                $params[$field] = $value;
            }
        }

        $district = trim((string) ($filters['district'] ?? ''));
        if ($district !== '') {
            $where[] = 'site.district = :district';
            $params['district'] = $district;
        }

        $schoolType = trim((string) ($filters['school_type'] ?? ''));
        if ($schoolType !== '') {
            $where[] = 'class.school_type = :school_type';
            $params['school_type'] = $schoolType;
        }

        $sql = "
            SELECT
                s.id,
                s.school_key,
                s.name,
                s.slug,
                s.country_code,
                s.federal_state_code,
                s.lifecycle_status,
                s.data_status,
                COALESCE(name_override.override_value, s.name) AS effective_name,
                site.postal_code,
                site.city,
                site.district,
                site.locality,
                class.school_year,
                class.school_type,
                class.school_category,
                class.operator_name,
                MAX(CASE WHEN si.identifier_type = 'bsn' THEN si.identifier_value ELSE NULL END) AS bsn,
                COUNT(DISTINCT overrides.id) AS override_count
            FROM cod_schools s
            LEFT JOIN cod_school_sites site
                ON site.school_id = s.id AND site.is_primary = 1
            LEFT JOIN cod_school_classifications class
                ON class.school_id = s.id
                AND class.id = (
                    SELECT class2.id
                    FROM cod_school_classifications class2
                    WHERE class2.school_id = s.id
                    ORDER BY class2.school_year DESC, class2.id DESC
                    LIMIT 1
                )
            LEFT JOIN cod_school_identifiers si
                ON si.school_id = s.id
            LEFT JOIN cod_school_contacts c
                ON c.school_id = s.id
            LEFT JOIN cod_school_overrides name_override
                ON name_override.school_id = s.id
                AND name_override.field_key = 'display_name'
            LEFT JOIN cod_school_overrides overrides
                ON overrides.school_id = s.id
            " . ($where === [] ? '' : ' WHERE ' . implode(' AND ', $where)) . "
            GROUP BY
                s.id,
                s.school_key,
                s.name,
                s.slug,
                s.country_code,
                s.federal_state_code,
                s.lifecycle_status,
                s.data_status,
                effective_name,
                site.postal_code,
                site.city,
                site.district,
                site.locality,
                class.school_year,
                class.school_type,
                class.school_category,
                class.operator_name
            ORDER BY effective_name ASC
            LIMIT :limit OFFSET :offset
        ";

        $statement = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $statement->bindValue(':' . $key, $value);
        }
        $statement->bindValue(':limit', max(1, min(200, $limit)), PDO::PARAM_INT);
        $statement->bindValue(':offset', max(0, $offset), PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    /**
     * @param array<string, mixed> $filters
     */
    public function countSearch(array $filters = []): int
    {
        $where = [];
        $params = [];

        $q = trim((string) ($filters['q'] ?? ''));
        if ($q !== '') {
            $where[] = "(
                s.name LIKE :q
                OR s.school_key LIKE :q
                OR si.identifier_value LIKE :q
                OR site.postal_code LIKE :q
                OR site.district LIKE :q
                OR site.locality LIKE :q
                OR site.street LIKE :q
                OR c.value LIKE :q
            )";
            $params['q'] = '%' . $q . '%';
        }

        foreach (['federal_state_code', 'lifecycle_status', 'data_status'] as $field) {
            $value = trim((string) ($filters[$field] ?? ''));
            if ($value !== '') {
                $where[] = "s.`{$field}` = :{$field}";
                $params[$field] = $value;
            }
        }

        $sql = "
            SELECT COUNT(DISTINCT s.id)
            FROM cod_schools s
            LEFT JOIN cod_school_sites site ON site.school_id = s.id
            LEFT JOIN cod_school_identifiers si ON si.school_id = s.id
            LEFT JOIN cod_school_contacts c ON c.school_id = s.id
            " . ($where === [] ? '' : ' WHERE ' . implode(' AND ', $where));

        $statement = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $statement->bindValue(':' . $key, $value);
        }
        $statement->execute();

        return (int) $statement->fetchColumn();
    }

    /**
     * @return array<string, mixed>
     */
    public function find(int $schoolId): array
    {
        $statement = $this->pdo->prepare('SELECT * FROM cod_schools WHERE id = :id LIMIT 1');
        $statement->execute(['id' => $schoolId]);

        return $statement->fetch() ?: [];
    }

    public function updateStatus(int $schoolId, string $lifecycleStatus, string $dataStatus): bool
    {
        $statement = $this->pdo->prepare("
            UPDATE cod_schools
            SET lifecycle_status = :lifecycle_status,
                data_status = :data_status
            WHERE id = :id
        ");

        return $statement->execute([
            'id' => $schoolId,
            'lifecycle_status' => $lifecycleStatus,
            'data_status' => $dataStatus,
        ]);
    }

    public function markVerified(int $schoolId): bool
    {
        $statement = $this->pdo->prepare("
            UPDATE cod_schools
            SET data_status = 'manually_verified'
            WHERE id = :id
        ");

        return $statement->execute(['id' => $schoolId]);
    }

    /**
     * @return array<string, mixed>
     */
    public function primarySite(int $schoolId): array
    {
        $statement = $this->pdo->prepare("
            SELECT *
            FROM cod_school_sites
            WHERE school_id = :school_id
            ORDER BY is_primary DESC, id ASC
            LIMIT 1
        ");
        $statement->execute(['school_id' => $schoolId]);

        return $statement->fetch() ?: [];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function contacts(int $schoolId): array
    {
        $statement = $this->pdo->prepare("
            SELECT *
            FROM cod_school_contacts
            WHERE school_id = :school_id
            ORDER BY contact_type ASC, is_primary DESC, id ASC
        ");
        $statement->execute(['school_id' => $schoolId]);

        return $statement->fetchAll();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function identifiers(int $schoolId): array
    {
        $statement = $this->pdo->prepare("
            SELECT *
            FROM cod_school_identifiers
            WHERE school_id = :school_id
            ORDER BY source_key ASC, identifier_type ASC, identifier_value ASC
        ");
        $statement->execute(['school_id' => $schoolId]);

        return $statement->fetchAll();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function classifications(int $schoolId): array
    {
        $statement = $this->pdo->prepare("
            SELECT *
            FROM cod_school_classifications
            WHERE school_id = :school_id
            ORDER BY school_year DESC, id DESC
        ");
        $statement->execute(['school_id' => $schoolId]);

        return $statement->fetchAll();
    }
}
