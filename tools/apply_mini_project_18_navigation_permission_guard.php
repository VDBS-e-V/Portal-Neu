<?php

declare(strict_types=1);

/**
 * Mini-Projekt 18: Navigation-/Menürechte final vereinheitlichen.
 *
 * Dieses Tool installiert nur Seeds und QA-Tools. Es überschreibt keine
 * produktiven Navigation-Klassen, damit bestehende Layout-/Renderer-Details
 * nicht zerstört werden. Die QA erzwingt stattdessen den Zielzustand:
 * pt_menu_items.permission_key ist die einzige Berechtigungsquelle.
 */

$root = dirname(__DIR__);
$stamp = 'mini-project-18-' . date('Ymd-His');

function mp18_write_project_file(string $root, string $relativePath, string $content, string $stamp): void
{
    $target = $root . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativePath);
    $dir = dirname($target);
    if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
        throw new RuntimeException('Verzeichnis konnte nicht erstellt werden: ' . $dir);
    }

    if (is_file($target)) {
        $backup = $target . '.bak-' . $stamp;
        if (!copy($target, $backup)) {
            throw new RuntimeException('Backup konnte nicht erstellt werden: ' . $backup);
        }
        echo "Backup: {$backup}\n";
    }

    if (file_put_contents($target, $content) === false) {
        throw new RuntimeException('Datei konnte nicht geschrieben werden: ' . $target);
    }

    echo "Aktualisiert: {$relativePath}\n";
}

$seed = <<<'SQL'
-- Mini-Projekt 18: Navigation-/Menürechte final vereinheitlichen
-- Ziel: pt_menu_items.permission_key ist die einzige Berechtigungsquelle.

UPDATE pt_menu_items
SET permission_key = NULL
WHERE permission_key IS NOT NULL
  AND TRIM(permission_key) = '';

-- Bekannte kanonische Verwaltung-/Administration-Menüpunkte absichern, sofern sie existieren.
-- Die Updates sind bewusst weich und ändern nur Einträge ohne Permission.
UPDATE pt_menu_items
SET permission_key = 'portal.verwaltung.dashboard.view'
WHERE (permission_key IS NULL OR permission_key = '')
  AND (url = '/verwaltung' OR route = '/verwaltung');

UPDATE pt_menu_items
SET permission_key = 'portal.verwaltung.personen.view'
WHERE (permission_key IS NULL OR permission_key = '')
  AND (url = '/verwaltung/personen' OR route = '/verwaltung/personen');

UPDATE pt_menu_items
SET permission_key = 'portal.verwaltung.audit.view'
WHERE (permission_key IS NULL OR permission_key = '')
  AND (url = '/verwaltung/audit' OR route = '/verwaltung/audit');

UPDATE pt_menu_items
SET permission_key = 'portal.verwaltung.einladungen.view'
WHERE (permission_key IS NULL OR permission_key = '')
  AND (url = '/verwaltung/einladungen' OR route = '/verwaltung/einladungen');

UPDATE pt_menu_items
SET permission_key = 'portal.verwaltung.datenschutz.view'
WHERE (permission_key IS NULL OR permission_key = '')
  AND (url = '/verwaltung/datenschutz' OR route = '/verwaltung/datenschutz');

UPDATE pt_menu_items
SET permission_key = 'portal.verwaltung.dashboard.view'
WHERE (permission_key IS NULL OR permission_key = '')
  AND (url = '/administration' OR route = '/administration');

UPDATE pt_menu_items
SET permission_key = 'portal.verwaltung.gruppen.view'
WHERE (permission_key IS NULL OR permission_key = '')
  AND (url = '/administration/gruppen' OR route = '/administration/gruppen' OR url = '/verwaltung/gruppen' OR route = '/verwaltung/gruppen');

UPDATE pt_menu_items
SET permission_key = 'portal.verwaltung.permissions.view'
WHERE (permission_key IS NULL OR permission_key = '')
  AND (url = '/administration/permissions' OR route = '/administration/permissions' OR url = '/verwaltung/berechtigungen' OR route = '/verwaltung/berechtigungen');

UPDATE pt_menu_items
SET permission_key = 'portal.verwaltung.systeme.view'
WHERE (permission_key IS NULL OR permission_key = '')
  AND (url = '/administration/systeme' OR route = '/administration/systeme');

UPDATE pt_menu_items
SET permission_key = 'portal.verwaltung.personen.view'
WHERE (permission_key IS NULL OR permission_key = '')
  AND (url = '/administration/personen' OR route = '/administration/personen');
SQL;

