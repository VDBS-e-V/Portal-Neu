<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Http\Request\Request;
use App\Http\Response\Response;
use App\Presentation\Templating\Renderer;
use App\Repository\AreaRepository;
use App\Repository\MenuItemRepository;
use App\Repository\MenuRepository;
use App\Repository\UserRepository;
use App\Security\SessionAuth;

final class UserAccountController extends PageController
{
    public function __construct(
        Renderer $renderer,
        AreaRepository $areas,
        MenuRepository $menus,
        MenuItemRepository $menuItems,
        private readonly UserRepository $users,
        private readonly SessionAuth $auth,
    ) {
        parent::__construct($renderer, $areas, $menus, $menuItems);
    }

    public function profile(Request $request): Response
    {
        $user = $this->requireUser();

        if ($user === null) {
            return $this->redirect('/login?redirect=/user');
        }

        $userId = (int) $user['id'];

        return $this->renderPage($request, 'pages/user/profile', [
            'title' => 'Mein Profil',
            'pageTitle' => 'Mein Profil',
            'areaName' => 'Konto',
            'areaRootLink' => '/user',
            'user' => $user,
            'name' => $this->users->nameForUser($userId),
            'contacts' => $this->users->contactsForUser($userId),
            'address' => $this->users->primaryAddressForUser($userId),
            'settings' => $this->users->settingsForUser($userId),
            'csrfToken' => $this->auth->csrfToken(),
            'saved' => ($request->query['saved'] ?? '') === 'profile',
        ]);
    }

    public function updateProfile(Request $request): Response
    {
        $user = $this->requireUser();

        if ($user === null) {
            return $this->redirect('/login?redirect=/user');
        }

        if (!$this->auth->validateCsrf($request->body['_csrf'] ?? null)) {
            return $this->redirect('/user');
        }

        $userId = (int) $user['id'];

        $this->users->saveName($userId, [
            'salutation' => $request->body['salutation'] ?? null,
            'title' => $request->body['title'] ?? null,
            'first_name' => $request->body['first_name'] ?? null,
            'middle_name' => $request->body['middle_name'] ?? null,
            'last_name' => $request->body['last_name'] ?? null,
            'preferred_name' => $request->body['preferred_name'] ?? null,
            'pronouns' => $request->body['pronouns'] ?? null,
        ]);

        $this->users->saveSimpleContacts(
            $userId,
            $request->body['phone'] ?? null,
            $request->body['mobile'] ?? null,
            $request->body['website'] ?? null,
        );

        $this->users->savePrimaryAddress($userId, [
            'address_type' => $request->body['address_type'] ?? 'private',
            'recipient_name' => $request->body['recipient_name'] ?? null,
            'organization' => $request->body['organization'] ?? null,
            'street' => $request->body['street'] ?? null,
            'house_number' => $request->body['house_number'] ?? null,
            'address_addition' => $request->body['address_addition'] ?? null,
            'postal_code' => $request->body['postal_code'] ?? null,
            'city' => $request->body['city'] ?? null,
            'state' => $request->body['state'] ?? null,
            'country' => $request->body['country'] ?? 'DE',
        ]);

        $displayName = trim((string) ($request->body['preferred_name'] ?? ''));

        if ($displayName === '') {
            $displayName = trim(
                (string) ($request->body['first_name'] ?? '')
                . ' '
                . (string) ($request->body['last_name'] ?? '')
            );
        }

        if ($displayName !== '') {
            $this->users->update($userId, [
                'display_name' => $displayName,
            ]);
        }

        return $this->redirect('/user?saved=profile');
    }

    public function settings(Request $request): Response
    {
        $user = $this->requireUser();

        if ($user === null) {
            return $this->redirect('/login?redirect=/user/settings');
        }

        return $this->renderPage($request, 'pages/user/settings', [
            'title' => 'Kontoeinstellungen',
            'pageTitle' => 'Kontoeinstellungen',
            'areaName' => 'Konto',
            'areaRootLink' => '/user',
            'user' => $user,
            'settings' => $this->users->settingsForUser((int) $user['id']),
            'csrfToken' => $this->auth->csrfToken(),
            'saved' => ($request->query['saved'] ?? '') === 'settings',
            'passwordChanged' => ($request->query['saved'] ?? '') === 'password',
            'passwordError' => null,
        ]);
    }

    public function updateSettings(Request $request): Response
    {
        $user = $this->requireUser();

        if ($user === null) {
            return $this->redirect('/login?redirect=/user/settings');
        }

        if (!$this->auth->validateCsrf($request->body['_csrf'] ?? null)) {
            return $this->redirect('/user/settings');
        }

        $this->users->saveSettings((int) $user['id'], [
            'language' => $request->body['language'] ?? 'de',
            'timezone' => $request->body['timezone'] ?? 'Europe/Berlin',
            'email_notifications' => $request->body['email_notifications'] ?? null,
            'profile_visibility' => $request->body['profile_visibility'] ?? 'private',
        ]);

        return $this->redirect('/user/settings?saved=settings');
    }

    public function updatePassword(Request $request): Response
    {
        $user = $this->requireUser();

        if ($user === null) {
            return $this->redirect('/login?redirect=/user/settings');
        }

        if (!$this->auth->validateCsrf($request->body['_csrf'] ?? null)) {
            return $this->redirect('/user/settings');
        }

        $currentPassword = (string) ($request->body['current_password'] ?? '');
        $newPassword = (string) ($request->body['new_password'] ?? '');
        $newPasswordRepeat = (string) ($request->body['new_password_repeat'] ?? '');
        $hash = (string) ($user['password_hash'] ?? '');

        if ($hash === '' || !password_verify($currentPassword, $hash)) {
            return $this->settingsWithPasswordError($request, $user, 'Das aktuelle Passwort ist falsch.');
        }

        if (strlen($newPassword) < 10) {
            return $this->settingsWithPasswordError($request, $user, 'Das neue Passwort muss mindestens 10 Zeichen lang sein.');
        }

        if ($newPassword !== $newPasswordRepeat) {
            return $this->settingsWithPasswordError($request, $user, 'Die Wiederholung des neuen Passworts stimmt nicht überein.');
        }

        $this->users->update((int) $user['id'], [
            'password_hash' => password_hash($newPassword, PASSWORD_DEFAULT),
        ]);

        return $this->redirect('/user/settings?saved=password');
    }

    private function settingsWithPasswordError(Request $request, array $user, string $error): Response
    {
        return $this->renderPage($request, 'pages/user/settings', [
            'title' => 'Kontoeinstellungen',
            'pageTitle' => 'Kontoeinstellungen',
            'areaName' => 'Konto',
            'areaRootLink' => '/user',
            'user' => $user,
            'settings' => $this->users->settingsForUser((int) $user['id']),
            'csrfToken' => $this->auth->csrfToken(),
            'saved' => false,
            'passwordChanged' => false,
            'passwordError' => $error,
        ], 422);
    }

    private function requireUser(): ?array
    {
        $id = $this->auth->id();

        if ($id === null) {
            return null;
        }

        $user = $this->users->find($id);

        if ($user === [] || ($user['status'] ?? null) !== 'active') {
            $this->auth->logout();

            return null;
        }

        return $user;
    }
}