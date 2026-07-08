#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Manueller Smoke-Test für den Mailversand aus Schritt 1 (Issue #2).
 *
 * Verwendung:
 *   php bin/mail-test.php empfaenger@example.org
 *
 * Bewusst NICHT als bin/console-Unterbefehl gebaut: wie Befehle dort
 * registriert werden (Auto-Discovery? feste Liste in bin/console?) war
 * für mich ohne Einblick in bin/console nicht zu ermitteln. Dieses Skript
 * kommt daher ohne Annahmen über die restliche Konsolen-Infrastruktur aus
 * und kann bei Bedarf später 1:1 in einen echten Konsolen-Befehl überführt
 * werden.
 */

require __DIR__ . '/../vendor/autoload.php';

use App\Mail\MailerFactory;
use App\Mail\Message;

// Minimaler .env-Fallback-Loader: greift nur, falls MAIL_HOST beim Aufruf
// dieses Skripts noch nicht durch die reguläre Bootstrap-Logik der
// Anwendung gesetzt wurde.
$envFile = __DIR__ . '/../.env';
if (is_file($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value, " \t\n\r\0\x0B\"'");
        if (getenv($key) === false) {
            putenv("{$key}={$value}");
            $_ENV[$key] = $value;
        }
    }
}

$recipient = $argv[1] ?? null;
if ($recipient === null) {
    fwrite(STDERR, "Verwendung: php bin/mail-test.php empfaenger@example.org\n");
    exit(1);
}

$mailer = MailerFactory::fromEnv();

$message = new Message(
    to: [$recipient],
    subject: 'VDBS Portal – Test-Mail (Basis-Mailversand)',
    textBody: "Diese Mail bestätigt, dass der Mailversand aus Schritt 1 (Issue #2) korrekt konfiguriert ist.\n\nZeitpunkt: " . date('Y-m-d H:i:s'),
);

try {
    $mailer->send($message);
    echo sprintf("OK: Mail an %s wurde übergeben (%s).\n", $recipient, $mailer::class);
} catch (\Throwable $e) {
    fwrite(STDERR, 'Fehler beim Mailversand: ' . $e->getMessage() . "\n");
    exit(1);
}
