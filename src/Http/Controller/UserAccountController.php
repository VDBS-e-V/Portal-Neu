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

final class UserAccountController extends Controller
{
    public function __construct(
        Renderer $renderer,
        AreaRepository $areas,
        MenuRepository $menus,
        MenuItemRepository $menuItems,
        private readonly UserRepository $users,
        private readonly SessionAuth $auth
    ) {
        // Diese Dependencies bleiben aus Kompatibilitätsgründen in der Service-Factory,
        // werden im neuen Konto-Controller aber nicht mehr benötigt.
        unset($areas, $menus, $menuItems);

        parent::__construct($renderer);
    }

    public function profile(Request $request): Response
    {
        $user = $this->requireUser();
        if ($user === null) {
            return $this->redirect('/login?redirect=/konto');
        }

        $userId = (int) $user['id'];

        return $this->renderKontoPage('pages/konto/profile', [
            'title' => 'Mein Konto',
            'pageTitle' => 'Mein Konto',
            'areaName' => 'Konto',
            'areaRootLink' => '/konto',
            'canonicalProfileUrl' => '/konto',
            'canonicalSettingsUrl' => '/konto/einstellungen',
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
            return $this->redirect('/login?redirect=/konto');
        }

        if (!$this->auth->validateCsrf($request->body['_csrf'] ?? null)) {
            return $this->redirect('/konto');
        }

        $userId = (int) $user['id'];

        $this->users->saveName($userId, [
            'salutation' => $this->nullableString($request->body['salutation'] ?? null),
            'title' => $this->nullableString($request->body['title'] ?? null),
            'first_name' => $this->nullableString($request->body['first_name'] ?? null),
            'middle_name' => $this->nullableString($request->body['middle_name'] ?? null),
            'last_name' => $this->nullableString($request->body['last_name'] ?? null),
            'preferred_name' => $this->nullableString($request->body['preferred_name'] ?? null),
            'pronouns' => $this->nullableString($request->body['pronouns'] ?? null),
        ]);

        $this->users->saveSimpleContacts(
            $userId,
            $this->nullableString($request->body['phone'] ?? null),
            $this->nullableString($request->body['mobile'] ?? null),
            $this->nullableString($request->body['website'] ?? null),
        );

        $this->users->savePrimaryAddress($userId, [
            'address_type' => $this->nullableString($request->body['address_type'] ?? null) ?: 'private',
            'recipient_name' => $this->nullableString($request->body['recipient_name'] ?? null),
            'organization' => $this->nullableString($request->body['organization'] ?? null),
            'street' => $this->nullableString($request->body['street'] ?? null),
            'house_number' => $this->nullableString($request->body['house_number'] ?? null),
            'address_addition' => $this->nullableString($request->body['address_addition'] ?? null),
            'postal_code' => $this->nullableString($request->body['postal_code'] ?? null),
            'city' => $this->nullableString($request->body['city'] ?? null),
            'state' => $this->nullableString($request->body['state'] ?? null),
            'country' => $this->nullableString($request->body['country'] ?? null) ?: 'DE',
        ]);

        $displayName = $this->displayNameFromRequest($request);
        if ($displayName !== '') {
            $this->users->update($userId, ['display_name' => $displayName]);
        }

        return $this->redirect('/konto?saved=profile');
    }

    public function settings(Request $request): Response
    {
        $user = $this->requireUser();
        if ($user === null) {
            return $this->redirect('/login?redirect=/konto/einstellungen');
        }

        return $this->renderSettings($request, $user);
    }

    public function updateSettings(Request $request): Response
    {
        $user = $this->requireUser();
        if ($user === null) {
            return $this->redirect('/login?redirect=/konto/einstellungen');
        }

        if (!$this->auth->validateCsrf($request->body['_csrf'] ?? null)) {
            return $this->redirect('/konto/einstellungen');
        }

        $this->users->saveSettings((int) $user['id'], [
            'language' => $this->nullableString($request->body['language'] ?? null) ?: 'de',
            'timezone' => $this->nullableString($request->body['timezone'] ?? null) ?: 'Europe/Berlin',
            'email_notifications' => isset($request->body['email_notifications']) ? 1 : 0,
            'profile_visibility' => $this->nullableString($request->body['profile_visibility'] ?? null) ?: 'private',
        ]);

        return $this->redirect('/konto/einstellungen?saved=settings');
    }

    public function updatePassword(Request $request): Response
    {
        $user = $this->requireUser();
        if ($user === null) {
            return $this->redirect('/login?redirect=/konto/einstellungen');
        }

        if (!$this->auth->validateCsrf($request->body['_csrf'] ?? null)) {
            return $this->redirect('/konto/einstellungen');
        }

        $currentPassword = (string) ($request->body['current_password'] ?? '');
        $newPassword = (string) ($request->body['new_password'] ?? '');
        $newPasswordRepeat = (string) ($request->body['new_password_repeat'] ?? '');
        $hash = (string) ($user['password_hash'] ?? '');

        if ($hash === '' || !password_verify($currentPassword, $hash)) {
            return $this->renderSettings($request, $user, 'Das aktuelle Passwort ist falsch.', 422);
        }

        if (strlen($newPassword) < 10) {
            return $this->renderSettings($request, $user, 'Das neue Passwort muss mindestens 10 Zeichen lang sein.', 422);
        }

        if ($newPassword !== $newPasswordRepeat) {
            return $this->renderSettings($request, $user, 'Die Wiederholung des neuen Passworts stimmt nicht überein.', 422);
        }

        $this->users->update((int) $user['id'], [
            'password_hash' => password_hash($newPassword, PASSWORD_DEFAULT),
        ]);

        return $this->redirect('/konto/einstellungen?saved=password');
    }

    private function renderSettings(Request $request, array $user, ?string $passwordError = null, int $status = 200): Response
    {
        return $this->renderKontoPage('pages/konto/settings', [
            'title' => 'Kontoeinstellungen',
            'pageTitle' => 'Kontoeinstellungen',
            'areaName' => 'Konto',
            'areaRootLink' => '/konto',
            'canonicalProfileUrl' => '/konto',
            'canonicalSettingsUrl' => '/konto/einstellungen',
            'user' => $user,
            'settings' => $this->users->settingsForUser((int) $user['id']),
            'csrfToken' => $this->auth->csrfToken(),
            'saved' => ($request->query['saved'] ?? '') === 'settings',
            'passwordChanged' => ($request->query['saved'] ?? '') === 'password',
            'passwordError' => $passwordError,
        ], $status);
    }

    /**
     * @param array<string,mixed> $parameters
     */
    private function renderKontoPage(string $view, array $parameters = [], int $status = 200): Response
    {
        return new Response(
            $status,
            ['Content-Type' => 'text/html; charset=utf-8'],
            $this->renderer->renderPage($view, $parameters)
        );
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

    private function nullableString(mixed $value): ?string
    {
        $value = trim((string) ($value ?? ''));
        return $value === '' ? null : $value;
    }

    private function displayNameFromRequest(Request $request): string
    {
        $preferredName = trim((string) ($request->body['preferred_name'] ?? ''));
        if ($preferredName !== '') {
            return $preferredName;
        }

        return trim(
            (string) ($request->body['first_name'] ?? '') . ' ' .
            (string) ($request->body['last_name'] ?? '')
        );
    }
}
