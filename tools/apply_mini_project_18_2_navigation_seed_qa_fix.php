<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$stamp = 'mini-project-18-2-' . date('Ymd-His');

function mp18_2_path(string $relative): string
{
    global $root;
    return $root . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relative);
}

function mp18_2_backup(string $path, string $stamp): void
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

function mp18_2_write(string $relative, string $content, string $stamp): void
{
    $path = mp18_2_path($relative);
    $dir = dirname($path);
    if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
        throw new RuntimeException('Verzeichnis konnte nicht erstellt werden: ' . $dir);
    }
    mp18_2_backup($path, $stamp);
    if (file_put_contents($path, $content) === false) {
        throw new RuntimeException('Datei konnte nicht geschrieben werden: ' . $path);
    }
    echo 'Aktualisiert: ' . $relative . PHP_EOL;
}

$seedAdministration = <<<'SQL'
/*
 * Mini-Projekt 18.2
 *
 * Dieser Seed ist bewusst ein No-op.
 *
 * Die alten Administration-Menüeinträge wurden bereits durch frühere Seeds erzeugt.
 * Seit dem finalen Navigation-Permission-Modell darf dieser Seed keine Legacy-Spalten
 * wie page_group_id mehr schreiben. Permissions für vorhandene Menüeinträge werden durch
 * seed_mini_project_18_navigation_permission_integrity.sql normalisiert.
 */
SET @mini_project_18_2_seed_administration_menu_items_noop = 1;
SQL;

$checkNavigationPermissionModel = <<<'PHP'
<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);

function mp18_2_load_env(string $root): array
{
    $env = [];
    $file = $root . DIRECTORY_SEPARATOR . '.env';
    if (!is_file($file)) {
        return $env;
    }

    foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        if ((str_starts_with($value, '"') && str_ends_with($value, '"')) || (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
            $value = substr($value, 1, -1);
        }
        $env[$key] = $value;
    }

    return $env;
}

function mp18_2_pdo(string $root): PDO
{
    $env = mp18_2_load_env($root);

    $host = $env['DB_HOST'] ?? '127.0.0.1';
    $port = $env['DB_PORT'] ?? '3306';
    $db = $env['DB_DATABASE'] ?? ($env['DB_NAME'] ?? 'vdbs_sys');
    $user = $env['DB_USERNAME'] ?? ($env['DB_USER'] ?? 'root');
    $pass = $env['DB_PASSWORD'] ?? ($env['DB_PASS'] ?? '');

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci',
    ];

    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $db);
    try {
        return new PDO($dsn, $user, $pass, $options);
    } catch (PDOException $e) {
        if ($host !== 'localhost') {
            $fallback = sprintf('mysql:host=localhost;port=%s;dbname=%s;charset=utf8mb4', $port, $db);
            return new PDO($fallback, $user, $pass, $options);
        }
        throw $e;
    }
}

function mp18_2_table_exists(PDO $pdo, string $table): bool
{
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table');
    $stmt->execute(['table' => $table]);
    return (int) $stmt->fetchColumn() > 0;
}

function mp18_2_column_exists(PDO $pdo, string $table, string $column): bool
{
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table AND COLUMN_NAME = :column');
    $stmt->execute(['table' => $table, 'column' => $column]);
    return (int) $stmt->fetchColumn() > 0;
}

$pdo = mp18_2_pdo($root);
$errors = [];

if (!mp18_2_table_exists($pdo, 'pt_menu_items')) {
    $errors[] = 'Tabelle pt_menu_items fehlt.';
} else {
    if (!mp18_2_column_exists($pdo, 'pt_menu_items', 'permission_key')) {
        $errors[] = 'pt_menu_items.permission_key fehlt.';
    }
    if (mp18_2_column_exists($pdo, 'pt_menu_items', 'page_group_id')) {
        $errors[] = 'Legacy-Spalte pt_menu_items.page_group_id existiert noch.';
    }
}

