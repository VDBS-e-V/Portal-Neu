<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Audit\AuditLogger;
use App\Http\Request\Request;
use App\Http\Response\Response;
use App\Presentation\Templating\Renderer;
use App\Repository\UserPasswordRepository;
use App\Security\AccountSessionContext;
use App\Security\AuthorizationException;
use App\Security\CsrfGuard;
use InvalidArgumentException;

final class AccountController
{
    public function __construct(
        private readonly Renderer $renderer,
        private readonly AccountSessionContext $account,
        private readonly UserPasswordRepository $passwords,
        private readonly CsrfGuard $csrf,
        private readonly AuditLogger $audit
    ) {
    }

    public function index(Request $request): Response
    {
        try {
            $user = $this->account->requireCurrentUser();
        } catch (AuthorizationException) {
            return $this->redirect('/login');
        }

        return new Response($this->renderer->render('pages/account/index', [
            'title' => 'Mein Konto',
            'user' => $user,
        ]));
    }

    public function passwordForm(Request $request): Response
    {
        try {
            $user = $this->account->requireCurrentUser();
        } catch (AuthorizationException) {
            return $this->redirect('/login');
        }

        return new Response($this->renderer->render('pages/account/password', [
            'title' => 'Passwort ändern',
            'user' => $user,
            'errors' => [],
            'message' => '',
            'csrfToken' => $this->csrf->token('account.password.change'),
        ]));
    }

    public function changePassword(Request $request): Response
    {
        try {
            $user = $this->account->requireCurrentUser();
        } catch (AuthorizationException) {
            return $this->redirect('/login');
        }

        try {
            $this->csrf->requireValid($request, 'account.password.change');

            $currentPassword = $this->bodyString($request->body, 'current_password');
            $newPassword = $this->bodyString($request->body, 'new_password');
            $newPasswordRepeat = $this->bodyString($request->body, 'new_password_repeat');

            if ($newPassword !== $newPasswordRepeat) {
                throw new InvalidArgumentException('Die neuen Passwörter stimmen nicht überein.');
            }

            $this->passwords->changePassword((int) $user['id'], $currentPassword, $newPassword);

            $this->audit->log('account.password_changed', 'ids_users', (int) $user['id'], [
                'actor_user_id' => (int) $user['id'],
                'entity_label' => $user['email'] ?? null,
            ]);

            return new Response($this->renderer->render('pages/account/password', [
                'title' => 'Passwort ändern',
                'user' => $user,
                'errors' => [],
                'message' => 'Das Passwort wurde geändert.',
                'csrfToken' => $this->csrf->token('account.password.change'),
            ]));
        } catch (InvalidArgumentException|\RuntimeException $exception) {
            return new Response($this->renderer->render('pages/account/password', [
                'title' => 'Passwort ändern',
                'user' => $user,
                'errors' => [$exception->getMessage()],
                'message' => '',
                'csrfToken' => $this->csrf->token('account.password.change'),
            ]), 422);
        }
    }

    private function redirect(string $location, int $status = 302): Response
    {
        return new Response('', $status, ['Location' => $location]);
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
