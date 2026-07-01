<?php

declare(strict_types=1);

namespace App\Identity;

final class PortalPermissions
{
    public const DASHBOARD_VIEW = 'portal.dashboard.view';

    public const VERWALTUNG_PERSONEN_VIEW = 'portal.verwaltung.personen.view';
    public const VERWALTUNG_PERSONEN_CREATE = 'portal.verwaltung.personen.create';
    public const VERWALTUNG_PERSONEN_EDIT = 'portal.verwaltung.personen.edit';
    public const VERWALTUNG_PERSONEN_DELETE = 'portal.verwaltung.personen.delete';
    public const VERWALTUNG_PERSONEN_EXPORT = 'portal.verwaltung.personen.export';

    public const VERWALTUNG_AUDIT_VIEW = 'portal.verwaltung.audit.view';
}
