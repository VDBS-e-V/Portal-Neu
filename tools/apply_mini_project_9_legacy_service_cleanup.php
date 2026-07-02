<?php

declare(strict_types=1);

/**
 * Mini-Projekt 9: Legacy-Service-/Klassenreferenzen entfernen.
 *
 * Entfernt PageGroupRepository, PageGroupAccessRepository und den alten
 * AdminSafetyService aus config/services.php und archiviert den alten
 * src/Security/AdminSafetyService.php, falls vorhanden.
 */

$root = dirname(__DIR__);
$stamp = 'mini-project-9-' . date('Ymd-His');
$archiveDir = $root . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'archive' . DIRECTORY_SEPARATOR . 'mini-project-9-legacy-services-' . $stamp;

function rel_path(string $root, string $path): string
{
    $root = rtrim(str_replace('\\', '/', $root), '/');
    $path = str_replace('\\', '/', $path);
    if (str_starts_with($path, $root . '/')) {
        return substr($path, strlen($root) + 1);
    }
    return $path;
}

function backup_file(string $path, string $stamp): void
{
    if (!is_file($path)) {
        return;
    }
    $backup = $path . '.bak-' . $stamp;
    if (!copy($path, $backup)) {
        throw new RuntimeException('Backup konnte nicht erstellt werden: ' . $backup);
    }
    echo 'Backup: ' . $backup . PHP_EOL;
}

function ensure_dir(string $dir): void
{
    if (!is_dir($dir) && !mkdir($dir, 0777, true) && !is_dir($dir)) {
        throw new RuntimeException('Verzeichnis konnte nicht erstellt werden: ' . $dir);
    }
}

function archive_file(string $root, string $archiveDir, string $relative, array &$manifest): void
{
    $source = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative);
    if (!is_file($source)) {
        echo 'Schon nicht vorhanden: ' . $relative . PHP_EOL;
        return;
    }

    $target = $archiveDir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative);
    ensure_dir(dirname($target));

    if (!rename($source, $target)) {
        throw new RuntimeException('Archivierung fehlgeschlagen: ' . $relative);
    }

    $manifest[] = [
        'source' => $relative,
        'archive' => rel_path($root, $target),
    ];

    echo 'Archiviert: ' . $relative . ' -> ' . rel_path($root, $target) . PHP_EOL;
}

function remove_use_line(string $content, string $fqcn): string
{
    $quoted = preg_quote('use ' . $fqcn . ';', '/');
    return preg_replace('/^\s*' . $quoted . '\s*\R/m', '', $content) ?? $content;
}

function remove_service_block(string $content, string $className): string
{
    $needle = $className . '::class =>';

    while (($pos = strpos($content, $needle)) !== false) {
        $lineStart = strrpos(substr($content, 0, $pos), "\n");
        $start = $lineStart === false ? 0 : $lineStart + 1;

        $brace = strpos($content, '{', $pos);
        if ($brace === false) {
            break;
        }

        $depth = 0;
        $len = strlen($content);
        $endBrace = null;
        for ($i = $brace; $i < $len; $i++) {
            $ch = $content[$i];
            if ($ch === '{') {
                $depth++;
            } elseif ($ch === '}') {
                $depth--;
                if ($depth === 0) {
                    $endBrace = $i;
                    break;
                }
            }
        }

        if ($endBrace === null) {
            break;
        }

        $end = $endBrace + 1;
        while ($end < $len && preg_match('/\s/', $content[$end]) === 1) {
            $end++;
        }
        if ($end < $len && $content[$end] === ',') {
            $end++;
        }
        while ($end < $len && preg_match('/[ \t\r\n]/', $content[$end]) === 1) {
            $end++;
        }

        $content = substr($content, 0, $start) . substr($content, $end);
    }

    return $content;
}

function normalize_blank_lines(string $content): string
{
    $content = preg_replace("/\n{3,}/", "\n\n", $content) ?? $content;
    return $content;
}

