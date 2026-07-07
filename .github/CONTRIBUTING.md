# Beitragen zu Portal-Neu

Danke, dass du zu Portal-Neu beitragen möchtest! Diese Kurzanleitung hilft dir, schnell loszulegen.

## Lokales Setup

```bash
composer install
php bin/console db:hard-reset --force --seed --admin-password=secret
php bin/console routes
php -S localhost:8000 -t public
```

Demo-Logins findest du in der `README.md`. Ausführliche Installationshinweise stehen in `docs/INSTALLATION.md`, Fehlersuche in `docs/TROUBLESHOOTING.md`.

## Bevor du ein Issue erstellst

1. Kurze Suche in den bestehenden Issues, ob dein Anliegen schon existiert.
2. Passendes Formular wählen:
   - 🐛 **Bug Report** – etwas funktioniert nicht wie erwartet
   - ✨ **Feature Request** – Vorschlag für neue Funktionalität
   - 🛠️ **Aufgabe/Chore** – Refactoring, QA, Deployment, interne Wartung
   - 📚 **Dokumentation** – Fehler oder Lücken in den Docs
   - ❓ **Frage** – Nutzungs- oder Konfigurationsfragen
3. **Sicherheitslücken bitte niemals als öffentliches Issue melden** – siehe [SECURITY.md](SECURITY.md).

## Architektur-Grundlagen

Bevor du Änderungen planst, lohnt sich ein Blick in:

- `docs/ROUTEN.md` – Übersicht aller Routen
- `docs/BERECHTIGUNGEN.md` – Berechtigungsmodell (`Area.PageGroup`)
- `docs/DATENBANK.md` – Datenmodell, insbesondere die Trennung von **Person** (Stammdaten) und **Login** (optionaler Account-Zugang)
- `docs/SICHERHEIT.md` – Sicherheitsrelevante Konventionen

## Branches & Commits

- Branch-Namen: `feat/kurzbeschreibung`, `fix/kurzbeschreibung`, `chore/kurzbeschreibung`, `docs/kurzbeschreibung`
- Commit-Nachrichten möglichst im Conventional-Commits-Stil, z. B.:
  - `fix: verhindert doppelte Einladungen in verwaltung.einladungen`
  - `feat: fügt Audit-Filter nach Zeitraum hinzu`
  - `docs: aktualisiert BERECHTIGUNGEN.md`

## Vor dem Pull Request

```bash
php tools/qa/run_all.php
phpunit
```

Bitte fülle die PR-Vorlage vollständig aus, insbesondere die Checkliste zu Berechtigungen und Datenbank-Migrationen – das Berechtigungsmodell (`Area.PageGroup`) ist sicherheitskritisch und wird bei jedem Review geprüft.

## Verhalten & Umgangston

Portal-Neu wird im Kontext eines Vereins (VDBS e. V.) entwickelt. Wir gehen respektvoll und konstruktiv miteinander um – unabhängig von Erfahrungsstand oder Rolle im Verein.
