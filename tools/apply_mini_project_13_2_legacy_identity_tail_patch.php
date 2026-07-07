<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$stamp = 'mini-project-13-2-' . date('Ymd-His');

function project_path(string $relative): string
{
    global $root;
    return $root . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relative);
}

function backup_file(string $path, string $stamp): void
{
    if (!is_file($path)) {
        return;
    }

    $backup = $path . '.bak-' . $stamp;
    if (!copy($path, $backup)) {
        throw new RuntimeException('Backup fehlgeschlagen: ' . $path);
    }
    echo 'Backup: ' . $backup . PHP_EOL;
}

function write_project_file(string $relative, string $content, string $stamp): void
{
    $path = project_path($relative);
    $dir = dirname($path);
    if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
        throw new RuntimeException('Verzeichnis konnte nicht erstellt werden: ' . $dir);
    }

    backup_file($path, $stamp);

    if (file_put_contents($path, $content) === false) {
        throw new RuntimeException('Datei konnte nicht geschrieben werden: ' . $path);
    }

    echo 'Aktualisiert: ' . $relative . PHP_EOL;
}

function read_required(string $relative): string
{
    $path = project_path($relative);
    if (!is_file($path)) {
        throw new RuntimeException('Datei fehlt: ' . $relative);
    }

    $content = file_get_contents($path);
    if ($content === false) {
        throw new RuntimeException('Datei konnte nicht gelesen werden: ' . $relative);
    }

    return $content;
}

function write_existing(string $relative, string $content, string $stamp): void
{
    $path = project_path($relative);
    backup_file($path, $stamp);
    if (file_put_contents($path, $content) === false) {
        throw new RuntimeException('Datei konnte nicht geschrieben werden: ' . $relative);
    }
    echo 'Aktualisiert: ' . $relative . PHP_EOL;
}

function remove_lines_containing(string $content, string $needle): string
{
    $lines = preg_split('/\R/', $content);
    if ($lines === false) {
        return $content;
    }

    $kept = [];
    foreach ($lines as $line) {
        if (str_contains($line, $needle)) {
            continue;
        }
        $kept[] = $line;
    }

    return implode(PHP_EOL, $kept);
}

function remove_if_blocks_mentioning_table(string $content, string $table): string
{
    $offset = 0;

    while (($tablePos = strpos($content, $table, $offset)) !== false) {
        $before = substr($content, 0, $tablePos);
        $ifPos = strrpos($before, 'if');

        if ($ifPos === false || ($tablePos - $ifPos) > 300) {
            $offset = $tablePos + strlen($table);
            continue;
        }

        $between = substr($content, $ifPos, $tablePos - $ifPos);
        if (!str_contains($between, 'tableExists')) {
            $offset = $tablePos + strlen($table);
            continue;
        }

        $bracePos = strpos($content, '{', $tablePos);
        if ($bracePos === false) {
            $offset = $tablePos + strlen($table);
            continue;
        }

        $level = 0;
        $end = null;
        $length = strlen($content);
        for ($i = $bracePos; $i < $length; $i++) {
            $char = $content[$i];
            if ($char === '{') {
                $level++;
            } elseif ($char === '}') {
                $level--;
                if ($level === 0) {
                    $end = $i + 1;
                    break;
                }
            }
        }

        if ($end === null) {
            $offset = $tablePos + strlen($table);
            continue;
        }

        $start = $ifPos;
        while ($start > 0 && $content[$start - 1] !== "\n" && $content[$start - 1] !== "\r") {
            $start--;
        }

        while ($end < $length && ($content[$end] === "\r" || $content[$end] === "\n")) {
            $end++;
        }

        $content = substr($content, 0, $start) . substr($content, $end);
        $offset = $start;
    }

    return $content;
}

