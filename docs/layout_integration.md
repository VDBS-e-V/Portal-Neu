# Layout-Integration

## Ziel

Menüeinträge mit `pt_menu_items.page_group_id` werden nur angezeigt, wenn der aktuelle Login Zugriff auf die verbundene PageGroup hat.

## Service

```php
use App\Navigation\AuthorizedMainMenu;
```

## Beispiel im Controller oder Page-Composer

```php
$mainMenuItems = $container
    ->get(AuthorizedMainMenu::class)
    ->portalMain();
```

## Beispiel im Layout

```php
<?php
$mainMenuItems = $mainMenuItems ?? [];
require __DIR__ . '/../partials/main_menu_authorized.php';
?>
```

## Verwaltung-Menü

```php
$verwaltungMenuItems = $container
    ->get(AuthorizedMainMenu::class)
    ->verwaltungMain();
```

## Bestehende VerwaltungNavigation

Mini-Projekt 10 ersetzt `src/Navigation/VerwaltungNavigation.php` aus Mini-Projekt 9.

Neu:
- Navigation filtert Einträge nach PageGroup-Zugriff.
- Konstruktor erlaubt weiterhin `null`, damit der alte Einsatz nicht sofort bricht.
- Der Service sollte aber mit `AuthorizationService` registriert werden.

## Menü-Seeds

Nach Migration:

```bat
php bin/console migrate
```

Danach Seed-Datei ausführen oder in deinen Seed-Prozess aufnehmen:

```txt
database/seeds/seed_verwaltung_menu.sql
```

Wenn dein Console-Seeder nur `seed_initial_data.sql` ausführt, kopiere den Inhalt von `seed_verwaltung_menu.sql` ans Ende von `seed_initial_data.sql`.
