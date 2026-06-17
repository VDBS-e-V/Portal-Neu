# Manueller Testplan

## 1. DB Reset

```bat
php bin/console db:hard-reset --force --seed --admin-password=secret
```

## 2. Login

```txt
E-Mail: admin@example.org
Passwort: secret
```

## 3. Verwaltung

```txt
/verwaltung
/verwaltung/personen
/verwaltung/gruppen
/verwaltung/berechtigungen
/verwaltung/audit
```

Erwartung:

```txt
Alle Seiten laden ohne 500.
Navigation zeigt nur berechtigte Bereiche.
```

## 4. Personenverwaltung

```txt
Person anlegen
Person bearbeiten
Status deaktivieren
Kontakt hinzufügen
Adresse hinzufügen
Gruppe zuweisen
```

Erwartung:

```txt
Änderungen werden gespeichert.
Audit-Log erhält Einträge.
CSRF-Token ist in jedem POST-Formular vorhanden.
```

## 5. Gruppenverwaltung

```txt
Gruppe anlegen
Gruppe bearbeiten
Mitglieder anzeigen
Systemgruppe löschen testen
```

Erwartung:

```txt
Systemgruppe ist nicht löschbar.
Selbst-Lockout wird verhindert.
```

## 6. Berechtigungen

```txt
PageGroup-Zugriff einer Gruppe ändern
```

Erwartung:

```txt
Speicherung funktioniert.
Admin verliert nicht versehentlich eigenen Zugriff.
```

## 7. Einladungen

```txt
Person mit Login anlegen
Einladung erzeugen
Einladungslink öffnen
Passwort setzen
Login testen
```

Erwartung:

```txt
Login wird active.
Token kann nicht erneut genutzt werden.
```

## 8. DSGVO

```txt
DSGVO-Löschung beantragen
freigeben
abschließen
```

Erwartung:

```txt
Person wird anonymisiert.
Kontakte/Adressen/Gruppen werden entfernt.
Login wird disabled.
Audit-Einträge vorhanden.
```

## 9. Konto

```txt
/konto
/konto/passwort
/konto/profil
/konto/kontakte
/konto/adressen
/konto/sicherheit
```

Erwartung:

```txt
Eigene Daten sind sichtbar.
Eigene Kontakte/Adressen können gepflegt werden.
Sicherheitsereignisse werden angezeigt, sofern LoginEventRepository eingebunden ist.
```

## 10. Audit-Integration

```txt
/verwaltung/personen/1/audit
/verwaltung/gruppen/1/audit
/verwaltung/datenschutz/1/audit
```

Erwartung:

```txt
Timeline zeigt passende Audit-Einträge.
```
