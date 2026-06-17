# Patch-Beispiele für vorhandene Controller

## GruppenController

### use ergänzen

```php
use App\Security\CsrfGuard;
```

### Constructor

```php
private readonly CsrfGuard $csrf
```

### createForm()

```php
'csrfToken' => $this->csrf->token('group.create'),
```

### create()

```php
$this->csrf->requireValid($request, 'group.create');
```

### editForm()

```php
'csrfToken' => $this->csrf->token('group.edit.' . $groupId),
```

### edit()

```php
$this->csrf->requireValid($request, 'group.edit.' . $groupId);
```

### show()

```php
'csrfTokens' => [
    'delete' => $this->csrf->token('group.delete.' . $groupId),
],
```

### delete()

```php
$this->csrf->requireValid($request, 'group.delete.' . $groupId);
```

## BerechtigungenController

### group()

```php
'csrfToken' => $this->csrf->token('permissions.group.' . $groupId),
```

### updateGroup()

```php
$this->csrf->requireValid($request, 'permissions.group.' . $groupId);
```

## EinladungenController

### createForPerson()

```php
$this->csrf->requireValid($request, 'invitation.create.person.' . $personId);
```

### revoke()

```php
$this->csrf->requireValid($request, 'invitation.revoke.' . $invitationId);
```

## DatenschutzController

### createForm()

```php
'csrfToken' => $this->csrf->token('erasure.create.person.' . $personId),
```

### create()

```php
$this->csrf->requireValid($request, 'erasure.create.person.' . $personId);
```

### show()

```php
'csrfTokens' => [
    'approve' => $this->csrf->token('erasure.approve.' . $requestId),
    'reject' => $this->csrf->token('erasure.reject.' . $requestId),
    'cancel' => $this->csrf->token('erasure.cancel.' . $requestId),
    'complete' => $this->csrf->token('erasure.complete.' . $requestId),
],
```

### approve()

```php
$this->csrf->requireValid($request, 'erasure.approve.' . $requestId);
```

### reject()

```php
$this->csrf->requireValid($request, 'erasure.reject.' . $requestId);
```

### cancel()

```php
$this->csrf->requireValid($request, 'erasure.cancel.' . $requestId);
```

### complete()

```php
$this->csrf->requireValid($request, 'erasure.complete.' . $requestId);
```
