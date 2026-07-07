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
use App\Repository\PersonPermissionGroupRepository;
use App\Security\AdminSafetyService;
use App\Security\AuthorizationService;
use App\Security\VerwaltungAccess;
use InvalidArgumentException;

final class GruppenController extends PageController
{
    public function __construct(
        Renderer $renderer,
        AreaRepository $areas,
        MenuRepository $menus,
        MenuItemRepository $menuItems,
        private readonly PermissionGroupRepository $groups,
        private readonly PersonPermissionGroupRepository $personGroups,
        private readonly AuthorizationService $authorization,
        private readonly AdminSafetyService $adminSafety,
        private readonly AuditLogger $audit
    ) {
        parent::__construct($renderer, $areas, $menus, $menuItems);
    }

    public function index(Request $request): Response
    {
        $this->authorization->requirePageGroupAccess(VerwaltungAccess::AREA, VerwaltungAccess::GRUPPEN);

        return $this->renderPage($request, 'pages/verwaltung/gruppen/index', $this->pageParams([
            'title' => 'Gruppen',
            'pageTitle' => 'Gruppen',
            'activeKey' => 'gruppen',
            'groups' => $this->groups->findAll(),
            'message' => $this->queryString($request, 'message'),
        ]));
    }

    public function show(Request $request): Response
    {
        $this->authorization->requirePageGroupAccess(VerwaltungAccess::AREA, VerwaltungAccess::GRUPPEN);

        $groupId = $this->routeInt($request, 'id');
        $group = $this->groups->find($groupId);

        if ($group === []) {
            return $this->text('Gruppe nicht gefunden.', 404);
        }

        return $this->renderPage($request, 'pages/verwaltung/gruppen/show', $this->pageParams([
            'title' => 'Gruppe anzeigen',
            'pageTitle' => (string) ($group['name'] ?? 'Gruppe'),
            'activeKey' => 'gruppen',
            'group' => $group,
            'members' => $this->personGroups->personsForGroup($groupId),
            'message' => $this->queryString($request, 'message'),
        ]));
    }

    public function createForm(Request $request): Response
    {
        $this->authorization->requirePageGroupAccess(VerwaltungAccess::AREA, VerwaltungAccess::GRUPPEN);

        return $this->renderPage($request, 'pages/verwaltung/gruppen/form', $this->pageParams([
            'title' => 'Gruppe anlegen',
            'pageTitle' => 'Gruppe anlegen',
            'activeKey' => 'gruppen',
            'mode' => 'create',
            'action' => '/verwaltung/gruppen/create',
            'group' => [],
            'errors' => [],
        ]));
    }

    public function create(Request $request): Response
    {
        $actor = $this->authorization->requirePageGroupAccess(VerwaltungAccess::AREA, VerwaltungAccess::GRUPPEN);

        try {
            $data = $this->groupDataFromRequest($request);
            $groupId = $this->groups->create($data);

            $this->audit->log('permission_group.created', 'ids_permission_groups', $groupId, [
                'actor_user_id' => (int) $actor['id'],
                'entity_label' => $data['group_key'] ?? null,
                'new_values' => $data,
            ]);

            return $this->redirect('/verwaltung/gruppen/' . $groupId . '?message=created');
        } catch (InvalidArgumentException $exception) {
            return $this->renderPage($request, 'pages/verwaltung/gruppen/form', $this->pageParams([
                'title' => 'Gruppe anlegen',
                'pageTitle' => 'Gruppe anlegen',
                'activeKey' => 'gruppen',
                'mode' => 'create',
                'action' => '/verwaltung/gruppen/create',
                'group' => $request->body,
                'errors' => [$exception->getMessage()],
            ]), 422);
        }
    }

    public function editForm(Request $request): Response
    {
        $this->authorization->requirePageGroupAccess(VerwaltungAccess::AREA, VerwaltungAccess::GRUPPEN);

        $groupId = $this->routeInt($request, 'id');
        $group = $this->groups->find($groupId);

        if ($group === []) {
            return $this->text('Gruppe nicht gefunden.', 404);
        }

        return $this->renderPage($request, 'pages/verwaltung/gruppen/form', $this->pageParams([
            'title' => 'Gruppe bearbeiten',
            'pageTitle' => 'Gruppe bearbeiten',
            'activeKey' => 'gruppen',
            'mode' => 'edit',
            'action' => '/verwaltung/gruppen/' . $groupId . '/edit',
            'group' => $group,
            'errors' => [],
        ]));
    }

