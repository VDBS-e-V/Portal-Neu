<?php

declare(strict_types=1);

namespace App\Http\Controller\Administration;

use App\Http\Request\Request;
use App\Http\Response\Response;
use App\Navigation\AdministrationNavigation;
use App\Repository\IdentityAdministrationRepository;
use App\Security\AuthorizationService;
use App\Presentation\Templating\Renderer;
use InvalidArgumentException;

final class SystemeController extends AbstractAdministrationController
{
    public function __construct(
        Renderer $renderer,
        private readonly IdentityAdministrationRepository $administration,
        private readonly AdministrationNavigation $navigation,
        private readonly AuthorizationService $authorization
    ) {
        parent::__construct($renderer);
    }

    public function index(Request $request): Response
    {
        $this->authorization->requirePermission('identity.systeme.view');
        return $this->renderPage($request, 'pages/administration/systeme/index', $this->pageParams($request, [
            'pageTitle' => 'Systeme',
            'systems' => $this->administration->systems(),
        ]));
    }

    public function createForm(Request $request): Response
    {
        $this->authorization->requirePermission('identity.systeme.create');
        return $this->renderPage($request, 'pages/administration/systeme/form', $this->pageParams($request, [
            'pageTitle' => 'System anlegen',
            'mode' => 'create',
            'action' => '/administration/systeme/create',
            'system' => ['is_active' => 1, 'is_external' => 0, 'sorting' => 100],
            'errors' => [],
        ]));
    }

    public function create(Request $request): Response
    {
        $this->authorization->requirePermission('identity.systeme.create');
        try {
            $id = $this->administration->createSystem($request->body);
            return $this->redirect('/administration/systeme/' . $id . '?message=created');
        } catch (InvalidArgumentException $e) {
            return $this->renderPage($request, 'pages/administration/systeme/form', $this->pageParams($request, [
                'pageTitle' => 'System anlegen',
                'mode' => 'create',
                'action' => '/administration/systeme/create',
                'system' => $request->body,
                'errors' => [$e->getMessage()],
            ]), 422);
        }
    }

    public function show(Request $request): Response
    {
        $this->authorization->requirePermission('identity.systeme.view');
        $id = $this->routeInt($request, 'id');
        $system = $this->administration->system($id);
        if ($system === []) {
            return $this->text('System nicht gefunden.', 404);
        }
        return $this->renderPage($request, 'pages/administration/systeme/show', $this->pageParams($request, [
            'pageTitle' => (string) $system['name'],
            'system' => $system,
            'groups' => $this->administration->groups($id),
            'permissions' => $this->administration->permissions($id),
        ]));
    }

    public function editForm(Request $request): Response
    {
        $this->authorization->requirePermission('identity.systeme.edit');
        $id = $this->routeInt($request, 'id');
        $system = $this->administration->system($id);
        if ($system === []) {
            return $this->text('System nicht gefunden.', 404);
        }
        return $this->renderPage($request, 'pages/administration/systeme/form', $this->pageParams($request, [
            'pageTitle' => 'System bearbeiten',
            'mode' => 'edit',
            'action' => '/administration/systeme/' . $id . '/edit',
            'system' => $system,
            'errors' => [],
        ]));
    }

    public function edit(Request $request): Response
    {
        $this->authorization->requirePermission('identity.systeme.edit');
        $id = $this->routeInt($request, 'id');
        $system = $this->administration->system($id);
        if ($system === []) {
            return $this->text('System nicht gefunden.', 404);
        }
        try {
            $this->administration->updateSystem($id, $request->body);
            return $this->redirect('/administration/systeme/' . $id . '?message=updated');
        } catch (InvalidArgumentException $e) {
            return $this->renderPage($request, 'pages/administration/systeme/form', $this->pageParams($request, [
                'pageTitle' => 'System bearbeiten',
                'mode' => 'edit',
                'action' => '/administration/systeme/' . $id . '/edit',
                'system' => array_merge($system, $request->body),
                'errors' => [$e->getMessage()],
            ]), 422);
        }
    }

    /** @param array<string,mixed> $overrides @return array<string,mixed> */
    private function pageParams(Request $request, array $overrides): array
    {
        return array_replace([
            'title' => 'Systeme',
            'areaName' => 'Administration',
            'pageTitle' => 'Systeme',
            'headerAreaKey' => 'administration',
            'areaRootLink' => '/administration',
            'areaNav' => $this->navigation->items('systeme'),
            'message' => $this->queryString($request, 'message'),
        ], $overrides);
    }
}
