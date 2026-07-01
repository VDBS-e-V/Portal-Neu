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
use App\Repository\SchoolDirectoryRepository;
use App\Security\AuthorizationService;
use App\Service\SchoolDirectoryReadService;
use App\Service\SchoolDirectorySimpleEditService;
use InvalidArgumentException;

final class SchulverzeichnisController extends PageController
{
    private const AREA = 'verwaltung';
    private const PAGE_GROUP = 'schulverzeichnis';

    public function __construct(
        Renderer $renderer,
        AreaRepository $areas,
        MenuRepository $menus,
        MenuItemRepository $menuItems,
        private readonly VerwaltungNavigation $navigation,
        private readonly AuthorizationService $authorization,
        private readonly SchoolDirectoryRepository $schools,
        private readonly SchoolDirectoryReadService $readService,
        private readonly SchoolDirectorySimpleEditService $editService
    ) {
        parent::__construct($renderer, $areas, $menus, $menuItems);
    }

    public function index(Request $request): Response
    {
        $this->authorization->requirePageGroupAccess(self::AREA, self::PAGE_GROUP);

        $page = max(1, $this->queryInt($request, 'page', 1));
        $perPage = 50;
        $filters = [
            'q' => $this->queryString($request, 'q'),
            'federal_state_code' => $this->queryString($request, 'federal_state_code'),
            'district' => $this->queryString($request, 'district'),
            'school_type' => $this->queryString($request, 'school_type'),
            'lifecycle_status' => $this->queryString($request, 'lifecycle_status', 'active'),
            'data_status' => $this->queryString($request, 'data_status'),
        ];

        $total = $this->schools->countSearch($filters);
        $schools = $this->schools->search($filters, $perPage, ($page - 1) * $perPage);

        return $this->renderPage($request, 'pages/verwaltung/schulverzeichnis/index', [
            'title' => 'Schulverzeichnis',
            'areaName' => 'Verwaltung',
            'pageTitle' => 'Schulverzeichnis',
            'headerAreaKey' => 'verwaltung',
            'areaRootLink' => '/verwaltung',
            'areaNav' => $this->navigation->items('schulverzeichnis'),
            'schools' => $schools,
            'filters' => $filters,
            'page' => $page,
            'perPage' => $perPage,
            'total' => $total,
        ]);
    }

    public function show(Request $request): Response
    {
        $this->authorization->requirePageGroupAccess(self::AREA, self::PAGE_GROUP);

        $schoolId = $this->routeInt($request, 'schoolId');
        $detail = $this->readService->detail($schoolId);

        if ($detail === []) {
            return $this->redirect('/verwaltung/schulverzeichnis');
        }

        return $this->renderPage($request, 'pages/verwaltung/schulverzeichnis/show', [
            'title' => 'Schule',
            'areaName' => 'Verwaltung',
            'pageTitle' => (string) ($detail['effective']['name'] ?? 'Schule'),
            'headerAreaKey' => 'verwaltung',
            'areaRootLink' => '/verwaltung',
            'areaNav' => $this->navigation->items('schulverzeichnis'),
            'detail' => $detail,
        ]);
    }

    public function edit(Request $request): Response
    {
        $this->authorization->requirePageGroupAccess(self::AREA, self::PAGE_GROUP);

        $schoolId = $this->routeInt($request, 'schoolId');
        $detail = $this->readService->detail($schoolId);

        if ($detail === []) {
            return $this->redirect('/verwaltung/schulverzeichnis');
        }

        return $this->renderEdit($request, $detail);
    }

    public function update(Request $request): Response
    {
        $this->authorization->requirePageGroupAccess(self::AREA, self::PAGE_GROUP);

        $schoolId = $this->routeInt($request, 'schoolId');
        $detail = $this->readService->detail($schoolId);

        if ($detail === []) {
            return $this->redirect('/verwaltung/schulverzeichnis');
        }

        try {
            $this->editService->update(
                $schoolId,
                $request->body,
                $this->authorization->currentPersonId()
            );
        } catch (InvalidArgumentException $exception) {
            return $this->renderEdit($request, $detail, [$exception->getMessage()]);
        }

        return $this->redirect('/verwaltung/schulverzeichnis/' . urlencode((string) $schoolId));
    }

    public function verify(Request $request): Response
    {
        $this->authorization->requirePageGroupAccess(self::AREA, self::PAGE_GROUP);

        $schoolId = $this->routeInt($request, 'schoolId');
        if ($schoolId > 0) {
            $this->schools->markVerified($schoolId);
        }

        return $this->redirect('/verwaltung/schulverzeichnis/' . urlencode((string) $schoolId));
    }

    /**
     * @param array<string, mixed> $detail
     * @param array<int, string> $errors
     */
    private function renderEdit(Request $request, array $detail, array $errors = []): Response
    {
        return $this->renderPage($request, 'pages/verwaltung/schulverzeichnis/edit', [
            'title' => 'Kleine Anpassung',
            'areaName' => 'Verwaltung',
            'pageTitle' => 'Kleine Anpassung',
            'headerAreaKey' => 'verwaltung',
            'areaRootLink' => '/verwaltung',
            'areaNav' => $this->navigation->items('schulverzeichnis'),
            'detail' => $detail,
            'errors' => $errors,
        ]);
    }
}
