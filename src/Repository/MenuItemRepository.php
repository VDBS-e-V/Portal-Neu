<?php

declare(strict_types=1);

namespace App\Repository;

use InvalidArgumentException;
use PDO;

final class MenuItemRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    private function validateColumns(array $columns): void
    {
        foreach ($columns as $column) {
            if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $column)) {
                throw new InvalidArgumentException("Invalid column name: {$column}");
            }
        }
    }

    public function create(array $data): int
    {
        if (empty($data)) {
            return 0;
        }

        $columns = array_keys($data);
        $this->validateColumns($columns);

        $sql = sprintf(
            'INSERT INTO pt_menu_items (%s) VALUES (%s)',
            implode(', ', $columns),
            ':' . implode(', :', $columns)
        );

        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute($data)) {
            return 0;
        }

        $id = $this->pdo->lastInsertId();
        return $id === '' ? 0 : (int) $id;
    }

    public function find(int $id): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM pt_menu_items WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM pt_menu_items ORDER BY menu_id ASC, parent_id ASC, order_index ASC, id ASC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function findByMenuIdAndParent(int $menuId, ?int $parentId = null): array
    {
        $sql = 'SELECT * FROM pt_menu_items WHERE menu_id = :menu_id AND is_active = 1';
        $params = ['menu_id' => $menuId];

        if ($parentId === null) {
            $sql .= ' AND parent_id IS NULL';
        } else {
            $sql .= ' AND parent_id = :parent_id';
            $params['parent_id'] = $parentId;
        }

        $sql .= ' ORDER BY COALESCE(order_index, 999999) ASC, id ASC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function update(int $id, array $data): bool
    {
        if (empty($data)) {
            return false;
        }

        $columns = array_keys($data);
        $this->validateColumns($columns);

        $set = implode(', ', array_map(static fn (string $column): string => "{$column} = :{$column}", $columns));
        $sql = sprintf('UPDATE pt_menu_items SET %s WHERE id = :id', $set);

        $stmt = $this->pdo->prepare($sql);
        $params = $data;
        $params['id'] = $id;
        return (bool) $stmt->execute($params);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM pt_menu_items WHERE id = :id');
        return (bool) $stmt->execute(['id' => $id]);
    }
}
