<?php

declare(strict_types=1);

namespace App\Http\Controller\Administration;

use App\Http\Controller\Controller;
use App\Http\Request\Request;
use App\Http\Response\Response;
use App\Navigation\AdministrationNavigation;
use App\Repository\IdentityAdministrationRepository;
use App\Security\AuthorizationService;
use App\Security\IdentityAdminSafetyService;
use App\Presentation\Templating\Renderer;
use InvalidArgumentException;
use RuntimeException;

final class GruppenController extends Controller
{
    public function __construct(
        Renderer $renderer,
        private readonly IdentityAdministrationRepository $administration,
        private readonly AdministrationNavigation $navigation,
        private readonly AuthorizationService $authorization,
        private readonly IdentityAdminSafetyService $safety
    ) {
        parent::__construct($renderer);
    }

    public function index(Request $request): Response
    {
        $this->authorization->requirePermission('identity.gruppen.view');
        $systemId = $this->queryInt($request, 'system_id');
        return $this->renderPage($request, 'pages/administration/gruppen/index', $this->pageParams($request, [
            'pageTitle' => 'Gruppen',
            'groups' => $this->administration->groups($systemId > 0 ? $systemId : null),
            'systems' => $this->administration->systems(),
            'filters' => ['system_id' => $systemId],
        ]));
    }

    public function createForm(Request $request): Response
    {
        $this->authorization->requirePermission('identity.gruppen.create');
        return $this->renderPage($request, 'pages/administration/gruppen/form', $this->pageParams($request, [
            'pageTitle' => 'Gruppe anlegen',
            'mode' => 'create',
            'action' => '/administration/gruppen/create',
            'group' => ['is_active' => 1, 'is_system' => 0, 'is_default' => 0, 'is_assignable' => 1, 'sorting' => 100],
            'systems' => $this->administration->systems(),
            'errors' => [],
        ]));
    }

    public function create(Request $request): Response
    {
        $this->authorization->requirePermission('identity.gruppen.create');
        try {
            $id = $this->administration->createGroup($request->body);
            return $this->redirect('/administration/gruppen/' . $id . '?message=created');
        } catch (InvalidArgumentException $e) {
            return $this->renderPage($request, 'pages/administration/gruppen/form', $this->pageParams($request, [
                'pageTitle' => 'Gruppe anlegen',
                'mode' => 'create',
                'action' => '/administration/gruppen/create',
                'group' => $request->body,
                'systems' => $this->administration->systems(),
                'errors' => [$e->getMessage()],
            ]), 422);
        }
    }

    public function show(Request $request): Response
    {
        $this->authorization->requirePermission('identity.gruppen.view');
        $id = $this->routeInt($request, 'id');
        $group = $this->administration->group($id);
        if ($group === []) {
            return $this->text('Gruppe nicht gefunden.', 404);
        }
        return $this->renderPage($request, 'pages/administration/gruppen/show', $this->pageParams($request, [
            'pageTitle' => (string) $group['name'],
            'group' => $group,
            'permissions' => $this->administration->permissions((int) $group['system_id'], false),
            'selectedPermissionIds' => $this->administration->permissionIdsForGroup($id),
        ]));
    }

    public function editForm(Request $request): Response
    {
        $this->authorization->requirePermission('identity.gruppen.edit');
        $id = $this->routeInt($request, 'id');
        $group = $this->administration->group($id);
        if ($group === []) {
            return $this->text('Gruppe nicht gefunden.', 404);
        }
        return $this->renderPage($request, 'pages/administration/gruppen/form', $this->pageParams($request, [
            'pageTitle' => 'Gruppe bearbeiten',
            'mode' => 'edit',
            'action' => '/administration/gruppen/' . $id . '/edit',
            'group' => $group,
            'systems' => $this->administration->systems(),
            'errors' => [],
        ]));
    }

    public function edit(Request $request): Response
    {
        $this->authorization->requirePermission('identity.gruppen.edit');
        $id = $this->routeInt($request, 'id');
        $group = $this->administration->group($id);
        if ($group === []) {
            return $this->text('Gruppe nicht gefunden.', 404);
        }
        try {
            $this->administration->updateGroup($id, $request->body);
            return $this->redirect('/administration/gruppen/' . $id . '?message=updated');
        } catch (InvalidArgumentException $e) {
            return $this->renderPage($request, 'pages/administration/gruppen/form', $this->pageParams($request, [
                'pageTitle' => 'Gruppe bearbeiten',
                'mode' => 'edit',
                'action' => '/administration/gruppen/' . $id . '/edit',
                'group' => array_merge($group, $request->body),
                'systems' => $this->administration->systems(),
                'errors' => [$e->getMessage()],
            ]), 422);
        }
    }

    public function permissionsForm(Request $request): Response
    {
        $this->authorization->requirePermission('identity.gruppen.permissions.manage');
        $id = $this->routeInt($request, 'id');
        $group = $this->administration->group($id);
        if ($group === []) {
            return $this->text('Gruppe nicht gefunden.', 404);
        }
        return $this->renderPage($request, 'pages/administration/gruppen/permissions', $this->pageParams($request, [
            'pageTitle' => 'Permissions: ' . (string) $group['name'],
            'group' => $group,
            'permissions' => $this->administration->permissions((int) $group['system_id'], false),
            'selectedPermissionIds' => $this->administration->permissionIdsForGroup($id),
            'errors' => [],
        ]));
    }

    public function permissions(Request $request): Response
    {
        $this->authorization->requirePermission('identity.gruppen.permissions.manage');
        $id = $this->routeInt($request, 'id');
        $group = $this->administration->group($id);
        if ($group === []) {
            return $this->text('Gruppe nicht gefunden.', 404);
        }
        try {
            $permissionIds = $request->body['permission_ids'] ?? [];
            if (!is_array($permissionIds)) {
                $permissionIds = [];
            }
            $this->administration->syncGroupPermissions($id, array_map('intval', $permissionIds));
            return $this->redirect('/administration/gruppen/' . $id . '?message=permissions');
        } catch (RuntimeException|InvalidArgumentException $e) {
            return $this->renderPage($request, 'pages/administration/gruppen/permissions', $this->pageParams($request, [
                'pageTitle' => 'Permissions: ' . (string) $group['name'],
                'group' => $group,
                'permissions' => $this->administration->permissions((int) $group['system_id'], false),
                'selectedPermissionIds' => array_map('intval', (array) ($request->body['permission_ids'] ?? [])),
                'errors' => [$e->getMessage()],
            ]), 422);
        }
    }

    public function delete(Request $request): Response
    {
        $this->authorization->requirePermission('identity.gruppen.delete');
        $id = $this->routeInt($request, 'id');
        $group = $this->administration->group($id);
        try {
            $this->safety->assertGroupCanBeDeleted($group);
            $this->administration->deleteGroup($id);
            return $this->redirect('/administration/gruppen?message=deleted');
        } catch (RuntimeException $e) {
            return $this->text($e->getMessage(), 409);
        }
    }

    /** @param array<string,mixed> $overrides @return array<string,mixed> */
    private function pageParams(Request $request, array $overrides): array
    {
        return array_replace([
            'title' => 'Gruppen',
            'areaName' => 'Administration',
            'pageTitle' => 'Gruppen',
            'headerAreaKey' => 'administration',
            'areaRootLink' => '/administration',
            'areaNav' => $this->navigation->items('gruppen'),
            'message' => $this->queryString($request, 'message'),
        ], $overrides);
    }
}
