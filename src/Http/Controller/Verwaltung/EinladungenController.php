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
use App\Repository\PersonRepository;
use App\Repository\UserInvitationRepository;
use App\Security\AuthorizationService;
use App\Security\VerwaltungAccess;
use InvalidArgumentException;

final class EinladungenController extends PageController
{
    public function __construct(
        Renderer $renderer,
        AreaRepository $areas,
        MenuRepository $menus,
        MenuItemRepository $menuItems,
        private readonly UserInvitationRepository $invitations,
        private readonly PersonRepository $persons,
        private readonly AuthorizationService $authorization,
        private readonly AuditLogger $audit
    ) {
        parent::__construct($renderer, $areas, $menus, $menuItems);
    }

    public function index(Request $request): Response
    {
        $this->authorization->requirePermission('portal.verwaltung.einladungen.view');

        $this->invitations->markExpired();

        return $this->renderPage($request, 'pages/verwaltung/einladungen/index', $this->pageParams([
            'title' => 'Einladungen',
            'pageTitle' => 'Einladungen',
            'activeKey' => 'personen',
            'invitations' => $this->invitations->latest(100),
            'lastInvitationUrl' => $this->queryString($request, 'url'),
            'message' => $this->queryString($request, 'message'),
        ]));
    }

    public function createForPerson(Request $request): Response
    {
        $actor = $this->authorization->requirePermission('portal.verwaltung.einladungen.view');

        $personId = $this->routeInt($request, 'id');
        $person = $this->persons->find($personId);

        if ($person === []) {
            return $this->text('Person nicht gefunden.', 404);
        }

        $userId = (int) ($person['user_id'] ?? 0);

        if ($userId <= 0) {
            return $this->text('Diese Person hat noch kein Login-Konto.', 422);
        }

        try {
            $result = $this->invitations->createForUser($userId, (int) $actor['id'], 14);

            $this->audit->log('invitation.created', 'ids_users', $userId, [
                'actor_user_id' => (int) $actor['id'],
                'entity_label' => $person['login_email'] ?? $person['display_name'] ?? null,
                'metadata' => [
                    'invitation_id' => $result['id'],
                    'person_id' => $personId,
                ],
            ]);

            return $this->redirect(
                '/verwaltung/einladungen?message=created&url=' . rawurlencode($result['accept_url'])
            );
        } catch (InvalidArgumentException $exception) {
            return $this->text($exception->getMessage(), 422);
        }
    }

    public function revoke(Request $request): Response
    {
        $actor = $this->authorization->requirePermission('portal.verwaltung.einladungen.view');

        $invitationId = $this->routeInt($request, 'id');
        $this->invitations->revoke($invitationId);

        $this->audit->log('invitation.revoked', 'ids_user_invitations', $invitationId, [
            'actor_user_id' => (int) $actor['id'],
        ]);

        return $this->redirect('/verwaltung/einladungen?message=revoked');
    }

    /**
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    private function pageParams(array $overrides): array
    {
        $activeKey = (string) ($overrides['activeKey'] ?? 'personen');

        return array_replace([
            'title' => 'Einladungen',
            'areaName' => 'Verwaltung',
            'pageTitle' => 'Einladungen',
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
                'label' => 'Einladungen',
                'href' => '/verwaltung/einladungen',
                'active' => $activeKey === 'einladungen',
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
