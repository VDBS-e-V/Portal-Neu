<?php

declare(strict_types=1);

namespace App\Http\Controller\Verwaltung;

use App\Http\Controller\PageController;
use App\Http\Request\Request;
use App\Http\Response\Response;
use App\Navigation\VerwaltungNavigation;
use App\Presentation\Templating\Renderer;
use App\Repository\AreaRepository;
use App\Repository\MenuItemRepository;
use App\Repository\MenuRepository;
use App\Repository\VerwaltungStatsRepository;
use App\Security\AuthorizationService;
use App\Security\VerwaltungAccess;

final class VerwaltungController extends PageController
{
    public function __construct(
        Renderer $renderer,
        AreaRepository $areas,
        MenuRepository $menus,
        MenuItemRepository $menuItems,
        private readonly VerwaltungStatsRepository $stats,
        private readonly VerwaltungNavigation $navigation,
        private readonly AuthorizationService $authorization
    ) {
        parent::__construct($renderer, $areas, $menus, $menuItems);
    }

    public function index(Request $request): Response
    {
        $this->authorization->requirePageGroupAccess(VerwaltungAccess::AREA, VerwaltungAccess::PERSONEN);

        return $this->renderPage($request, 'pages/verwaltung/index', [
            'title' => 'Verwaltung',
            'areaName' => 'Verwaltung',
            'pageTitle' => 'Verwaltung',
            'headerAreaKey' => 'verwaltung',
            'areaRootLink' => '/verwaltung',
            'areaNav' => $this->navigation->items('dashboard'),
            'quickLinks' => $this->navigation->quickLinks(),
            'stats' => $this->stats->overview(),
            'latestPersons' => $this->stats->latestPersons(5),
            'latestAuditEntries' => $this->stats->latestAuditEntries(5),
            'openTasks' => $this->stats->openTasks(10),
        ]);
    }
}
