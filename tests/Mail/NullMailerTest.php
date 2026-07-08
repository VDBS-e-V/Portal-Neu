<?php

declare(strict_types=1);

namespace Tests\Mail;

use App\Mail\Message;
use App\Mail\NullMailer;
use PHPUnit\Framework\TestCase;

final class NullMailerTest extends TestCase
{
    public function testWritesLogEntryInsteadOfSending(): void
    {
        $logPath = tempnam(sys_get_temp_dir(), 'mail_log_') . '.log';
        unlink($logPath); // NullMailer soll die Datei bei Bedarf selbst anlegen

        $mailer = new NullMailer($logPath);
        $message = new Message(
            to: ['empfaenger@example.org'],
            subject: 'Log-Test',
            textBody: 'Inhalt',
        );

        $mailer->send($message);

        self::assertFileExists($logPath);
        $content = file_get_contents($logPath);
        self::assertStringContainsString('empfaenger@example.org', $content);
        self::assertStringContainsString('Log-Test', $content);

        unlink($logPath);
    }
}