    public function edit(Request $request): Response
    {
        $actor = $this->authorization->requirePageGroupAccess(VerwaltungAccess::AREA, VerwaltungAccess::GRUPPEN);

        $groupId = $this->routeInt($request, 'id');
        $oldGroup = $this->groups->find($groupId);

        if ($oldGroup === []) {
            return $this->text('Gruppe nicht gefunden.', 404);
        }

        try {
            $data = $this->groupDataFromRequest($request);
            $this->groups->update($groupId, $data);

            $this->audit->logChange('permission_group.updated', 'ids_permission_groups', $groupId, $oldGroup, $data, [
                'actor_user_id' => (int) $actor['id'],
                'entity_label' => $data['group_key'] ?? $oldGroup['group_key'] ?? null,
            ]);

            return $this->redirect('/verwaltung/gruppen/' . $groupId . '?message=updated');
        } catch (InvalidArgumentException $exception) {
            return $this->renderPage($request, 'pages/verwaltung/gruppen/form', $this->pageParams([
                'title' => 'Gruppe bearbeiten',
                'pageTitle' => 'Gruppe bearbeiten',
                'activeKey' => 'gruppen',
                'mode' => 'edit',
                'action' => '/verwaltung/gruppen/' . $groupId . '/edit',
                'group' => array_merge($oldGroup, $request->body),
                'errors' => [$exception->getMessage()],
            ]), 422);
        }
    }

    public function members(Request $request): Response
    {
        $this->authorization->requirePageGroupAccess(VerwaltungAccess::AREA, VerwaltungAccess::GRUPPEN);

        $groupId = $this->routeInt($request, 'id');
        $group = $this->groups->find($groupId);

        if ($group === []) {
            return $this->text('Gruppe nicht gefunden.', 404);
        }

        return $this->renderPage($request, 'pages/verwaltung/gruppen/members', $this->pageParams([
            'title' => 'Gruppenmitglieder',
            'pageTitle' => 'Mitglieder: ' . (string) ($group['name'] ?? $group['group_key'] ?? 'Gruppe'),
            'activeKey' => 'gruppen',
            'group' => $group,
            'members' => $this->personGroups->personsForGroup($groupId),
            'message' => $this->queryString($request, 'message'),
        ]));
    }

    public function delete(Request $request): Response
    {
        $actor = $this->authorization->requirePageGroupAccess(VerwaltungAccess::AREA, VerwaltungAccess::GRUPPEN);

        $groupId = $this->routeInt($request, 'id');
        $group = $this->groups->find($groupId);

        if ($group === []) {
            return $this->text('Gruppe nicht gefunden.', 404);
        }

        $this->adminSafety->assertSystemGroupCanBeDeleted($groupId);
        $this->groups->delete($groupId);

        $this->audit->log('permission_group.deleted', 'ids_permission_groups', $groupId, [
            'actor_user_id' => (int) $actor['id'],
            'entity_label' => $group['group_key'] ?? null,
            'old_values' => $group,
        ]);

        return $this->redirect('/verwaltung/gruppen?message=deleted');
    }

    /**
     * @return array<string, mixed>
     */
    private function groupDataFromRequest(Request $request): array
    {
        return [
            'group_key' => $this->bodyString($request->body, 'group_key'),
            'name' => $this->bodyString($request->body, 'name'),
            'description' => $this->bodyNullableString($request->body, 'description'),
            'is_system' => $this->bodyBool($request->body, 'is_system'),
        ];
    }

    /**
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    private function pageParams(array $overrides): array
    {
        $activeKey = (string) ($overrides['activeKey'] ?? 'gruppen');

        return array_replace([
            'title' => 'Gruppen',
            'areaName' => 'Verwaltung',
            'pageTitle' => 'Gruppen',
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
}