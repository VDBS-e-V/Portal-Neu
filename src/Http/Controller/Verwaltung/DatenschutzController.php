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
use App\Repository\PersonErasureRepository;
use App\Repository\PersonRepository;
use App\Security\AdminSafetyService;
use App\Security\AuthorizationService;
use App\Security\VerwaltungAccess;
use InvalidArgumentException;

final class DatenschutzController extends PageController
{
    public function __construct(
        Renderer $renderer,
        AreaRepository $areas,
        MenuRepository $menus,
        MenuItemRepository $menuItems,
        private readonly PersonErasureRepository $erasures,
        private readonly PersonRepository $persons,
        private readonly AuthorizationService $authorization,
        private readonly AdminSafetyService $adminSafety,
        private readonly AuditLogger $audit
    ) {
        parent::__construct($renderer, $areas, $menus, $menuItems);
    }

    public function index(Request $request): Response
    {
        $this->authorization->requirePermission('portal.verwaltung.datenschutz.view');

        $filters = [
            'q' => $this->queryString($request, 'q'),
            'status' => $this->queryString($request, 'status'),
            'limit' => 100,
        ];

        return $this->renderPage($request, 'pages/verwaltung/datenschutz/index', $this->pageParams([
            'title' => 'DSGVO-Löschung',
            'pageTitle' => 'DSGVO-Löschung',
            'activeKey' => 'datenschutz',
            'requests' => $this->erasures->search($filters),
            'filters' => $filters,
            'message' => $this->queryString($request, 'message'),
        ]));
    }

    public function createForm(Request $request): Response
    {
        $this->authorization->requirePermission('portal.verwaltung.datenschutz.view');

        $personId = $this->routeInt($request, 'id');
        $person = $this->persons->find($personId);

        if ($person === []) {
            return $this->text('Person nicht gefunden.', 404);
        }

        return $this->renderPage($request, 'pages/verwaltung/datenschutz/form', $this->pageParams([
            'title' => 'DSGVO-Löschung beantragen',
            'pageTitle' => 'DSGVO-Löschung beantragen',
            'activeKey' => 'datenschutz',
            'person' => $person,
            'errors' => [],
        ]));
    }

    public function create(Request $request): Response
    {
        $actor = $this->authorization->requirePermission('portal.verwaltung.datenschutz.view');

        $personId = $this->routeInt($request, 'id');
        $person = $this->persons->find($personId);

        if ($person === []) {
            return $this->text('Person nicht gefunden.', 404);
        }

        try {
            $actorPersonId = $this->authorization->currentPersonId();

            $this->adminSafety->assertPersonCanBeDisabled($personId, $actorPersonId);

            $requestId = $this->erasures->createRequest(
                $personId,
                (int) $actor['id'],
                $this->bodyNullableString($request->body, 'reason')
            );

            $this->audit->log('person.erasure_requested', 'ids_persons', $personId, [
                'actor_user_id' => (int) $actor['id'],
                'entity_label' => $person['display_name'] ?? $person['login_email'] ?? null,
                'metadata' => [
                    'erasure_request_id' => $requestId,
                ],
            ]);

            return $this->redirect('/verwaltung/datenschutz/' . $requestId . '?message=requested');
        } catch (InvalidArgumentException $exception) {
            return $this->renderPage($request, 'pages/verwaltung/datenschutz/form', $this->pageParams([
                'title' => 'DSGVO-Löschung beantragen',
                'pageTitle' => 'DSGVO-Löschung beantragen',
                'activeKey' => 'datenschutz',
                'person' => $person,
                'errors' => [$exception->getMessage()],
            ]), 422);
        }
    }

    public function show(Request $request): Response
    {
        $this->authorization->requirePermission('portal.verwaltung.datenschutz.view');

        $requestId = $this->routeInt($request, 'id');
        $erasure = $this->erasures->find($requestId);

        if ($erasure === []) {
            return $this->text('DSGVO-Vorgang nicht gefunden.', 404);
        }

        return $this->renderPage($request, 'pages/verwaltung/datenschutz/show', $this->pageParams([
            'title' => 'DSGVO-Vorgang',
            'pageTitle' => 'DSGVO-Vorgang #' . $requestId,
            'activeKey' => 'datenschutz',
            'erasure' => $erasure,
            'message' => $this->queryString($request, 'message'),
        ]));
    }

