<?php

declare(strict_types=1);

namespace App\Repository;

use DateTimeImmutable;
use InvalidArgumentException;
use PDO;
use RuntimeException;

final class UserInvitationRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    /**
     * @return array{
     *   id: int,
     *   token: string,
     *   accept_url: string
     * }
     */
    public function createForUser(int $userId, ?int $createdByUserId, int $validDays = 14): array
    {
        if (!$this->tableExists('ids_user_invitations')) {
            throw new RuntimeException('Tabelle ids_user_invitations fehlt. Migration 0026 ausführen.');
        }

        $user = $this->findUser($userId);

        if ($user === []) {
            throw new InvalidArgumentException('Login-Konto nicht gefunden.');
        }

        $email = mb_strtolower(trim((string) ($user['email'] ?? '')));

        if ($email === '') {
            throw new InvalidArgumentException('Einladung benötigt eine Login-E-Mail.');
        }

        $this->revokePendingForUser($userId);

        $token = $this->generateToken();
        $tokenHash = $this->hashToken($token);
        $expiresAt = (new DateTimeImmutable('now'))
            ->modify('+' . max(1, min(90, $validDays)) . ' days')
            ->format('Y-m-d H:i:s');

        $stmt = $this->pdo->prepare(
            'INSERT INTO ids_user_invitations
                (user_id, token_hash, email, status, expires_at, created_by_user_id)
             VALUES
                (:user_id, :token_hash, :email, :status, :expires_at, :created_by_user_id)'
        );
        $stmt->execute([
            'user_id' => $userId,
            'token_hash' => $tokenHash,
            'email' => $email,
            'status' => 'pending',
            'expires_at' => $expiresAt,
            'created_by_user_id' => $createdByUserId,
        ]);

        return [
            'id' => (int) $this->pdo->lastInsertId(),
            'token' => $token,
            'accept_url' => '/einladung/' . rawurlencode($token),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function findPendingByToken(string $token): array
    {
        if (!$this->tableExists('ids_user_invitations')) {
            return [];
        }

        $token = trim($token);

        if ($token === '') {
            return [];
        }

        $stmt = $this->pdo->prepare(
            'SELECT
                invitation.*,
                u.email AS user_email,
                u.status AS user_status,
                u.person_id,
                p.display_name AS person_display_name
             FROM ids_user_invitations invitation
             INNER JOIN ids_users u ON u.id = invitation.user_id
             LEFT JOIN ids_persons p ON p.id = u.person_id
             WHERE invitation.token_hash = :token_hash
               AND invitation.status = :status
               AND invitation.expires_at >= CURRENT_TIMESTAMP
             LIMIT 1'
        );
        $stmt->execute([
            'token_hash' => $this->hashToken($token),
            'status' => 'pending',
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    public function accept(string $token, string $plainPassword): int
    {
        $plainPassword = trim($plainPassword);

        if (mb_strlen($plainPassword) < 8) {
            throw new InvalidArgumentException('Das Passwort muss mindestens 8 Zeichen lang sein.');
        }

        $invitation = $this->findPendingByToken($token);

        if ($invitation === []) {
            throw new InvalidArgumentException('Einladung ist ungültig oder abgelaufen.');
        }

        $userId = (int) ($invitation['user_id'] ?? 0);

        $this->pdo->beginTransaction();

        try {
            $stmt = $this->pdo->prepare(
                'UPDATE ids_users
                 SET password_hash = :password_hash,
                     status = :status
                 WHERE id = :user_id'
            );
            $stmt->execute([
                'user_id' => $userId,
                'password_hash' => password_hash($plainPassword, PASSWORD_DEFAULT),
                'status' => 'active',
            ]);

            $stmt = $this->pdo->prepare(
                'UPDATE ids_user_invitations
                 SET status = :status,
                     accepted_at = CURRENT_TIMESTAMP
                 WHERE id = :id'
            );
            $stmt->execute([
                'id' => (int) $invitation['id'],
                'status' => 'accepted',
            ]);

            $this->pdo->commit();

            return $userId;
        } catch (\Throwable $throwable) {
            $this->pdo->rollBack();
            throw $throwable;
        }
    }

    public function revoke(int $invitationId): bool
    {
        if (!$this->tableExists('ids_user_invitations')) {
            return false;
        }

        $stmt = $this->pdo->prepare(
            'UPDATE ids_user_invitations
             SET status = :status,
                 revoked_at = CURRENT_TIMESTAMP
             WHERE id = :id
               AND status = :old_status'
        );

        return (bool) $stmt->execute([
            'id' => $invitationId,
            'status' => 'revoked',
            'old_status' => 'pending',
        ]);
    }

    public function revokePendingForUser(int $userId): void
    {
        if (!$this->tableExists('ids_user_invitations')) {
            return;
        }

        $stmt = $this->pdo->prepare(
            'UPDATE ids_user_invitations
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
        if (!$this->tableExists('ids_user_invitations')) {
            return 0;
        }

        $stmt = $this->pdo->prepare(
            'UPDATE ids_user_invitations
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
    public function forUser(int $userId): array
    {
        if (!$this->tableExists('ids_user_invitations')) {
            return [];
        }

        $stmt = $this->pdo->prepare(
            'SELECT *
             FROM ids_user_invitations
             WHERE user_id = :user_id
             ORDER BY created_at DESC, id DESC'
        );
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function latest(int $limit = 100): array
    {
        if (!$this->tableExists('ids_user_invitations')) {
            return [];
        }

        $stmt = $this->pdo->prepare(
            'SELECT
                invitation.*,
                u.email AS user_email,
                u.status AS user_status,
                p.display_name AS person_display_name
             FROM ids_user_invitations invitation
             INNER JOIN ids_users u ON u.id = invitation.user_id
             LEFT JOIN ids_persons p ON p.id = u.person_id
             ORDER BY invitation.created_at DESC, invitation.id DESC
             LIMIT :limit'
        );
        $stmt->bindValue('limit', max(1, min(500, $limit)), PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * @return array<string, mixed>
     */
    private function findUser(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT *
             FROM ids_users
             WHERE id = :id
             LIMIT 1'
        );
        $stmt->execute(['id' => $userId]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    private function generateToken(): string
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }

    private function hashToken(string $token): string
    {
        return hash('sha256', $token);
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
