<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;
use InvalidArgumentException;

final class AreaRepository
{
    private PDO $pdo;
    private string $table = 'pt_areas';

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    private function validateColumns(array $columns): void
    {
        foreach ($columns as $col) {
            if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $col)) {
                throw new InvalidArgumentException("Invalid column name: {$col}");
            }
        }
    }

    public function create(array $data): int
    {
        if (empty($data)) {
            return 0;
        }
        $cols = array_keys($data);
        $this->validateColumns($cols);
        $columns = implode(', ', $cols);
        $placeholders = ':' . implode(', :', $cols);

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute($data)) {
            return 0;
        }
        $id = $this->pdo->lastInsertId();
        return $id === '' ? 0 : (int) $id;
    }

    public function find(int $id): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: [];
    }

    public function findAll(): array
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY sort_order ASC, name ASC, id ASC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function update(int $id, array $data): bool
    {
        if (empty($data)) {
            return false;
        }
        $cols = array_keys($data);
        $this->validateColumns($cols);
        $set = implode(', ', array_map(fn ($c) => "{$c} = :{$c}", $cols));

        $sql = "UPDATE {$this->table} SET {$set} WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $params = $data;
        $params['id'] = $id;
        return (bool) $stmt->execute($params);
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return (bool) $stmt->execute(['id' => $id]);
    }
}
