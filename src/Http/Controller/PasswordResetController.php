<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Audit\AuditLogger;
use App\Http\Request\Request;
use App\Http\Response\Response;
use App\Presentation\Templating\Renderer;
use App\Repository\PasswordResetRepository;
use App\Security\CsrfGuard;
use InvalidArgumentException;

final class PasswordResetController
{
    public function __construct(
        private readonly Renderer $renderer,
        private readonly PasswordResetRepository $resets,
        private readonly CsrfGuard $csrf,
        private readonly AuditLogger $audit
    ) {
    }

    public function requestForm(Request $request): Response
    {
        return new Response($this->renderer->render('pages/password/request', [
            'title' => 'Passwort vergessen',
            'errors' => [],
            'csrfToken' => $this->csrf->token('password.reset.request'),
        ]));
    }

    public function request(Request $request): Response
    {
        try {
            $this->csrf->requireValid($request, 'password.reset.request');

            $email = mb_strtolower($this->bodyString($request->body, 'email'));
            $result = $this->resets->createForEmail($email, 30);

            if ($result !== null) {
                $this->audit->log('password_reset.requested', 'ids_users', 0, [
                    'entity_label' => $email,
                    'metadata' => [
                        'password_reset_id' => $result['id'],
                    ],
                ]);
            }

            return new Response($this->renderer->render('pages/password/requested', [
                'title' => 'Passwort zurücksetzen',
                'resetUrl' => $result['reset_url'] ?? '',
            ]));
        } catch (InvalidArgumentException|\RuntimeException $exception) {
            return new Response($this->renderer->render('pages/password/request', [
                'title' => 'Passwort vergessen',
                'errors' => [$exception->getMessage()],
                'csrfToken' => $this->csrf->token('password.reset.request'),
            ]), 422);
        }
    }

    public function resetForm(Request $request): Response
    {
        $token = $this->routeString($request, 'token');
        $reset = $this->resets->findPendingByToken($token);

        if ($reset === []) {
            return new Response($this->renderer->render('pages/password/invalid', [
                'title' => 'Link ungültig',
            ]), 404);
        }

        return new Response($this->renderer->render('pages/password/reset', [
            'title' => 'Passwort neu setzen',
            'token' => $token,
            'reset' => $reset,
            'errors' => [],
            'csrfToken' => $this->csrf->token('password.reset.' . $token),
        ]));
    }

    public function reset(Request $request): Response
    {
        $token = $this->routeString($request, 'token');

        try {
            $this->csrf->requireValid($request, 'password.reset.' . $token);

            $password = $this->bodyString($request->body, 'password');
            $passwordRepeat = $this->bodyString($request->body, 'password_repeat');

            if ($password !== $passwordRepeat) {
                throw new InvalidArgumentException('Die Passwörter stimmen nicht überein.');
            }

            $userId = $this->resets->resetPassword($token, $password);

            $this->audit->log('password_reset.completed', 'ids_users', $userId, [
                'metadata' => [
                    'route' => '/passwort/zuruecksetzen/{token}',
                ],
            ]);

            return new Response($this->renderer->render('pages/password/reset_done', [
                'title' => 'Passwort geändert',
            ]));
        } catch (InvalidArgumentException|\RuntimeException $exception) {
            $reset = $this->resets->findPendingByToken($token);

            return new Response($this->renderer->render('pages/password/reset', [
                'title' => 'Passwort neu setzen',
                'token' => $token,
                'reset' => $reset,
                'errors' => [$exception->getMessage()],
                'csrfToken' => $this->csrf->token('password.reset.' . $token),
            ]), 422);
        }
    }

    private function routeString(Request $request, string $key, string $default = ''): string
    {
        $routeParams = $request->routeParams ?? [];

        if (array_key_exists($key, $routeParams)) {
            return trim((string) $routeParams[$key]);
        }

        return $default;
    }

    /**
     * @param array<string, mixed> $body
     */
    private function bodyString(array $body, string $key, string $default = ''): string
    {
        if (!array_key_exists($key, $body)) {
            return $default;
        }

        return trim((string) $body[$key]);
    }
}
