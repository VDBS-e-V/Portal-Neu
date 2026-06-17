# Sicherheit

## Authorization

Alle Verwaltungscontroller prüfen PageGroup-Zugriff.

Beispiel:

```php
$this->authorization->requirePageGroupAccess(
    VerwaltungAccess::AREA,
    VerwaltungAccess::PERSONEN
);
```

## CSRF

Alle POST-Formulare sollen enthalten:

```php
<input type="hidden" name="_csrf_token" value="<?= $e($csrfToken ?? '') ?>">
```

POST-Controller prüfen:

```php
$this->csrf->requireValid($request, 'form.name');
```

## Passwortregeln

```txt
mindestens 8 Zeichen
mindestens ein Großbuchstabe
mindestens ein Kleinbuchstabe
mindestens eine Zahl
```

## Token

Einladungen und Passwort-Resets speichern nur Hashes:

```txt
sha256(token)
```

Klartext-Token werden nur einmal im Link genutzt.

## Audit

Wichtige Aktionen schreiben Audit-Einträge:

```txt
person.created
person.updated
person.status_changed
group.created
group.updated
permissions.updated
invitation.created
invitation.accepted
person.erasure_requested
person.erasure_approved
person.erasure_completed
account.password_changed
password_reset.requested
password_reset.completed
```

## DSGVO

Standard-Löschung ist Deaktivierung. Echte Datenentfernung erfolgt nur über den DSGVO-Prozess:

```txt
requested -> approved -> completed
```

## Produktion

Vor Produktivbetrieb prüfen:

```txt
APP_DEBUG=0
starke DB-Passwörter
HTTPS
Secure/HttpOnly/SameSite Cookies
kein Anzeigen von Reset-Links im Browser
Mailer aktiv
QA-Checks erfolgreich
```
