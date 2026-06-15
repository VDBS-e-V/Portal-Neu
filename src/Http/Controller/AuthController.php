<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Http\Request\Request;
use App\Http\Response\Response;
use App\Presentation\Templating\Renderer;
use App\Repository\AreaRepository;
use App\Repository\UserRepository;
use App\Security\SessionAuth;

final class AuthController extends PageController
{
    public function __construct(
        Renderer $renderer,
        AreaRepository $areas,
        private UserRepository $users,
        private SessionAuth $auth
    ) {
        parent::__construct($renderer, $areas);
    }

    public function loginForm(Request $request): Response
    {
        if ($this->auth->isLoggedIn()) {
            return $this->redirect('/user');
        }

        return $this->loginPage($request);
    }

    public function login(Request $request): Response
    {
        $email = $this->bodyString($request->body, 'email');
        $password = (string) ($request->body['password'] ?? '');
        $csrfToken = (string) ($request->body['_csrf'] ?? '');

        if (!$this->auth->validateCsrfToken($csrfToken)) {
            return $this->loginPage(
                $request,
                ['Die Sitzung ist abgelaufen. Bitte erneut versuchen.'],
                $email,
                419
            );
        }

        $user = $email !== '' ? $this->users->findByEmail($email) : [];

        if (
            $user === []
            || !$this->userCanLogin($user)
            || !password_verify($password, (string) ($user['password_hash'] ?? ''))
        ) {
            return $this->loginPage(
                $request,
                ['E-Mail oder Passwort ist falsch.'],
                $email,
                422
            );
        }

        $this->auth->login((int) $user['id']);

        return $this->redirect('/user');
    }

    public function logout(Request $request): Response
    {
        $this->auth->logout();

        return $this->redirect('/login');
    }

    public function profile(Request $request): Response
    {
        $user = $this->currentUser();

        if ($user === []) {
            return $this->redirect('/login');
        }

        return $this->renderPage($request, 'pages/user/profile', [
            'title' => 'Mein Konto',
            'areaName' => 'VDBS Portal',
            'pageTitle' => 'Mein Konto',
            'areaRootLink' => '/portal',
            'headerAreaKey' => 'portal',
            'areaNav' => $this->accountNav('profile'),
            'user' => $this->publicUser($user),
            'csrfToken' => $this->auth->csrfToken(),
        ]);
    }

    public function apiMe(Request $request): Response
    {
        $user = $this->currentUser();

        if ($user === []) {
            return $this->json([
                'authenticated' => false,
                'user' => null,
            ], 401);
        }

        return $this->json([
            'authenticated' => true,
            'user' => $this->publicUser($user),
        ]);
    }

    private function loginPage(
        Request $request,
        array $errors = [],
        string $email = '',
        int $status = 200
    ): Response {
        return $this->renderPage($request, 'pages/auth/login', [
            'title' => 'Login',
            'areaName' => 'VDBS Portal',
            'pageTitle' => 'Login',
            'headerAreaKey' => 'portal',
            'areaNav' => $this->accountNav('login'),
            'errors' => $errors,
            'email' => $email,
            'csrfToken' => $this->auth->csrfToken(),
        ], $status);
    }

    private function currentUser(): array
    {
        $userId = $this->auth->userId();

        if ($userId === null) {
            return [];
        }

        return $this->users->find($userId);
    }

    private function publicUser(array $user): array
    {
        unset($user['password_hash']);

        return $user;
    }

    private function userCanLogin(array $user): bool
    {
        $status = strtolower(trim((string) ($user['status'] ?? 'active')));

        return $status === '' || $status === '1' || $status === 'active';
    }

    private function accountNav(string $activeKey): array
    {
        return [
            [
                'label' => 'Login',
                'href' => '/login',
                'active' => $activeKey === 'login',
            ],
            [
                'label' => 'Mein Konto',
                'href' => '/user',
                'active' => $activeKey === 'profile',
            ],
        ];
    }
}