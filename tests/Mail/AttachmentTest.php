<?php

declare(strict_types=1);

namespace Tests\Mail;

use App\Mail\Attachment;
use App\Mail\MailerException;
use PHPUnit\Framework\TestCase;

final class AttachmentTest extends TestCase
{
    public function testFromContentsStoresGivenMimeType(): void
    {
        $attachment = Attachment::fromContents('Inhalt', 'datei.txt', 'text/plain');

        self::assertSame('datei.txt', $attachment->filename);
        self::assertSame('text/plain', $attachment->mimeType);
        self::assertSame('Inhalt', $attachment->contents());
        self::assertFalse($attachment->isFileBased());
    }

    public function testFromContentsFallsBackToOctetStream(): void
    {
        $attachment = Attachment::fromContents('Inhalt', 'datei.bin');

        self::assertSame('application/octet-stream', $attachment->mimeType);
    }

    public function testFromContentsRequiresFilename(): void
    {
        $this->expectException(MailerException::class);

        Attachment::fromContents('Inhalt', '');
    }

    public function testFromPathThrowsForMissingFile(): void
    {
        $this->expectException(MailerException::class);

        Attachment::fromPath('/pfad/existiert/nicht.pdf');
    }

    public function testFromPathReadsExistingFile(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'mail_test_');
        file_put_contents($path, 'Testinhalt');

        try {
            $attachment = Attachment::fromPath($path, 'anhang.txt', 'text/plain');

            self::assertTrue($attachment->isFileBased());
            self::assertSame('anhang.txt', $attachment->filename);
            self::assertSame('Testinhalt', $attachment->contents());
        } finally {
            unlink($path);
        }
    }
}
