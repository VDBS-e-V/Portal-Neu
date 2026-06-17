<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Http\Request\Request;
use App\Security\CsrfGuard;

trait CsrfControllerTrait
{
    private readonly CsrfGuard $csrf;

    private function csrfToken(string $formName): string
    {
        return $this->csrf->token($formName);
    }

    private function csrfField(string $formName): string
    {
        return $this->csrf->hiddenField($formName);
    }

    private function requireCsrf(Request $request, string $formName): void
    {
        $this->csrf->requireValid($request, $formName);
    }

    private function requireCsrfAndRotate(Request $request, string $formName): void
    {
        $this->csrf->requireValidAndRotate($request, $formName);
    }

    /**
     * @return array<string, mixed>
     */
    private function csrfViewParams(string $formName): array
    {
        return [
            'csrfForm' => $formName,
            'csrfToken' => $this->csrfToken($formName),
            'csrfField' => $this->csrfField($formName),
        ];
    }
}
