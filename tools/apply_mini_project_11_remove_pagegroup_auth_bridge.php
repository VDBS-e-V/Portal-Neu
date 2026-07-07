<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$stamp = 'mini-project-11-' . date('Ymd-His');

function path_join(string ...$parts): string
{
    return implode(DIRECTORY_SEPARATOR, $parts);
}

function project_path(string $relative): string
{
    global $root;
    return path_join($root, ...explode('/', $relative));
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
    $target = project_path($relative);
    $dir = dirname($target);
    if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
        throw new RuntimeException('Verzeichnis konnte nicht erstellt werden: ' . $dir);
    }
    backup_file($target, $stamp);
    if (file_put_contents($target, $content) === false) {
        throw new RuntimeException('Datei konnte nicht geschrieben werden: ' . $target);
    }
    echo 'Aktualisiert: ' . $target . PHP_EOL;
}

function list_files_recursive(string $dir): array
{
    if (!is_dir($dir)) {
        return [];
    }
    $files = [];
    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS)
    );
    foreach ($it as $file) {
        if (!$file instanceof SplFileInfo || !$file->isFile()) {
            continue;
        }
        $path = $file->getPathname();
        $normalized = str_replace('\\', '/', $path);
        if (str_contains($normalized, '/vendor/') || str_contains($normalized, '/var/archive/')) {
            continue;
        }
        if (str_contains($file->getFilename(), '.bak-')) {
            continue;
        }
        $files[] = $path;
    }
    return $files;
}

function assert_no_productive_bridge_usage_before_removal(string $root): void
{
    $needles = [
        'requirePageGroupAccess',
        'canAccessPageGroup',
        'currentUserCanAccessPageGroup',
    ];

    $dirs = [
        path_join($root, 'src'),
        path_join($root, 'config'),
        path_join($root, 'resources'),
    ];

    $allowed = str_replace('\\', '/', path_join($root, 'src', 'Security', 'AuthorizationService.php'));
    $hits = [];

    foreach ($dirs as $dir) {
        foreach (list_files_recursive($dir) as $file) {
            $normalized = str_replace('\\', '/', $file);
            if ($normalized === $allowed) {
                continue;
            }
            $content = file_get_contents($file);
            if ($content === false) {
                continue;
            }
            foreach ($needles as $needle) {
                if (str_contains($content, $needle)) {
                    $hits[] = $normalized . ': ' . $needle;
                }
            }
        }
    }

    if ($hits !== []) {
        echo "ABBRUCH: Es gibt noch produktive Aufrufe der alten PageGroup-Authorization-Bridge.\n";
        foreach ($hits as $hit) {
            echo ' - ' . $hit . PHP_EOL;
        }
        echo "Bitte diese Aufrufe erst auf Permissions umstellen, dann Mini-Projekt 11 erneut ausführen.\n";
        exit(1);
    }
}

function find_method_start_with_docblock(string $code, string $method): ?array
{
    $pattern = '/(?:\n[ \t]*\/\*\*[\s\S]*?\*\/[ \t]*\n)?\n[ \t]*(?:public|protected|private)\s+function\s+' . preg_quote($method, '/') . '\s*\(/';
    if (!preg_match($pattern, $code, $m, PREG_OFFSET_CAPTURE)) {
        return null;
    }
    $start = $m[0][1];
    $functionPos = strpos($m[0][0], 'function');
    if ($functionPos === false) {
        return null;
    }
    $functionAbs = $start + $functionPos;
    return [$start, $functionAbs];
}

