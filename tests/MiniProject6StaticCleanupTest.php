<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class MiniProject6StaticCleanupTest extends TestCase
{
    public function testKnownVerwaltungControllersDoNotUsePageGroupAccess(): void
    {
        $root = dirname(__DIR__);
        $files = [
            'src/Http/Controller/Verwaltung/VerwaltungController.php',
            'src/Http/Controller/Verwaltung/PersonenController.php',
            'src/Http/Controller/Verwaltung/AuditLogController.php',
            'src/Http/Controller/Verwaltung/EntityAuditController.php',
            'src/Http/Controller/Verwaltung/DatenschutzController.php',
            'src/Http/Controller/Verwaltung/EinladungenController.php',
            'src/Http/Controller/Verwaltung/SchulverzeichnisController.php',
            'src/Http/Controller/Verwaltung/GruppenController.php',
            'src/Http/Controller/Verwaltung/BerechtigungenController.php',
        ];

        foreach ($files as $relative) {
            $path = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative);
            if (!is_file($path)) {
                continue;
            }
            $content = (string) file_get_contents($path);
            self::assertStringNotContainsString('requirePageGroupAccess(', $content, $relative);
            self::assertStringNotContainsString('canAccessPageGroup(', $content, $relative);
        }
    }
}
