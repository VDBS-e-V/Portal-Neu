<?php

declare(strict_types=1);

namespace App\Http\Controller\Verwaltung;

use App\Http\Controller\PageController;
use App\Http\Request\Request;
use App\Http\Response\Response;
use App\Presentation\Templating\Renderer;
use App\Repository\AreaRepository;
use App\Repository\AuditLogRepository;
use App\Repository\MenuItemRepository;
use App\Repository\MenuRepository;
use App\Security\AuthorizationService;
use App\Security\VerwaltungAccess;

final class AuditLogController extends PageController
{
    public function __construct(
        Renderer $renderer,
        AreaRepository $areas,
        MenuRepository $menus,
        MenuItemRepository $menuItems,
        private readonly AuditLogRepository $auditLogs,
        private readonly AuthorizationService $authorization
    ) {
        parent::__construct($renderer, $areas, $menus, $menuItems);
    }

    public function index(Request $request): Response
    {
        $this->authorization->requirePermission('portal.verwaltung.audit.view');

        $filters = [
            'q' => $this->queryString($request, 'q'),
            'action' => $this->queryString($request, 'action'),
            'entity_type' => $this->queryString($request, 'entity_type'),
            'from' => $this->queryString($request, 'from'),
            'to' => $this->queryString($request, 'to'),
            'limit' => $this->queryInt($request, 'limit') ?: 100,
        ];

        return $this->renderPage($request, 'pages/verwaltung/audit/index', $this->pageParams([
            'title' => 'Audit-Log',
            'pageTitle' => 'Audit-Log',
            'activeKey' => 'berechtigungen',
            'logs' => $this->auditLogs->search($filters),
            'filters' => $filters,
            'actions' => $this->auditLogs->distinctActions(),
            'entityTypes' => $this->auditLogs->distinctEntityTypes(),
        ]));
    }

    public function show(Request $request): Response
    {
        $this->authorization->requirePermission('portal.verwaltung.audit.view');

        $id = $this->routeInt($request, 'id');
        $log = $this->auditLogs->find($id);

        if ($log === []) {
            return $this->text('Audit-Eintrag nicht gefunden.', 404);
        }

        return $this->renderPage($request, 'pages/verwaltung/audit/show', $this->pageParams([
            'title' => 'Audit-Eintrag',
            'pageTitle' => 'Audit-Eintrag #' . $id,
            'activeKey' => 'berechtigungen',
            'log' => $log,
        ]));
    }

    /**
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    private function pageParams(array $overrides): array
    {
        $activeKey = (string) ($overrides['activeKey'] ?? 'berechtigungen');

        return array_replace([
            'title' => 'Audit-Log',
            'areaName' => 'Verwaltung',
            'pageTitle' => 'Audit-Log',
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
            [
                'label' => 'Audit-Log',
                'href' => '/verwaltung/audit',
                'active' => $activeKey === 'audit',
            ],
        ];
    }
}
