# Routen

## Verwaltung

```txt
GET  /verwaltung
```

## Personen

```txt
GET  /verwaltung/personen
GET  /verwaltung/personen/create
POST /verwaltung/personen/create
GET  /verwaltung/personen/{id}
GET  /verwaltung/personen/{id}/edit
POST /verwaltung/personen/{id}/edit
POST /verwaltung/personen/{id}/status
GET  /verwaltung/personen/{id}/gruppen
POST /verwaltung/personen/{id}/gruppen
GET  /verwaltung/personen/{id}/kontakte
POST /verwaltung/personen/{id}/kontakte
POST /verwaltung/personen/{id}/kontakte/{contactId}/delete
GET  /verwaltung/personen/{id}/adressen
POST /verwaltung/personen/{id}/adressen
POST /verwaltung/personen/{id}/adressen/{addressId}/delete
GET  /verwaltung/personen/{id}/audit
```

## Gruppen

```txt
GET  /verwaltung/gruppen
GET  /verwaltung/gruppen/create
POST /verwaltung/gruppen/create
GET  /verwaltung/gruppen/{id}
GET  /verwaltung/gruppen/{id}/edit
POST /verwaltung/gruppen/{id}/edit
GET  /verwaltung/gruppen/{id}/mitglieder
POST /verwaltung/gruppen/{id}/delete
GET  /verwaltung/gruppen/{id}/audit
```

## Berechtigungen

```txt
GET  /verwaltung/berechtigungen
GET  /verwaltung/berechtigungen/page-groups
GET  /verwaltung/berechtigungen/gruppen/{id}
POST /verwaltung/berechtigungen/gruppen/{id}
```

## Audit

```txt
GET  /verwaltung/audit
GET  /verwaltung/audit/{id}
GET  /verwaltung/personen/{id}/audit
GET  /verwaltung/gruppen/{id}/audit
GET  /verwaltung/datenschutz/{id}/audit
```

## Einladungen

```txt
GET  /einladung/{token}
POST /einladung/{token}
GET  /verwaltung/einladungen
POST /verwaltung/personen/{id}/einladung
POST /verwaltung/einladungen/{id}/revoke
```

## DSGVO

```txt
GET  /verwaltung/datenschutz
GET  /verwaltung/personen/{id}/datenschutz/loeschung
POST /verwaltung/personen/{id}/datenschutz/loeschung
GET  /verwaltung/datenschutz/{id}
POST /verwaltung/datenschutz/{id}/approve
POST /verwaltung/datenschutz/{id}/reject
POST /verwaltung/datenschutz/{id}/cancel
POST /verwaltung/datenschutz/{id}/complete
GET  /verwaltung/datenschutz/{id}/audit
```

## Konto

```txt
GET  /konto
GET  /konto/passwort
POST /konto/passwort
GET  /konto/profil
GET  /konto/profil/bearbeiten
POST /konto/profil/bearbeiten
GET  /konto/kontakte
POST /konto/kontakte
POST /konto/kontakte/{contactId}/delete
GET  /konto/adressen
POST /konto/adressen
POST /konto/adressen/{addressId}/delete
GET  /konto/sicherheit
```

## Passwort-Reset

```txt
GET  /passwort/vergessen
POST /passwort/vergessen
GET  /passwort/zuruecksetzen/{token}
POST /passwort/zuruecksetzen/{token}
```
