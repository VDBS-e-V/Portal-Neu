# Datenbank

## Kernmodell

```txt
ids_persons
ids_users
ids_person_names
ids_person_contact_details
ids_person_addresses
ids_permission_groups
ids_person_permission_groups
```

## Person/Login-Trennung

```txt
ids_persons.id
ids_users.person_id
```

Eine Person ist ein Stammdatensatz. Ein Login ist optional.

## Berechtigungen

```txt
pt_areas
pt_page_groups
pt_permission_group_page_group_access
ids_permission_groups
ids_person_permission_groups
```

## Audit

```txt
pt_audit_log
```

## Einladungen

```txt
ids_user_invitations
```

Token werden nicht im Klartext gespeichert:

```txt
token_hash = sha256(token)
```

## DSGVO

```txt
ids_person_erasure_requests
```

Status:

```txt
requested
approved
rejected
completed
cancelled
```

## Passwort-Reset

```txt
ids_user_password_resets
```

Status:

```txt
pending
used
revoked
expired
```

## Login-/Sicherheitsereignisse

```txt
ids_user_login_events
```

Event-Typen:

```txt
login_success
login_failed
logout
password_changed
password_reset
```

## Menü

```txt
pt_menus
pt_menu_items
pt_menu_items.page_group_id
```

`page_group_id` steuert die Sichtbarkeit von Menüeinträgen anhand der PageGroup-Berechtigung.
