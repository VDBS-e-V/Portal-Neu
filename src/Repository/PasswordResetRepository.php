<?php

declare(strict_types=1);

namespace App\Repository;

use DateTimeImmutable;
use InvalidArgumentException;
use PDO;
use RuntimeException;

final class PasswordResetRepository
{
    public function __construct(
        private readonly PDO $pdo,
        private readonly UserPasswordRepository $passwords
    ) {
    }

    /**
     * @return array{id:int,token:string,reset_url:string}|null
     */
    public function createForEmail(string $email, int $validMinutes = 30): ?array
    {
        $this->assertTableExists();

        $user = $this->passwords->findUserByEmail($email);

        if ($user === []) {
            return null;
        }

        $userId = (int) ($user['id'] ?? 0);
        $email = mb_strtolower(trim((string) ($user['email'] ?? $email)));

        if ($userId <= 0 || $email === '') {
            return null;
        }

        $this->revokePendingForUser($userId);

        $token = $this->generateToken();
        $expiresAt = (new DateTimeImmutable('now'))
            ->modify('+' . max(5, min(240, $validMinutes)) . ' minutes')
            ->format('Y-m-d H:i:s');

        $stmt = $this->pdo->prepare(
            'INSERT INTO ids_user_password_resets
                (user_id, token_hash, email, status, expires_at)
             VALUES
                (:user_id, :token_hash, :email, :status, :expires_at)'
        );
        $stmt->execute([
            'user_id' => $userId,
            'token_hash' => $this->hashToken($token),
            'email' => $email,
            'status' => 'pending',
            'expires_at' => $expiresAt,
        ]);

        return [
            'id' => (int) $this->pdo->lastInsertId(),
            'token' => $token,
            'reset_url' => '/passwort/zuruecksetzen/' . rawurlencode($token),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function findPendingByToken(string $token): array
    {
        if (!$this->tableExists('ids_user_password_resets')) {
            return [];
        }

        $token = trim($token);

        if ($token === '') {
            return [];
        }

        $stmt = $this->pdo->prepare(
            'SELECT
                reset.*,
                u.email AS user_email,
                u.status AS user_status,
                p.display_name AS person_display_name
             FROM ids_user_password_resets reset
             INNER JOIN ids_users u ON u.id = reset.user_id
             LEFT JOIN ids_persons p ON p.id = u.person_id
             WHERE reset.token_hash = :token_hash
               AND reset.status = :status
               AND reset.expires_at >= CURRENT_TIMESTAMP
             LIMIT 1'
        );
        $stmt->execute([
            'token_hash' => $this->hashToken($token),
            'status' => 'pending',
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    public function resetPassword(string $token, string $newPassword): int
    {
        $reset = $this->findPendingByToken($token);

        if ($reset === []) {
            throw new InvalidArgumentException('Der Passwort-Reset-Link ist ungültig oder abgelaufen.');
        }

        $userId = (int) ($reset['user_id'] ?? 0);

        if ($userId <= 0) {
            throw new InvalidArgumentException('Login-Konto nicht gefunden.');
        }

        $this->pdo->beginTransaction();

        try {
            $this->passwords->setPassword($userId, $newPassword);

            $stmt = $this->pdo->prepare(
                'UPDATE ids_user_password_resets
                 SET status = :status,
                     used_at = CURRENT_TIMESTAMP
                 WHERE id = :id'
            );
            $stmt->execute([
                'id' => (int) $reset['id'],
                'status' => 'used',
            ]);

            $this->pdo->commit();

            return $userId;
        } catch (\Throwable $throwable) {
            $this->pdo->rollBack();
            throw $throwable;
        }
    }

    public function revokePendingForUser(int $userId): void
    {
        if (!$this->tableExists('ids_user_password_resets')) {
            return;
        }

        $stmt = $this->pdo->prepare(
            'UPDATE ids_user_password_resets
             SET status = :status,
                 revoked_at = CURRENT_TIMESTAMP
             WHERE user_id = :user_id
               AND status = :old_status'
        );
        $stmt->execute([
            'user_id' => $userId,
            'status' => 'revoked',
            'old_status' => 'pending',
        ]);
    }

    public function markExpired(): int
    {
        if (!$this->tableExists('ids_user_password_resets')) {
            return 0;
        }

        $stmt = $this->pdo->prepare(
            'UPDATE ids_user_password_resets
             SET status = :status
             WHERE status = :old_status
               AND expires_at < CURRENT_TIMESTAMP'
        );
        $stmt->execute([
            'status' => 'expired',
            'old_status' => 'pending',
        ]);

        return $stmt->rowCount();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function latest(int $limit = 100): array
    {
        if (!$this->tableExists('ids_user_password_resets')) {
            return [];
        }

        $stmt = $this->pdo->prepare(
            'SELECT
                reset.*,
                u.email AS user_email,
                p.display_name AS person_display_name
             FROM ids_user_password_resets reset
             INNER JOIN ids_users u ON u.id = reset.user_id
             LEFT JOIN ids_persons p ON p.id = u.person_id
             ORDER BY reset.created_at DESC, reset.id DESC
             LIMIT :limit'
        );
        $stmt->bindValue('limit', max(1, min(500, $limit)), PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    private function generateToken(): string
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }

    private function hashToken(string $token): string
    {
        return hash('sha256', $token);
    }

    private function assertTableExists(): void
    {
        if (!$this->tableExists('ids_user_password_resets')) {
            throw new RuntimeException('Tabelle ids_user_password_resets fehlt. Migration 0029 ausführen.');
        }
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
