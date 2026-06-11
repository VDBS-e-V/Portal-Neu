<?php

declare(strict_types=1);

namespace App\Repository;

use InvalidArgumentException;
use PDO;

final class MenuRepository
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
            'INSERT INTO pt_menus (%s) VALUES (%s)',
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
        $stmt = $this->pdo->prepare('SELECT * FROM pt_menus WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM pt_menus ORDER BY area_id ASC, is_default DESC, name ASC, id ASC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function findForArea(int $areaId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM pt_menus WHERE area_id = :area_id ORDER BY is_default DESC, id ASC LIMIT 1');
        $stmt->execute(['area_id' => $areaId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    public function findDefaultForArea(int $areaId): array
    {
        return $this->findForArea($areaId);
    }

    public function update(int $id, array $data): bool
    {
        if (empty($data)) {
            return false;
        }

        $columns = array_keys($data);
        $this->validateColumns($columns);

        $set = implode(', ', array_map(static fn (string $column): string => "{$column} = :{$column}", $columns));
        $sql = sprintf('UPDATE pt_menus SET %s WHERE id = :id', $set);

        $stmt = $this->pdo->prepare($sql);
        $params = $data;
        $params['id'] = $id;
        return (bool) $stmt->execute($params);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM pt_menus WHERE id = :id');
        return (bool) $stmt->execute(['id' => $id]);
    }
}
