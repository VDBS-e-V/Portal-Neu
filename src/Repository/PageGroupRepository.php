<?php

declare(strict_types=1);

namespace App\Repository;

use InvalidArgumentException;
use PDO;

final class PageGroupRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    /** @return array<int, array<string, mixed>> */
    public function findAll(): array
    {
        if (!$this->tableExists('pt_page_groups')) {
            return [];
        }

        $stmt = $this->pdo->query(
            'SELECT pg.*, a.area_key, a.name AS area_name
             FROM pt_page_groups pg
             INNER JOIN pt_areas a ON a.id = pg.area_id
             ORDER BY a.sort_order ASC, a.area_key ASC, pg.sort_order ASC, pg.name ASC'
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /** @return array<int, array<string, mixed>> */
    public function findAllForArea(string $areaKey): array
    {
        if (!$this->tableExists('pt_page_groups')) {
            return [];
        }

        $stmt = $this->pdo->prepare(
            'SELECT pg.*, a.area_key, a.name AS area_name
             FROM pt_page_groups pg
             INNER JOIN pt_areas a ON a.id = pg.area_id
             WHERE a.area_key = :area_key
             ORDER BY pg.sort_order ASC, pg.name ASC'
        );
        $stmt->execute(['area_key' => $this->normalizeKey($areaKey)]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /** @return array<string, mixed> */
    public function find(int $id): array
    {
        if (!$this->tableExists('pt_page_groups')) {
            return [];
        }

        $stmt = $this->pdo->prepare(
            'SELECT pg.*, a.area_key, a.name AS area_name
             FROM pt_page_groups pg
             INNER JOIN pt_areas a ON a.id = pg.area_id
             WHERE pg.id = :id
             LIMIT 1'
        );
        $stmt->execute(['id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    /** @return array<string, mixed> */
    public function findByKey(string $areaKey, string $pageGroupKey): array
    {
        if (!$this->tableExists('pt_page_groups')) {
            return [];
        }

        $stmt = $this->pdo->prepare(
            'SELECT pg.*, a.area_key, a.name AS area_name
             FROM pt_page_groups pg
             INNER JOIN pt_areas a ON a.id = pg.area_id
             WHERE a.area_key = :area_key
               AND pg.page_group_key = :page_group_key
             LIMIT 1'
        );
        $stmt->execute([
            'area_key' => $this->normalizeKey($areaKey),
            'page_group_key' => $this->normalizeKey($pageGroupKey),
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    public function create(array $data): int
    {
        $data = $this->prepareData($data, false);

        $stmt = $this->pdo->prepare(
            'INSERT INTO pt_page_groups
               (area_id, page_group_key, name, description, start_path, sort_order, is_active)
             VALUES
               (:area_id, :page_group_key, :name, :description, :start_path, :sort_order, :is_active)'
        );
        $stmt->execute($data);

        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        if (!$this->tableExists('pt_page_groups')) {
            return false;
        }

        $allowed = [
            'area_id',
            'page_group_key',
            'name',
            'description',
            'start_path',
            'sort_order',
            'is_active',
        ];

        $prepared = [];

        foreach ($allowed as $column) {
            if (array_key_exists($column, $data)) {
                $prepared[$column] = $data[$column];
            }
        }

        $prepared = $this->prepareData($prepared, true);

        if ($prepared === []) {
            return true;
        }

        $sets = [];

        foreach (array_keys($prepared) as $column) {
            $sets[] = sprintf('%s = :%s', $column, $column);
        }

        $prepared['id'] = $id;

        $stmt = $this->pdo->prepare(
            sprintf('UPDATE pt_page_groups SET %s WHERE id = :id', implode(', ', $sets))
        );

        return (bool) $stmt->execute($prepared);
    }

    public function delete(int $id): bool
    {
        if (!$this->tableExists('pt_page_groups')) {
            return false;
        }

        $stmt = $this->pdo->prepare('DELETE FROM pt_page_groups WHERE id = :id');

        return (bool) $stmt->execute(['id' => $id]);
    }

    /** @return array<string, mixed> */
    private function prepareData(array $data, bool $partial): array
    {
        $prepared = [];

        if (!$partial || array_key_exists('area_id', $data)) {
            $prepared['area_id'] = (int) ($data['area_id'] ?? 0);

            if ($prepared['area_id'] <= 0) {
                throw new InvalidArgumentException('area_id ist erforderlich.');
            }
        }

        if (!$partial || array_key_exists('page_group_key', $data)) {
            $prepared['page_group_key'] = $this->normalizeKey((string) ($data['page_group_key'] ?? ''));

            if ($prepared['page_group_key'] === '') {
                throw new InvalidArgumentException('page_group_key ist erforderlich.');
            }
        }

        if (!$partial || array_key_exists('name', $data)) {
            $prepared['name'] = trim((string) ($data['name'] ?? ''));

            if ($prepared['name'] === '') {
                throw new InvalidArgumentException('Name ist erforderlich.');
            }
        }

        if (!$partial || array_key_exists('description', $data)) {
            $description = trim((string) ($data['description'] ?? ''));
            $prepared['description'] = $description === '' ? null : $description;
        }

        if (!$partial || array_key_exists('start_path', $data)) {
            $startPath = '/' . ltrim(trim((string) ($data['start_path'] ?? '/')), '/');
            $prepared['start_path'] = $startPath === '' ? '/' : $startPath;
        }

        if (!$partial || array_key_exists('sort_order', $data)) {
            $prepared['sort_order'] = (int) ($data['sort_order'] ?? 0);
        }

        if (!$partial || array_key_exists('is_active', $data)) {
            $prepared['is_active'] = !empty($data['is_active']) ? 1 : 0;
        }

        return $prepared;
    }

    private function normalizeKey(string $key): string
    {
        return mb_strtolower(trim($key));
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