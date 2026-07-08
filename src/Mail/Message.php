<?php

declare(strict_types=1);

namespace App\Mail;

/**
 * Unveränderliches Werteobjekt für eine zu versendende E-Mail.
 *
 * Validiert beim Erzeugen:
 * - mindestens ein Empfänger
 * - mindestens Text- oder HTML-Body vorhanden
 * - alle Adressen (to/cc/bcc/replyTo) sind syntaktisch gültige E-Mails
 *
 * @phpstan-type EmailAddress string
 */
final class Message
{
    /**
     * @param string[]     $to          Empfänger (mind. einer)
     * @param string[]     $cc
     * @param string[]     $bcc
     * @param Attachment[] $attachments
     */
    public function __construct(
        public readonly array $to,
        public readonly string $subject,
        public readonly ?string $textBody = null,
        public readonly ?string $htmlBody = null,
        public readonly array $cc = [],
        public readonly array $bcc = [],
        public readonly array $attachments = [],
        public readonly ?string $replyTo = null,
    ) {
        if ($this->to === []) {
            throw new MailerException('Eine Nachricht benötigt mindestens einen Empfänger.');
        }

        if ($this->textBody === null && $this->htmlBody === null) {
            throw new MailerException('Eine Nachricht benötigt einen Text- oder HTML-Body.');
        }

        foreach (['to' => $this->to, 'cc' => $this->cc, 'bcc' => $this->bcc] as $field => $addresses) {
            foreach ($addresses as $address) {
                if (!is_string($address) || !filter_var($address, FILTER_VALIDATE_EMAIL)) {
                    throw new MailerException(sprintf(
                        'Ungültige E-Mail-Adresse in "%s": %s',
                        $field,
                        is_string($address) ? $address : gettype($address),
                    ));
                }
            }
        }

        foreach ($this->attachments as $attachment) {
            if (!$attachment instanceof Attachment) {
                throw new MailerException('attachments darf ausschließlich Attachment-Objekte enthalten.');
            }
        }

        if ($this->replyTo !== null && !filter_var($this->replyTo, FILTER_VALIDATE_EMAIL)) {
            throw new MailerException(sprintf('Ungültige Reply-To-Adresse: %s', $this->replyTo));
        }
    }
}
