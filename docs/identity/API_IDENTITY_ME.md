# API: /identity/me

## Endpunkte

```text
GET /identity/me
GET /identity/me?system=portal
```

## Zweck

Der Endpunkt gibt die aktuelle Identity-Sicht des eingeloggten Users zurück. Er ist die Grundlage für spätere Integrationen und kann von Frontend, externen Diensten oder Debug-Werkzeugen genutzt werden.

## Inhalt

Die Antwort enthält typischerweise:

```text
subject
person
login
systems
groups
permissions
permission_version
cache_ttl_seconds
```

## Filterung

Mit `?system=<key>` kann die Ausgabe auf ein System begrenzt werden:

```text
/identity/me?system=portal
/identity/me?system=identity
```

## Cache

Die Antwort nennt eine TTL. Standardziel ist:

```text
cache_ttl_seconds = 300
```

Bei Permission-Änderungen muss `permission_version` erhöht beziehungsweise invalidiert werden.
