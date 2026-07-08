<?php

declare(strict_types=1);

namespace App\Mail;

/**
 * Wird geworfen, wenn eine E-Mail nicht aufgebaut oder nicht zugestellt
 * werden konnte (ungültige Konfiguration, SMTP-Fehler, ungültiger Anhang,
 * ungültige Empfänger-Adresse).
 */
final class MailerException extends \RuntimeException
{
}