if (mp18_2_table_exists($pdo, 'pt_menu_items') && mp18_2_column_exists($pdo, 'pt_menu_items', 'permission_key') && mp18_2_table_exists($pdo, 'ids_permissions')) {
    $permissionKeyColumn = mp18_2_column_exists($pdo, 'ids_permissions', 'key_name') ? 'key_name' : (mp18_2_column_exists($pdo, 'ids_permissions', 'permission_key') ? 'permission_key' : null);
    if ($permissionKeyColumn !== null) {
        $sql = sprintf(
            "SELECT COUNT(*)\n             FROM pt_menu_items mi\n             LEFT JOIN ids_permissions p ON p.%s COLLATE utf8mb4_unicode_ci = mi.permission_key COLLATE utf8mb4_unicode_ci\n             WHERE mi.permission_key IS NOT NULL\n               AND mi.permission_key <> ''\n               AND p.id IS NULL",
            $permissionKeyColumn
        );
        $missing = (int) $pdo->query($sql)->fetchColumn();
        if ($missing > 0) {
            $errors[] = 'pt_menu_items enthält permission_key-Werte ohne passende ids_permissions-Einträge: ' . $missing;
        }
    }
}

if ($errors !== []) {
    echo "Navigation-Permission-Modell-Check fehlgeschlagen:\n";
    foreach ($errors as $error) {
        echo ' - ' . $error . "\n";
    }
    exit(1);
}

echo "OK: Navigation nutzt pt_menu_items.permission_key ohne Legacy-Spalten.\n";
PHP;

$checkNavigationCode = <<<'PHP'
<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$scan = [
    'src/Repository/AuthorizedMenuRepository.php',
    'src/Navigation',
    'resources/views/layouts',
    'resources/views/partials',
];

$legacyPatterns = [
    'page_group_id',
    'pt_page_groups',
    'pt_permission_group_page_group_access',
    'ids_permission_groups',
    'ids_user_permission_groups',
    'ids_person_permission_groups',
    'permission_group_id',
];

$errors = [];

foreach ($scan as $relative) {
    $path = $root . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relative);
    if (is_file($path)) {
        $files = [$path];
    } elseif (is_dir($path)) {
        $files = [];
        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS));
        foreach ($it as $file) {
            if ($file->isFile() && strtolower($file->getExtension()) === 'php') {
                $files[] = $file->getPathname();
            }
        }
    } else {
        continue;
    }

    foreach ($files as $file) {
        $content = file_get_contents($file);
        if ($content === false) {
            continue;
        }
        foreach ($legacyPatterns as $pattern) {
            if (stripos($content, $pattern) !== false) {
                $rel = str_replace($root . DIRECTORY_SEPARATOR, '', $file);
                $errors[] = $rel . ': enthält Legacy-Navigationsbegriff ' . $pattern;
            }
        }
    }
}

if ($errors !== []) {
    echo "Navigation-Code-Check fehlgeschlagen:\n";
    foreach ($errors as $error) {
        echo ' - ' . $error . "\n";
    }
    exit(1);
}

echo "OK: Navigation-Code nutzt keine Legacy-Gruppen-/PageGroup-Begriffe produktiv.\n";
PHP;

$checkNavigationSeeds = <<<'PHP'
<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$seedDir = $root . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'seeds';

$legacyNavigationPatterns = [
    'page_group_id',
    'pt_page_groups',
    'pt_permission_group_page_group_access',
];

$ignoredFiles = [
    'seed_mini_project_7_clear_page_group_menu_links.sql',
    'seed_mini_project_8_deprecate_legacy_pagegroup_db.sql',
    'seed_mini_project_12_identity_cleanup_markers.sql',
    'seed_mini_project_13_deprecate_legacy_identity_permission_groups.sql',
];

$errors = [];

