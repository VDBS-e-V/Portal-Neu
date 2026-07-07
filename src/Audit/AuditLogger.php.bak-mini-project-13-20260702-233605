<?php

declare(strict_types=1);

namespace App\Audit;

use App\Repository\AuditLogRepository;
use App\Repository\PersonPermissionGroupRepository;
use App\Security\SessionAuth;
use JsonException;
use Throwable;

final class AuditLogger
{
    /** @var array<int, string> */
    private array $sensitiveKeys = [
        'password',
        'password_hash',
        'current_password',
        'new_password',
        'new_password_repeat',
        'token',
        '_csrf',
        'csrf',
        'secret',
    ];

    public function __construct(
        private readonly AuditLogRepository $auditLogs,
        private readonly SessionAuth $auth,
        private readonly PersonPermissionGroupRepository $personGroups
    ) {
    }

    /**
     * @param array<string, mixed> $context
     */
    public function log(string $action, string $entityType, ?int $entityId = null, array $context = []): void
    {
        $action = trim($action);
        $entityType = trim($entityType);

        if ($action === '' || $entityType === '') {
            return;
        }

        try {
            $actorUserId = $context['actor_user_id'] ?? $this->auth->id();
            $actorUserId = $actorUserId === null || $actorUserId === '' ? null : (int) $actorUserId;

            $actorPersonId = $context['actor_person_id'] ?? null;

            if ($actorPersonId === null && $actorUserId !== null) {
                $actorPersonId = $this->personGroups->personIdForUserId($actorUserId);
            }

            $actorPersonId = $actorPersonId === null || $actorPersonId === '' ? null : (int) $actorPersonId;

            $this->auditLogs->create([
                'actor_person_id' => $actorPersonId,
                'actor_user_id' => $actorUserId,
                'action' => $action,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'entity_uuid' => $context['entity_uuid'] ?? null,
                'entity_label' => $this->nullableString($context['entity_label'] ?? null),
                'request_id' => $this->nullableString($context['request_id'] ?? ($_SERVER['HTTP_X_REQUEST_ID'] ?? null)),
                'request_method' => $this->nullableString($context['request_method'] ?? ($_SERVER['REQUEST_METHOD'] ?? null)),
                'request_uri' => $this->nullableString($context['request_uri'] ?? ($_SERVER['REQUEST_URI'] ?? null)),
                'ip_address' => $this->nullableString($context['ip_address'] ?? ($_SERVER['REMOTE_ADDR'] ?? null)),
                'user_agent' => $this->nullableString($context['user_agent'] ?? ($_SERVER['HTTP_USER_AGENT'] ?? null)),
                'old_values' => $this->jsonOrNull($context['old_values'] ?? null),
                'new_values' => $this->jsonOrNull($context['new_values'] ?? null),
                'metadata' => $this->jsonOrNull($context['metadata'] ?? null),
            ]);
        } catch (Throwable) {
            // Audit darf normale Requests nicht kaputt machen.
        }
    }

    /**
     * @param array<string, mixed> $oldValues
     * @param array<string, mixed> $newValues
     * @param array<string, mixed> $context
     */
    public function logChange(
        string $action,
        string $entityType,
        ?int $entityId,
        array $oldValues,
        array $newValues,
        array $context = []
    ): void {
        $this->log($action, $entityType, $entityId, array_merge($context, [
            'old_values' => $this->redact($oldValues),
            'new_values' => $this->redact($newValues),
        ]));
    }

    private function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function jsonOrNull(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_string($value)) {
            return $value;
        }

        try {
            return json_encode(
                $this->redact($value),
                JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            );
        } catch (JsonException) {
            return null;
        }
    }

    private function redact(mixed $value): mixed
    {
        if (!is_array($value)) {
            return $value;
        }

        $redacted = [];

        foreach ($value as $key => $item) {
            $stringKey = (string) $key;

            if ($this->isSensitiveKey($stringKey)) {
                $redacted[$key] = '[redacted]';
                continue;
            }

            $redacted[$key] = is_array($item) ? $this->redact($item) : $item;
        }

        return $redacted;
    }

    private function isSensitiveKey(string $key): bool
    {
        $key = mb_strtolower($key);

        foreach ($this->sensitiveKeys as $sensitiveKey) {
            if (str_contains($key, $sensitiveKey)) {
                return true;
            }
        }

        return false;
    }
}