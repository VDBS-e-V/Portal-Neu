<?php

declare(strict_types=1);

namespace App\Mail;

/**
 * Vertrag für den Mailversand. Spätere Bausteine (notification_outbox-
 * Verarbeitung, Bestätigungsmail bei Ticket-Erstellung, täglicher Digest)
 * sollen ausschließlich gegen dieses Interface programmieren, nie direkt
 * gegen PHPMailer – so bleibt der Versand austauschbar und testbar.
 */
interface MailerInterface
{
    /**
     * @throws MailerException wenn die Nachricht nicht zugestellt werden konnte.
     */
    public function send(Message $message): void;
}
