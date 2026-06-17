<?php

declare(strict_types=1);

namespace App\Repository;

use InvalidArgumentException;
use PDO;

final class UserPasswordRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function findUser(int $userId): array
    {
        if ($userId <= 0) {
            return [];
        }

        $stmt = $this->pdo->prepare(
            'SELECT *
             FROM ids_users
             WHERE id = :id
             LIMIT 1'
        );
        $stmt->execute(['id' => $userId]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * @return array<string, mixed>
     */
    public function findUserByEmail(string $email): array
    {
        $email = mb_strtolower(trim($email));

        if ($email === '') {
            return [];
        }

        $stmt = $this->pdo->prepare(
            'SELECT *
             FROM ids_users
             WHERE email = :email
             LIMIT 1'
        );
        $stmt->execute(['email' => $email]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    public function changePassword(int $userId, string $currentPassword, string $newPassword): bool
    {
        $user = $this->findUser($userId);

        if ($user === []) {
            throw new InvalidArgumentException('Login-Konto nicht gefunden.');
        }

        $hash = (string) ($user['password_hash'] ?? '');

        if ($hash === '' || !password_verify($currentPassword, $hash)) {
            throw new InvalidArgumentException('Das aktuelle Passwort ist falsch.');
        }

        $this->assertValidPassword($newPassword);

        return $this->setPassword($userId, $newPassword);
    }

    public function setPassword(int $userId, string $newPassword): bool
    {
        $this->assertValidPassword($newPassword);

        $columns = [
            'password_hash = :password_hash',
            'status = :status',
        ];

        if ($this->columnExists('ids_users', 'password_changed_at')) {
            $columns[] = 'password_changed_at = CURRENT_TIMESTAMP';
        }

        $stmt = $this->pdo->prepare(
            'UPDATE ids_users
             SET ' . implode(', ', $columns) . '
             WHERE id = :id'
        );

        return (bool) $stmt->execute([
            'id' => $userId,
            'password_hash' => password_hash($newPassword, PASSWORD_DEFAULT),
            'status' => 'active',
        ]);
    }

    public function assertValidPassword(string $password): void
    {
        if (mb_strlen($password) < 8) {
            throw new InvalidArgumentException('Das Passwort muss mindestens 8 Zeichen lang sein.');
        }

        if (!preg_match('/[A-ZÄÖÜ]/u', $password)) {
            throw new InvalidArgumentException('Das Passwort muss mindestens einen Großbuchstaben enthalten.');
        }

        if (!preg_match('/[a-zäöüß]/u', $password)) {
            throw new InvalidArgumentException('Das Passwort muss mindestens einen Kleinbuchstaben enthalten.');
        }

        if (!preg_match('/[0-9]/', $password)) {
            throw new InvalidArgumentException('Das Passwort muss mindestens eine Zahl enthalten.');
        }
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
