<?php

declare(strict_types=1);

namespace App\Navigation;

use App\Identity\IdentityPermissions;
use App\Identity\PortalPermissions;
use App\Security\AuthorizationService;

/**
 * Navigation für die neue Administrationsoberfläche unter /administration.
 */
final class AdministrationNavigation
{
    public function __construct(private readonly ?AuthorizationService $authorization = null)
    {
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function items(string $activeKey = ''): array
    {
        $items = [
            [
                'key' => 'dashboard',
                'label' => 'Übersicht',
                'href' => '/administration',
                'permission' => PortalPermissions::VERWALTUNG_DASHBOARD_VIEW,
                'active' => $activeKey === 'dashboard',
            ],
            [
                'key' => 'personen',
                'label' => 'Personen',
                'href' => '/administration/personen',
                'permission' => PortalPermissions::VERWALTUNG_PERSONEN_VIEW,
                'active' => $activeKey === 'personen',
            ],
            [
                'key' => 'gruppen',
                'label' => 'Gruppen',
                'href' => '/administration/gruppen',
                'permission' => IdentityPermissions::GRUPPEN_VIEW,
                'active' => $activeKey === 'gruppen',
            ],
            [
                'key' => 'permissions',
                'label' => 'Permissions',
                'href' => '/administration/permissions',
                'permission' => IdentityPermissions::PERMISSIONS_VIEW,
                'active' => $activeKey === 'permissions',
            ],
            [
                'key' => 'systeme',
                'label' => 'Systeme',
                'href' => '/administration/systeme',
                'permission' => IdentityPermissions::SYSTEME_VIEW,
                'active' => $activeKey === 'systeme',
            ],
            [
                'key' => 'einladungen',
                'label' => 'Einladungen',
                'href' => '/administration/einladungen',
                'permission' => PortalPermissions::VERWALTUNG_EINLADUNGEN_VIEW,
                'active' => $activeKey === 'einladungen',
            ],
            [
                'key' => 'datenschutz',
                'label' => 'DSGVO',
                'href' => '/administration/datenschutz',
                'permission' => PortalPermissions::VERWALTUNG_DATENSCHUTZ_VIEW,
                'active' => $activeKey === 'datenschutz',
            ],
            [
                'key' => 'audit',
                'label' => 'Audit-Log',
                'href' => '/administration/audit',
                'permission' => PortalPermissions::VERWALTUNG_AUDIT_VIEW,
                'active' => $activeKey === 'audit',
            ],
        ];

        return array_values(array_filter(
            $items,
            fn (array $item): bool => $this->canSee($item)
        ));
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function flatItems(string $activeKey = ''): array
    {
        return $this->items($activeKey);
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function quickLinks(): array
    {
        $links = [
            [
                'label' => 'Person anlegen',
                'href' => '/administration/personen/create',
                'permission' => PortalPermissions::VERWALTUNG_PERSONEN_CREATE,
                'description' => 'Neue Person mit optionalem Login-Konto erfassen.',
            ],
            [
                'label' => 'Gruppen verwalten',
                'href' => '/administration/gruppen',
                'permission' => IdentityPermissions::GRUPPEN_VIEW,
                'description' => 'Identity-Gruppen pro System anzeigen und pflegen.',
            ],
            [
                'label' => 'Permissions verwalten',
                'href' => '/administration/permissions',
                'permission' => IdentityPermissions::PERMISSIONS_VIEW,
                'description' => 'Feine technische Permissions anzeigen und bearbeiten.',
            ],
            [
                'label' => 'Systeme verwalten',
                'href' => '/administration/systeme',
                'permission' => IdentityPermissions::SYSTEME_VIEW,
                'description' => 'Identity-Systeme anzeigen und konfigurieren.',
            ],
            [
                'label' => 'Einladungen',
                'href' => '/administration/einladungen',
                'permission' => PortalPermissions::VERWALTUNG_EINLADUNGEN_VIEW,
                'description' => 'Account-Einladungen prüfen und widerrufen.',
            ],
            [
                'label' => 'Audit-Log',
                'href' => '/administration/audit',
                'permission' => PortalPermissions::VERWALTUNG_AUDIT_VIEW,
                'description' => 'Änderungen und sicherheitsrelevante Aktionen prüfen.',
            ],
        ];

        return array_values(array_filter(
            $links,
            fn (array $item): bool => $this->canSee($item)
        ));
    }

    /** @param array<string,mixed> $item */
    private function canSee(array $item): bool
    {
        if ($this->authorization === null) {
            return true;
        }

        $permissionKey = trim((string) ($item['permission'] ?? ''));
        if ($permissionKey === '') {
            return true;
        }

        return $this->authorization->can($permissionKey);
    }
}
