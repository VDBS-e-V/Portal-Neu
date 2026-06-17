# View-Integration Beispiele

## Einfaches Formular

```php
<form method="post" action="/verwaltung/personen/create">
    <input type="hidden" name="_csrf_token" value="<?= $e($csrfToken ?? '') ?>">

    ...
</form>
```

## Mehrere Formulare in einer View

Controller:

```php
'csrfTokens' => [
    'status' => $this->csrf->token('person.status.' . $personId),
    'groups' => $this->csrf->token('person.groups.' . $personId),
],
```

View:

```php
<form method="post" action="/verwaltung/personen/<?= $personId ?>/status">
    <input type="hidden" name="_csrf_token" value="<?= $e($csrfTokens['status'] ?? '') ?>">
    ...
</form>
```

## Tabellen mit Löschformularen

Controller:

```php
$csrfTokens = [];

foreach ($contacts as $contact) {
    $contactId = (int) $contact['id'];
    $csrfTokens['contact_delete_' . $contactId] = $this->csrf->token(
        'person.contact.delete.' . $personId . '.' . $contactId
    );
}
```

View:

```php
<form method="post" action="/verwaltung/personen/<?= $personId ?>/kontakte/<?= $contactId ?>/delete">
    <input
        type="hidden"
        name="_csrf_token"
        value="<?= $e($csrfTokens['contact_delete_' . $contactId] ?? '') ?>"
    >
    <button type="submit">Löschen</button>
</form>
```
