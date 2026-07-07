<?php

declare(strict_types=1);

namespace App\Http\Controller\Verwaltung;

use App\Http\Controller\PageController;
use App\Http\Request\Request;
use App\Http\Response\Response;
use App\Navigation\VerwaltungNavigation;
use App\Presentation\Templating\Renderer;
use App\Repository\AreaRepository;
use App\Repository\EntityAuditRepository;
use App\Repository\MenuItemRepository;
use App\Repository\MenuRepository;
use App\Repository\PersonErasureRepository;
use App\Repository\PersonRepository;
use App\Security\AuthorizationService;
use App\Security\VerwaltungAccess;

final class EntityAuditController extends PageController
{
    public function __construct(
        Renderer $renderer,
        AreaRepository $areas,
        MenuRepository $menus,
        MenuItemRepository $menuItems,
        private readonly EntityAuditRepository $audit,
        private readonly PersonRepository $persons,
        private readonly PersonErasureRepository $erasures,
        private readonly VerwaltungNavigation $navigation,
        private readonly AuthorizationService $authorization
    ) {
        parent::__construct($renderer, $areas, $menus, $menuItems);
    }

    public function person(Request $request): Response
    {
        $this->authorization->requirePermission('portal.verwaltung.entity-audit.view');

        $personId = $this->routeInt($request, 'id');
        $person = $this->persons->find($personId);

        if ($person === []) {
            return $this->text('Person nicht gefunden.', 404);
        }

        return $this->renderPage($request, 'pages/verwaltung/audit/entity', $this->pageParams([
            'title' => 'Audit: Person',
            'pageTitle' => 'Audit: ' . $this->label($person, 'Person #' . $personId),
            'activeKey' => 'personen',
            'backHref' => '/verwaltung/personen/' . $personId,
            'backLabel' => 'Zur Person',
            'entityTitle' => $this->label($person, 'Person #' . $personId),
            'entitySubtitle' => 'Person #' . $personId,
            'entityType' => 'ids_persons',
            'entityId' => $personId,
            'entries' => $this->audit->forPerson($personId),
        ]));
    }

    public function group(Request $request): Response
    {
        $this->authorization->requirePermission('portal.verwaltung.entity-audit.view');
        $groupId = $this->routeInt($request, 'id');

        return $this->renderPage($request, 'pages/verwaltung/audit/entity', $this->pageParams([
            'title' => 'Audit: Gruppe',
            'pageTitle' => 'Audit: Gruppe #' . $groupId,
            'activeKey' => 'gruppen',
            'backHref' => '/administration/gruppen/' . $groupId,
            'backLabel' => 'Zur Gruppe',
            'entityTitle' => 'Gruppe #' . $groupId,
            'entitySubtitle' => 'Identity-Gruppe #' . $groupId,
            'entityType' => 'ids_groups',
            'entityId' => $groupId,
            'entries' => $this->audit->forGroup($groupId),
        ]));
    }

    public function erasure(Request $request): Response
    {
        $this->authorization->requirePermission('portal.verwaltung.entity-audit.view');

        $requestId = $this->routeInt($request, 'id');
        $erasure = $this->erasures->find($requestId);

        if ($erasure === []) {
            return $this->text('DSGVO-Vorgang nicht gefunden.', 404);
        }

        $label = trim((string) ($erasure['display_name'] ?? ''));

        if ($label === '') {
            $label = 'DSGVO-Vorgang #' . $requestId;
        }

        return $this->renderPage($request, 'pages/verwaltung/audit/entity', $this->pageParams([
            'title' => 'Audit: DSGVO-Vorgang',
            'pageTitle' => 'Audit: DSGVO-Vorgang #' . $requestId,
            'activeKey' => 'datenschutz',
            'backHref' => '/verwaltung/datenschutz/' . $requestId,
            'backLabel' => 'Zum DSGVO-Vorgang',
            'entityTitle' => $label,
            'entitySubtitle' => 'DSGVO-Vorgang #' . $requestId . ' · Person #' . (int) ($erasure['person_id'] ?? 0),
            'entityType' => 'ids_person_erasure_requests',
            'entityId' => $requestId,
            'entries' => $this->audit->forErasureRequest($requestId),
        ]));
    }

    /**
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    private function pageParams(array $overrides): array
    {
        $activeKey = (string) ($overrides['activeKey'] ?? 'audit');

        return array_replace([
            'title' => 'Audit',
            'areaName' => 'Verwaltung',
            'pageTitle' => 'Audit',
            'headerAreaKey' => 'verwaltung',
            'areaRootLink' => '/verwaltung',
            'areaNav' => $this->navigation->items($activeKey),
        ], $overrides);
    }

    /**
     * @param array<string, mixed> $row
     */
    private function label(array $row, string $fallback): string
    {
        foreach (['display_name', 'name', 'group_name', 'label', 'title', 'login_email', 'email'] as $key) {
            $value = trim((string) ($row[$key] ?? ''));

            if ($value !== '') {
                return $value;
            }
        }

        return $fallback;
    }

    private function routeInt(Request $request, string $key, int $default = 0): int
    {
        $routeParams = $request->routeParams ?? [];

        if (array_key_exists($key, $routeParams)) {
            return (int) $routeParams[$key];
        }

        return $default;
    }

    private function text(string $content, int $status = 200): Response
    {
        return new Response($content, $status, ['Content-Type' => 'text/plain; charset=utf-8']);
    }
}