$checkModel = <<<'PHP'
<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);

function mp18_env(string $root): array
{
    $env = [];
    $file = $root . DIRECTORY_SEPARATOR . '.env';
    if (is_file($file)) {
        foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
                continue;
            }
            [$key, $value] = explode('=', $line, 2);
            $env[trim($key)] = trim(trim($value), "\"'");
        }
    }
    return $env;
}

function mp18_pdo(string $root): PDO
{
    $env = mp18_env($root);
    $db = $env['DB_DATABASE'] ?? $env['DB_NAME'] ?? 'vdbs_sys';
    $user = $env['DB_USERNAME'] ?? $env['DB_USER'] ?? 'root';
    $pass = $env['DB_PASSWORD'] ?? $env['DB_PASS'] ?? '';
    $port = (int)($env['DB_PORT'] ?? 3306);
    $hosts = array_values(array_unique(array_filter([
        $env['DB_HOST'] ?? null,
        '127.0.0.1',
        'localhost',
    ])));

    $last = null;
    foreach ($hosts as $host) {
        try {
            return new PDO(
                "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4",
                $user,
                $pass,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (Throwable $e) {
            $last = $e;
        }
    }

    throw new RuntimeException('Keine DB-Verbindung möglich: ' . ($last?->getMessage() ?? 'unbekannt'));
}

function mp18_table_exists(PDO $pdo, string $table): bool
{
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table');
    $stmt->execute(['table' => $table]);
    return (int)$stmt->fetchColumn() > 0;
}

function mp18_column_exists(PDO $pdo, string $table, string $column): bool
{
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table AND COLUMN_NAME = :column');
    $stmt->execute(['table' => $table, 'column' => $column]);
    return (int)$stmt->fetchColumn() > 0;
}

function mp18_first_existing_column(PDO $pdo, string $table, array $columns): ?string
{
    foreach ($columns as $column) {
        if (mp18_column_exists($pdo, $table, $column)) {
            return $column;
        }
    }
    return null;
}

$pdo = mp18_pdo($root);
$errors = [];
$warnings = [];

if (!mp18_table_exists($pdo, 'pt_menu_items')) {
    $errors[] = 'Tabelle pt_menu_items fehlt.';
} else {
    if (!mp18_column_exists($pdo, 'pt_menu_items', 'permission_key')) {
        $errors[] = 'pt_menu_items.permission_key fehlt.';
    }

    foreach (['page_group_id', 'permission_group_id', 'group_id'] as $legacyColumn) {
        if (mp18_column_exists($pdo, 'pt_menu_items', $legacyColumn)) {
            $errors[] = "Legacy-Spalte pt_menu_items.{$legacyColumn} existiert noch.";
        }
    }
}

if (!mp18_table_exists($pdo, 'ids_permissions')) {
    $errors[] = 'Tabelle ids_permissions fehlt.';
}

if ($errors === [] && mp18_column_exists($pdo, 'pt_menu_items', 'permission_key')) {
    $permKeyColumn = mp18_first_existing_column($pdo, 'ids_permissions', ['key_name', 'permission_key', 'key']);
    if ($permKeyColumn === null) {
        $errors[] = 'ids_permissions hat keine erkennbare Key-Spalte (key_name/permission_key/key).';
    } else {
        $stmt = $pdo->query("SELECT COUNT(*) FROM pt_menu_items WHERE permission_key IS NOT NULL AND TRIM(permission_key) = ''");
        if ((int)$stmt->fetchColumn() > 0) {
            $errors[] = 'pt_menu_items enthält leere permission_key-Werte. Seed erneut ausführen.';
        }

        $stmt = $pdo->query("\n            SELECT mi.id, mi.permission_key\n            FROM pt_menu_items mi\n            LEFT JOIN ids_permissions p ON p.`{$permKeyColumn}` = mi.permission_key\n            WHERE mi.permission_key IS NOT NULL\n              AND TRIM(mi.permission_key) <> ''\n              AND p.id IS NULL\n            ORDER BY mi.id\n            LIMIT 20\n        ");
        $invalid = $stmt->fetchAll();
        foreach ($invalid as $row) {
            $errors[] = 'Menüeintrag #' . $row['id'] . ' verweist auf unbekannte Permission: ' . $row['permission_key'];
        }

        if (mp18_column_exists($pdo, 'ids_permissions', 'is_active')) {
            $stmt = $pdo->query("\n                SELECT mi.id, mi.permission_key\n                FROM pt_menu_items mi\n                INNER JOIN ids_permissions p ON p.`{$permKeyColumn}` = mi.permission_key\n                WHERE mi.permission_key IS NOT NULL\n                  AND TRIM(mi.permission_key) <> ''\n                  AND COALESCE(p.is_active, 1) = 0\n                ORDER BY mi.id\n                LIMIT 20\n            ");
            foreach ($stmt->fetchAll() as $row) {
                $warnings[] = 'Menüeintrag #' . $row['id'] . ' verweist auf inaktive Permission: ' . $row['permission_key'];
            }
        }
    }
}

if ($errors !== []) {
    echo "Navigation-Permission-Modell-Check fehlgeschlagen:\n";
    foreach ($errors as $error) {
        echo " - {$error}\n";
    }
    if ($warnings !== []) {
        echo "\nWarnungen:\n";
        foreach ($warnings as $warning) {
            echo " - {$warning}\n";
        }
    }
    exit(1);
}

foreach ($warnings as $warning) {
    echo "WARN: {$warning}\n";
}

echo "OK: Menü-/Navigation-Daten nutzen permission_key ohne Legacy-Gruppenfelder.\n";
PHP;

$checkCode = <<<'PHP'
<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$errors = [];
$warnings = [];

$legacyPatterns = [
    'page_group_id',
    'page_group',
    'pageGroup',
    'pt_page_groups',
    'pt_permission_group_page_group_access',
    'permission_group_id',
    'ids_permission_groups',
    'ids_user_permission_groups',
    'ids_person_permission_groups',
    'PermissionGroupRepository',
    'PersonPermissionGroupRepository',
];

$navigationTargets = [
    'src/Repository/AuthorizedMenuRepository.php',
    'src/Navigation',
    'src/Presentation/Navigation',
    'resources/views/layouts',
    'resources/views/partials',
    'resources/views/components',
];

function mp18_scan_file(string $file, array $patterns): array
{
    $hits = [];
    $lines = file($file, FILE_IGNORE_NEW_LINES) ?: [];
    foreach ($lines as $index => $line) {
        foreach ($patterns as $pattern) {
            if (stripos($line, $pattern) !== false) {
                $hits[] = basename($file) . ':' . ($index + 1) . ': ' . $pattern . ': ' . trim($line);
            }
        }
    }
    return $hits;
}

foreach ($navigationTargets as $relative) {
    $path = $root . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relative);
    if (is_file($path)) {
        foreach (mp18_scan_file($path, $legacyPatterns) as $hit) {
            $errors[] = $relative . ': ' . $hit;
        }
        continue;
    }

    if (is_dir($path)) {
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS));
        foreach ($iterator as $fileInfo) {
            if (!$fileInfo->isFile()) {
                continue;
            }
            $file = $fileInfo->getPathname();
            if (!preg_match('/\.(php|phtml|inc)$/i', $file)) {
                continue;
            }
            $relativeFile = str_replace($root . DIRECTORY_SEPARATOR, '', $file);
            foreach (mp18_scan_file($file, $legacyPatterns) as $hit) {
                $errors[] = $relativeFile . ': ' . $hit;
            }
        }
    }
}

