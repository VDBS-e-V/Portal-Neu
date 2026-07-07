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
use App\Repository\PageGroupAccessRepository;
use App\Repository\PageGroupRepository;
use App\Repository\PermissionGroupRepository;
use App\Security\AuthorizationService;
use App\Security\VerwaltungAccess;

final class BerechtigungenController extends PageController
{
    public function __construct(
        Renderer $renderer,
        AreaRepository $areas,
        MenuRepository $menus,
        MenuItemRepository $menuItems,
        private readonly PermissionGroupRepository $permissionGroups,
        private readonly PageGroupRepository $pageGroups,
        private readonly PageGroupAccessRepository $pageGroupAccess,
        private readonly AuthorizationService $authorization,
        private readonly AuditLogger $audit
    ) {
        parent::__construct($renderer, $areas, $menus, $menuItems);
    }

    public function index(Request $request): Response
    {
        $this->authorization->requirePageGroupAccess(VerwaltungAccess::AREA, VerwaltungAccess::BERECHTIGUNGEN);

        $groups = $this->permissionGroups->findAll();
        $pageGroups = $this->pageGroups->findAll();

        $accessMatrix = [];

        foreach ($groups as $group) {
            $groupId = (int) ($group['id'] ?? 0);
            $accessMatrix[$groupId] = [];

            foreach ($this->pageGroupAccess->pageGroupsForPermissionGroup($groupId) as $pageGroup) {
                $accessMatrix[$groupId][(int) ($pageGroup['id'] ?? 0)] = true;
            }
        }

        return $this->renderPage($request, 'pages/verwaltung/berechtigungen/index', $this->pageParams([
            'title' => 'Berechtigungen',
            'pageTitle' => 'Berechtigungen',
            'activeKey' => 'berechtigungen',
            'groups' => $groups,
            'pageGroups' => $pageGroups,
            'accessMatrix' => $accessMatrix,
            'message' => $this->queryString($request, 'message'),
        ]));
    }

    public function group(Request $request): Response
    {
        $this->authorization->requirePageGroupAccess(VerwaltungAccess::AREA, VerwaltungAccess::BERECHTIGUNGEN);

        $groupId = $this->routeInt($request, 'id');
        $group = $this->permissionGroups->find($groupId);

        if ($group === []) {
            return $this->text('Gruppe nicht gefunden.', 404);
        }

        $assignedPageGroups = $this->pageGroupAccess->pageGroupsForPermissionGroup($groupId);
        $assignedIds = [];

        foreach ($assignedPageGroups as $pageGroup) {
            $assignedIds[(int) ($pageGroup['id'] ?? 0)] = true;
        }

        return $this->renderPage($request, 'pages/verwaltung/berechtigungen/group', $this->pageParams([
            'title' => 'Berechtigungen bearbeiten',
            'pageTitle' => 'Berechtigungen: ' . (string) ($group['name'] ?? $group['group_key'] ?? 'Gruppe'),
            'activeKey' => 'berechtigungen',
            'group' => $group,
            'pageGroups' => $this->pageGroups->findAll(),
            'assignedPageGroups' => $assignedPageGroups,
            'assignedIds' => $assignedIds,
            'message' => $this->queryString($request, 'message'),
        ]));
    }

    public function updateGroup(Request $request): Response
    {
        $actor = $this->authorization->requirePageGroupAccess(VerwaltungAccess::AREA, VerwaltungAccess::BERECHTIGUNGEN);

        $groupId = $this->routeInt($request, 'id');
        $group = $this->permissionGroups->find($groupId);

        if ($group === []) {
            return $this->text('Gruppe nicht gefunden.', 404);
        }

        $oldPageGroups = $this->pageGroupAccess->pageGroupsForPermissionGroup($groupId);
        $pageGroupIds = $this->pageGroupIdsFromBody($request->body);

        $this->pageGroupAccess->syncPageGroupsForPermissionGroup($groupId, $pageGroupIds);

        $this->audit->log('permission_group.page_groups_updated', 'ids_permission_groups', $groupId, [
            'actor_user_id' => (int) $actor['id'],
            'entity_label' => $group['group_key'] ?? null,
            'old_values' => [
                'page_group_ids' => array_values(array_map(
                    static fn (array $pageGroup): int => (int) ($pageGroup['id'] ?? 0),
                    $oldPageGroups
                )),
            ],
            'new_values' => [
                'page_group_ids' => $pageGroupIds,
            ],
        ]);

        return $this->redirect('/verwaltung/berechtigungen/gruppen/' . $groupId . '?message=updated');
    }

    public function pageGroups(Request $request): Response
    {
        $this->authorization->requirePageGroupAccess(VerwaltungAccess::AREA, VerwaltungAccess::BERECHTIGUNGEN);

        return $this->renderPage($request, 'pages/verwaltung/berechtigungen/page_groups', $this->pageParams([
            'title' => 'PageGroups',
            'pageTitle' => 'PageGroups',
            'activeKey' => 'berechtigungen',
            'pageGroups' => $this->pageGroups->findAll(),
            'message' => $this->queryString($request, 'message'),
        ]));
    }

    /**
     * @param array<string, mixed> $body
     * @return array<int, int>
     */
    private function pageGroupIdsFromBody(array $body): array
    {
        $raw = $body['page_group_ids'] ?? [];

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
        $activeKey = (string) ($overrides['activeKey'] ?? 'berechtigungen');

        return array_replace([
            'title' => 'Berechtigungen',
            'areaName' => 'Verwaltung',
            'pageTitle' => 'Berechtigungen',
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
