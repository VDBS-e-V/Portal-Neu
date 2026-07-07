# Migrations- und Mini-Projekt-Verlauf

## 1 bis 5: Aufbau

- Grundschema für `ids_subjects`, `ids_persons`, `ids_users`, `ids_systems`, `ids_groups`, `ids_permissions`, `ids_group_permissions`, `ids_subject_groups`
- Permission-basierte Portalnavigation
- Administration-UI
- `/identity/me`
- Konto- und Legacy-Bridge-Härtung

## 6 bis 11: PageGroup entfernen

- PageGroup aus aktiven Runtime-Pfaden entfernt
- alte PageGroup-Dateien archiviert
- produktive PageGroup-DB-Nutzung entfernt
- alte Services entfernt
- PageGroup-Tabellen und `pt_menu_items.page_group_id` aus laufender DB entfernt
- Authorization-Bridge entfernt

## 12 bis 14: Legacy-Identity-Gruppen entfernen

- Integritätschecks für neues Identity-Modell
- alte `ids_permission_groups`-/`ids_user_permission_groups`-Logik entfernt
- Resttreffer bereinigt
- Legacy-Identity-Gruppentabellen aus laufender DB entfernt

## 15 bis 18: Härtung

- Personenverwaltung auf neues Identity-Modell geprüft
- Admin-Safety und Self-Lockout-Schutz
- Audit-/Erasure-Guards
- Navigation final auf `pt_menu_items.permission_key`

## 19 bis 20: Abschluss

- zentrale Smoke-Test-Suite
- Dokumentation und Betriebscheck

## Historische Migrationen

Alte Migrationen bleiben historisch erhalten. Die laufende DB ist der relevante Zielzustand; alte Tabellen können in historischen Migrationen weiterhin vorkommen.
