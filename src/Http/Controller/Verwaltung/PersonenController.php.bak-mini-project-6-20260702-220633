<?php

declare(strict_types=1);

namespace App\Http\Controller\Verwaltung;

use App\Audit\AuditLogger;
use App\Http\Controller\PageController;
use App\Http\Request\Request;
use App\Http\Response\Response;
use App\Presentation\Templating\Renderer;
use App\Repository\AreaRepository;
use App\Repository\MenuItemRepository;
use App\Repository\MenuRepository;
use App\Repository\PermissionGroupRepository;
use App\Repository\PersonAddressRepository;
use App\Repository\PersonContactRepository;
use App\Repository\PersonPermissionGroupRepository;
use App\Repository\PersonRepository;
use App\Security\AdminSafetyService;
use App\Security\AuthorizationService;
use App\Security\VerwaltungAccess;
use InvalidArgumentException;

final class PersonenController extends PageController
{
    public function __construct(
        Renderer $renderer,
        AreaRepository $areas,
        MenuRepository $menus,
        MenuItemRepository $menuItems,
        private readonly PersonRepository $persons,
        private readonly PersonContactRepository $contacts,
        private readonly PersonAddressRepository $addresses,
        private readonly PermissionGroupRepository $permissionGroups,
        private readonly PersonPermissionGroupRepository $personGroups,
        private readonly AuthorizationService $authorization,
        private readonly AdminSafetyService $adminSafety,
        private readonly AuditLogger $audit
    ) {
        parent::__construct($renderer, $areas, $menus, $menuItems);
    }

    public function index(Request $request): Response
    {
        $this->authorization->requirePageGroupAccess(VerwaltungAccess::AREA, VerwaltungAccess::PERSONEN);

        $filters = [
            'q' => $this->queryString($request, 'q'),
            'status' => $this->queryString($request, 'status'),
            'group_id' => $this->queryInt($request, 'group_id'),
            'limit' => 100,
        ];

        return $this->renderPage($request, 'pages.verwaltung.personen.index', $this->pageParams([
            'title' => 'Personen',
            'pageTitle' => 'Personen',
            'activeKey' => 'personen',
            'persons' => $this->persons->search($filters),
            'groups' => $this->permissionGroups->findAll(),
            'filters' => $filters,
            'message' => $this->queryString($request, 'message'),
        ]));
    }

    public function show(Request $request): Response
    {
        $this->authorization->requirePageGroupAccess(VerwaltungAccess::AREA, VerwaltungAccess::PERSONEN);

        $personId = $this->routeInt($request, 'id');
        $person = $this->persons->find($personId);

        if ($person === []) {
            return $this->text('Person nicht gefunden.', 404);
        }

        return $this->renderPage($request, 'pages.verwaltung.personen.show', $this->pageParams([
            'title' => 'Person anzeigen',
            'pageTitle' => $this->personLabel($person),
            'activeKey' => 'personen',
            'person' => $person,
            'contacts' => $this->contacts->forPerson($personId),
            'addresses' => $this->addresses->forPerson($personId),
            'groups' => $this->personGroups->groupsForPerson($personId),
            'message' => $this->queryString($request, 'message'),
        ]));
    }

    public function createForm(Request $request): Response
    {
        $this->authorization->requirePageGroupAccess(VerwaltungAccess::AREA, VerwaltungAccess::PERSONEN);

        return $this->renderPage($request, 'pages.verwaltung.personen.form', $this->pageParams([
            'title' => 'Person anlegen',
            'pageTitle' => 'Person anlegen',
            'activeKey' => 'personen',
            'mode' => 'create',
            'action' => '/verwaltung/personen/create',
            'person' => [],
            'errors' => [],
        ]));
    }

    public function create(Request $request): Response
    {
        $actor = $this->authorization->requirePageGroupAccess(VerwaltungAccess::AREA, VerwaltungAccess::PERSONEN);

        try {
            $data = $this->personDataFromRequest($request);
            $personId = $this->persons->create($data);

            $this->audit->log('person.created', 'ids_persons', $personId, [
                'actor_user_id' => (int) $actor['id'],
                'entity_label' => $data['display_name'] ?? null,
                'new_values' => $data,
            ]);

            return $this->redirect('/verwaltung/personen/' . $personId . '?message=created');
        } catch (InvalidArgumentException $exception) {
            return $this->renderPage($request, 'pages.verwaltung.personen.form', $this->pageParams([
                'title' => 'Person anlegen',
                'pageTitle' => 'Person anlegen',
                'activeKey' => 'personen',
                'mode' => 'create',
                'action' => '/verwaltung/personen/create',
                'person' => $request->body,
                'errors' => [$exception->getMessage()],
            ]), 422);
        }
    }

