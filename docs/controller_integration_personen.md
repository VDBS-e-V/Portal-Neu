# Controller-Integration: PersonenController

## use ergänzen

```php
use App\Security\CsrfException;
use App\Security\CsrfGuard;
```

## Constructor erweitern

```php
public function __construct(
    Renderer $renderer,
    AreaRepository $areas,
    MenuRepository $menus,
    MenuItemRepository $menuItems,
    private readonly PersonRepository $persons,
    private readonly PersonContactRepository $contacts,
    private readonly PersonAddressRepository $addresses,
    private readonly PermissionGroupRepository $permissionGroups,
    private readonly PersonPermissionGroupRepository $personGroups,
    private readonly AuthorizationService $authorization,
    private readonly AdminSafetyService $adminSafety,
    private readonly AuditLogger $audit,
    private readonly CsrfGuard $csrf
) {
    parent::__construct($renderer, $areas, $menus, $menuItems);
}
```

## GET-Views Token mitgeben

Beispiel `createForm()`:

```php
return $this->renderPage($request, 'pages/verwaltung/personen/form', $this->pageParams([
    'title' => 'Person anlegen',
    'pageTitle' => 'Person anlegen',
    'activeKey' => 'personen',
    'mode' => 'create',
    'action' => '/verwaltung/personen/create',
    'person' => [],
    'errors' => [],
    'csrfToken' => $this->csrf->token('person.create'),
]));
```

Beispiel `editForm()`:

```php
'csrfToken' => $this->csrf->token('person.edit.' . $personId),
```

## POST validieren

Beispiel `create()`:

```php
$this->csrf->requireValid($request, 'person.create');
```

Beispiel `edit()`:

```php
$this->csrf->requireValid($request, 'person.edit.' . $personId);
```

Beispiel `updateStatus()`:

```php
$this->csrf->requireValid($request, 'person.status.' . $personId);
```

Beispiel `updateGroups()`:

```php
$this->csrf->requireValid($request, 'person.groups.' . $personId);
```

Beispiel Kontakt anlegen:

```php
$this->csrf->requireValid($request, 'person.contact.create.' . $personId);
```

Beispiel Kontakt löschen:

```php
$this->csrf->requireValid($request, 'person.contact.delete.' . $personId . '.' . $contactId);
```

## View-Hidden-Field

In jedes POST-Formular:

```php
<input type="hidden" name="_csrf_token" value="<?= $e($csrfToken ?? '') ?>">
```

Oder bei mehreren Formularen in einer View:

```php
<input type="hidden" name="_csrf_token" value="<?= $e($csrfTokens['status'] ?? '') ?>">
```
