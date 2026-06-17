<?php

declare(strict_types=1);

namespace App\Security;

use PDO;

final class AccountSessionContext
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function currentUser(): array
    {
        $userId = $this->currentUserId();

        if ($userId <= 0) {
            return [];
        }

        $stmt = $this->pdo->prepare(
            'SELECT
                u.*,
                p.display_name AS person_display_name,
                p.status AS person_status,
                n.first_name,
                n.last_name,
                n.preferred_name
             FROM ids_users u
             LEFT JOIN ids_persons p ON p.id = u.person_id
             LEFT JOIN ids_person_names n ON n.person_id = p.id
             WHERE u.id = :id
             LIMIT 1'
        );
        $stmt->execute(['id' => $userId]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * @return array<string, mixed>
     */
    public function requireCurrentUser(): array
    {
        $user = $this->currentUser();

        if ($user === []) {
            throw new AuthorizationException('Du musst angemeldet sein.');
        }

        return $user;
    }

    public function currentUserId(): int
    {
        $this->ensureSession();

        $candidates = [
            $_SESSION['user_id'] ?? null,
            $_SESSION['auth_user_id'] ?? null,
            $_SESSION['identity_user_id'] ?? null,
            $_SESSION['account_user_id'] ?? null,
            $_SESSION['user']['id'] ?? null,
            $_SESSION['auth']['user_id'] ?? null,
        ];

        foreach ($candidates as $candidate) {
            $userId = (int) $candidate;

            if ($userId > 0) {
                return $userId;
            }
        }

        return 0;
    }

    private function ensureSession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        if (!headers_sent()) {
            session_start();
            return;
        }

        if (!isset($_SESSION) || !is_array($_SESSION)) {
            $_SESSION = [];
        }
    }
}
