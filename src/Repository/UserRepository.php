<?php

declare(strict_types=1);

namespace App\Repository;

use InvalidArgumentException;
use PDO;

final class UserRepository
{
    private PDO $pdo;

    private string $table = 'ids_users';

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

    public function findByEmail(string $email): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['email' => mb_strtolower(trim($email))]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: [];
    }

    public function findAll(): array
    {
        $sql = "SELECT * FROM {$this->table}";
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

    public function nameForUser(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM ids_user_names WHERE user_id = :user_id LIMIT 1'
        );
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    public function contactsForUser(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM ids_user_contact_details
             WHERE user_id = :user_id
             ORDER BY is_primary DESC, id ASC'
        );
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function primaryAddressForUser(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM ids_user_addresses
             WHERE user_id = :user_id
             ORDER BY is_primary DESC, id ASC
             LIMIT 1'
        );
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    public function settingsForUser(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM ids_user_account_settings WHERE user_id = :user_id LIMIT 1'
        );
        $stmt->execute(['user_id' => $userId]);

        $settings = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($settings !== false) {
            return $settings;
        }

        $stmt = $this->pdo->prepare(
            'INSERT INTO ids_user_account_settings (user_id) VALUES (:user_id)'
        );
        $stmt->execute(['user_id' => $userId]);

        return $this->settingsForUser($userId);
    }

    public function saveName(int $userId, array $data): void
    {
        $sql = '
            INSERT INTO ids_user_names (
                user_id,
                salutation,
                title,
                first_name,
                middle_name,
                last_name,
                preferred_name,
                pronouns
            ) VALUES (
                :user_id,
                :salutation,
                :title,
                :first_name,
                :middle_name,
                :last_name,
                :preferred_name,
                :pronouns
            )
            ON DUPLICATE KEY UPDATE
                salutation = VALUES(salutation),
                title = VALUES(title),
                first_name = VALUES(first_name),
                middle_name = VALUES(middle_name),
                last_name = VALUES(last_name),
                preferred_name = VALUES(preferred_name),
                pronouns = VALUES(pronouns)
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'user_id' => $userId,
            'salutation' => $this->nullable($data['salutation'] ?? null),
            'title' => $this->nullable($data['title'] ?? null),
            'first_name' => $this->nullable($data['first_name'] ?? null),
            'middle_name' => $this->nullable($data['middle_name'] ?? null),
            'last_name' => $this->nullable($data['last_name'] ?? null),
            'preferred_name' => $this->nullable($data['preferred_name'] ?? null),
            'pronouns' => $this->nullable($data['pronouns'] ?? null),
        ]);
    }

    public function savePrimaryAddress(int $userId, array $data): void
    {
        $current = $this->primaryAddressForUser($userId);

        if ($current === []) {
            $sql = '
                INSERT INTO ids_user_addresses (
                    user_id,
                    address_type,
                    recipient_name,
                    organization,
                    street,
                    house_number,
                    address_addition,
                    postal_code,
                    city,
                    state,
                    country,
                    is_primary
                ) VALUES (
                    :user_id,
                    :address_type,
                    :recipient_name,
                    :organization,
                    :street,
                    :house_number,
                    :address_addition,
                    :postal_code,
                    :city,
                    :state,
                    :country,
                    1
                )
            ';

            $params = [
                'user_id' => $userId,
            ];
        } else {
            $sql = '
                UPDATE ids_user_addresses
                SET
                    address_type = :address_type,
                    recipient_name = :recipient_name,
                    organization = :organization,
                    street = :street,
                    house_number = :house_number,
                    address_addition = :address_addition,
                    postal_code = :postal_code,
                    city = :city,
                    state = :state,
                    country = :country,
                    is_primary = 1
                WHERE id = :id AND user_id = :user_id
            ';

            $params = [
                'id' => (int) $current['id'],
                'user_id' => $userId,
            ];
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array_merge($params, [
            'address_type' => $this->allowed(
                (string) ($data['address_type'] ?? 'private'),
                ['private', 'work', 'billing', 'shipping', 'other'],
                'private'
            ),
            'recipient_name' => $this->nullable($data['recipient_name'] ?? null),
            'organization' => $this->nullable($data['organization'] ?? null),
            'street' => $this->nullable($data['street'] ?? null),
            'house_number' => $this->nullable($data['house_number'] ?? null),
            'address_addition' => $this->nullable($data['address_addition'] ?? null),
            'postal_code' => $this->nullable($data['postal_code'] ?? null),
            'city' => $this->nullable($data['city'] ?? null),
            'state' => $this->nullable($data['state'] ?? null),
            'country' => strtoupper(substr((string) ($data['country'] ?? 'DE'), 0, 2)) ?: 'DE',
        ]));
    }

    public function saveSimpleContacts(
        int $userId,
        ?string $phone,
        ?string $mobile,
        ?string $website
    ): void {
        $this->deleteManagedContacts($userId);

        $this->insertContact($userId, 'phone', 'Telefon', $phone, true);
        $this->insertContact($userId, 'mobile', 'Mobil', $mobile, false);
        $this->insertContact($userId, 'website', 'Website', $website, false);
    }

    public function saveSettings(int $userId, array $data): void
    {
        $sql = '
            INSERT INTO ids_user_account_settings (
                user_id,
                language,
                timezone,
                email_notifications,
                profile_visibility
            ) VALUES (
                :user_id,
                :language,
                :timezone,
                :email_notifications,
                :profile_visibility
            )
            ON DUPLICATE KEY UPDATE
                language = VALUES(language),
                timezone = VALUES(timezone),
                email_notifications = VALUES(email_notifications),
                profile_visibility = VALUES(profile_visibility)
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'user_id' => $userId,
            'language' => $this->allowed((string) ($data['language'] ?? 'de'), ['de', 'en'], 'de'),
            'timezone' => $this->nullable($data['timezone'] ?? 'Europe/Berlin') ?: 'Europe/Berlin',
            'email_notifications' => !empty($data['email_notifications']) ? 1 : 0,
            'profile_visibility' => $this->allowed(
                (string) ($data['profile_visibility'] ?? 'private'),
                ['private', 'members', 'public'],
                'private'
            ),
        ]);
    }

    private function deleteManagedContacts(int $userId): void
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM ids_user_contact_details
             WHERE user_id = :user_id
             AND contact_type IN ('phone', 'mobile', 'website')"
        );
        $stmt->execute(['user_id' => $userId]);
    }

    private function insertContact(
        int $userId,
        string $type,
        string $label,
        ?string $value,
        bool $primary
    ): void {
        $value = $this->nullable($value);

        if ($value === null) {
            return;
        }

        $stmt = $this->pdo->prepare(
            'INSERT INTO ids_user_contact_details (
                user_id,
                contact_type,
                label,
                value,
                is_primary,
                is_verified
            ) VALUES (
                :user_id,
                :contact_type,
                :label,
                :value,
                :is_primary,
                0
            )'
        );

        $stmt->execute([
            'user_id' => $userId,
            'contact_type' => $type,
            'label' => $label,
            'value' => $value,
            'is_primary' => $primary ? 1 : 0,
        ]);
    }

    private function nullable(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function allowed(string $value, array $allowed, string $fallback): string
    {
        return in_array($value, $allowed, true) ? $value : $fallback;
    }

    public function headerProfileForUser(int $userId): array
    {
        $user = $this->find($userId);

        if ($user === []) {
            return [];
        }

        $name = [];

        try {
            $stmt = $this->pdo->prepare(
                'SELECT *
                FROM ids_user_names
                WHERE user_id = :user_id
                LIMIT 1'
            );
            $stmt->execute(['user_id' => $userId]);
            $name = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
        } catch (\Throwable) {
            $name = [];
        }

        $displayName = trim((string) ($name['preferred_name'] ?? ''));

        if ($displayName === '') {
            $displayName = trim(
                trim((string) ($name['first_name'] ?? ''))
                . ' '
                . trim((string) ($name['last_name'] ?? ''))
            );
        }

        if ($displayName === '') {
            $displayName = trim((string) ($user['display_name'] ?? ''));
        }

        if ($displayName === '') {
            $displayName = trim((string) ($user['email'] ?? ''));
        }

        $username = trim((string) ($user['username'] ?? ''));

        if ($username === '') {
            $email = (string) ($user['email'] ?? '');
            $username = $email !== '' ? strstr($email, '@', true) ?: $email : 'user';
        }

        $initialsSource = $displayName !== '' ? $displayName : $username;
        $parts = preg_split('/\s+/', trim($initialsSource)) ?: [];
        $initials = '';

        foreach ($parts as $part) {
            if ($part === '') {
                continue;
            }

            $initials .= mb_strtoupper(mb_substr($part, 0, 1));

            if (mb_strlen($initials) >= 2) {
                break;
            }
        }

        if ($initials === '') {
            $initials = 'U';
        }

        return [
            'id' => (int) ($user['id'] ?? $userId),
            'email' => (string) ($user['email'] ?? ''),
            'username' => $username,
            'display_name' => $displayName,
            'initials' => $initials,
            'avatar_path' => (string) ($user['avatar_path'] ?? '/assets/images/avatars/default.jpg'),
            'status' => (string) ($user['status'] ?? ''),
        ];
    }
}