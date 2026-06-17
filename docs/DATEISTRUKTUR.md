# Dateistruktur

```txt
config/
  routes.php
  services.php

database/
  migrations/
  seeds/

src/
  Audit/
  Http/
    Controller/
      Verwaltung/
  Navigation/
  Repository/
  Security/

resources/
  views/
    pages/
      account/
      invitations/
      password/
      verwaltung/
    partials/

tools/
  qa/

docs/
```

## Wichtige Klassen

```txt
App\Security\AuthorizationService
App\Security\AdminSafetyService
App\Security\CsrfGuard
App\Security\CsrfTokenManager
App\Security\AccountSessionContext

App\Audit\AuditLogger

App\Navigation\VerwaltungNavigation
App\Navigation\AuthorizedMainMenu

App\Repository\PersonRepository
App\Repository\PermissionGroupRepository
App\Repository\AuditLogRepository
App\Repository\EntityAuditRepository
```
