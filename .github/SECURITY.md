# Sicherheitsrichtlinie

Portal-Neu verwaltet personenbezogene Daten (Personen, Logins, Kontakte, Adressen) sowie ein
Berechtigungssystem (`Area.PageGroup`) und DSGVO-Anonymisierungsfunktionen. Sicherheitslücken
werden entsprechend ernst genommen.

## Meldung einer Sicherheitslücke

**Bitte melde Sicherheitslücken NICHT über ein öffentliches Issue.**

Nutze stattdessen einen der folgenden vertraulichen Wege:

1. **GitHub Security Advisories** (bevorzugt):
   „Report a vulnerability“ unter <https://github.com/VDBS-e-V/Portal-Neu/security/advisories/new>
2. **E-Mail**: `security@vdbs-ev.de` *(bitte durch die tatsächliche Kontaktadresse des Vereins ersetzen)*

Bitte gib nach Möglichkeit an:

- Betroffener Bereich (z. B. Login, `verwaltung.berechtigungen`, `konto/sicherheit`, DSGVO-Anonymisierung)
- Schritte zur Reproduktion
- Mögliche Auswirkung (z. B. Rechte-Eskalation, Zugriff auf fremde Personendaten, Umgehung der Anonymisierung)
- Ob dir bereits ein Fix vorschwebt

## Besonders sicherheitsrelevante Bereiche

Aufgrund der Architektur (getrenntes Personen-/Login-Modell, Berechtigungsgruppen, Einladungen,
Audit-Log, DSGVO-Anonymisierung) gelten folgende Bereiche als besonders kritisch und sollten mit
erhöhter Sorgfalt gemeldet und geprüft werden:

- Authentifizierung & Passwort-Reset (`/passwort/vergessen`, `/konto/sicherheit`)
- Berechtigungsprüfung (`verwaltung.berechtigungen`)
- Einladungs-Mechanismus (`verwaltung.einladungen`) – z. B. Token-Vorhersehbarkeit, fehlende Ablaufzeit
- Datenschutz/Anonymisierung (`verwaltung.datenschutz`)
- Audit-Log-Integrität (`verwaltung.audit`)

## Reaktionszeit

Wir bemühen uns, eingehende Meldungen zeitnah zu sichten. Da es sich um ein Vereinsprojekt
handelt, kann die Reaktionszeit variieren – bitte hab dafür Verständnis.

## Offenlegung

Wir bitten um „Coordinated Disclosure“: Bitte veröffentliche Details zu einer Lücke erst,
nachdem ein Fix verfügbar ist oder wir uns gemeinsam auf einen Zeitpunkt geeinigt haben.
