# Smoke-Tests

## Zentrale Suite

```bat
php tools\qa\run_identity_smoke_suite.php
```

Mit Seed:

```bat
php tools\qa\run_identity_smoke_suite.php --with-seed
```

## Einzeltests

```bat
php tools\qa\check_identity_core_smoke.php
php tools\qa\check_route_smoke_matrix.php
php tools\qa\check_identity_http_smoke.php
```

## Optionaler HTTP-Test

```bat
set SMOKE_BASE_URL=http://localhost/vdbs_portal/public
php tools\qa\check_identity_http_smoke.php
```

## Erfolgskriterien

```text
OK: Identity-Core-Smoke-Check bestanden.
OK: Route-Smoke-Matrix bestanden.
OK: Mini-Projekt-19-Smoke-Test-Suite bestanden.
```

HTTP-Smoke darf übersprungen werden, wenn keine Base-URL gesetzt ist.