foreach (glob($seedDir . DIRECTORY_SEPARATOR . '*.sql') ?: [] as $file) {
    $name = basename($file);
    if (in_array($name, $ignoredFiles, true)) {
        continue;
    }

    $content = file_get_contents($file);
    if ($content === false) {
        continue;
    }

    $lines = preg_split('/\R/', $content) ?: [];
    foreach ($lines as $index => $line) {
        $trimmed = trim($line);
        if ($trimmed === '' || str_starts_with($trimmed, '--') || str_starts_with($trimmed, '/*') || str_starts_with($trimmed, '*')) {
            continue;
        }
        foreach ($legacyNavigationPatterns as $pattern) {
            if (stripos($line, $pattern) !== false) {
                $errors[] = sprintf('%s:%d: enthält Legacy-Navigationsfeld/-tabelle %s', $name, $index + 1, $pattern);
            }
        }
    }
}

if ($errors !== []) {
    echo "Aktive Seed-Dateien enthalten noch PageGroup-Navigationsverweise:\n";
    foreach ($errors as $error) {
        echo ' - ' . $error . "\n";
    }
    exit(1);
}

echo "OK: Aktive Seeds enthalten keine Legacy-Navigationsverweise mehr.\n";
PHP;

$runChecks = <<<'PHP'
<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$php = PHP_BINARY;

$commands = [
    ['-l', $root . '/config/routes.php'],
    ['-l', $root . '/config/services.php'],
    [$root . '/tools/qa/check_navigation_permission_model.php'],
    [$root . '/tools/qa/check_navigation_code_permission_model.php'],
    [$root . '/tools/qa/check_navigation_active_seeds.php'],
    [$root . '/tools/qa/run_identity_admin_safety_checks.php'],
];

$failed = false;

foreach ($commands as $args) {
    $cmd = array_merge([$php], $args);
    $display = implode(' ', array_map(static fn (string $part): string => '"' . $part . '"', $cmd));
    echo 'Running: ' . $display . PHP_EOL;
    passthru($display, $exitCode);
    if ($exitCode !== 0) {
        echo 'FAILED: ' . $display . PHP_EOL;
        $failed = true;
    }
}

if ($failed) {
    echo "Mini-Projekt-18-Navigation-Permission-Checks haben Fehler gefunden.\n";
    exit(1);
}

echo "OK: Mini-Projekt-18-Navigation-Permission-Checks bestanden.\n";
PHP;

$verifySql = <<<'SQL'
SELECT 'pt_menu_items.permission_key exists' AS check_name,
       COUNT(*) AS ok
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'pt_menu_items'
  AND COLUMN_NAME = 'permission_key';

SELECT 'pt_menu_items.page_group_id absent' AS check_name,
       CASE WHEN COUNT(*) = 0 THEN 1 ELSE 0 END AS ok
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'pt_menu_items'
  AND COLUMN_NAME = 'page_group_id';
SQL;

mp18_2_write('database/seeds/seed_administration_menu_items.sql', $seedAdministration, $stamp);
mp18_2_write('tools/qa/check_navigation_permission_model.php', $checkNavigationPermissionModel, $stamp);
mp18_2_write('tools/qa/check_navigation_code_permission_model.php', $checkNavigationCode, $stamp);
mp18_2_write('tools/qa/check_navigation_active_seeds.php', $checkNavigationSeeds, $stamp);
mp18_2_write('tools/qa/run_identity_navigation_permission_checks.php', $runChecks, $stamp);
mp18_2_write('database/sql/verify_mini_project_18_navigation_permissions.sql', $verifySql, $stamp);

echo PHP_EOL;
echo "Mini-Projekt 18.2 Navigation-Seed-/QA-Fix wurde angewendet." . PHP_EOL;
echo "Bitte ausführen:" . PHP_EOL;
echo "  php bin\\console seed" . PHP_EOL;
echo "  php tools\\qa\\check_navigation_permission_model.php" . PHP_EOL;
echo "  php tools\\qa\\check_navigation_code_permission_model.php" . PHP_EOL;
echo "  php tools\\qa\\check_navigation_active_seeds.php" . PHP_EOL;
echo "  php tools\\qa\\run_identity_navigation_permission_checks.php" . PHP_EOL;
