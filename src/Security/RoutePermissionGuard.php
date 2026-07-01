<?php

declare(strict_types=1);

namespace App\Security;

use App\Http\Request\Request;

/**
 * Erzwingt Permission-Prüfungen auf Routenebene.
 *
 * Dadurch müssen alte PageGroup-Prüfungen nicht mehr in jedem Controller
 * ersetzt werden, bevor die Route abgesichert ist.
 */
final class RoutePermissionGuard
{
    public function __construct(
        private readonly AuthorizationService $authorization,
        private readonly RoutePermissionMap $permissionMap
    ) {
    }

    public function guard(Request $request): void
    {
        $permission = $this->permissionMap->permissionFor($request->method, $request->path);
        if ($permission === null) {
            return;
        }

        $this->authorization->requirePermission($permission);
    }
}
