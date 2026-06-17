<?php

declare(strict_types=1);

namespace App\Repository;

use InvalidArgumentException;
use PDO;

final class PermissionGroupRepository
{
    private string $table = 'ids_permission_groups';

    public function __construct(private readonly PDO $pdo)
    {
    }

    /** @return array<int, array<string, mixed>> */
    public function findAll(): array
    {
        $stmt = $this->pdo->query(
            "SELECT * FROM {$this->table} ORDER BY is_system DESC, group_key ASC, name ASC"
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /** @return array<string, mixed> */
    public function find(int $id): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    /** @return array<string, mixed> */
    public function findByKey(string $groupKey): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE group_key = :group_key LIMIT 1");
        $stmt->execute(['group_key' => $this->normalizeKey($groupKey)]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * @param array<int, string> $groupKeys
     * @return array<int, array<string, mixed>>
     */
    public function findByKeys(array $groupKeys): array
    {
        $groupKeys = array_values(array_unique(array_filter(array_map(
            fn (string $key): string => $this->normalizeKey($key),
            $groupKeys
        ))));

        if ($groupKeys === []) {
            return [];
        }

        $placeholders = [];
        $params = [];

        foreach ($groupKeys as $index => $groupKey) {
            $name = 'group_key_' . $index;
            $placeholders[] = ':' . $name;
            $params[$name] = $groupKey;
        }

        $sql = sprintf(
            'SELECT * FROM %s WHERE group_key IN (%s) ORDER BY group_key ASC',
            $this->table,
            implode(', ', $placeholders)
        );

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function create(array $data): int
    {
        $data = $this->prepareData($data, false);
        $this->assertGroupKeyIsAvailable((string) $data['group_key']);

        $stmt = $this->pdo->prepare(
            'INSERT INTO ids_permission_groups (group_key, name, description, is_system)
             VALUES (:group_key, :name, :description, :is_system)'
        );
        $stmt->execute($data);

        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $current = $this->find($id);

        if ($current === []) {
            return false;
        }

        $isSystemGroup = (int) ($current['is_system'] ?? 0) === 1;
        $data = $this->prepareData($data, $isSystemGroup);

        if ($isSystemGroup) {
            unset($data['group_key'], $data['is_system']);
        } elseif (isset($data['group_key']) && $data['group_key'] !== (string) $current['group_key']) {
            $this->assertGroupKeyIsAvailable((string) $data['group_key'], $id);
        }

        if ($data === []) {
            return true;
        }

        $sets = [];

        foreach (array_keys($data) as $column) {
            $this->assertColumn($column);
            $sets[] = sprintf('%s = :%s', $column, $column);
        }

        $data['id'] = $id;

        $stmt = $this->pdo->prepare(
            sprintf('UPDATE %s SET %s WHERE id = :id', $this->table, implode(', ', $sets))
        );

        return (bool) $stmt->execute($data);
    }

    public function delete(int $id): bool
    {
        $group = $this->find($id);

        if ($group === []) {
            return false;
        }

        if ((int) ($group['is_system'] ?? 0) === 1) {
            throw new InvalidArgumentException('Systemgruppen dürfen nicht gelöscht werden.');
        }

        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = :id AND is_system = 0");

        return (bool) $stmt->execute(['id' => $id]);
    }

    public function idForKey(string $groupKey): ?int
    {
        $group = $this->findByKey($groupKey);

        if ($group === []) {
            return null;
        }

        return (int) $group['id'];
    }

    /**
     * @param array<int, string> $groupKeys
     * @return array<int, int>
     */
    public function idsForKeys(array $groupKeys): array
    {
        $ids = [];

        foreach ($this->findByKeys($groupKeys) as $group) {
            $ids[] = (int) $group['id'];
        }

        return $ids;
    }

    private function normalizeKey(string $key): string
    {
        return mb_strtolower(trim($key));
    }

    /** @return array<string, mixed> */
    private function prepareData(array $data, bool $systemGroup): array
    {
        $prepared = [];

        if (!$systemGroup && array_key_exists('group_key', $data)) {
            $prepared['group_key'] = $this->normalizeKey((string) $data['group_key']);

            if ($prepared['group_key'] === '') {
                throw new InvalidArgumentException('group_key darf nicht leer sein.');
            }
        }

        if (array_key_exists('name', $data)) {
            $prepared['name'] = trim((string) $data['name']);

            if ($prepared['name'] === '') {
                throw new InvalidArgumentException('Name darf nicht leer sein.');
            }
        }

        if (array_key_exists('description', $data)) {
            $description = trim((string) $data['description']);
            $prepared['description'] = $description === '' ? null : $description;
        }

        if (!$systemGroup && array_key_exists('is_system', $data)) {
            $prepared['is_system'] = !empty($data['is_system']) ? 1 : 0;
        }

        return $prepared;
    }

    private function assertGroupKeyIsAvailable(string $groupKey, ?int $ignoreId = null): void
    {
        $sql = "SELECT id FROM {$this->table} WHERE group_key = :group_key";
        $params = ['group_key' => $groupKey];

        if ($ignoreId !== null) {
            $sql .= ' AND id <> :ignore_id';
            $params['ignore_id'] = $ignoreId;
        }

        $sql .= ' LIMIT 1';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        if ($stmt->fetch(PDO::FETCH_ASSOC) !== false) {
            throw new InvalidArgumentException('group_key ist bereits vergeben.');
        }
    }

    private function assertColumn(string $column): void
    {
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $column)) {
            throw new InvalidArgumentException('Ungültiger Spaltenname: ' . $column);
        }
    }
}