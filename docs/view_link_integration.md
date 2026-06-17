# Link-Integration in bestehende Views

## Personen-Detail

In `resources/views/pages/verwaltung/personen/show.php` ergänzen:

```php
<p>
    <a class="button" href="/verwaltung/personen/<?= (int) $person['id'] ?>/audit">
        Audit anzeigen
    </a>
</p>
```

## Gruppen-Detail

In `resources/views/pages/verwaltung/gruppen/show.php` ergänzen:

```php
<p>
    <a class="button" href="/verwaltung/gruppen/<?= (int) $group['id'] ?>/audit">
        Audit anzeigen
    </a>
</p>
```

## DSGVO-Detail

In `resources/views/pages/verwaltung/datenschutz/show.php` ergänzen:

```php
<p>
    <a class="button" href="/verwaltung/datenschutz/<?= (int) $erasure['id'] ?>/audit">
        Audit anzeigen
    </a>
</p>
```

## Audit-Log Detail

Optional in `resources/views/pages/verwaltung/audit/show.php` anhand von `entity_type` verlinken:

```php
<?php if (($entry['entity_type'] ?? '') === 'ids_persons'): ?>
    <a href="/verwaltung/personen/<?= (int) $entry['entity_id'] ?>/audit">Entitäts-Audit</a>
<?php endif; ?>
```
