# VDBS Portal

Minimaler PHP-Skeleton für den Portal-Einstieg.

## Setup
1. `composer install`
2. `.env.example` nach `.env` kopieren und die Werte bei Bedarf anpassen
3. Den Webserver auf `public/` als Docroot zeigen lassen, zum Beispiel über XAMPP / Apache

## Prüfung
1. `GET /health` liefert `ok`
2. `GET /api/health` liefert JSON mit `status: ok`
3. `php bin/console routes` zeigt die registrierten Routen

## Routen
- `/`
- `/health`
- `/api/health`