    public function editForm(Request $request): Response
    {
        $this->authorization->requirePageGroupAccess(VerwaltungAccess::AREA, VerwaltungAccess::PERSONEN);

        $personId = $this->routeInt($request, 'id');
        $person = $this->persons->find($personId);

        if ($person === []) {
            return $this->text('Person nicht gefunden.', 404);
        }

        return $this->renderPage($request, 'pages.verwaltung.personen.form', $this->pageParams([
            'title' => 'Person bearbeiten',
            'pageTitle' => 'Person bearbeiten',
            'activeKey' => 'personen',
            'mode' => 'edit',
            'action' => '/verwaltung/personen/' . $personId . '/edit',
            'person' => $person,
            'errors' => [],
        ]));
    }

    public function edit(Request $request): Response
    {
        $actor = $this->authorization->requirePageGroupAccess(VerwaltungAccess::AREA, VerwaltungAccess::PERSONEN);

        $personId = $this->routeInt($request, 'id');
        $oldPerson = $this->persons->find($personId);

        if ($oldPerson === []) {
            return $this->text('Person nicht gefunden.', 404);
        }

        try {
            $data = $this->personDataFromRequest($request);
            $this->persons->update($personId, $data);

            $this->audit->logChange('person.updated', 'ids_persons', $personId, $oldPerson, $data, [
                'actor_user_id' => (int) $actor['id'],
                'entity_label' => $data['display_name'] ?? null,
            ]);

            return $this->redirect('/verwaltung/personen/' . $personId . '?message=updated');
        } catch (InvalidArgumentException $exception) {
            return $this->renderPage($request, 'pages.verwaltung.personen.form', $this->pageParams([
                'title' => 'Person bearbeiten',
                'pageTitle' => 'Person bearbeiten',
                'activeKey' => 'personen',
                'mode' => 'edit',
                'action' => '/verwaltung/personen/' . $personId . '/edit',
                'person' => array_merge($oldPerson, $request->body),
                'errors' => [$exception->getMessage()],
            ]), 422);
        }
    }

    public function updateStatus(Request $request): Response
    {
        $actor = $this->authorization->requirePageGroupAccess(VerwaltungAccess::AREA, VerwaltungAccess::PERSONEN);

        $personId = $this->routeInt($request, 'id');
        $status = $this->bodyString($request->body, 'status', 'disabled');
        $person = $this->persons->find($personId);

        if ($person === []) {
            return $this->text('Person nicht gefunden.', 404);
        }

        $actorPersonId = $this->authorization->currentPersonId();

        if ($status === 'disabled') {
            $this->adminSafety->assertPersonCanBeDisabled($personId, $actorPersonId);
        }

        $this->persons->setStatus($personId, $status);

        $this->audit->logChange('person.status_changed', 'ids_persons', $personId, $person, ['status' => $status], [
            'actor_user_id' => (int) $actor['id'],
            'entity_label' => $this->personLabel($person),
        ]);

        return $this->redirect('/verwaltung/personen/' . $personId . '?message=status');
    }

    public function groups(Request $request): Response
    {
        $this->authorization->requirePageGroupAccess(VerwaltungAccess::AREA, VerwaltungAccess::PERSONEN);

        $personId = $this->routeInt($request, 'id');
        $person = $this->persons->find($personId);

        if ($person === []) {
            return $this->text('Person nicht gefunden.', 404);
        }

        return $this->renderPage($request, 'pages.verwaltung.personen.groups', $this->pageParams([
            'title' => 'Gruppen der Person',
            'pageTitle' => 'Gruppen: ' . $this->personLabel($person),
            'activeKey' => 'personen',
            'person' => $person,
            'assignedGroups' => $this->personGroups->groupsForPerson($personId),
            'allGroups' => $this->permissionGroups->findAll(),
            'message' => $this->queryString($request, 'message'),
        ]));
    }

