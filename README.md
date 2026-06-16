# VDBS Portal

Minimaler PHP-Skeleton für den Portal-Einstieg.

## Setup

1. `composer install`
2. `.env.example` nach `.env` kopieren und die Werte bei Bedarf anpassen
3. Den Webserver auf `public/` als Docroot zeigen lassen, zum Beispiel über XAMPP / Apache

## Datenbank

### Migrationen ausführen

```bat
php bin/console migrate
```

### Seeds ausführen

```bat
php bin/console seed --admin-password=DEIN_PASSWORT
```

### Datenbank vollständig neu aufbauen

```bat
php bin/console db:hard-reset --force
php bin/console migrate
php bin/console seed --admin-password=DEIN_PASSWORT
```

Alternativ, wenn der lokale Befehl `db:hard-reset --seed` die Seeds vollständig ausführt:

```bat
php bin/console db:hard-reset --force --seed --admin-password=DEIN_PASSWORT
```

## Hinweis zu XAMPP / `pdo_firebird`

Wenn beim Ausführen von `php bin/console ...` folgende Warnung erscheint:

```txt
PHP Warning: PHP Startup: Unable to load dynamic library 'pdo_firebird'
```

ist das unabhängig von den Portal-Migrationen. Die PHP-Konfiguration versucht die Firebird-PDO-Erweiterung zu laden, obwohl die DLL nicht vorhanden ist. Für MySQL/MariaDB kann die Warnung ignoriert oder in der verwendeten `php.ini` durch Auskommentieren der Zeile behoben werden:

```ini
;extension=pdo_firebird
```

## DB-Fix für Seed-Fehler `Unknown column 'is_default'`

Der Fehler

```txt
Failed to seed seed_initial_data.sql: SQLSTATE[42S22]: Column not found: 1054 Unknown column 'is_default' in 'field list'
```

entsteht, wenn `database/seeds/seed_initial_data.sql` in `pt_menus.is_default` schreibt, die lokal angewendete Migration `0017_create_pt_menus.sql` diese Spalte aber nicht enthält.

### Erwartete Dateien für den Fix

Diese Dateien müssen zusammenpassen und gemeinsam ersetzt werden:

```txt
database/migrations/0017_create_pt_menus.sql
database/seeds/seed_initial_data.sql
```

Danach die DB neu aufbauen:

```bat
php bin/console db:hard-reset --force

php bin/console seed --admin-password=DEIN_PASSWORT
```

## Neues Personenmodell

Die aktuellen Seeds und Migrations sind für das neue Personenmodell gedacht. Dabei ist eine Person der fachliche Stammdatensatz, während ein Login optional ist.

Personen und Login-Konten sind getrennt:

- Eine Person kann ohne Login existieren.
- Eine Person kann optional genau ein Login-Konto haben.
- Eine Login-E-Mail ist nur erforderlich, wenn ein Login-Konto existiert.
- Gruppen und Berechtigungen werden fachlich an Personen vergeben, nicht direkt an Login-Konten.
- Der Login dient nur als Zugangskanal zur Person.

Erwartete Tabellen und Spalten:

```txt
ids_persons
ids_person_names
ids_person_contact_details
ids_person_addresses
ids_users.person_id
ids_person_permission_groups
pt_page_groups
pt_permission_group_page_group_access
pt_audit_log
```

Wenn lokal noch alte Tabellen wie diese erzeugt werden, sind noch alte Migrationsdateien aktiv:

```txt
ids_user_permission_groups
ids_user_namesphp bin/console migrate
ids_user_contact_details
ids_user_addresses
```

In diesem Fall müssen die alten Migrationsdateien durch die angepassten Dateien ersetzt werden. Besonders wichtig sind:

```txt
0001_create_ids_users.sql
0003_create_ids_person_permission_groups.sql
0005_create_pt_permission_group_area_access.sql
0024_create_person_profile_tables.sql
0025_create_pt_audit_log.sql
```

Die alten Varianten sollten danach nicht zusätzlich im Ordner `database/migrations/` liegen:

```txt
0003_create_ids_user_permission_groups.sql
0024_create_user_profile_tables.sql
```

## Beispielnutzer aus den Seeds

Die Seed-Daten legen mehrere Beispielpersonen und optionale Login-Konten an. Diese dienen nur der lokalen Entwicklung und dürfen nicht produktiv verwendet werden.

| Person | Login-E-Mail | Passwort | Status | Zweck |
|---|---|---:|---|---|
| System Administrator | `admin@example.org` | wird über `--admin-password` gesetzt | `active` | Haupt-Admin für lokale Entwicklung |
| Verwaltung Admin | `verwaltung.admin@example.org` | `demo123` | `active` | Beispielnutzer für die Verwaltungsbereiche |
| Demo Nutzer | `demo.user@example.org` | `demo123` | `active` | Normaler Beispielnutzer ohne Verwaltungsrechte |
| Eingeladene Person | `invited@example.org` | kein Passwort | `invited` | Beispiel für Einladung/Selbstregistrierung |
| Person ohne Login | kein Login | kein Passwort | `active` | Beispiel für reine Personen-/Kontaktdaten ohne Login-Zugang |

### Admin-Passwort setzen

Das Passwort für `admin@example.org` wird beim Seeding über den Parameter `--admin-password` gesetzt:

```bat
php bin/console db:hard-reset --force --seed --admin-password=DEIN_PASSWORT
```

Oder bei getrenntem Ablauf:

```bat
php bin/console db:hard-reset --force
php bin/console migrate
php bin/console seed --admin-password=DEIN_PASSWORT
```

### Beispielrechte

Die Seeds vergeben initial Zugriff über die Gruppe:

```txt
verwaltung.administrator
```

Diese Gruppe erhält Zugriff auf die PageGroups:

```txt
verwaltung.personen
verwaltung.gruppen
verwaltung.berechtigungen
```

Damit kann der Beispielnutzer `verwaltung.admin@example.org` die Verwaltungsbereiche verwenden:

```txt
/verwaltung/personen
/verwaltung/gruppen
/verwaltung/berechtigungen
```

### Sicherheitshinweis

Die Demo-Zugänge sind ausschließlich für lokale Entwicklung gedacht. Vor produktiver Nutzung müssen Demo-Nutzer entfernt oder deaktiviert und alle Passwörter geändert werden.

## Prüfung

1. `GET /health` liefert `ok`
2. `GET /api/health` liefert JSON mit `status: ok`
3. `php bin/console routes` zeigt die registrierten Routen
4. `php bin/console migrate` läuft ohne Fehler durch
5. `php bin/console seed --admin-password=DEIN_PASSWORT` läuft ohne Fehler durch

## Routen

- `/`
- `/health`
- `/api/health`
