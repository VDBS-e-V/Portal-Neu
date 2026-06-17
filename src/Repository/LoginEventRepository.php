<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class LoginEventRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    /**
     * @param array<string, mixed> $metadata
     */
    public function record(
        ?int $userId,
        ?string $email,
        string $eventType,
        ?string $ipAddress = null,
        ?string $userAgent = null,
        ?string $requestUri = null,
        array $metadata = []
    ): int {
        if (!$this->tableExists('ids_user_login_events')) {
            return 0;
        }

        $stmt = $this->pdo->prepare(
            'INSERT INTO ids_user_login_events
                (user_id, email, event_type, ip_address, user_agent, request_uri, metadata)
             VALUES
                (:user_id, :email, :event_type, :ip_address, :user_agent, :request_uri, :metadata)'
        );
        $stmt->execute([
            'user_id' => $userId,
            'email' => $email !== null ? mb_strtolower(trim($email)) : null,
            'event_type' => $this->normalizeEventType($eventType),
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'request_uri' => $requestUri,
            'metadata' => $metadata !== [] ? json_encode($metadata, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function latestForUser(int $userId, int $limit = 50): array
    {
        if (!$this->tableExists('ids_user_login_events')) {
            return [];
        }

        $stmt = $this->pdo->prepare(
            'SELECT *
             FROM ids_user_login_events
             WHERE user_id = :user_id
             ORDER BY occurred_at DESC, id DESC
             LIMIT :limit'
        );
        $stmt->bindValue('user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue('limit', max(1, min(200, $limit)), PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    private function normalizeEventType(string $eventType): string
    {
        $eventType = mb_strtolower(trim($eventType));

        $allowed = [
            'login_success',
            'login_failed',
            'logout',
            'password_changed',
            'password_reset',
        ];

        if (!in_array($eventType, $allowed, true)) {
            return 'login_failed';
        }

        return $eventType;
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
