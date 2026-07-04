# Berechtigungskonzept

## Permission-Namensschema

Permissions folgen dem Muster:

```text
<system>.<bereich>.<ressource>.<aktion>
```

Beispiele:

```text
portal.verwaltung.dashboard.view
portal.verwaltung.personen.view
portal.verwaltung.audit.view
identity.administration.groups.manage
identity.administration.permissions.manage
```

## Standardaktionen

| Aktion | Zweck |
|---|---|
| `view` | ansehen |
| `create` | erstellen |
| `edit` | bearbeiten |
| `delete` | löschen/deaktivieren |
| `manage` | verwalten |
| `assign` | zuweisen |
| `remove` | entfernen |
| `export` | exportieren |
| `import` | importieren |
| `approve` | freigeben |
| `archive` | archivieren |

## Systemgrenzen

Eine Gruppe gehört zu genau einem System in `ids_systems`. Eine Permission gehört ebenfalls zu genau einem System. `ids_group_permissions` darf nur Zuordnungen innerhalb desselben Systems enthalten.

## Administratorgruppen

Für jedes aktive System kann eine Administratorgruppe existieren, zum Beispiel:

```text
identity.administrator
portal.administrator
bibliocollect.administrator
methodenmatrix.administrator
```

Der Initial-Admin soll Mitglied der Administratorgruppen aller aktiven Systeme sein. Administratorgruppen sollen alle aktiven Permissions ihres Systems abdecken.

## Default-Gruppen

`ids_groups.is_default` markiert Gruppen, die automatisch oder als Basismitgliedschaft vergeben werden können. Default-Gruppen geben keine Sonderstellung außerhalb ihres Systems.

## Prüfung im Code

Controller, Guards und Services prüfen Permissions. Gruppen werden nicht direkt als Autorisierungsentscheidung verwendet.

Richtig:

```text
Prüfe: portal.verwaltung.personen.view
```

Falsch:

```text
Prüfe: ist Mitglied von alter PageGroup oder Legacy-PermissionGroup
```
## Subject-Gruppen-Zuordnung

Die Tabelle `ids_subject_groups` ist die zentrale Zuordnung zwischen digitalen Identitäten und Gruppen.
Sie verbindet `ids_subjects` mit `ids_groups` und ersetzt die alten benutzer- oder personenbezogenen Legacy-Gruppentabellen.

Regeln:

- Berechtigungen werden nicht direkt an Personen oder User vergeben, sondern über Gruppen.
- Eine Person besitzt über `ids_persons.subject_id` genau die digitale Identität, deren Gruppenmitgliedschaften gelten.
- Logins in `ids_users` verweisen auf Personen; die effektiven Rechte ergeben sich daraus über `ids_subjects` und `ids_subject_groups`.
- Gruppenmitgliedschaften können über `ids_subject_groups.expires_at` zeitlich begrenzt werden.
- Der produktive Berechtigungspfad lautet: `ids_subjects` → `ids_subject_groups` → `ids_groups` → `ids_group_permissions` → `ids_permissions`.
- Alte Tabellen wie `ids_permission_groups`, `ids_user_permission_groups` und `ids_person_permission_groups` werden nicht mehr produktiv verwendet.
