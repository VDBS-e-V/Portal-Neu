<?php

declare(strict_types=1);

namespace App\Mail;

/**
 * Ein einzelner Datei-Anhang einer E-Mail.
 *
 * Zwei Erzeugungswege:
 * - fromPath(): Anhang liegt bereits als Datei auf der Platte
 *   (z. B. ein hochgeladenes ticket_attachments-Original unter storage/).
 * - fromContents(): Anhang liegt als Bytes im Speicher vor, ohne dass dafür
 *   eine temporäre Datei angelegt werden muss.
 *
 * Bewusst ohne Bezug zur ticket_attachments-Tabelle aus Issue #2 –
 * das ist erst Teil eines späteren Schritts (Datenmodell). Diese Klasse
 * kennt nur "eine Datei mit Namen und MIME-Type", nichts Ticket-Spezifisches.
 */
final class Attachment
{
    private function __construct(
        public readonly string $filename,
        public readonly string $mimeType,
        private readonly ?string $path,
        private readonly ?string $contents,
    ) {
    }

    public static function fromPath(string $path, ?string $filename = null, ?string $mimeType = null): self
    {
        if (!is_file($path) || !is_readable($path)) {
            throw new MailerException(sprintf('Anhang-Datei nicht lesbar: %s', $path));
        }

        return new self(
            filename: $filename ?? basename($path),
            mimeType: $mimeType ?? (self::detectMimeType($path) ?? 'application/octet-stream'),
            path: $path,
            contents: null,
        );
    }

    public static function fromContents(string $contents, string $filename, ?string $mimeType = null): self
    {
        if ($filename === '') {
            throw new MailerException('Ein Anhang benötigt einen Dateinamen.');
        }

        return new self(
            filename: $filename,
            mimeType: $mimeType ?? 'application/octet-stream',
            path: null,
            contents: $contents,
        );
    }

    public function isFileBased(): bool
    {
        return $this->path !== null;
    }

    public function path(): ?string
    {
        return $this->path;
    }

    public function contents(): string
    {
        if ($this->contents !== null) {
            return $this->contents;
        }

        $data = file_get_contents($this->path);
        if ($data === false) {
            throw new MailerException(sprintf('Anhang-Datei konnte nicht gelesen werden: %s', $this->path));
        }

        return $data;
    }

    private static function detectMimeType(string $path): ?string
    {
        if (!function_exists('finfo_open')) {
            return null;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo === false) {
            return null;
        }

        $mime = finfo_file($finfo, $path) ?: null;
        finfo_close($finfo);

        return $mime;
    }
}
