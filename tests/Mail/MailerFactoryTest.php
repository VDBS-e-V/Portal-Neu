<?php

declare(strict_types=1);

namespace Tests\Mail;

use App\Mail\MailerFactory;
use App\Mail\NullMailer;
use App\Mail\PhpMailerMailer;
use PHPUnit\Framework\TestCase;

final class MailerFactoryTest extends TestCase
{
    protected function tearDown(): void
    {
        putenv('MAIL_HOST');
        unset($_ENV['MAIL_HOST']);
        putenv('MAIL_FROM_ADDRESS');
        unset($_ENV['MAIL_FROM_ADDRESS']);
    }

    public function testFallsBackToNullMailerWithoutMailHost(): void
    {
        putenv('MAIL_HOST');
        unset($_ENV['MAIL_HOST']);

        $logPath = sys_get_temp_dir() . '/mail_factory_test.log';
        $mailer = MailerFactory::fromEnv($logPath);

        self::assertInstanceOf(NullMailer::class, $mailer);
    }

    public function testBuildsPhpMailerMailerWhenMailHostIsSet(): void
    {
        putenv('MAIL_HOST=smtp.example.org');
        $_ENV['MAIL_HOST'] = 'smtp.example.org';
        putenv('MAIL_FROM_ADDRESS=no-reply@example.org');
        $_ENV['MAIL_FROM_ADDRESS'] = 'no-reply@example.org';

        $mailer = MailerFactory::fromEnv();

        self::assertInstanceOf(PhpMailerMailer::class, $mailer);
    }
}
