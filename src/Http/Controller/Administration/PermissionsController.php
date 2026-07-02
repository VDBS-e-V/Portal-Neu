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

final class PermissionsController extends Controller
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
        $this->authorization->requirePermission('identity.permissions.view');
        $systemId = $this->queryInt($request, 'system_id');
        return $this->renderPage($request, 'pages/administration/permissions/index', $this->pageParams($request, [
            'pageTitle' => 'Permissions',
            'permissions' => $this->administration->permissions($systemId > 0 ? $systemId : null),
            'systems' => $this->administration->systems(),
            'filters' => ['system_id' => $systemId],
        ]));
    }

    public function createForm(Request $request): Response
    {
        $this->authorization->requirePermission('identity.permissions.create');
        return $this->renderPage($request, 'pages/administration/permissions/form', $this->pageParams($request, [
            'pageTitle' => 'Permission anlegen',
            'mode' => 'create',
            'action' => '/administration/permissions/create',
            'permission' => ['is_active' => 1, 'is_system' => 0],
            'systems' => $this->administration->systems(),
            'errors' => [],
        ]));
    }

    public function create(Request $request): Response
    {
        $this->authorization->requirePermission('identity.permissions.create');
        try {
            $id = $this->administration->createPermission($request->body);
            return $this->redirect('/administration/permissions/' . $id . '?message=created');
        } catch (InvalidArgumentException $e) {
            return $this->renderPage($request, 'pages/administration/permissions/form', $this->pageParams($request, [
                'pageTitle' => 'Permission anlegen',
                'mode' => 'create',
                'action' => '/administration/permissions/create',
                'permission' => $request->body,
                'systems' => $this->administration->systems(),
                'errors' => [$e->getMessage()],
            ]), 422);
        }
    }

    public function show(Request $request): Response
    {
        $this->authorization->requirePermission('identity.permissions.view');
        $id = $this->routeInt($request, 'id');
        $permission = $this->administration->permission($id);
        if ($permission === []) {
            return $this->text('Permission nicht gefunden.', 404);
        }
        return $this->renderPage($request, 'pages/administration/permissions/show', $this->pageParams($request, [
            'pageTitle' => (string) $permission['key_name'],
            'permission' => $permission,
        ]));
    }

    public function editForm(Request $request): Response
    {
        $this->authorization->requirePermission('identity.permissions.edit');
        $id = $this->routeInt($request, 'id');
        $permission = $this->administration->permission($id);
        if ($permission === []) {
            return $this->text('Permission nicht gefunden.', 404);
        }
        return $this->renderPage($request, 'pages/administration/permissions/form', $this->pageParams($request, [
            'pageTitle' => 'Permission bearbeiten',
            'mode' => 'edit',
            'action' => '/administration/permissions/' . $id . '/edit',
            'permission' => $permission,
            'systems' => $this->administration->systems(),
            'errors' => [],
        ]));
    }

    public function edit(Request $request): Response
    {
        $this->authorization->requirePermission('identity.permissions.edit');
        $id = $this->routeInt($request, 'id');
        $permission = $this->administration->permission($id);
        if ($permission === []) {
            return $this->text('Permission nicht gefunden.', 404);
        }
        try {
            $this->administration->updatePermission($id, $request->body);
            return $this->redirect('/administration/permissions/' . $id . '?message=updated');
        } catch (InvalidArgumentException $e) {
            return $this->renderPage($request, 'pages/administration/permissions/form', $this->pageParams($request, [
                'pageTitle' => 'Permission bearbeiten',
                'mode' => 'edit',
                'action' => '/administration/permissions/' . $id . '/edit',
                'permission' => array_merge($permission, $request->body),
                'systems' => $this->administration->systems(),
                'errors' => [$e->getMessage()],
            ]), 422);
        }
    }

    public function deactivate(Request $request): Response
    {
        $this->authorization->requirePermission('identity.permissions.delete');
        $id = $this->routeInt($request, 'id');
        $permission = $this->administration->permission($id);
        try {
            $this->safety->assertPermissionCanBeDeactivated($permission);
            $this->administration->deactivatePermission($id, $this->bodyNullableString($request->body, 'deprecated_reason'));
            return $this->redirect('/administration/permissions/' . $id . '?message=deactivated');
        } catch (RuntimeException $e) {
            return $this->text($e->getMessage(), 409);
        }
    }

    /** @param array<string,mixed> $overrides @return array<string,mixed> */
    private function pageParams(Request $request, array $overrides): array
    {
        return array_replace([
            'title' => 'Permissions',
            'areaName' => 'Administration',
            'pageTitle' => 'Permissions',
            'headerAreaKey' => 'administration',
            'areaRootLink' => '/administration',
            'areaNav' => $this->navigation->items('permissions'),
            'message' => $this->queryString($request, 'message'),
        ], $overrides);
    }
}