    public function approve(Request $request): Response
    {
        $actor = $this->authorization->requirePermission('portal.verwaltung.datenschutz.view');

        $requestId = $this->routeInt($request, 'id');
        $erasure = $this->erasures->find($requestId);

        if ($erasure === []) {
            return $this->text('DSGVO-Vorgang nicht gefunden.', 404);
        }

        $this->erasures->approve(
            $requestId,
            (int) $actor['id'],
            $this->bodyNullableString($request->body, 'review_note')
        );

        $this->audit->log('person.erasure_approved', 'ids_person_erasure_requests', $requestId, [
            'actor_user_id' => (int) $actor['id'],
            'entity_label' => $erasure['display_name'] ?? $erasure['login_email'] ?? null,
            'metadata' => [
                'person_id' => (int) ($erasure['person_id'] ?? 0),
            ],
        ]);

        return $this->redirect('/verwaltung/datenschutz/' . $requestId . '?message=approved');
    }

    public function reject(Request $request): Response
    {
        $actor = $this->authorization->requirePermission('portal.verwaltung.datenschutz.view');

        $requestId = $this->routeInt($request, 'id');
        $erasure = $this->erasures->find($requestId);

        if ($erasure === []) {
            return $this->text('DSGVO-Vorgang nicht gefunden.', 404);
        }

        $this->erasures->reject(
            $requestId,
            (int) $actor['id'],
            $this->bodyNullableString($request->body, 'review_note')
        );

        $this->audit->log('person.erasure_rejected', 'ids_person_erasure_requests', $requestId, [
            'actor_user_id' => (int) $actor['id'],
            'entity_label' => $erasure['display_name'] ?? $erasure['login_email'] ?? null,
            'metadata' => [
                'person_id' => (int) ($erasure['person_id'] ?? 0),
            ],
        ]);

        return $this->redirect('/verwaltung/datenschutz/' . $requestId . '?message=rejected');
    }

    public function cancel(Request $request): Response
    {
        $actor = $this->authorization->requirePermission('portal.verwaltung.datenschutz.view');

        $requestId = $this->routeInt($request, 'id');
        $erasure = $this->erasures->find($requestId);

        if ($erasure === []) {
            return $this->text('DSGVO-Vorgang nicht gefunden.', 404);
        }

        $this->erasures->cancel(
            $requestId,
            (int) $actor['id'],
            $this->bodyNullableString($request->body, 'review_note')
        );

        $this->audit->log('person.erasure_cancelled', 'ids_person_erasure_requests', $requestId, [
            'actor_user_id' => (int) $actor['id'],
            'entity_label' => $erasure['display_name'] ?? $erasure['login_email'] ?? null,
            'metadata' => [
                'person_id' => (int) ($erasure['person_id'] ?? 0),
            ],
        ]);

        return $this->redirect('/verwaltung/datenschutz/' . $requestId . '?message=cancelled');
    }

    public function complete(Request $request): Response
    {
        $actor = $this->authorization->requirePermission('portal.verwaltung.datenschutz.view');

        $requestId = $this->routeInt($request, 'id');
        $erasure = $this->erasures->find($requestId);

        if ($erasure === []) {
            return $this->text('DSGVO-Vorgang nicht gefunden.', 404);
        }

        $personId = (int) ($erasure['person_id'] ?? 0);
        $actorPersonId = $this->authorization->currentPersonId();

        $this->adminSafety->assertPersonCanBeDisabled($personId, $actorPersonId);

        $this->erasures->completeAnonymization($requestId, (int) $actor['id']);

        $this->audit->log('person.erasure_completed', 'ids_person_erasure_requests', $requestId, [
            'actor_user_id' => (int) $actor['id'],
            'entity_label' => $erasure['display_name'] ?? $erasure['login_email'] ?? null,
            'metadata' => [
                'person_id' => $personId,
            ],
        ]);

        return $this->redirect('/verwaltung/datenschutz/' . $requestId . '?message=completed');
    }

    /**
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    private function pageParams(array $overrides): array
    {
        $activeKey = (string) ($overrides['activeKey'] ?? 'datenschutz');

        return array_replace([
            'title' => 'DSGVO-Löschung',
            'areaName' => 'Verwaltung',
            'pageTitle' => 'DSGVO-Löschung',
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
                'label' => 'DSGVO',
                'href' => '/verwaltung/datenschutz',
                'active' => $activeKey === 'datenschutz',
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
}
