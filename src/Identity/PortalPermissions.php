<?php

declare(strict_types=1);

namespace App\Identity;

final class PortalPermissions
{
    public const DASHBOARD_VIEW = 'portal.dashboard.view';

    public const VERWALTUNG_DASHBOARD_VIEW = 'portal.verwaltung.dashboard.view';

    public const VERWALTUNG_PERSONEN_VIEW = 'portal.verwaltung.personen.view';
    public const VERWALTUNG_PERSONEN_CREATE = 'portal.verwaltung.personen.create';
    public const VERWALTUNG_PERSONEN_EDIT = 'portal.verwaltung.personen.edit';
    public const VERWALTUNG_PERSONEN_DELETE = 'portal.verwaltung.personen.delete';
    public const VERWALTUNG_PERSONEN_EXPORT = 'portal.verwaltung.personen.export';

    public const VERWALTUNG_EINLADUNGEN_VIEW = 'portal.verwaltung.einladungen.view';
    public const VERWALTUNG_EINLADUNGEN_CREATE = 'portal.verwaltung.einladungen.create';
    public const VERWALTUNG_EINLADUNGEN_REVOKE = 'portal.verwaltung.einladungen.revoke';

    public const VERWALTUNG_DATENSCHUTZ_VIEW = 'portal.verwaltung.datenschutz.view';
    public const VERWALTUNG_DATENSCHUTZ_EDIT = 'portal.verwaltung.datenschutz.edit';
    public const VERWALTUNG_DATENSCHUTZ_APPROVE = 'portal.verwaltung.datenschutz.approve';

    public const VERWALTUNG_AUDIT_VIEW = 'portal.verwaltung.audit.view';

    public const VERWALTUNG_SCHULVERZEICHNIS_VIEW = 'portal.verwaltung.schulverzeichnis.view';
    public const VERWALTUNG_SCHULVERZEICHNIS_EDIT = 'portal.verwaltung.schulverzeichnis.edit';
}
