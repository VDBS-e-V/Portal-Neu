<?php

declare(strict_types=1);

namespace App\Mail;

/**
 * Baut den passenden Mailer anhand der Umgebungsvariablen (s. .env.example).
 *
 * Ist MAIL_HOST nicht gesetzt (lokale Entwicklung ohne SMTP-Zugang, Tests,
 * frisches Setup vor Konfiguration), wird auf den NullMailer zurückgefallen,
 * statt einen Fehler zu werfen.
 *
 * ANMERKUNG: Diese Klasse liest ENV-Variablen direkt über getenv()/$_ENV.
 * Falls das Projekt bereits eine eigene Konfigurations-/Env-Klasse hat
 * (in src/, aber für mich wegen robots.txt auf /tree/ nicht einsehbar),
 * sollte fromEnv() ggf. darauf umgestellt werden, damit es nur eine
 * Quelle der Wahrheit für Konfiguration gibt.
 */
final class MailerFactory
{
    public static function fromEnv(?string $fallbackLogPath = null): MailerInterface
    {
        $host = self::env('MAIL_HOST');

        if ($host === null || $host === '') {
            return new NullMailer($fallbackLogPath ?? dirname(__DIR__, 2) . '/var/log/mail.log');
        }

        return new PhpMailerMailer([
            'host' => $host,
            'port' => (int) (self::env('MAIL_PORT') ?? '587'),
            'username' => self::env('MAIL_USERNAME') ?? '',
            'password' => self::env('MAIL_PASSWORD') ?? '',
            'encryption' => self::env('MAIL_ENCRYPTION') ?? 'tls',
            'from_address' => self::env('MAIL_FROM_ADDRESS') ?? 'no-reply@localhost',
            'from_name' => self::env('MAIL_FROM_NAME') ?? 'VDBS Portal',
        ]);
    }

    private static function env(string $key): ?string
    {
        $value = $_ENV[$key] ?? getenv($key);

        if ($value === false || $value === null || $value === '') {
            return null;
        }

        return (string) $value;
    }
}