function remove_method(string $code, string $method): array
{
    $match = find_method_start_with_docblock($code, $method);
    if ($match === null) {
        return [$code, false];
    }

    [$removeStart, $functionAbs] = $match;
    $braceStart = strpos($code, '{', $functionAbs);
    if ($braceStart === false) {
        throw new RuntimeException('Methodenrumpf nicht gefunden: ' . $method);
    }

    $len = strlen($code);
    $depth = 0;
    $inSingle = false;
    $inDouble = false;
    $inLineComment = false;
    $inBlockComment = false;
    $escape = false;

    for ($i = $braceStart; $i < $len; $i++) {
        $ch = $code[$i];
        $next = $i + 1 < $len ? $code[$i + 1] : '';

        if ($inLineComment) {
            if ($ch === "\n") {
                $inLineComment = false;
            }
            continue;
        }

        if ($inBlockComment) {
            if ($ch === '*' && $next === '/') {
                $inBlockComment = false;
                $i++;
            }
            continue;
        }

        if ($inSingle) {
            if ($escape) {
                $escape = false;
                continue;
            }
            if ($ch === '\\') {
                $escape = true;
                continue;
            }
            if ($ch === "'") {
                $inSingle = false;
            }
            continue;
        }

        if ($inDouble) {
            if ($escape) {
                $escape = false;
                continue;
            }
            if ($ch === '\\') {
                $escape = true;
                continue;
            }
            if ($ch === '"') {
                $inDouble = false;
            }
            continue;
        }

        if ($ch === '/' && $next === '/') {
            $inLineComment = true;
            $i++;
            continue;
        }
        if ($ch === '/' && $next === '*') {
            $inBlockComment = true;
            $i++;
            continue;
        }
        if ($ch === "'") {
            $inSingle = true;
            continue;
        }
        if ($ch === '"') {
            $inDouble = true;
            continue;
        }
        if ($ch === '{') {
            $depth++;
        } elseif ($ch === '}') {
            $depth--;
            if ($depth === 0) {
                $removeEnd = $i + 1;
                while ($removeEnd < $len && ($code[$removeEnd] === "\r" || $code[$removeEnd] === "\n")) {
                    $removeEnd++;
                }
                $new = substr($code, 0, $removeStart) . "\n" . substr($code, $removeEnd);
                return [$new, true];
            }
        }
    }

    throw new RuntimeException('Methodenende nicht gefunden: ' . $method);
}

function remove_bridge_methods(string $stamp): void
{
    $file = project_path('src/Security/AuthorizationService.php');
    if (!is_file($file)) {
        throw new RuntimeException('AuthorizationService.php fehlt: ' . $file);
    }

    $code = file_get_contents($file);
    if ($code === false) {
        throw new RuntimeException('AuthorizationService.php konnte nicht gelesen werden.');
    }

    $methods = [
        'requirePageGroupAccess',
        'canAccessPageGroup',
        'currentUserCanAccessPageGroup',
    ];

    $removed = [];
    foreach ($methods as $method) {
        [$code, $didRemove] = remove_method($code, $method);
        if ($didRemove) {
            $removed[] = $method;
        }
    }

    if ($removed !== []) {
        backup_file($file, $stamp);
        if (file_put_contents($file, $code) === false) {
            throw new RuntimeException('AuthorizationService.php konnte nicht geschrieben werden.');
        }
        echo 'AuthorizationService.php bereinigt: ' . implode(', ', $removed) . PHP_EOL;
    } else {
        echo "AuthorizationService.php: Keine alten Bridge-Methoden gefunden, nichts zu entfernen.\n";
    }
}

$checkNoBridge = <<<'PHPQA'
<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);

function mp11_files(string $dir): array
{
    if (!is_dir($dir)) {
        return [];
    }
    $files = [];
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS));
    foreach ($it as $file) {
        if (!$file instanceof SplFileInfo || !$file->isFile()) {
            continue;
        }
        $path = $file->getPathname();
        $normalized = str_replace('\\', '/', $path);
        if (str_contains($normalized, '/vendor/') || str_contains($normalized, '/var/archive/')) {
            continue;
        }
        if (str_contains($file->getFilename(), '.bak-')) {
            continue;
        }
        $files[] = $path;
    }
    return $files;
}

$authFile = $root . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Security' . DIRECTORY_SEPARATOR . 'AuthorizationService.php';
if (!is_file($authFile)) {
    fwrite(STDERR, "AuthorizationService.php fehlt.\n");
    exit(1);
}

$auth = file_get_contents($authFile);
if ($auth === false) {
    fwrite(STDERR, "AuthorizationService.php konnte nicht gelesen werden.\n");
    exit(1);
}

$errors = [];
$methodDefinitions = [
    'requirePageGroupAccess',
    'canAccessPageGroup',
    'currentUserCanAccessPageGroup',
];

foreach ($methodDefinitions as $method) {
    if (preg_match('/function\s+' . preg_quote($method, '/') . '\s*\(/', $auth)) {
        $errors[] = 'AuthorizationService enthält noch alte Bridge-Methode: ' . $method;
    }
}

$needles = $methodDefinitions;
$dirs = [
    $root . DIRECTORY_SEPARATOR . 'src',
    $root . DIRECTORY_SEPARATOR . 'config',
    $root . DIRECTORY_SEPARATOR . 'resources',
];

foreach ($dirs as $dir) {
    foreach (mp11_files($dir) as $file) {
        $content = file_get_contents($file);
        if ($content === false) {
            continue;
        }
        foreach ($needles as $needle) {
            if (str_contains($content, $needle)) {
                $rel = str_replace('\\', '/', substr($file, strlen($root) + 1));
                $errors[] = $rel . ': alte Bridge-Referenz gefunden: ' . $needle;
            }
        }
    }
}

