<?php

declare(strict_types=1);

namespace App\Security;

use RuntimeException;

final class AuthorizationException extends RuntimeException
{
    public function __construct(
        string $message = 'Zugriff verweigert.',
        private readonly int $statusCode = 403
    ) {
        parent::__construct($message, $statusCode);
    }

    public function statusCode(): int
    {
        return $this->statusCode;
    }
}