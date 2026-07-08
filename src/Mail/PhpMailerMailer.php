<?php

declare(strict_types=1);

namespace App\Mail;

use PHPMailer\PHPMailer\Exception as PHPMailerNativeException;
use PHPMailer\PHPMailer\PHPMailer;

/**
 * SMTP-Mailversand über PHPMailer.
 *
 * Bewusst als dünner Adapter gehalten: die eigentliche MIME-/SMTP-Logik
 * (Boundaries, Header-Encoding, Attachment-Kodierung) übernimmt PHPMailer
 * vollständig – das von Hand nachzubauen war die Fehlerquelle, die im
 * Issue (Abschnitt 6) explizit vermieden werden sollte.
 */
final class PhpMailerMailer implements MailerInterface
{
    /**
     * @param array{
     *     host: string,
     *     port: int,
     *     username?: string,
     *     password?: string,
     *     encryption?: string,
     *     from_address: string,
     *     from_name: string,
     *     timeout?: int,
     * } $config
     */
    public function __construct(private readonly array $config)
    {
        foreach (['host', 'port', 'from_address', 'from_name'] as $required) {
            if (!array_key_exists($required, $this->config) || $this->config[$required] === '') {
                throw new MailerException(sprintf('Mail-Konfiguration unvollständig: "%s" fehlt.', $required));
            }
        }
    }

    public function send(Message $message): void
    {
        $mailer = new PHPMailer(true);

        try {
            $mailer->isSMTP();
            $mailer->Host = $this->config['host'];
            $mailer->Port = $this->config['port'];
            $mailer->Timeout = $this->config['timeout'] ?? 10;

            $username = $this->config['username'] ?? '';
            $mailer->SMTPAuth = $username !== '';
            $mailer->Username = $username;
            $mailer->Password = $this->config['password'] ?? '';

            $encryption = strtolower($this->config['encryption'] ?? '');
            $mailer->SMTPSecure = match ($encryption) {
                'tls' => PHPMailer::ENCRYPTION_STARTTLS,
                'ssl' => PHPMailer::ENCRYPTION_SMTPS,
                default => '',
            };
            $mailer->SMTPAutoTLS = $encryption !== '';

            $mailer->CharSet = 'UTF-8';
            $mailer->setFrom($this->config['from_address'], $this->config['from_name']);

            if ($message->replyTo !== null) {
                $mailer->addReplyTo($message->replyTo);
            }

            foreach ($message->to as $address) {
                $mailer->addAddress($address);
            }
            foreach ($message->cc as $address) {
                $mailer->addCC($address);
            }
            foreach ($message->bcc as $address) {
                $mailer->addBCC($address);
            }

            $mailer->Subject = $message->subject;

            if ($message->htmlBody !== null) {
                $mailer->isHTML(true);
                $mailer->Body = $message->htmlBody;
                $mailer->AltBody = $message->textBody ?? strip_tags($message->htmlBody);
            } else {
                $mailer->isHTML(false);
                $mailer->Body = (string) $message->textBody;
            }

            foreach ($message->attachments as $attachment) {
                if ($attachment->isFileBased()) {
                    $mailer->addAttachment(
                        $attachment->path(),
                        $attachment->filename,
                        PHPMailer::ENCODING_BASE64,
                        $attachment->mimeType,
                    );
                } else {
                    $mailer->addStringAttachment(
                        $attachment->contents(),
                        $attachment->filename,
                        PHPMailer::ENCODING_BASE64,
                        $attachment->mimeType,
                    );
                }
            }

            $mailer->send();
        } catch (PHPMailerNativeException $e) {
            throw new MailerException('Mailversand fehlgeschlagen: ' . $mailer->ErrorInfo, previous: $e);
        }
    }
}