$repo = $root . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Repository' . DIRECTORY_SEPARATOR . 'AuthorizedMenuRepository.php';
if (!is_file($repo)) {
    $warnings[] = 'src/Repository/AuthorizedMenuRepository.php existiert nicht. Falls Navigation anders implementiert ist, ist das ok; sonst fehlt der zentrale Menüfilter.';
} else {
    $content = file_get_contents($repo) ?: '';
    if (stripos($content, 'permission_key') === false) {
        $errors[] = 'AuthorizedMenuRepository.php referenziert permission_key nicht.';
    }
    if (!preg_match('/AuthorizationService|hasPermission|can\(|requirePermission|currentUser/i', $content)) {
        $warnings[] = 'AuthorizedMenuRepository.php referenziert keinen offensichtlichen AuthorizationService/Permission-Check. Bitte Browser-Smoke-Test mit Nicht-Admin durchführen.';
    }
}

if ($errors !== []) {
    echo "Navigation-Code-Check fehlgeschlagen:\n";
    foreach ($errors as $error) {
        echo " - {$error}\n";
    }
    if ($warnings !== []) {
        echo "\nWarnungen:\n";
        foreach ($warnings as $warning) {
            echo " - {$warning}\n";
        }
    }
    exit(1);
}

foreach ($warnings as $warning) {
    echo "WARN: {$warning}\n";
}

echo "OK: Navigation-/Layout-Code ist frei von Legacy-Gruppenverweisen und verwendet permission_key.\n";
PHP;

