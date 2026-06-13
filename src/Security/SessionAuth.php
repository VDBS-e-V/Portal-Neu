<?php

declare(strict_types=1);

namespace App\Security;

final class SessionAuth
{
    private string $userIdKey = 'auth_user_id';
    private string $csrfTokenKey = 'auth_csrf_token';

    public function __construct()
    {
        $this->startSession();
    }

    public function login(int $userId): void
    {
        $this->startSession();

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }

        $_SESSION[$this->userIdKey] = $userId;
    }

    public function logout(): void
    {
        $this->startSession();

        unset($_SESSION[$this->userIdKey]);

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }

    public function isLoggedIn(): bool
    {
        return $this->userId() !== null;
    }

    public function userId(): ?int
    {
        $this->startSession();

        $value = $_SESSION[$this->userIdKey] ?? null;

        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }

    public function csrfToken(): string
    {
        $this->startSession();

        if (empty($_SESSION[$this->csrfTokenKey]) || !is_string($_SESSION[$this->csrfTokenKey])) {
            $_SESSION[$this->csrfTokenKey] = bin2hex(random_bytes(32));
        }

        return $_SESSION[$this->csrfTokenKey];
    }

    public function validateCsrfToken(string $token): bool
    {
        return $token !== '' && hash_equals($this->csrfToken(), $token);
    }

    private function startSession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public function id(): ?int
    {
        return $this->userId();
    }

    public function check(): bool
    {
        return $this->isLoggedIn();
    }

    public function validateCsrf(?string $token): bool
    {
        return $this->validateCsrfToken((string) $token);
    }
}