// 1) EntityAuditRepository: alte ids_person_permission_groups-Audit-Zielzeile entfernen.
$entityAudit = read_required('src/Repository/EntityAuditRepository.php');
$entityAudit = preg_replace(
    '/^\h*\[\s*[\'\"]ids_person_permission_groups[\'\"]\s*,\s*\$groupId\s*\]\s*,?\h*\R/m',
    '',
    $entityAudit
) ?? $entityAudit;
$entityAudit = remove_lines_containing($entityAudit, 'ids_person_permission_groups');
write_existing('src/Repository/EntityAuditRepository.php', $entityAudit, $stamp);

// 2) PersonErasureRepository: alte Löschung aus ids_person_permission_groups entfernen.
$erasure = read_required('src/Repository/PersonErasureRepository.php');
$erasure = remove_if_blocks_mentioning_table($erasure, 'ids_person_permission_groups');
$erasure = remove_lines_containing($erasure, 'ids_person_permission_groups');
write_existing('src/Repository/PersonErasureRepository.php', $erasure, $stamp);

// 3) QA-Tools ohne Installer-Quelldatei-Abhängigkeiten direkt schreiben.
$tailCheck = <<<'PHPFILE'
<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$files = [
    'src/Repository/EntityAuditRepository.php',
    'src/Repository/PersonErasureRepository.php',
];

$errors = [];
foreach ($files as $relative) {
    $path = $root . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relative);
    if (!is_file($path)) {
        $errors[] = $relative . ' fehlt.';
        continue;
    }
    $content = (string) file_get_contents($path);
    if (str_contains($content, 'ids_person_permission_groups')) {
        $errors[] = $relative . ' enthält noch ids_person_permission_groups.';
    }
}

if ($errors !== []) {
    echo "Mini-Projekt-13.2-Tail-Cleanup fehlgeschlagen:\n";
    foreach ($errors as $error) {
        echo ' - ' . $error . "\n";
    }
    exit(1);
}

echo "OK: Mini-Projekt-13.2-Tail-Cleanup ist vollständig.\n";
PHPFILE;
write_project_file('tools/qa/check_mini_project_13_2_tail_cleanup.php', $tailCheck, $stamp);
// Kompatibilität: bestehender Runner ruft teilweise noch den 13.1-Namen auf.
write_project_file('tools/qa/check_mini_project_13_1_tail_cleanup.php', $tailCheck, $stamp);

$productiveCheck = <<<'PHPFILE'
<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$scanRoots = [
    'src',
    'config',
    'resources/views',
];

$tokens = [
    'ids_permission_groups',
    'ids_user_permission_groups',
    'ids_person_permission_groups',
    'PermissionGroupRepository',
    'PersonPermissionGroupRepository',
    'permission_group_id',
];

$allowedRelativeFiles = [
    // Keine produktiven Ausnahmen mehr. Historische Dateien liegen in var/archive und werden hier nicht gescannt.
];

$hits = [];

foreach ($scanRoots as $scanRoot) {
    $base = $root . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $scanRoot);
    if (!is_dir($base)) {
        continue;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($base, FilesystemIterator::SKIP_DOTS)
    );

    foreach ($iterator as $file) {
        if (!$file instanceof SplFileInfo || !$file->isFile()) {
            continue;
        }

        $path = $file->getPathname();
        $extension = strtolower($file->getExtension());
        if (!in_array($extension, ['php', 'phtml', 'inc'], true)) {
            continue;
        }

        $relative = str_replace($root . DIRECTORY_SEPARATOR, '', $path);
        $relative = str_replace(DIRECTORY_SEPARATOR, '/', $relative);
        if (in_array($relative, $allowedRelativeFiles, true)) {
            continue;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES);
        if ($lines === false) {
            continue;
        }

        foreach ($lines as $lineNumber => $line) {
            foreach ($tokens as $token) {
                if (str_contains($line, $token)) {
                    $hits[] = sprintf('%s:%d: %s: %s', $relative, $lineNumber + 1, $token, trim($line));
                }
            }
        }
    }
}

