# Schritt 1: Basis-Mailversand — Integrationsanleitung

Umsetzung von Punkt 1 aus der Umsetzungsreihenfolge in Issue #2
("Basis-Mailversand (Bibliothek wegen Anhängen)").

## Was enthalten ist

```
src/Mail/MailerInterface.php    Vertrag für den Versand
src/Mail/Message.php            Unveränderliches Werteobjekt, validiert sich selbst
src/Mail/Attachment.php         Datei- oder inhaltsbasierter Anhang
src/Mail/PhpMailerMailer.php    Echter SMTP-Versand über PHPMailer
src/Mail/NullMailer.php         Protokolliert statt zu versenden (Dev/Test/Fallback)
src/Mail/MailerFactory.php      Baut anhand ENV automatisch den richtigen Mailer
src/Mail/MailerException.php    Einheitliche Exception für alle Mail-Fehler

tests/Mail/*.php                PHPUnit-Tests für alle oben genannten Klassen

bin/mail-test.php               Eigenständiges Skript zum manuellen Durchtesten
env-ergaenzung.txt              Zeilen zum Anhängen an .env.example / .env
```

## Installation

1. **Abhängigkeit hinzufügen** (im Repo-Root):
   ```
   composer require phpmailer/phpmailer:^6.9
   ```
   Das ist die "sinnvolle kleine Abhängigkeit" aus Abschnitt 6 des Issues —
   eine einzelne, zweckgebundene Bibliothek statt selbstgebautem
   MIME-/SMTP-Handling.

2. **Dateien kopieren**
   - `src/Mail/*` → `src/Mail/` im Repo (passt zum bestehenden PSR-4
     Autoload `App\ → src/` aus eurer `composer.json`).
   - `tests/Mail/*` → `tests/Mail/`.
   - `bin/mail-test.php` → `bin/mail-test.php`, danach ausführbar machen:
     `chmod +x bin/mail-test.php`.

3. **`.env.example` und lokale `.env` ergänzen** um den Inhalt aus
   `env-ergaenzung.txt`.

4. **Manuell testen** (mit echten SMTP-Zugangsdaten in `.env`):
   ```
   php bin/mail-test.php deine-adresse@example.org
   ```
   Ohne `MAIL_HOST` läuft automatisch der `NullMailer` — es landet dann
   ein Log-Eintrag in `var/log/mail.log`, es wird nichts verschickt.

5. **Automatisierte Tests laufen lassen** (Befehl ggf. an eure
   `docs/TESTS.md`-Konvention anpassen, z. B. `vendor/bin/phpunit`):
   ```
   vendor/bin/phpunit tests/Mail
   ```

## Offene Punkte, die ich ohne Repo-Einblick nicht abschließend lösen konnte

GitHub blockiert automatisiertes Lesen von `/tree/`- und `/commits/`-Pfaden
per robots.txt — ich hatte daher nur Zugriff auf README, `composer.json`
und `.env.example`, nicht auf den tatsächlichen Code in `src/`. Drei Stellen
sind dadurch bewusst als Annahme markiert:

1. **Testsuite/Autoload:** Ich gehe von `namespace Tests\Mail` aus. Falls
   eure `composer.json` noch kein `autoload-dev` hat, ergänzt:
   ```json
   "autoload-dev": {
       "psr-4": { "Tests\\": "tests/" }
   }
   ```
   gefolgt von `composer dump-autoload`. Falls ihr eine andere
   Test-Namespace-Konvention nutzt, sagt Bescheid, dann passe ich es an.

2. **Log-Pfad des `NullMailer`:** Ich nutze `var/log/mail.log`, da `var/`
   im Repo existiert. Falls Logs bei euch woanders liegen (z. B. `storage/logs`),
   ist das ein Ein-Zeilen-Fix in `MailerFactory::fromEnv()`.

3. **Anbindung an `bin/console`:** Ich habe bewusst *kein*
   `bin/console mail:test` gebaut, weil ich nicht weiß, wie Befehle dort
   registriert werden (feste Liste? Auto-Discovery? Command-Klassen unter
   `src/Console/`?). Das eigenständige `bin/mail-test.php` funktioniert
   unabhängig davon. Wenn du mir `bin/console` (oder die Klasse, die
   Befehle registriert) zeigst, baue ich daraus gerne einen echten,
   ins Konsolen-Framework integrierten Befehl.

## Bewusst nicht Teil dieses Schritts

Datenmodell (`tickets`, `ticket_attachments`, `notification_outbox`, …),
Area `tickets`/PageGroups, Routen, Digest-Cronjob — das sind die
nachfolgenden Punkte 2–15 aus der Umsetzungsreihenfolge und bauen auf
`MailerInterface` auf, sobald es im Repo verfügbar ist.
