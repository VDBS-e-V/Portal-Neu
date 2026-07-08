<?php

declare(strict_types=1);

namespace Tests\Mail;

use App\Mail\Attachment;
use App\Mail\MailerException;
use App\Mail\Message;
use PHPUnit\Framework\TestCase;

final class MessageTest extends TestCase
{
    public function testValidMessageIsConstructed(): void
    {
        $message = new Message(
            to: ['ersteller@example.org'],
            subject: 'Test-Betreff',
            textBody: 'Test-Inhalt',
        );

        self::assertSame(['ersteller@example.org'], $message->to);
        self::assertSame('Test-Betreff', $message->subject);
        self::assertSame('Test-Inhalt', $message->textBody);
        self::assertSame([], $message->attachments);
    }

    public function testThrowsWithoutRecipient(): void
    {
        $this->expectException(MailerException::class);

        new Message(to: [], subject: 'Test', textBody: 'Test');
    }

    public function testThrowsWithoutAnyBody(): void
    {
        $this->expectException(MailerException::class);

        new Message(to: ['a@example.org'], subject: 'Test');
    }

    public function testThrowsOnInvalidRecipientAddress(): void
    {
        $this->expectException(MailerException::class);

        new Message(to: ['keine-email'], subject: 'Test', textBody: 'Test');
    }

    public function testThrowsOnInvalidReplyTo(): void
    {
        $this->expectException(MailerException::class);

        new Message(to: ['a@example.org'], subject: 'Test', textBody: 'Test', replyTo: 'keine-email');
    }

    public function testAcceptsAttachments(): void
    {
        $attachment = Attachment::fromContents('Inhalt', 'datei.txt', 'text/plain');

        $message = new Message(
            to: ['a@example.org'],
            subject: 'Test',
            textBody: 'Test',
            attachments: [$attachment],
        );

        self::assertCount(1, $message->attachments);
        self::assertSame('datei.txt', $message->attachments[0]->filename);
    }
}