if ($errors !== []) {
    echo "Legacy-Authorization-Bridge-Check fehlgeschlagen:\n";
    foreach (array_unique($errors) as $error) {
        echo ' - ' . $error . PHP_EOL;
    }
    exit(1);
}

echo "OK: Alte PageGroup-Authorization-Bridge ist aus produktivem Code entfernt.\n";
PHPQA;

$checkAuthMethods = <<<'PHPQA'
<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$file = $root . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Security' . DIRECTORY_SEPARATOR . 'AuthorizationService.php';

if (!is_file($file)) {
    fwrite(STDERR, "AuthorizationService.php fehlt.\n");
    exit(1);
}

$content = file_get_contents($file);
if ($content === false) {
    fwrite(STDERR, "AuthorizationService.php konnte nicht gelesen werden.\n");
    exit(1);
}

$requiredFragments = [
    'class AuthorizationService',
];

$missing = [];
foreach ($requiredFragments as $fragment) {
    if (!str_contains($content, $fragment)) {
        $missing[] = $fragment;
    }
}

if ($missing !== []) {
    echo "AuthorizationService-Struktur wirkt beschädigt. Fehlend:\n";
    foreach ($missing as $fragment) {
        echo ' - ' . $fragment . PHP_EOL;
    }
    exit(1);
}

$hasPermissionApi = false;
foreach (['requirePermission', 'hasPermission', 'can(', 'canPermission', 'hasAnyPermission'] as $fragment) {
    if (str_contains($content, $fragment)) {
        $hasPermissionApi = true;
        break;
    }
}

if (!$hasPermissionApi) {
    echo "WARN: Keine bekannte Permission-API-Fragmente gefunden. Das ist kein harter Fehler, bitte bei Browser-Fehlern prüfen.\n";
} else {
    echo "OK: AuthorizationService enthält weiterhin Permission-API-Fragmente.\n";
}
PHPQA;

$runChecks = <<<'PHPQA'
<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$php = PHP_BINARY;

$commands = [
    [$php, '-l', $root . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Security' . DIRECTORY_SEPARATOR . 'AuthorizationService.php'],
    [$php, '-l', $root . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Security' . DIRECTORY_SEPARATOR . 'RoutePermissionMap.php'],
    [$php, '-l', $root . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'services.php'],
    [$php, $root . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'qa' . DIRECTORY_SEPARATOR . 'check_no_pagegroup_authorization_bridge.php'],
    [$php, $root . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'qa' . DIRECTORY_SEPARATOR . 'check_identity_authorization_methods.php'],
];

$optional = [
    'tools/qa/run_identity_legacy_drop_checks.php',
    'tools/qa/run_identity_db_legacy_checks.php',
    'tools/qa/run_identity_service_cleanup_checks.php',
    'tools/qa/run_identity_cleanup_checks.php',
    'tools/qa/run_identity_runtime_checks.php',
];

foreach ($optional as $relative) {
    $path = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative);
    if (is_file($path)) {
        $commands[] = [$php, $path];
    }
}

$failed = false;
foreach ($commands as $cmd) {
    $display = array_map(static fn(string $part): string => '"' . $part . '"', $cmd);
    echo 'Running: ' . implode(' ', $display) . PHP_EOL;
    $escaped = array_map('escapeshellarg', $cmd);
    passthru(implode(' ', $escaped), $exitCode);
    if ($exitCode !== 0) {
        echo 'FAILED: ' . implode(' ', $display) . PHP_EOL;
        $failed = true;
    }
}

if ($failed) {
    echo "Mini-Projekt-11-Authorization-Cleanup-Checks haben Fehler gefunden.\n";
    exit(1);
}

echo "OK: Mini-Projekt-11-Authorization-Cleanup-Checks bestanden.\n";
PHPQA;

try {
    assert_no_productive_bridge_usage_before_removal($root);
    remove_bridge_methods($stamp);

    write_project_file('tools/qa/check_no_pagegroup_authorization_bridge.php', $checkNoBridge, $stamp);
    write_project_file('tools/qa/check_identity_authorization_methods.php', $checkAuthMethods, $stamp);
    write_project_file('tools/qa/run_identity_authorization_cleanup_checks.php', $runChecks, $stamp);

    echo PHP_EOL;
    echo "Mini-Projekt 11 Authorization-Bridge-Cleanup wurde angewendet.\n";
    echo "Bitte ausführen:\n";
    echo "  php -l src\\Security\\AuthorizationService.php\n";
    echo "  php tools\\qa\\check_no_pagegroup_authorization_bridge.php\n";
    echo "  php tools\\qa\\run_identity_authorization_cleanup_checks.php\n";
} catch (Throwable $e) {
    fwrite(STDERR, 'FEHLER: ' . $e->getMessage() . PHP_EOL);
    exit(1);
}
