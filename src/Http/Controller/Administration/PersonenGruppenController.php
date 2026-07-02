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
use RuntimeException;

final class PersonenGruppenController extends Controller
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
        $this->authorization->requirePermission('identity.subjects.groups.view');
        return $this->renderPage($request, 'pages/administration/personen/index', $this->pageParams($request, [
            'pageTitle' => 'Personen-Gruppen',
            'persons' => $this->administration->persons($this->queryString($request, 'q')),
            'filters' => ['q' => $this->queryString($request, 'q')],
        ]));
    }

    public function groups(Request $request): Response
    {
        $this->authorization->requirePermission('identity.subjects.groups.view');
        $personId = $this->routeInt($request, 'id');
        $person = $this->administration->personWithSubject($personId);
        if ($person === []) {
            return $this->text('Person nicht gefunden.', 404);
        }
        $subjectId = (int) ($person['subject_id'] ?? 0);
        if ($subjectId <= 0) {
            $subjectId = 0;
        }
        return $this->renderPage($request, 'pages/administration/personen/groups', $this->pageParams($request, [
            'pageTitle' => 'Gruppen: ' . (string) ($person['display_name'] ?? ('Person #' . $personId)),
            'person' => $person,
            'assignedGroups' => $subjectId > 0 ? $this->administration->subjectGroups($subjectId) : [],
            'availableGroups' => $this->administration->groups(),
            'errors' => [],
        ]));
    }

    public function assign(Request $request): Response
    {
        $this->authorization->requirePermission('identity.subjects.groups.assign');
        $personId = $this->routeInt($request, 'id');
        $actorSubjectId = $this->authorization->currentSubjectId();
        try {
            $this->administration->assignGroupToPerson(
                $personId,
                $this->bodyInt($request->body, 'group_id'),
                $actorSubjectId,
                $this->bodyNullableString($request->body, 'expires_at'),
                $this->bodyNullableString($request->body, 'note')
            );
            return $this->redirect('/administration/personen/' . $personId . '/gruppen?message=assigned');
        } catch (RuntimeException $e) {
            $person = $this->administration->personWithSubject($personId);
            $subjectId = (int) ($person['subject_id'] ?? 0);
            return $this->renderPage($request, 'pages/administration/personen/groups', $this->pageParams($request, [
                'pageTitle' => 'Gruppen zuweisen',
                'person' => $person,
                'assignedGroups' => $subjectId > 0 ? $this->administration->subjectGroups($subjectId) : [],
                'availableGroups' => $this->administration->groups(),
                'errors' => [$e->getMessage()],
            ]), 422);
        }
    }

    public function remove(Request $request): Response
    {
        $this->authorization->requirePermission('identity.subjects.groups.remove');
        $personId = $this->routeInt($request, 'id');
        $groupId = $this->routeInt($request, 'groupId');
        $person = $this->administration->personWithSubject($personId);
        if ($person === []) {
            return $this->text('Person nicht gefunden.', 404);
        }
        $subjectId = (int) ($person['subject_id'] ?? 0);
        $group = $this->administration->group($groupId);
        try {
            $this->safety->assertSubjectGroupCanBeRemoved($subjectId, $group);
            $this->administration->removeGroupFromSubject($subjectId, $groupId);
            return $this->redirect('/administration/personen/' . $personId . '/gruppen?message=removed');
        } catch (RuntimeException $e) {
            return $this->text($e->getMessage(), 409);
        }
    }

    /** @param array<string,mixed> $overrides @return array<string,mixed> */
    private function pageParams(Request $request, array $overrides): array
    {
        return array_replace([
            'title' => 'Personen-Gruppen',
            'areaName' => 'Administration',
            'pageTitle' => 'Personen-Gruppen',
            'headerAreaKey' => 'administration',
            'areaRootLink' => '/administration',
            'areaNav' => $this->navigation->items('personen'),
            'message' => $this->queryString($request, 'message'),
        ], $overrides);
    }
}
