# Login-Event-Integration

Mini-Projekt 13 liefert:

```txt
database/migrations/0030_create_ids_user_login_events.sql
src/Repository/LoginEventRepository.php
```

Damit die Sicherheitsseite Daten zeigt, sollten Login/Logout/Passwort-Aktionen Ereignisse schreiben.

## Erfolgreicher Login

```php
$loginEvents->record(
    (int) $user['id'],
    (string) $user['email'],
    'login_success',
    $_SERVER['REMOTE_ADDR'] ?? null,
    $_SERVER['HTTP_USER_AGENT'] ?? null,
    $_SERVER['REQUEST_URI'] ?? null
);
```

## Fehlgeschlagener Login

```php
$loginEvents->record(
    null,
    $email,
    'login_failed',
    $_SERVER['REMOTE_ADDR'] ?? null,
    $_SERVER['HTTP_USER_AGENT'] ?? null,
    $_SERVER['REQUEST_URI'] ?? null
);
```

## Logout

```php
$loginEvents->record(
    $userId,
    $email,
    'logout',
    $_SERVER['REMOTE_ADDR'] ?? null,
    $_SERVER['HTTP_USER_AGENT'] ?? null,
    $_SERVER['REQUEST_URI'] ?? null
);
```

## Passwortänderung

Kann zusätzlich zu Audit geschrieben werden:

```php
$loginEvents->record(
    $userId,
    $email,
    'password_changed',
    $_SERVER['REMOTE_ADDR'] ?? null,
    $_SERVER['HTTP_USER_AGENT'] ?? null,
    $_SERVER['REQUEST_URI'] ?? null
);
```
