# Troubleshooting

## Renderer-Klasse nicht gefunden

Korrekte Klasse:

```php
use App\Presentation\Templating\Renderer;
```

Nicht verwenden:

```php
use App\View\Renderer;
use App\Http\Response\Renderer;
```

## Firebird-Warnung

Wenn PHP beim CLI Firebird-Warnungen zeigt:

```ini
;extension=pdo_firebird
```

in der aktiven `php.ini` deaktivieren.

## Seed: Unknown column is_default

Lokale Tabelle `pt_menus` hat evtl. keine Spalte `is_default`.

Lösung:

```txt
Seed defensiv machen oder DB-Migrationen aktualisieren.
```

## Seed: Unknown column school_key

Lokale Migration `0007_create_cod_schools.sql` ist wahrscheinlich veraltet.

Lösung:

```txt
Migration mit aktuellem Branch abgleichen.
```

## Route nicht gefunden

Prüfen:

```bat
php bin/console routes
```

Danach kontrollieren:

```txt
config/routes.php
use-Import vorhanden?
Route im return-Array eingefügt?
Controller-Klasse existiert?
```

## Service nicht gefunden

Prüfen:

```txt
config/services.php
use-Import vorhanden?
Factory im return-Array eingefügt?
alle Constructor-Argumente registriert?
```

## CSRF-Fehler

Prüfen:

```txt
Session aktiv?
Hidden Field _csrf_token vorhanden?
Formularname im GET und POST identisch?
CsrfGuard im Controller injected?
```

## 500 nach Controller-Einbau

Prüfen:

```txt
Namespace korrekt?
Constructor-Argumente passen zur Service-Factory?
Repository-Methode existiert?
View-Datei existiert?
```

## Menü zeigt Verwaltung nicht

Prüfen:

```sql
SELECT title, slug, url, page_group_id
FROM pt_menu_items
ORDER BY menu_id, order_index;
```

Außerdem:

```txt
Mini-Projekt 10 Seed ausgeführt?
pt_menu_items.page_group_id vorhanden?
aktueller User hat PageGroup-Zugriff?
```
