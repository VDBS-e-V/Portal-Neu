# Berechtigungen

## Modell

```txt
Area.PageGroup
```

Beispiel:

```txt
verwaltung.personen
verwaltung.gruppen
verwaltung.berechtigungen
verwaltung.einladungen
verwaltung.datenschutz
verwaltung.audit
```

## Tabellen

```txt
pt_areas
pt_page_groups
ids_permission_groups
ids_person_permission_groups
pt_permission_group_page_group_access
```

## Initiale Gruppe

```txt
verwaltung.administrator
```

## Zugriff prüfen

```php
$user = $this->authorization->requirePageGroupAccess(
    VerwaltungAccess::AREA,
    VerwaltungAccess::PERSONEN
);
```

## Konstanten

```php
final class VerwaltungAccess
{
    public const AREA = 'verwaltung';
    public const PERSONEN = 'personen';
    public const GRUPPEN = 'gruppen';
    public const BERECHTIGUNGEN = 'berechtigungen';
    public const ADMIN_GROUP = 'verwaltung.administrator';
}
```

## Self-Lockout-Schutz

AdminSafetyService verhindert:

```txt
eigene Deaktivierung
Entzug der letzten eigenen Admin-Berechtigung
Löschung von Systemgruppen
```

## Menü-Sichtbarkeit

Seit Mini-Projekt 10:

```txt
pt_menu_items.page_group_id
```

Wenn ein Menüeintrag eine PageGroup hat, wird er nur angezeigt, wenn der aktuelle Login Zugriff auf diese PageGroup besitzt.