    public function updateGroups(Request $request): Response
    {
        $actor = $this->authorization->requirePageGroupAccess(VerwaltungAccess::AREA, VerwaltungAccess::PERSONEN);

        $personId = $this->routeInt($request, 'id');
        $person = $this->persons->find($personId);

        if ($person === []) {
            return $this->text('Person nicht gefunden.', 404);
        }

        $actorPersonId = $this->authorization->currentPersonId();
        $groupIds = $this->groupIdsFromBody($request->body);
        $oldGroups = $this->personGroups->groupsForPerson($personId);

        foreach ($oldGroups as $oldGroup) {
            $oldGroupId = (int) ($oldGroup['id'] ?? 0);

            if ($oldGroupId > 0 && !in_array($oldGroupId, $groupIds, true)) {
                $this->adminSafety->assertGroupCanBeRemovedFromPerson($personId, $oldGroupId, $actorPersonId);
            }
        }

        $this->personGroups->syncGroupsForPerson($personId, $groupIds, $actorPersonId);

        $this->audit->log('person.groups_updated', 'ids_persons', $personId, [
            'actor_user_id' => (int) $actor['id'],
            'entity_label' => $this->personLabel($person),
            'old_values' => ['groups' => array_column($oldGroups, 'group_key')],
            'new_values' => ['group_ids' => $groupIds],
        ]);

        return $this->redirect('/verwaltung/personen/' . $personId . '/gruppen?message=updated');
    }

    public function contacts(Request $request): Response
    {
        $this->authorization->requirePageGroupAccess(VerwaltungAccess::AREA, VerwaltungAccess::PERSONEN);

        $personId = $this->routeInt($request, 'id');
        $person = $this->persons->find($personId);

        if ($person === []) {
            return $this->text('Person nicht gefunden.', 404);
        }

        return $this->renderPage($request, 'pages.verwaltung.personen.contacts', $this->pageParams([
            'title' => 'Kontakte',
            'pageTitle' => 'Kontakte: ' . $this->personLabel($person),
            'activeKey' => 'personen',
            'person' => $person,
            'contacts' => $this->contacts->forPerson($personId),
            'message' => $this->queryString($request, 'message'),
        ]));
    }

    public function createContact(Request $request): Response
    {
        $actor = $this->authorization->requirePageGroupAccess(VerwaltungAccess::AREA, VerwaltungAccess::PERSONEN);

        $personId = $this->routeInt($request, 'id');
        $person = $this->persons->find($personId);

        if ($person === []) {
            return $this->text('Person nicht gefunden.', 404);
        }

        $contactId = $this->contacts->create($personId, [
            'contact_type' => $this->bodyString($request->body, 'contact_type', 'email'),
            'label' => $this->bodyNullableString($request->body, 'label'),
            'value' => $this->bodyString($request->body, 'value'),
            'is_primary' => $this->bodyBool($request->body, 'is_primary'),
            'is_verified' => $this->bodyBool($request->body, 'is_verified'),
        ]);

        $this->audit->log('person.contact.created', 'ids_person_contact_details', $contactId, [
            'actor_user_id' => (int) $actor['id'],
            'entity_label' => $this->personLabel($person),
        ]);

        return $this->redirect('/verwaltung/personen/' . $personId . '/kontakte?message=created');
    }

    public function deleteContact(Request $request): Response
    {
        $actor = $this->authorization->requirePageGroupAccess(VerwaltungAccess::AREA, VerwaltungAccess::PERSONEN);

        $personId = $this->routeInt($request, 'id');
        $contactId = $this->routeInt($request, 'contactId');

        $this->contacts->delete($personId, $contactId);

        $this->audit->log('person.contact.deleted', 'ids_person_contact_details', $contactId, [
            'actor_user_id' => (int) $actor['id'],
            'entity_id' => $contactId,
            'metadata' => ['person_id' => $personId],
        ]);

        return $this->redirect('/verwaltung/personen/' . $personId . '/kontakte?message=deleted');
    }

    public function addresses(Request $request): Response
    {
        $this->authorization->requirePageGroupAccess(VerwaltungAccess::AREA, VerwaltungAccess::PERSONEN);

        $personId = $this->routeInt($request, 'id');
        $person = $this->persons->find($personId);

        if ($person === []) {
            return $this->text('Person nicht gefunden.', 404);
        }

        return $this->renderPage($request, 'pages.verwaltung.personen.addresses', $this->pageParams([
            'title' => 'Adressen',
            'pageTitle' => 'Adressen: ' . $this->personLabel($person),
            'activeKey' => 'personen',
            'person' => $person,
            'addresses' => $this->addresses->forPerson($personId),
            'message' => $this->queryString($request, 'message'),
        ]));
    }

