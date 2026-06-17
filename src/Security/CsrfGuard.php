<?php

declare(strict_types=1);

namespace App\Security;

use App\Http\Request\Request;

final class CsrfGuard
{
    public const FIELD_NAME = '_csrf_token';

    public function __construct(private readonly CsrfTokenManager $tokens)
    {
    }

    public function token(string $formName): string
    {
        return $this->tokens->token($formName);
    }

    public function hiddenField(string $formName): string
    {
        return sprintf(
            '<input type="hidden" name="%s" value="%s">',
            htmlspecialchars(self::FIELD_NAME, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($this->token($formName), ENT_QUOTES, 'UTF-8')
        );
    }

    public function requireValid(Request $request, string $formName): void
    {
        $token = null;

        if (isset($request->body[self::FIELD_NAME])) {
            $token = (string) $request->body[self::FIELD_NAME];
        }

        if (!$this->tokens->validate($formName, $token)) {
            throw new CsrfException('Ungültiges oder abgelaufenes Formular-Token.');
        }
    }

    public function requireValidAndRotate(Request $request, string $formName): void
    {
        $this->requireValid($request, $formName);
        $this->tokens->rotate($formName);
    }
}
