# Verwaltung: mehrstufige Header-Aufklappmenüs

## Ziel

Die Verwaltungsnavigation wird direkt in die bereits vorhandenen Header-Aufklappmenüs eingebaut.

Genutzt werden die vorhandenen Header-Klassen:

```txt
header-bottom-nav-list-item
has-submenu
header-bottom-submenu
header-bottom-submenu-item
header-bottom-submenu-link
header-bottom-submenu--nested
```

## Menüstruktur

```txt
Übersicht
  Dashboard

Stammdaten
  Personen
    Personenübersicht
    Person anlegen
    Gruppenzuweisungen
    Kontakte & Adressen
  Gruppen
    Gruppenübersicht
    Gruppe anlegen

Zugang
  Einladungen
    Alle Einladungen
  Berechtigungen
    Berechtigungsmatrix
    PageGroups

Datenschutz & Sicherheit
  DSGVO-Löschung
    Offene Vorgänge
    Freigegebene Vorgänge
  Audit-Log
    Audit-Suche
```

## Dateien

```txt
src/Presentation/Navigation/HeaderDataProvider.php
src/Navigation/VerwaltungNavigation.php
database/seeds/seed_verwaltung_menu.sql
public/assets/css/parts/header.css
```

## Warum HeaderDataProvider ersetzt wird

Die bestehende Header-View kann bereits aus `parent_id` einen Menübaum bauen.

Dafür muss der Provider aber alle Menüeinträge laden:

```php
$this->menuItems->findByMenuId((int) $areaMenu['id'], true);
```

Nicht nur:

```php
$this->menuItems->findByMenuIdAndParent((int) $areaMenu['id'], null);
```

## Warum header.css ersetzt wird

Level 1 -> Level 2 war bereits vorhanden.

Für Level 2 -> Level 3 werden ergänzende Regeln gebraucht:

```txt
.header-bottom-submenu--nested
```

## Einbau

```bat
php bin/console seed --admin-password=secret
```

oder sauber:

```bat
php bin/console db:hard-reset --force --seed --admin-password=secret
```

## Entfernen alter falscher Subnav

Falls vorher eingebaut:

```txt
public/assets/css/verwaltung-subnav.css
```

und in `public/assets/css/app.css`:

```css
@import url("./verwaltung-subnav.css");
```

wieder entfernen.