    public function createAddress(Request $request): Response
    {
        $actor = $this->authorization->requirePageGroupAccess(VerwaltungAccess::AREA, VerwaltungAccess::PERSONEN);

        $personId = $this->routeInt($request, 'id');
        $person = $this->persons->find($personId);

        if ($person === []) {
            return $this->text('Person nicht gefunden.', 404);
        }

        $addressId = $this->addresses->create($personId, [
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

        $this->audit->log('person.address.created', 'ids_person_addresses', $addressId, [
            'actor_user_id' => (int) $actor['id'],
            'entity_label' => $this->personLabel($person),
        ]);

        return $this->redirect('/verwaltung/personen/' . $personId . '/adressen?message=created');
    }

    public function deleteAddress(Request $request): Response
    {
        $actor = $this->authorization->requirePageGroupAccess(VerwaltungAccess::AREA, VerwaltungAccess::PERSONEN);

        $personId = $this->routeInt($request, 'id');
        $addressId = $this->routeInt($request, 'addressId');

        $this->addresses->delete($personId, $addressId);

        $this->audit->log('person.address.deleted', 'ids_person_addresses', $addressId, [
            'actor_user_id' => (int) $actor['id'],
            'metadata' => ['person_id' => $personId],
        ]);

        return $this->redirect('/verwaltung/personen/' . $personId . '/adressen?message=deleted');
    }

    /**
     * @return array<string, mixed>
     */
    private function personDataFromRequest(Request $request): array
    {
        return [
            'display_name' => $this->bodyNullableString($request->body, 'display_name'),
            'status' => $this->bodyString($request->body, 'status', 'active'),
            'salutation' => $this->bodyNullableString($request->body, 'salutation'),
            'title' => $this->bodyNullableString($request->body, 'title'),
            'first_name' => $this->bodyNullableString($request->body, 'first_name'),
            'middle_name' => $this->bodyNullableString($request->body, 'middle_name'),
            'last_name' => $this->bodyNullableString($request->body, 'last_name'),
            'preferred_name' => $this->bodyNullableString($request->body, 'preferred_name'),
            'pronouns' => $this->bodyNullableString($request->body, 'pronouns'),
            'login_email' => $this->bodyNullableString($request->body, 'login_email'),
            'login_password' => $this->bodyNullableString($request->body, 'login_password'),
        ];
    }

    /**
     * @param array<string, mixed> $body
     * @return array<int, int>
     */
    private function groupIdsFromBody(array $body): array
    {
        $raw = $body['group_ids'] ?? [];

        if (!is_array($raw)) {
            $raw = [$raw];
        }

        $ids = [];

        foreach ($raw as $id) {
            $id = (int) $id;

            if ($id > 0) {
                $ids[] = $id;
            }
        }

        return array_values(array_unique($ids));
    }

    /**
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    private function pageParams(array $overrides): array
    {
        $activeKey = (string) ($overrides['activeKey'] ?? 'personen');

        return array_replace([
            'title' => 'Personen',
            'areaName' => 'Verwaltung',
            'pageTitle' => 'Personen',
            'headerAreaKey' => 'verwaltung',
            'areaRootLink' => '/verwaltung',
            'areaNav' => $this->verwaltungNav($activeKey),
        ], $overrides);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function verwaltungNav(string $activeKey): array
    {
        return [
            [
                'label' => 'Personen',
                'href' => '/verwaltung/personen',
                'active' => $activeKey === 'personen',
            ],
            [
                'label' => 'Gruppen',
                'href' => '/verwaltung/gruppen',
                'active' => $activeKey === 'gruppen',
            ],
            [
                'label' => 'Berechtigungen',
                'href' => '/verwaltung/berechtigungen',
                'active' => $activeKey === 'berechtigungen',
            ],
        ];
    }

    /**
     * @param array<string, mixed> $person
     */
    private function personLabel(array $person): string
    {
        $parts = array_filter([
            trim((string) ($person['display_name'] ?? '')),
            trim((string) ($person['preferred_name'] ?? '')),
            trim(trim((string) ($person['first_name'] ?? '')) . ' ' . trim((string) ($person['last_name'] ?? ''))),
            trim((string) ($person['login_email'] ?? '')),
        ]);

        return (string) reset($parts) ?: 'Person #' . (string) ($person['id'] ?? '');
    }
}