<?php

declare(strict_types=1);

namespace App\Mail;

/**
 * Versendet keine echten Mails, sondern protokolliert sie in eine Datei.
 *
 * Gedacht für lokale Entwicklung/CI, solange keine MAIL_HOST-Zugangsdaten
 * hinterlegt sind (s. MailerFactory) – die Anwendung bleibt so lauffähig,
 * ohne dass jede Umgebung echten SMTP-Zugang braucht.
 */
final class NullMailer implements MailerInterface
{
    public function __construct(private readonly string $logPath)
    {
    }

    public function send(Message $message): void
    {
        $directory = dirname($this->logPath);
        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            throw new MailerException(sprintf('Log-Verzeichnis konnte nicht angelegt werden: %s', $directory));
        }

        $line = sprintf(
            "[%s] an=%s cc=%s bcc=%s betreff=%s anhaenge=%d\n",
            date('Y-m-d H:i:s'),
            implode(',', $message->to),
            implode(',', $message->cc),
            implode(',', $message->bcc),
            $message->subject,
            count($message->attachments),
        );

        if (file_put_contents($this->logPath, $line, FILE_APPEND | LOCK_EX) === false) {
            throw new MailerException(sprintf('Mail-Log konnte nicht geschrieben werden: %s', $this->logPath));
        }
    }
}
