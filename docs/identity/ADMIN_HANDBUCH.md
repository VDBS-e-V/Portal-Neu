# Admin-Handbuch

## Anmeldung

Der lokale Initial-Admin kommt aus `.env`:

```env
INITIAL_ADMIN_NAME=admin
INITIAL_ADMIN_EMAIL=local@admin.com
INITIAL_ADMIN_PASSWORD=password
```

Für produktive Systeme muss dieses Passwort direkt geändert werden.

## Wichtige Admin-URLs

```text
/administration
/administration/gruppen
/administration/permissions
/administration/systeme
/administration/personen
/identity/me
/konto
/konto/einstellungen
```

## Gruppen verwalten

Gruppen werden systembezogen gepflegt. Beim Zuweisen von Permissions muss die Systemgrenze eingehalten werden: Eine Portalgruppe darf nur Portalpermissions erhalten.

## Personen und Gruppen

Personen sind über `ids_persons.subject_id` mit einem Subject verbunden. Gruppenmitgliedschaften hängen am Subject, nicht direkt an der Person oder am User.

## Self-Lockout-Schutz

Der Admin-Safety-Service verhindert kritische Aktionen, zum Beispiel:

- den letzten aktiven `identity.administrator` entfernen,
- den letzten aktiven Identity-Admin deaktivieren,
- die `identity.administrator`-Gruppe löschen,
- systemfremde Permission-Zuweisungen,
- kritische Identity-Admin-Permissions deaktivieren.

## Konto

Normale Konto-/Profileinstellungen laufen über:

```text
/konto
/konto/einstellungen
```

Profilfelder liegen an `ids_persons` und den Person-Profil-Tabellen. Account-Einstellungen bleiben userbezogen.