function write_file(string $path, string $content, string $stamp): void
{
    ensure_dir(dirname($path));
    backup_file($path, $stamp);
    if (file_put_contents($path, $content) === false) {
        throw new RuntimeException('Datei konnte nicht geschrieben werden: ' . $path);
    }
    echo 'Aktualisiert: ' . $path . PHP_EOL;
}

$servicesFile = $root . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'services.php';
if (!is_file($servicesFile)) {
    throw new RuntimeException('config/services.php nicht gefunden.');
}

$services = file_get_contents($servicesFile);
if ($services === false) {
    throw new RuntimeException('config/services.php konnte nicht gelesen werden.');
}

$services = remove_use_line($services, 'App\\Repository\\PageGroupRepository');
$services = remove_use_line($services, 'App\\Repository\\PageGroupAccessRepository');
$services = remove_use_line($services, 'App\\Security\\AdminSafetyService');

$services = remove_service_block($services, 'PageGroupRepository');
$services = remove_service_block($services, 'PageGroupAccessRepository');
$services = remove_service_block($services, 'AdminSafetyService');
$services = normalize_blank_lines($services);

write_file($servicesFile, $services, $stamp);

ensure_dir($archiveDir);
$manifest = [];
archive_file($root, $archiveDir, 'src/Security/AdminSafetyService.php', $manifest);

$manifestFile = $archiveDir . DIRECTORY_SEPARATOR . 'manifest.json';
file_put_contents($manifestFile, json_encode([
    'created_at' => date(DATE_ATOM),
    'project' => 'mini-project-9-legacy-service-cleanup',
    'files' => $manifest,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
echo 'Manifest: ' . rel_path($root, $manifestFile) . PHP_EOL;

$checkLegacyServiceReferences = <<<'PHP_QA'
<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$errors = [];

function file_text(string $path): string
{
    if (!is_file($path)) {
        return '';
    }
    $content = file_get_contents($path);
    return $content === false ? '' : $content;
}

$servicesFile = $root . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'services.php';
$services = file_text($servicesFile);

$forbiddenServices = [
    'PageGroupRepository' => '/\bPageGroupRepository\b/',
    'PageGroupAccessRepository' => '/\bPageGroupAccessRepository\b/',
    'old AdminSafetyService' => '/\bAdminSafetyService\b/',
];

foreach ($forbiddenServices as $label => $pattern) {
    if (preg_match($pattern, $services)) {
        $errors[] = 'config/services.php enthält noch Legacy-Service-Referenz: ' . $label;
    }
}

$forbiddenFiles = [
    'src/Repository/PageGroupRepository.php',
    'src/Repository/PageGroupAccessRepository.php',
    'src/Security/AdminSafetyService.php',
];

foreach ($forbiddenFiles as $relative) {
    $path = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative);
    if (is_file($path)) {
        $errors[] = 'Legacy-Datei ist noch produktiv vorhanden: ' . $relative;
    }
}

$identityAdminSafety = $root . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Security' . DIRECTORY_SEPARATOR . 'IdentityAdminSafetyService.php';
if (!is_file($identityAdminSafety)) {
    $errors[] = 'IdentityAdminSafetyService.php fehlt. Der neue Safety-Service muss erhalten bleiben.';
}

if ($errors !== []) {
    echo "Legacy-Service-Check fehlgeschlagen:\n";
    foreach ($errors as $error) {
        echo ' - ' . $error . "\n";
    }
    exit(1);
}

echo "OK: Alte PageGroup-Service- und AdminSafetyService-Referenzen sind aus der produktiven Services-Konfiguration entfernt.\n";
PHP_QA;

$runIdentityServiceCleanupChecks = <<<'PHP_QA'
<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$php = PHP_BINARY;

$commands = [
    [$php, '-l', $root . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'services.php'],
    [$php, $root . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'qa' . DIRECTORY_SEPARATOR . 'run_identity_db_legacy_checks.php'],
    [$php, $root . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'qa' . DIRECTORY_SEPARATOR . 'check_legacy_service_references.php'],
];

$failed = false;

foreach ($commands as $command) {
    $display = implode(' ', array_map(static fn (string $part): string => '"' . $part . '"', $command));
    echo 'Running: ' . $display . PHP_EOL;

    $escaped = array_map('escapeshellarg', $command);
    passthru(implode(' ', $escaped), $code);

    if ($code !== 0) {
        echo 'FAILED: ' . $display . PHP_EOL;
        $failed = true;
    }
}

if ($failed) {
    echo "Mini-Projekt-9-Service-Cleanup-Checks haben Fehler gefunden.\n";
    exit(1);
}

echo "OK: Mini-Projekt-9-Service-Cleanup-Checks bestanden.\n";
PHP_QA;

$scanServiceResidue = <<<'PHP_QA'
<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$needles = [
    'PageGroupRepository',
    'PageGroupAccessRepository',
    'AdminSafetyService',
    'pt_page_groups',
    'pt_permission_group_page_group_access',
    'page_group_id',
];

$directories = [
    'config',
    'src',
    'resources/views',
    'tools',
    'database/seeds',
];

$ignoredFragments = [
    '.bak-',
    '/var/archive/',
    '\\var\\archive\\',
    'restore_mini_project_',
    'scan_legacy_service_residue.php',
];

$matches = [];

foreach ($directories as $directory) {
    $base = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $directory);
    if (!is_dir($base)) {
        continue;
    }

    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base, FilesystemIterator::SKIP_DOTS));
    foreach ($iterator as $file) {
        if (!$file instanceof SplFileInfo || !$file->isFile()) {
            continue;
        }
        $path = $file->getPathname();
        $normalized = str_replace('\\', '/', $path);
        foreach ($ignoredFragments as $fragment) {
            if (str_contains($normalized, str_replace('\\', '/', $fragment))) {
                continue 2;
            }
        }
        $content = file_get_contents($path);
        if ($content === false) {
            continue;
        }
        $lines = preg_split('/\R/', $content) ?: [];
        foreach ($lines as $i => $line) {
            foreach ($needles as $needle) {
                if (stripos($line, $needle) !== false) {
                    $relative = substr($normalized, strlen(str_replace('\\', '/', $root)) + 1);
                    $matches[] = $relative . ':' . ($i + 1) . ': ' . trim($line);
                    break;
                }
            }
        }
    }
}

