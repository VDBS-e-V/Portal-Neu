# Betrieb, Backup und Restore

## Backup vor kritischen Migrationen

Vor Drop- oder Cleanup-Schritten immer ein vollständiges DB-Backup erstellen:

```bat
mysqldump -u root vdbs_sys > backup_vdbs_sys_YYYYMMDD.sql
```

Falls ein Passwort gesetzt ist:

```bat
mysqldump -u root -p vdbs_sys > backup_vdbs_sys_YYYYMMDD.sql
```

## Restore

```bat
mysql -u root vdbs_sys < backup_vdbs_sys_YYYYMMDD.sql
```

Mit Passwort:

```bat
mysql -u root -p vdbs_sys < backup_vdbs_sys_YYYYMMDD.sql
```

## Standardprüfung nach Deployment

```bat
php bin\console migrate
php bin\console seed
php tools\qa\run_identity_smoke_suite.php
```

Optional inklusive Seed im Smoke-Test:

```bat
php tools\qa\run_identity_smoke_suite.php --with-seed
```

## Browser-Smoke-Test

```text
/login
/konto
/konto/einstellungen
/identity/me
/identity/me?system=portal
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

## HTTP-Smoke-Test

```bat
set SMOKE_BASE_URL=http://localhost/vdbs_portal/public
php tools\qa\check_identity_http_smoke.php
```

Der HTTP-Smoke-Test ist optional und hängt von lokaler XAMPP-/Apache-Konfiguration ab.
