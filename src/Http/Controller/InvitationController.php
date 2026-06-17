<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Audit\AuditLogger;
use App\Http\Request\Request;
use App\Http\Response\Response;
use App\Presentation\Templating\Renderer;
use App\Repository\UserInvitationRepository;
use InvalidArgumentException;

final class InvitationController
{
    public function __construct(
        private readonly Renderer $renderer,
        private readonly UserInvitationRepository $invitations,
        private readonly AuditLogger $audit
    ) {
    }

    public function acceptForm(Request $request): Response
    {
        $token = $this->routeString($request, 'token');
        $invitation = $this->invitations->findPendingByToken($token);

        if ($invitation === []) {
            return new Response(
                $this->renderer->render('pages/invitations/invalid', [
                    'title' => 'Einladung ungültig',
                ]),
                404
            );
        }

        return new Response($this->renderer->render('pages/invitations/accept', [
            'title' => 'Einladung annehmen',
            'token' => $token,
            'invitation' => $invitation,
            'errors' => [],
        ]));
    }

    public function accept(Request $request): Response
    {
        $token = $this->routeString($request, 'token');

        try {
            $password = $this->bodyString($request->body, 'password');
            $passwordRepeat = $this->bodyString($request->body, 'password_repeat');

            if ($password !== $passwordRepeat) {
                throw new InvalidArgumentException('Die Passwörter stimmen nicht überein.');
            }

            $userId = $this->invitations->accept($token, $password);

            $this->audit->log('invitation.accepted', 'ids_users', $userId, [
                'metadata' => [
                    'route' => '/einladung/{token}',
                ],
            ]);

            return new Response($this->renderer->render('pages/invitations/accepted', [
                'title' => 'Einladung angenommen',
            ]));
        } catch (InvalidArgumentException $exception) {
            $invitation = $this->invitations->findPendingByToken($token);

            return new Response($this->renderer->render('pages/invitations/accept', [
                'title' => 'Einladung annehmen',
                'token' => $token,
                'invitation' => $invitation,
                'errors' => [$exception->getMessage()],
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