if ($matches === []) {
    echo "OK: Keine Legacy-Service-/PageGroup-Reste in produktiven Suchpfaden gefunden.\n";
    exit(0);
}

echo "Legacy-Reste in produktiven Suchpfaden gefunden:\n";
foreach ($matches as $match) {
    echo ' - ' . $match . "\n";
}
echo "\nHinweis: Treffer in Migrationshistorie oder Archivdateien sind absichtlich nicht Teil dieser Suche.\n";
exit(1);
PHP_QA;

write_file($root . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'qa' . DIRECTORY_SEPARATOR . 'check_legacy_service_references.php', $checkLegacyServiceReferences, $stamp);
write_file($root . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'qa' . DIRECTORY_SEPARATOR . 'run_identity_service_cleanup_checks.php', $runIdentityServiceCleanupChecks, $stamp);
write_file($root . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'qa' . DIRECTORY_SEPARATOR . 'scan_legacy_service_residue.php', $scanServiceResidue, $stamp);

echo PHP_EOL;
echo 'Mini-Projekt 9 Legacy-Service-Cleanup wurde angewendet.' . PHP_EOL;
echo 'Bitte ausführen:' . PHP_EOL;
echo '  php -l config\\services.php' . PHP_EOL;
echo '  php tools\\qa\\check_legacy_service_references.php' . PHP_EOL;
echo '  php tools\\qa\\run_identity_service_cleanup_checks.php' . PHP_EOL;
echo PHP_EOL;
echo 'Optionaler Bericht:' . PHP_EOL;
echo '  php tools\\qa\\scan_legacy_service_residue.php' . PHP_EOL;
