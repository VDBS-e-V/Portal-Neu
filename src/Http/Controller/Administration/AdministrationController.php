<?php

declare(strict_types=1);

namespace App\Http\Controller\Administration;

use App\Http\Controller\Controller;
use App\Http\Request\Request;
use App\Http\Response\Response;
use App\Navigation\AdministrationNavigation;
use App\Repository\IdentityAdministrationRepository;
use App\Security\AuthorizationService;
use App\Presentation\Templating\Renderer;

final class AdministrationController extends Controller
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
        $this->authorization->requirePermission('portal.verwaltung.dashboard.view');

        return $this->renderPage($request, 'pages/administration/index', $this->pageParams([
            'pageTitle' => 'Administration',
            'activeKey' => 'dashboard',
            'stats' => $this->administration->dashboardStats(),
            'quickLinks' => $this->navigation->quickLinks(),
        ]));
    }

    /** @param array<string,mixed> $overrides @return array<string,mixed> */
    private function pageParams(array $overrides): array
    {
        $activeKey = (string) ($overrides['activeKey'] ?? 'dashboard');
        return array_replace([
            'title' => 'Administration',
            'areaName' => 'Administration',
            'pageTitle' => 'Administration',
            'headerAreaKey' => 'administration',
            'areaRootLink' => '/administration',
            'areaNav' => $this->navigation->items($activeKey),
        ], $overrides);
    }
}
