<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Audit\AuditLogger;
use App\Http\Request\Request;
use App\Http\Response\Response;
use App\Presentation\Templating\Renderer;
use App\Repository\AccountProfileRepository;
use App\Repository\LoginEventRepository;
use App\Security\AccountSessionContext;
use App\Security\AuthorizationException;
use App\Security\CsrfGuard;
use InvalidArgumentException;

final class ProfileController
{
    public function __construct(
        private readonly Renderer $renderer,
        private readonly AccountSessionContext $account,
        private readonly AccountProfileRepository $profiles,
        private readonly LoginEventRepository $loginEvents,
        private readonly CsrfGuard $csrf,
        private readonly AuditLogger $audit
    ) {
    }

    public function profile(Request $request): Response
    {
        $user = $this->requireUserOrRedirect();

        if ($user instanceof Response) {
            return $user;
        }

        $userId = (int) $user['id'];

        return new Response($this->renderer->render('pages/account/profile', [
            'title' => 'Mein Profil',
            'profile' => $this->profiles->profileForUser($userId),
            'contacts' => $this->profiles->contactsForUser($userId),
            'addresses' => $this->profiles->addressesForUser($userId),
        ]));
    }

    public function editForm(Request $request): Response
    {
        $user = $this->requireUserOrRedirect();

        if ($user instanceof Response) {
            return $user;
        }

        return new Response($this->renderer->render('pages/account/profile_form', [
            'title' => 'Profil bearbeiten',
            'profile' => $this->profiles->profileForUser((int) $user['id']),
            'errors' => [],
            'csrfToken' => $this->csrf->token('account.profile.edit'),
        ]));
    }

    public function edit(Request $request): Response
    {
        $user = $this->requireUserOrRedirect();

        if ($user instanceof Response) {
            return $user;
        }

        $userId = (int) $user['id'];

        try {
            $this->csrf->requireValid($request, 'account.profile.edit');

            $data = $this->profileDataFromRequest($request);
            $oldProfile = $this->profiles->profileForUser($userId);

            $this->profiles->updateProfileForUser($userId, $data);

            $this->audit->logChange('account.profile_updated', 'ids_users', $userId, $oldProfile, $data, [
                'actor_user_id' => $userId,
                'entity_label' => $user['email'] ?? null,
            ]);

            return $this->redirect('/konto/profil?message=updated');
        } catch (InvalidArgumentException|\RuntimeException $exception) {
            return new Response($this->renderer->render('pages/account/profile_form', [
                'title' => 'Profil bearbeiten',
                'profile' => array_merge($this->profiles->profileForUser($userId), $request->body),
                'errors' => [$exception->getMessage()],
                'csrfToken' => $this->csrf->token('account.profile.edit'),
            ]), 422);
        }
    }

    public function contacts(Request $request): Response
    {
        $user = $this->requireUserOrRedirect();

        if ($user instanceof Response) {
            return $user;
        }

        $userId = (int) $user['id'];
        $contacts = $this->profiles->contactsForUser($userId);
        $deleteTokens = [];

        foreach ($contacts as $contact) {
            $contactId = (int) ($contact['id'] ?? 0);
            $deleteTokens[$contactId] = $this->csrf->token('account.contact.delete.' . $contactId);
        }

        return new Response($this->renderer->render('pages/account/contacts', [
            'title' => 'Meine Kontakte',
            'contacts' => $contacts,
            'errors' => [],
            'message' => $this->queryString($request, 'message'),
            'csrfToken' => $this->csrf->token('account.contact.create'),
            'deleteTokens' => $deleteTokens,
        ]));
    }

    public function createContact(Request $request): Response
    {
        $user = $this->requireUserOrRedirect();

        if ($user instanceof Response) {
            return $user;
        }

        try {
            $this->csrf->requireValid($request, 'account.contact.create');

            $contactId = $this->profiles->createContactForUser((int) $user['id'], [
                'contact_type' => $this->bodyString($request->body, 'contact_type', 'email'),
                'label' => $this->bodyNullableString($request->body, 'label'),
                'value' => $this->bodyString($request->body, 'value'),
                'is_primary' => $this->bodyBool($request->body, 'is_primary'),
            ]);

            $this->audit->log('account.contact.created', 'account_contact', $contactId, [
                'actor_user_id' => (int) $user['id'],
                'entity_label' => $user['email'] ?? null,
            ]);

            return $this->redirect('/konto/kontakte?message=created');
        } catch (InvalidArgumentException|\RuntimeException $exception) {
            return new Response($this->renderer->render('pages/account/contacts', [
                'title' => 'Meine Kontakte',
                'contacts' => $this->profiles->contactsForUser((int) $user['id']),
                'errors' => [$exception->getMessage()],
                'message' => '',
                'csrfToken' => $this->csrf->token('account.contact.create'),
                'deleteTokens' => [],
            ]), 422);
        }
    }

    public function deleteContact(Request $request): Response
    {
        $user = $this->requireUserOrRedirect();

        if ($user instanceof Response) {
            return $user;
        }

        $contactId = $this->routeInt($request, 'contactId');

        $this->csrf->requireValid($request, 'account.contact.delete.' . $contactId);
        $this->profiles->deleteContactForUser((int) $user['id'], $contactId);

        $this->audit->log('account.contact.deleted', 'account_contact', $contactId, [
            'actor_user_id' => (int) $user['id'],
            'entity_label' => $user['email'] ?? null,
        ]);

        return $this->redirect('/konto/kontakte?message=deleted');
    }

