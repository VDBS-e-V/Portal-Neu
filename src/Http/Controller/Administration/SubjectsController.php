<?php

declare(strict_types=1);

namespace App\Http\Controller\Administration;

use App\Http\Request\Request;
use App\Http\Response\Response;
use App\Navigation\AdministrationNavigation;
use App\Repository\IdentityAdministrationRepository;
use App\Security\AuthorizationService;
use App\Presentation\Templating\Renderer;

final class SubjectsController extends AbstractAdministrationController
{
    public function __construct(
        Renderer $renderer,
        private readonly IdentityAdministrationRepository $administration,
        private readonly AdministrationNavigation $navigation,
        private readonly AuthorizationService $authorization
    ) {
        parent::__construct($renderer);
    }

    public function show(Request $request): Response
    {
        $this->authorization->requirePermission('identity.subjects.view');
        $subjectId = $this->routeInt($request, 'id');
        $subject = $this->administration->subject($subjectId);
        if ($subject === []) {
            return $this->text('Subject nicht gefunden.', 404);
        }

        return $this->renderPage($request, 'pages/administration/subjects/show', [
            'title' => 'Subject',
            'areaName' => 'Administration',
            'pageTitle' => 'Subject ' . (string) $subject['uuid'],
            'headerAreaKey' => 'administration',
            'areaRootLink' => '/administration',
            'areaNav' => $this->navigation->items('personen'),
            'subject' => $subject,
            'groups' => $this->administration->subjectGroups($subjectId),
            'permissions' => $this->authorization->currentSubjectId() === $subjectId
                ? $this->authorization->permissionsForCurrentSubject()
                : [],
            'message' => $this->queryString($request, 'message'),
        ]);
    }
}
