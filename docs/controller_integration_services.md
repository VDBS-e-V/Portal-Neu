# Service-Integration

## config/services.php

`use` ergänzen:

```php
use App\Security\CsrfGuard;
use App\Security\CsrfTokenManager;
```

Service-Einträge aus `config/services_csrf_block.php` übernehmen.

## Controller-Factories erweitern

Jeder Controller mit POST-Aktionen bekommt zusätzlich:

```php
$container->get(CsrfGuard::class)
```

Beispiel:

```php
PersonenController::class => static function (Container $container): PersonenController {
    return new PersonenController(
        $container->get(Renderer::class),
        $container->get(AreaRepository::class),
        $container->get(MenuRepository::class),
        $container->get(MenuItemRepository::class),
        $container->get(PersonRepository::class),
        $container->get(PersonContactRepository::class),
        $container->get(PersonAddressRepository::class),
        $container->get(PermissionGroupRepository::class),
        $container->get(PersonPermissionGroupRepository::class),
        $container->get(AuthorizationService::class),
        $container->get(AdminSafetyService::class),
        $container->get(AuditLogger::class),
        $container->get(CsrfGuard::class)
    );
},
```

## Empfohlene Formularnamen

```txt
person.create
person.edit.{personId}
person.status.{personId}
person.groups.{personId}
person.contact.create.{personId}
person.contact.delete.{personId}.{contactId}
person.address.create.{personId}
person.address.delete.{personId}.{addressId}

group.create
group.edit.{groupId}
group.delete.{groupId}

permissions.group.{groupId}

invitation.accept.{token}
invitation.create.person.{personId}
invitation.revoke.{invitationId}

erasure.create.person.{personId}
erasure.approve.{requestId}
erasure.reject.{requestId}
erasure.cancel.{requestId}
erasure.complete.{requestId}
```
