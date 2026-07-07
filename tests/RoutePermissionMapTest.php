<?php

declare(strict_types=1);

use App\Security\RoutePermissionMap;
use PHPUnit\Framework\TestCase;

final class RoutePermissionMapTest extends TestCase
{
    public function testAdministrationDashboardRequiresPortalDashboardPermission(): void
    {
        $map = new RoutePermissionMap();

        self::assertSame(
            'portal.verwaltung.dashboard.view',
            $map->permissionFor('GET', '/administration')
        );
    }

    public function testAdministrationGroupPermissionAssignmentRequiresManagePermission(): void
    {
        $map = new RoutePermissionMap();

        self::assertSame(
            'identity.gruppen.permissions.manage',
            $map->permissionFor('POST', '/administration/gruppen/5/permissions')
        );
    }

    public function testExistingVerwaltungPersonEditRouteIsMapped(): void
    {
        $map = new RoutePermissionMap();

        self::assertSame(
            'portal.verwaltung.personen.edit',
            $map->permissionFor('POST', '/verwaltung/personen/123')
        );
    }

    public function testUnknownRouteHasNoPermission(): void
    {
        $map = new RoutePermissionMap();

        self::assertNull($map->permissionFor('GET', '/konto/profil'));
    }
}