$checkSeeds = <<<'PHP'
<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$seedDir = $root . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'seeds';
$errors = [];

$legacyPatterns = [
    'page_group_id',
    'pt_page_groups',
    'pt_permission_group_page_group_access',
];

if (is_dir($seedDir)) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($seedDir, FilesystemIterator::SKIP_DOTS));
    foreach ($iterator as $fileInfo) {
        if (!$fileInfo->isFile() || strtolower($fileInfo->getExtension()) !== 'sql') {
            continue;
        }
        $file = $fileInfo->getPathname();
        $name = basename($file);
        $content = file_get_contents($file) ?: '';
        foreach ($legacyPatterns as $pattern) {
            if (stripos($content, $pattern) !== false) {
                $errors[] = $name . ': enthält Legacy-Navigationsfeld/-tabelle ' . $pattern;
            }
        }
    }
}

if ($errors !== []) {
    echo "Aktive Seed-Dateien enthalten noch PageGroup-Navigationsverweise:\n";
    foreach ($errors as $error) {
        echo " - {$error}\n";
    }
    exit(1);
}

echo "OK: Aktive Seeds enthalten keine PageGroup-Navigationsverweise mehr.\n";
PHP;

$run = <<<'PHP'
<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$php = PHP_BINARY;
$commands = [
    [$php, '-l', $root . '/config/routes.php'],
    [$php, '-l', $root . '/config/services.php'],
    [$php, $root . '/tools/qa/check_navigation_permission_model.php'],
    [$php, $root . '/tools/qa/check_navigation_code_permission_model.php'],
    [$php, $root . '/tools/qa/check_navigation_active_seeds.php'],
];

$optional = [
    $root . '/tools/qa/run_identity_admin_safety_checks.php',
    $root . '/tools/qa/run_personen_identity_checks.php',
];

foreach ($optional as $tool) {
    if (is_file($tool)) {
        $commands[] = [$php, $tool];
    }
}

$failed = false;
foreach ($commands as $command) {
    $display = implode(' ', array_map(static fn (string $part): string => '"' . $part . '"', $command));
    echo "Running: {$display}\n";
    $process = proc_open($command, [STDIN, STDOUT, STDERR], $pipes, $root);
    if (!is_resource($process)) {
        echo "FAILED: Prozess konnte nicht gestartet werden.\n";
        $failed = true;
        continue;
    }
    $exit = proc_close($process);
    if ($exit !== 0) {
        echo "FAILED: {$display}\n";
        $failed = true;
    }
}

if ($failed) {
    echo "Mini-Projekt-18-Navigation-Permission-Checks haben Fehler gefunden.\n";
    exit(1);
}

echo "OK: Mini-Projekt-18-Navigation-Permission-Checks bestanden.\n";
PHP;

$verify = <<<'SQL'
-- Mini-Projekt 18: Navigation-/Menürechte prüfen

SELECT 'menu_items_without_permission_column_check' AS check_name,
       COUNT(*) AS menu_items_total
FROM pt_menu_items;

SELECT 'invalid_menu_permissions' AS check_name,
       COUNT(*) AS invalid_count
FROM pt_menu_items mi
LEFT JOIN ids_permissions p ON p.key_name = mi.permission_key
WHERE mi.permission_key IS NOT NULL
  AND TRIM(mi.permission_key) <> ''
  AND p.id IS NULL;
SQL;

mp18_write_project_file($root, 'database/seeds/seed_mini_project_18_navigation_permission_integrity.sql', $seed, $stamp);
mp18_write_project_file($root, 'tools/qa/check_navigation_permission_model.php', $checkModel, $stamp);
mp18_write_project_file($root, 'tools/qa/check_navigation_code_permission_model.php', $checkCode, $stamp);
mp18_write_project_file($root, 'tools/qa/check_navigation_active_seeds.php', $checkSeeds, $stamp);
mp18_write_project_file($root, 'tools/qa/run_identity_navigation_permission_checks.php', $run, $stamp);
mp18_write_project_file($root, 'database/sql/verify_mini_project_18_navigation_permissions.sql', $verify, $stamp);

echo "\nMini-Projekt 18 Navigation-Permission-Guard wurde angewendet.\n";
echo "Bitte ausführen:\n";
echo "  php bin\\console seed\n";
echo "  php tools\\qa\\check_navigation_permission_model.php\n";
echo "  php tools\\qa\\check_navigation_code_permission_model.php\n";
echo "  php tools\\qa\\check_navigation_active_seeds.php\n";
echo "  php tools\\qa\\run_identity_navigation_permission_checks.php\n";