    public function addresses(Request $request): Response
    {
        $user = $this->requireUserOrRedirect();

        if ($user instanceof Response) {
            return $user;
        }

        $userId = (int) $user['id'];
        $addresses = $this->profiles->addressesForUser($userId);
        $deleteTokens = [];

        foreach ($addresses as $address) {
            $addressId = (int) ($address['id'] ?? 0);
            $deleteTokens[$addressId] = $this->csrf->token('account.address.delete.' . $addressId);
        }

        return new Response($this->renderer->render('pages/account/addresses', [
            'title' => 'Meine Adressen',
            'addresses' => $addresses,
            'errors' => [],
            'message' => $this->queryString($request, 'message'),
            'csrfToken' => $this->csrf->token('account.address.create'),
            'deleteTokens' => $deleteTokens,
        ]));
    }

    public function createAddress(Request $request): Response
    {
        $user = $this->requireUserOrRedirect();

        if ($user instanceof Response) {
            return $user;
        }

        try {
            $this->csrf->requireValid($request, 'account.address.create');

            $addressId = $this->profiles->createAddressForUser((int) $user['id'], [
                'address_type' => $this->bodyString($request->body, 'address_type', 'private'),
                'recipient_name' => $this->bodyNullableString($request->body, 'recipient_name'),
                'organization' => $this->bodyNullableString($request->body, 'organization'),
                'street' => $this->bodyNullableString($request->body, 'street'),
                'house_number' => $this->bodyNullableString($request->body, 'house_number'),
                'address_addition' => $this->bodyNullableString($request->body, 'address_addition'),
                'postal_code' => $this->bodyNullableString($request->body, 'postal_code'),
                'city' => $this->bodyNullableString($request->body, 'city'),
                'state' => $this->bodyNullableString($request->body, 'state'),
                'country' => $this->bodyString($request->body, 'country', 'DE'),
                'is_primary' => $this->bodyBool($request->body, 'is_primary'),
            ]);

            $this->audit->log('account.address.created', 'account_address', $addressId, [
                'actor_user_id' => (int) $user['id'],
                'entity_label' => $user['email'] ?? null,
            ]);

            return $this->redirect('/konto/adressen?message=created');
        } catch (InvalidArgumentException|\RuntimeException $exception) {
            return new Response($this->renderer->render('pages/account/addresses', [
                'title' => 'Meine Adressen',
                'addresses' => $this->profiles->addressesForUser((int) $user['id']),
                'errors' => [$exception->getMessage()],
                'message' => '',
                'csrfToken' => $this->csrf->token('account.address.create'),
                'deleteTokens' => [],
            ]), 422);
        }
    }

    public function deleteAddress(Request $request): Response
    {
        $user = $this->requireUserOrRedirect();

        if ($user instanceof Response) {
            return $user;
        }

        $addressId = $this->routeInt($request, 'addressId');

        $this->csrf->requireValid($request, 'account.address.delete.' . $addressId);
        $this->profiles->deleteAddressForUser((int) $user['id'], $addressId);

        $this->audit->log('account.address.deleted', 'account_address', $addressId, [
            'actor_user_id' => (int) $user['id'],
            'entity_label' => $user['email'] ?? null,
        ]);

        return $this->redirect('/konto/adressen?message=deleted');
    }

    public function security(Request $request): Response
    {
        $user = $this->requireUserOrRedirect();

        if ($user instanceof Response) {
            return $user;
        }

        return new Response($this->renderer->render('pages/account/security', [
            'title' => 'Sicherheit',
            'user' => $user,
            'events' => $this->loginEvents->latestForUser((int) $user['id'], 50),
        ]));
    }

    /**
     * @return array<string, mixed>|Response
     */
    private function requireUserOrRedirect(): array|Response
    {
        try {
            return $this->account->requireCurrentUser();
        } catch (AuthorizationException) {
            return $this->redirect('/login');
        }
    }

    private function redirect(string $location, int $status = 302): Response
    {
        return new Response('', $status, ['Location' => $location]);
    }

    private function routeInt(Request $request, string $key, int $default = 0): int
    {
        $routeParams = $request->routeParams ?? [];

        if (array_key_exists($key, $routeParams)) {
            return (int) $routeParams[$key];
        }

        return $default;
    }

    private function queryString(Request $request, string $key, string $default = ''): string
    {
        $query = $request->query ?? [];

        if (array_key_exists($key, $query)) {
            return trim((string) $query[$key]);
        }

        return $default;
    }

    /**
     * @return array<string, mixed>
     */
    private function profileDataFromRequest(Request $request): array
    {
        return [
            'display_name' => $this->bodyNullableString($request->body, 'display_name'),
            'salutation' => $this->bodyNullableString($request->body, 'salutation'),
            'title' => $this->bodyNullableString($request->body, 'title'),
            'first_name' => $this->bodyNullableString($request->body, 'first_name'),
            'middle_name' => $this->bodyNullableString($request->body, 'middle_name'),
            'last_name' => $this->bodyNullableString($request->body, 'last_name'),
            'preferred_name' => $this->bodyNullableString($request->body, 'preferred_name'),
            'pronouns' => $this->bodyNullableString($request->body, 'pronouns'),
        ];
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

    /**
     * @param array<string, mixed> $body
     */
    private function bodyNullableString(array $body, string $key): ?string
    {
        $value = $this->bodyString($body, $key);

        return $value === '' ? null : $value;
    }

    /**
     * @param array<string, mixed> $body
     */
    private function bodyBool(array $body, string $key): bool
    {
        if (!array_key_exists($key, $body)) {
            return false;
        }

        $value = $body[$key];

        return $value === true || $value === '1' || $value === 1 || $value === 'on' || $value === 'yes';
    }
}
