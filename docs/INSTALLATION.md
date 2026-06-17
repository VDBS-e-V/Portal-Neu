# Installation

## Voraussetzungen

```txt
PHP 8.2+
Composer
MySQL/MariaDB
PDO MySQL
Webserver oder PHP Built-in Server
```

## Repository vorbereiten

```bat
git clone https://github.com/VDBS-e-V/Portal-Neu.git
cd Portal-Neu
composer install
```

## Environment

Beispiel `.env.local`:

```ini
APP_ENV=local
APP_DEBUG=1

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=portal_neu
DB_USERNAME=root
DB_PASSWORD=
```

Alternativ:

```ini
DB_DSN=mysql:host=127.0.0.1;port=3306;dbname=portal_neu;charset=utf8mb4
DB_USERNAME=root
DB_PASSWORD=
```

## Datenbank neu aufbauen

```bat
php bin/console db:hard-reset --force --seed --admin-password=secret
```

Alternativ Schritt für Schritt:

```bat
php bin/console db:hard-reset --force
php bin/console migrate
php bin/console seed --admin-password=secret
```

## Routen prüfen

```bat
php bin/console routes
```

## Lokaler Server

```bat
php -S localhost:8000 -t public
```

## Login

```txt
admin@example.org / secret
```
