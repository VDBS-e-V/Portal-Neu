<?php

declare(strict_types=1);

namespace App\Navigation;

use App\Security\AuthorizationService;

final class VerwaltungNavigation
{
    public function __construct(private readonly ?AuthorizationService $authorization = null)
    {
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function items(string $activeKey = ''): array
    {
        $items = [
            [
                'key' => 'dashboard',
                'label' => 'Übersicht',
                'href' => '/verwaltung',
                'area' => 'verwaltung',
                'page_group' => 'personen',
                'active' => $activeKey === 'dashboard',
            ],
            [
                'key' => 'personen',
                'label' => 'Personen',
                'href' => '/verwaltung/personen',
                'area' => 'verwaltung',
                'page_group' => 'personen',
                'active' => $activeKey === 'personen',
            ],
            [
                'key' => 'einladungen',
                'label' => 'Einladungen',
                'href' => '/verwaltung/einladungen',
                'area' => 'verwaltung',
                'page_group' => 'einladungen',
                'active' => $activeKey === 'einladungen',
            ],
            [
                'key' => 'datenschutz',
                'label' => 'DSGVO',
                'href' => '/verwaltung/datenschutz',
                'area' => 'verwaltung',
                'page_group' => 'datenschutz',
                'active' => $activeKey === 'datenschutz',
            ],
            [
                'key' => 'gruppen',
                'label' => 'Gruppen',
                'href' => '/verwaltung/gruppen',
                'area' => 'verwaltung',
                'page_group' => 'gruppen',
                'active' => $activeKey === 'gruppen',
            ],
            [
                'key' => 'berechtigungen',
                'label' => 'Berechtigungen',
                'href' => '/verwaltung/berechtigungen',
                'area' => 'verwaltung',
                'page_group' => 'berechtigungen',
                'active' => $activeKey === 'berechtigungen',
            ],
            [
                'key' => 'audit',
                'label' => 'Audit-Log',
                'href' => '/verwaltung/audit',
                'area' => 'verwaltung',
                'page_group' => 'audit',
                'active' => $activeKey === 'audit',
            ],
        ];

        return array_values(array_filter(
            $items,
            fn (array $item): bool => $this->canSee($item)
        ));
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function quickLinks(): array
    {
        $links = [
            [
                'label' => 'Person anlegen',
                'href' => '/verwaltung/personen/create',
                'area' => 'verwaltung',
                'page_group' => 'personen',
                'description' => 'Neue Person mit optionalem Login-Konto erfassen.',
            ],
            [
                'label' => 'Gruppen verwalten',
                'href' => '/verwaltung/gruppen',
                'area' => 'verwaltung',
                'page_group' => 'gruppen',
                'description' => 'Berechtigungsgruppen anzeigen und pflegen.',
            ],
            [
                'label' => 'Berechtigungsmatrix',
                'href' => '/verwaltung/berechtigungen',
                'area' => 'verwaltung',
                'page_group' => 'berechtigungen',
                'description' => 'PageGroup-Zugriffe je Gruppe bearbeiten.',
            ],
            [
                'label' => 'Einladungen',
                'href' => '/verwaltung/einladungen',
                'area' => 'verwaltung',
                'page_group' => 'einladungen',
                'description' => 'Account-Einladungen prüfen und widerrufen.',
            ],
            [
                'label' => 'DSGVO-Vorgänge',
                'href' => '/verwaltung/datenschutz',
                'area' => 'verwaltung',
                'page_group' => 'datenschutz',
                'description' => 'Löschersuchen prüfen und anonymisieren.',
            ],
            [
                'label' => 'Audit-Log',
                'href' => '/verwaltung/audit',
                'area' => 'verwaltung',
                'page_group' => 'audit',
                'description' => 'Änderungen und sicherheitsrelevante Aktionen prüfen.',
            ],
        ];

        return array_values(array_filter(
            $links,
            fn (array $item): bool => $this->canSee($item)
        ));
    }

    /**
     * @param array<string, mixed> $item
     */
    private function canSee(array $item): bool
    {
        if ($this->authorization === null) {
            return true;
        }

        $areaKey = trim((string) ($item['area'] ?? ''));
        $pageGroupKey = trim((string) ($item['page_group'] ?? ''));

        if ($areaKey === '' || $pageGroupKey === '') {
            return true;
        }

        return $this->authorization->currentUserCanAccessPageGroup($areaKey, $pageGroupKey);
    }
}
