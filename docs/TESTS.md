# Tests und QA

## Alle Checks

```bat
php tools/qa/run_all.php
```

## Einzelchecks

```bat
php tools/qa/check_mini_project_files.php
php tools/qa/check_database_schema.php
php tools/qa/check_seed_logins.php
php tools/qa/check_permissions.php
php tools/qa/check_routes_config.php
php tools/qa/check_csrf_in_forms.php
```

## Vorbereitender Reset

```bat
php bin/console db:hard-reset --force --seed --admin-password=secret
```

## Manuelle Kernprüfung

```txt
1. Login mit admin@example.org / secret
2. /verwaltung öffnen
3. Person anlegen
4. Gruppe anlegen
5. Berechtigung setzen
6. Einladung erzeugen
7. Einladung annehmen
8. DSGVO-Vorgang beantragen/freigeben/abschließen
9. /konto/profil öffnen
10. /verwaltung/audit prüfen
```

## SQL Checks

```txt
docs/sql_checks.sql
```
