# Deployment-Checkliste

## Vor Deployment

```txt
[ ] composer install --no-dev --optimize-autoloader
[ ] APP_ENV=prod
[ ] APP_DEBUG=0
[ ] Datenbank-Backup erstellt
[ ] Migrationen getestet
[ ] Seeds geprüft
[ ] QA-Checks erfolgreich
[ ] HTTPS aktiv
[ ] Mailer konfiguriert
[ ] Reset-Links werden nicht mehr im Browser angezeigt
[ ] Dateirechte geprüft
[ ] Logs beschreibbar
```

## DB

```bat
php bin/console migrate
```

## QA

```bat
php tools/qa/run_all.php
```

## Smoke-Test

```txt
[ ] Login funktioniert
[ ] /verwaltung lädt
[ ] Personenliste lädt
[ ] Berechtigungen laden
[ ] Audit-Log lädt
[ ] Konto-Seite lädt
[ ] Passwortänderung funktioniert
[ ] Logout/Login funktioniert
```

## Nach Deployment

```txt
[ ] Error-Logs prüfen
[ ] Audit-Log prüfen
[ ] Admin-Login testen
[ ] Nicht-Admin-Zugriff testen
[ ] Backup-Zeitplan prüfen
```
