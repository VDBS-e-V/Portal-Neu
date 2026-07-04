# QA-Checkliste

## Basis

```bat
php -l config\routes.php
php -l config\services.php
php -l src\Security\AuthorizationService.php
php -l src\Security\RoutePermissionMap.php
```

## Zentrale Suites

```bat
php tools\qa\run_identity_runtime_checks.php
php tools\qa\run_identity_cleanup_checks.php
php tools\qa\run_identity_db_legacy_checks.php
php tools\qa\run_identity_service_cleanup_checks.php
php tools\qa\run_identity_integrity_checks.php
php tools\qa\run_identity_permission_group_cleanup_checks.php
php tools\qa\run_personen_identity_checks.php
php tools\qa\run_identity_admin_safety_checks.php
php tools\qa\run_identity_navigation_permission_checks.php
php tools\qa\run_identity_smoke_suite.php
```

## Erwartete Kernsignale

```text
OK: Keine aktiven PageGroup-Runtime-Aufrufe oder alten PageGroup-Routen gefunden.
OK: Keine produktiven Legacy-PageGroup-DB-Verweise gefunden.
OK: Keine produktiven Legacy-Identity-PermissionGroup-Codeverweise gefunden.
OK: Personen, User, Subjects und Gruppen-Zuordnungen sind im neuen Identity-Modell konsistent.
OK: Admin-Safety-Service blockiert Self-Lockout und systemfremde Permission-Zuweisungen.
OK: Navigation nutzt pt_menu_items.permission_key ohne Legacy-Spalten.
OK: Mini-Projekt-19-Smoke-Test-Suite bestanden.
```

## Browser-Check

Als Admin anmelden und zentrale Seiten öffnen:

```text
/konto
/konto/einstellungen
/identity/me
/administration
/administration/gruppen
/administration/permissions
/administration/personen
/verwaltung
/verwaltung/personen
/verwaltung/audit
/verwaltung/einladungen
/verwaltung/datenschutz
```

Keine dieser Seiten darf mit 500 abbrechen.
