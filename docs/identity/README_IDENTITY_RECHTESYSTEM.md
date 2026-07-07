# Identity-Rechtesystem

## Zweck

Das Portal nutzt ein zentrales Identity-/Permission-System. Es ersetzt alte PageGroup- und Legacy-PermissionGroup-Modelle durch ein stabiles Subject-basiertes Modell.

## Kernmodell

| Tabelle | Bedeutung |
|---|---|
| `ids_subjects` | stabile digitale Identität |
| `ids_persons` | reale Person/Stammdaten; verweist auf `subject_id` |
| `ids_users` | Login/Zugang; verweist auf `person_id` |
| `ids_systems` | registrierte Systeme wie `identity`, `portal`, `bibliocollect`, `methodenmatrix` |
| `ids_groups` | Gruppen pro System |
| `ids_permissions` | feingranulare Rechte pro System |
| `ids_group_permissions` | Gruppen bekommen Permissions |
| `ids_subject_groups` | Subjects bekommen Gruppen |

## Grundregeln

Code prüft Permissions, nicht Gruppen. Gruppen sind nur Bündel von Permissions. Jede Gruppe gehört genau einem System. Eine Gruppe darf nur Permissions desselben Systems enthalten. Es gibt keine globalen Gruppen und keine Gruppenhierarchie.

## Aktuelle kanonische Konto-URLs

```text
GET  /konto
POST /konto
GET  /konto/einstellungen
POST /konto/einstellungen
POST /konto/passwort
```

Alte Web-URLs wie `/user` und `/user/settings` sind entfernt. `/api/user/me` kann unabhängig davon weiter existieren.

## Zentrale Identity-API

```text
GET /identity/me
GET /identity/me?system=portal
```

Die Antwort enthält Subject, Person, Login, Systeme, Gruppen, Permissions und eine Cache-TTL.

## Navigation

Menüeinträge werden über `pt_menu_items.permission_key` gesteuert. Die alte Spalte `page_group_id` existiert nicht mehr.

## Entfernte Legacy-Modelle

Aus produktiver Laufzeit entfernt wurden:

```text
pt_page_groups
pt_permission_group_page_group_access
pt_menu_items.page_group_id
ids_permission_groups
ids_user_permission_groups
ids_person_permission_groups
```

Historische Migrationen bleiben erhalten, damit die Projektgeschichte nachvollziehbar bleibt.
