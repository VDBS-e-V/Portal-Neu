<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$stamp = 'mini-project-16-' . date('Ymd-His');

function mp16_path(string $relative): string
{
    global $root;
    return $root . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relative);
}

function mp16_backup(string $file, string $stamp): void
{
    if (!is_file($file)) {
        return;
    }
    $backup = $file . '.bak-' . $stamp;
    if (!copy($file, $backup)) {
        throw new RuntimeException('Backup fehlgeschlagen: ' . $file);
    }
    echo 'Backup: ' . $backup . PHP_EOL;
}

function mp16_write(string $relative, string $content, string $stamp): void
{
    $file = mp16_path($relative);
    $dir = dirname($file);
    if (!is_dir($dir) && !mkdir($dir, 0777, true) && !is_dir($dir)) {
        throw new RuntimeException('Verzeichnis konnte nicht erstellt werden: ' . $dir);
    }
    mp16_backup($file, $stamp);
    if (file_put_contents($file, $content) === false) {
        throw new RuntimeException('Datei konnte nicht geschrieben werden: ' . $file);
    }
    echo 'Aktualisiert: ' . $relative . PHP_EOL;
}

function mp16_ensure_use(string $content, string $use): string
{
    if (str_contains($content, $use)) {
        return $content;
    }

    $lines = preg_split('/\R/', $content);
    if ($lines === false) {
        return $content;
    }

    $insertAt = null;
    foreach ($lines as $i => $line) {
        if (str_starts_with(trim($line), 'use ')) {
            $insertAt = $i + 1;
        }
    }

    if ($insertAt === null) {
        array_splice($lines, 1, 0, [$use]);
    } else {
        array_splice($lines, $insertAt, 0, [$use]);
    }

    return implode(PHP_EOL, $lines);
}

$servicesFile = mp16_path('config/services.php');
if (!is_file($servicesFile)) {
    throw new RuntimeException('config/services.php fehlt.');
}
$services = file_get_contents($servicesFile);
if ($services === false) {
    throw new RuntimeException('config/services.php konnte nicht gelesen werden.');
}

$services = mp16_ensure_use($services, 'use App\\Security\\IdentityAdminSafetyService;');
$services = mp16_ensure_use($services, 'use App\\Repository\\IdentityAdministrationRepository;');
$services = mp16_ensure_use($services, 'use PDO;');

$factory = <<<'PHPFACTORY'
    IdentityAdminSafetyService::class => static function (Container $container): IdentityAdminSafetyService {
        return new IdentityAdminSafetyService(
            $container->get(IdentityAdministrationRepository::class),
            $container->get(PDO::class)
        );
    },
PHPFACTORY;

$pattern = '/\n\s*IdentityAdminSafetyService::class\s*=>\s*static function\s*\(Container \$container\)\s*:\s*IdentityAdminSafetyService\s*\{.*?\n\s*\},/s';
if (preg_match($pattern, $services) === 1) {
    $services = preg_replace($pattern, PHP_EOL . $factory, $services, 1) ?? $services;
} elseif (str_contains($services, 'return [')) {
    $pos = strrpos($services, '];');
    if ($pos === false) {
        throw new RuntimeException('Ende des Service-Arrays nicht gefunden.');
    }
    $services = substr($services, 0, $pos) . PHP_EOL . $factory . PHP_EOL . substr($services, $pos);
} else {
    throw new RuntimeException('Service-Array in config/services.php nicht erkannt.');
}

mp16_backup($servicesFile, $stamp);
if (file_put_contents($servicesFile, $services) === false) {
    throw new RuntimeException('config/services.php konnte nicht geschrieben werden.');
}
echo 'Aktualisiert: config/services.php' . PHP_EOL;

// Die übrigen Projektdateien liegen im ZIP bereits an ihrer Zielposition. Damit
// spätere Extraktionsvarianten trotzdem robust sind, werden fehlende Dateien mit
// einem klaren Hinweis gemeldet.
foreach ([
    'src/Security/IdentityAdminSafetyService.php',
    'tools/qa/check_admin_safety_service.php',
    'tools/qa/check_admin_safety_wiring.php',
    'tools/qa/run_identity_admin_safety_checks.php',
    'database/sql/verify_mini_project_16_admin_safety.sql',
] as $relative) {
    if (!is_file(mp16_path($relative))) {
        throw new RuntimeException('Datei fehlt nach dem Entpacken: ' . $relative);
    }
}

echo PHP_EOL;
echo 'Mini-Projekt 16 Admin-Safety-Guard wurde angewendet.' . PHP_EOL;
echo 'Bitte ausführen:' . PHP_EOL;
echo '  php -l src\\Security\\IdentityAdminSafetyService.php' . PHP_EOL;
echo '  php -l config\\services.php' . PHP_EOL;
echo '  php tools\\qa\\check_admin_safety_wiring.php' . PHP_EOL;
echo '  php tools\\qa\\check_admin_safety_service.php' . PHP_EOL;
echo '  php tools\\qa\\run_identity_admin_safety_checks.php' . PHP_EOL;