if ($hits !== []) {
    echo "Produktive Legacy-Identity-PermissionGroup-Verweise gefunden:\n";
    foreach ($hits as $hit) {
        echo ' - ' . $hit . "\n";
    }
    exit(1);
}

echo "OK: Keine produktiven Legacy-Identity-PermissionGroup-Codeverweise gefunden.\n";
PHPFILE;
write_project_file('tools/qa/check_no_productive_legacy_identity_permission_group_usage.php', $productiveCheck, $stamp);

$runner = <<<'PHPFILE'
<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$php = PHP_BINARY;

$commands = [
    [$php, '-l', $root . '/config/services.php'],
    [$php, '-l', $root . '/src/Repository/PersonRepository.php'],
    [$php, '-l', $root . '/src/Repository/VerwaltungStatsRepository.php'],
    [$php, '-l', $root . '/src/Repository/EntityAuditRepository.php'],
    [$php, '-l', $root . '/src/Repository/PersonErasureRepository.php'],
    [$php, '-l', $root . '/src/Http/Controller/Verwaltung/PersonenController.php'],
    [$php, '-l', $root . '/src/Http/Controller/Verwaltung/EntityAuditController.php'],
    [$php, $root . '/tools/qa/run_identity_integrity_checks.php'],
    [$php, $root . '/tools/qa/check_identity_permission_group_repositories_archived.php'],
    [$php, $root . '/tools/qa/check_mini_project_13_2_tail_cleanup.php'],
    [$php, $root . '/tools/qa/check_no_productive_legacy_identity_permission_group_usage.php'],
];

$failed = false;
foreach ($commands as $command) {
    $display = implode(' ', array_map(static fn (string $part): string => '"' . $part . '"', $command));
    echo 'Running: ' . $display . PHP_EOL;
    $escaped = implode(' ', array_map('escapeshellarg', $command));
    passthru($escaped, $exitCode);
    if ($exitCode !== 0) {
        echo 'FAILED: ' . $display . PHP_EOL;
        $failed = true;
    }
}

if ($failed) {
    echo "Mini-Projekt-13-Identity-PermissionGroup-Cleanup-Checks haben Fehler gefunden.\n";
    exit(1);
}

echo "OK: Mini-Projekt-13-Identity-PermissionGroup-Cleanup-Checks bestanden.\n";
PHPFILE;
write_project_file('tools/qa/run_identity_permission_group_cleanup_checks.php', $runner, $stamp);

$remaining = [];
foreach (['src/Repository/EntityAuditRepository.php', 'src/Repository/PersonErasureRepository.php'] as $relative) {
    $content = read_required($relative);
    if (str_contains($content, 'ids_person_permission_groups')) {
        $remaining[] = $relative;
    }
}

if ($remaining !== []) {
    echo PHP_EOL . 'WARN: Folgende Dateien enthalten noch ids_person_permission_groups:' . PHP_EOL;
    foreach ($remaining as $relative) {
        echo ' - ' . $relative . PHP_EOL;
    }
    echo 'Bitte Dateien prüfen; der QA-Check wird fehlschlagen.' . PHP_EOL;
}

echo PHP_EOL . 'Mini-Projekt 13.2 Legacy-Identity-Tail-Patch wurde angewendet.' . PHP_EOL;
echo 'Bitte ausführen:' . PHP_EOL;
echo '  php -l src\Repository\EntityAuditRepository.php' . PHP_EOL;
echo '  php -l src\Repository\PersonErasureRepository.php' . PHP_EOL;
echo '  php tools\qa\check_mini_project_13_2_tail_cleanup.php' . PHP_EOL;
echo '  php tools\qa\check_no_productive_legacy_identity_permission_group_usage.php' . PHP_EOL;
echo '  php tools\qa\run_identity_permission_group_cleanup_checks.php' . PHP_EOL;
