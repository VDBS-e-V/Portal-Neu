<?php

declare(strict_types=1);

namespace App\Security;

final class CsrfTokenManager
{
    private const SESSION_KEY = '_csrf_tokens';

    public function token(string $formName): string
    {
        $this->ensureSession();

        $formName = $this->normalizeFormName($formName);

        if (!isset($_SESSION[self::SESSION_KEY][$formName])) {
            $_SESSION[self::SESSION_KEY][$formName] = $this->newToken();
        }

        return (string) $_SESSION[self::SESSION_KEY][$formName];
    }

    public function rotate(string $formName): string
    {
        $this->ensureSession();

        $formName = $this->normalizeFormName($formName);
        $_SESSION[self::SESSION_KEY][$formName] = $this->newToken();

        return (string) $_SESSION[self::SESSION_KEY][$formName];
    }

    public function validate(string $formName, ?string $submittedToken): bool
    {
        $this->ensureSession();

        $formName = $this->normalizeFormName($formName);
        $submittedToken = trim((string) $submittedToken);

        if ($submittedToken === '') {
            return false;
        }

        $knownToken = (string) ($_SESSION[self::SESSION_KEY][$formName] ?? '');

        if ($knownToken === '') {
            return false;
        }

        return hash_equals($knownToken, $submittedToken);
    }

    public function invalidate(string $formName): void
    {
        $this->ensureSession();

        $formName = $this->normalizeFormName($formName);
        unset($_SESSION[self::SESSION_KEY][$formName]);
    }

    /**
     * @return array<string, string>
     */
    public function all(): array
    {
        $this->ensureSession();

        $tokens = $_SESSION[self::SESSION_KEY] ?? [];

        if (!is_array($tokens)) {
            return [];
        }

        return $tokens;
    }

    private function ensureSession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            if (!isset($_SESSION[self::SESSION_KEY]) || !is_array($_SESSION[self::SESSION_KEY])) {
                $_SESSION[self::SESSION_KEY] = [];
            }

            return;
        }

        if (headers_sent()) {
            if (!isset($_SESSION) || !is_array($_SESSION)) {
                $_SESSION = [];
            }

            if (!isset($_SESSION[self::SESSION_KEY]) || !is_array($_SESSION[self::SESSION_KEY])) {
                $_SESSION[self::SESSION_KEY] = [];
            }

            return;
        }

        session_start();

        if (!isset($_SESSION[self::SESSION_KEY]) || !is_array($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = [];
        }
    }

    private function newToken(): string
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }

    private function normalizeFormName(string $formName): string
    {
        $formName = trim($formName);

        return $formName === '' ? 'default' : $formName;
    }
}
