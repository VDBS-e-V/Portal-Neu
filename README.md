# Portal-Neu

Portal-Neu ist ein PHP-Portal mit getrenntem Personen-/Login-Modell, Verwaltungsbereich, Berechtigungsgruppen, PageGroups, Audit-Log, Einladungen, DSGVO-Anonymisierung und Account-Selbstverwaltung.

## Schnellstart

```bat
composer install
php bin/console db:hard-reset --force --seed --admin-password=secret
php bin/console routes
php -S localhost:8000 -t public
```

Danach im Browser öffnen:

```txt
http://localhost:8000
```

## Demo-Logins

```txt
admin@example.org             / secret
verwaltung.admin@example.org  / demo123
demo.user@example.org         / demo123
```

## Wichtige Bereiche

```txt
/verwaltung
/verwaltung/personen
/verwaltung/gruppen
/verwaltung/berechtigungen
/verwaltung/audit
/verwaltung/einladungen
/verwaltung/datenschutz

/konto
/konto/passwort
/konto/profil
/konto/kontakte
/konto/adressen
/konto/sicherheit

/passwort/vergessen
```

## Dokumentation

```txt
docs/INSTALLATION.md
docs/MINI_PROJEKTE.md
docs/ROUTEN.md
docs/DATENBANK.md
docs/BERECHTIGUNGEN.md
docs/SICHERHEIT.md
docs/TESTS.md
docs/TROUBLESHOOTING.md
docs/DEPLOYMENT_CHECKLIST.md
docs/CHANGELOG_PORTAL_AUSBAU.md
```

## Architektur-Kern

```txt
Person = Stammdaten / Mensch / Kontakt
Login  = optionaler Account-Zugang zu einer Person
```

Eine Person kann ohne Login existieren. Ein Login kann später per Einladung aktiviert werden.

## Berechtigungsmodell

```txt
Area.PageGroup
```

Beispiele:

```txt
verwaltung.personen
verwaltung.gruppen
verwaltung.berechtigungen
verwaltung.einladungen
verwaltung.datenschutz
verwaltung.audit
```

Initiale Admin-Gruppe:

```txt
verwaltung.administrator
```

## QA

```bat
php tools/qa/run_all.php
```
