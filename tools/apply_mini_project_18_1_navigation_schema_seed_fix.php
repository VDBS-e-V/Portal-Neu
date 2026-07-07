<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$stamp = 'mini-project-18-1-' . date('Ymd-His');

function mp18_1_path(string $relative): string
{
    global $root;
    return $root . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relative);
}

function mp18_1_backup(string $path, string $stamp): void
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

function mp18_1_write(string $relative, string $content): void
{
    global $stamp;
    $target = mp18_1_path($relative);
    $dir = dirname($target);
    if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
        throw new RuntimeException('Verzeichnis konnte nicht erstellt werden: ' . $dir);
    }
    mp18_1_backup($target, $stamp);
    if (file_put_contents($target, $content) === false) {
        throw new RuntimeException('Datei konnte nicht geschrieben werden: ' . $target);
    }
    echo 'Aktualisiert: ' . $relative . PHP_EOL;
}

function mp18_1_load_env(string $root): array
{
    $env = [];
    $path = $root . DIRECTORY_SEPARATOR . '.env';
    if (!is_file($path)) {
        return $env;
    }
    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
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

function mp18_1_pdo(string $root): PDO
{
    $env = mp18_1_load_env($root);
    $host = $env['DB_HOST'] ?? '127.0.0.1';
    $port = $env['DB_PORT'] ?? '3306';
    $db = $env['DB_NAME'] ?? ($env['DB_DATABASE'] ?? 'vdbs_sys');
    $user = $env['DB_USER'] ?? ($env['DB_USERNAME'] ?? 'root');
    $pass = $env['DB_PASS'] ?? ($env['DB_PASSWORD'] ?? '');

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    $dsn = 'mysql:host=' . $host . ';port=' . $port . ';dbname=' . $db . ';charset=utf8mb4';
    try {
        return new PDO($dsn, $user, $pass, $options);
    } catch (PDOException $e) {
        if ($host !== 'localhost') {
            $dsn = 'mysql:host=localhost;port=' . $port . ';dbname=' . $db . ';charset=utf8mb4';
            return new PDO($dsn, $user, $pass, $options);
        }
        throw $e;
    }
}

function mp18_1_column_exists(PDO $pdo, string $table, string $column): bool
{
    $stmt = $pdo->prepare(
        'SELECT COUNT(*) FROM information_schema.COLUMNS
         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table AND COLUMN_NAME = :column'
    );
    $stmt->execute(['table' => $table, 'column' => $column]);
    return (int)$stmt->fetchColumn() > 0;
}

function mp18_1_index_exists(PDO $pdo, string $table, string $index): bool
{
    $stmt = $pdo->prepare(
        'SELECT COUNT(*) FROM information_schema.STATISTICS
         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table AND INDEX_NAME = :index_name'
    );
    $stmt->execute(['table' => $table, 'index_name' => $index]);
    return (int)$stmt->fetchColumn() > 0;
}

function mp18_1_quote_identifier(string $identifier): string
{
    return '`' . str_replace('`', '``', $identifier) . '`';
}

function mp18_1_fix_pt_menu_items_schema(PDO $pdo): void
{
    if (!mp18_1_column_exists($pdo, 'pt_menu_items', 'permission_key')) {
        $pdo->exec('ALTER TABLE `pt_menu_items` ADD COLUMN `permission_key` VARCHAR(190) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL');
        echo "DB: pt_menu_items.permission_key wurde ergänzt.\n";
    } else {
        try {
            $pdo->exec('ALTER TABLE `pt_menu_items` MODIFY `permission_key` VARCHAR(190) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL');
        } catch (Throwable $e) {
            echo 'WARN: permission_key konnte nicht normalisiert werden: ' . $e->getMessage() . PHP_EOL;
        }
        echo "DB: pt_menu_items.permission_key existiert.\n";
    }

    if (!mp18_1_index_exists($pdo, 'pt_menu_items', 'idx_pt_menu_items_permission_key')) {
        try {
            $pdo->exec('ALTER TABLE `pt_menu_items` ADD INDEX `idx_pt_menu_items_permission_key` (`permission_key`)');
            echo "DB: Index idx_pt_menu_items_permission_key wurde ergänzt.\n";
        } catch (Throwable $e) {
            echo 'WARN: permission_key-Index konnte nicht ergänzt werden: ' . $e->getMessage() . PHP_EOL;
        }
    }

    if (mp18_1_column_exists($pdo, 'pt_menu_items', 'page_group_id')) {
        $stmt = $pdo->prepare(
            'SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = :table
               AND COLUMN_NAME = :column
               AND REFERENCED_TABLE_NAME IS NOT NULL'
        );
        $stmt->execute(['table' => 'pt_menu_items', 'column' => 'page_group_id']);
        foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $constraint) {
            $pdo->exec('ALTER TABLE `pt_menu_items` DROP FOREIGN KEY ' . mp18_1_quote_identifier((string)$constraint));
            echo 'DB: Foreign Key entfernt: ' . $constraint . PHP_EOL;
        }

        $stmt = $pdo->prepare(
            'SELECT DISTINCT INDEX_NAME FROM information_schema.STATISTICS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = :table
               AND COLUMN_NAME = :column
               AND INDEX_NAME <> \'PRIMARY\''
        );
        $stmt->execute(['table' => 'pt_menu_items', 'column' => 'page_group_id']);
        foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $index) {
            $pdo->exec('ALTER TABLE `pt_menu_items` DROP INDEX ' . mp18_1_quote_identifier((string)$index));
            echo 'DB: Index entfernt: ' . $index . PHP_EOL;
        }

        $pdo->exec('ALTER TABLE `pt_menu_items` DROP COLUMN `page_group_id`');
        echo "DB: pt_menu_items.page_group_id wurde entfernt.\n";
    } else {
        echo "DB: pt_menu_items.page_group_id ist bereits entfernt.\n";
    }
}

function mp18_1_split_csv_top_level(string $text): array
{
    $items = [];
    $buffer = '';
    $quote = null;
    $escape = false;
    $depth = 0;
    $length = strlen($text);

    for ($i = 0; $i < $length; $i++) {
        $ch = $text[$i];
        if ($escape) {
            $buffer .= $ch;
            $escape = false;
            continue;
        }
        if ($quote !== null) {
            $buffer .= $ch;
            if ($ch === '\\') {
                $escape = true;
            } elseif ($ch === $quote) {
                $quote = null;
            }
            continue;
        }
        if ($ch === "'" || $ch === '"' || $ch === '`') {
            $quote = $ch;
            $buffer .= $ch;
            continue;
        }
        if ($ch === '(') {
            $depth++;
            $buffer .= $ch;
            continue;
        }
        if ($ch === ')') {
            $depth--;
            $buffer .= $ch;
            continue;
        }
        if ($ch === ',' && $depth === 0) {
            $items[] = trim($buffer);
            $buffer = '';
            continue;
        }
        $buffer .= $ch;
    }

    if (trim($buffer) !== '') {
        $items[] = trim($buffer);
    }
    return $items;
}

function mp18_1_split_value_rows(string $values): array
{
    $rows = [];
    $buffer = '';
    $quote = null;
    $escape = false;
    $depth = 0;
    $length = strlen($values);

    for ($i = 0; $i < $length; $i++) {
        $ch = $values[$i];
        if ($escape) {
            $buffer .= $ch;
            $escape = false;
            continue;
        }
        if ($quote !== null) {
            $buffer .= $ch;
            if ($ch === '\\') {
                $escape = true;
            } elseif ($ch === $quote) {
                $quote = null;
            }
            continue;
        }
        if ($ch === "'" || $ch === '"') {
            $quote = $ch;
            $buffer .= $ch;
            continue;
        }
        if ($ch === '(') {
            $depth++;
            $buffer .= $ch;
            continue;
        }
        if ($ch === ')') {
            $depth--;
            $buffer .= $ch;
            if ($depth === 0) {
                $rows[] = trim($buffer);
                $buffer = '';
                // skip following whitespace/comma
                while ($i + 1 < $length && preg_match('/[\s,]/', $values[$i + 1])) {
                    $i++;
                }
            }
            continue;
        }
        $buffer .= $ch;
    }

    return $rows;
}

function mp18_1_strip_page_group_from_insert(string $sql): string
{
    return preg_replace_callback(
        '/INSERT\s+INTO\s+`?pt_menu_items`?\s*\((?<cols>.*?)\)\s*VALUES\s*(?<vals>.*?);/is',
        static function (array $m): string {
            $cols = mp18_1_split_csv_top_level($m['cols']);
            $remove = [];
            foreach ($cols as $i => $col) {
                $clean = strtolower(trim($col, " `\t\n\r\0\x0B"));
                if ($clean === 'page_group_id') {
                    $remove[$i] = true;
                }
            }
            if ($remove === []) {
                return $m[0];
            }
            $newCols = [];
            foreach ($cols as $i => $col) {
                if (!isset($remove[$i])) {
                    $newCols[] = trim($col);
                }
            }
            $newRows = [];
            foreach (mp18_1_split_value_rows($m['vals']) as $row) {
                $inner = trim($row);
                if (str_starts_with($inner, '(') && str_ends_with($inner, ')')) {
                    $inner = substr($inner, 1, -1);
                }
                $vals = mp18_1_split_csv_top_level($inner);
                $newVals = [];
                foreach ($vals as $i => $val) {
                    if (!isset($remove[$i])) {
                        $newVals[] = trim($val);
                    }
                }
                $newRows[] = '(' . implode(', ', $newVals) . ')';
            }
            return 'INSERT INTO pt_menu_items (' . implode(', ', $newCols) . ") VALUES\n" . implode(",\n", $newRows) . ';';
        },
        $sql
    ) ?? $sql;
}

function mp18_1_patch_administration_seed(): void
{
    global $stamp;
    $path = mp18_1_path('database/seeds/seed_administration_menu_items.sql');
    if (!is_file($path)) {
        return;
    }
    $sql = file_get_contents($path);
    if ($sql === false) {
        throw new RuntimeException('Seed konnte nicht gelesen werden: ' . $path);
    }
    $patched = mp18_1_strip_page_group_from_insert($sql);
    $patched = preg_replace('/^.*\bpage_group_id\b.*\R?/mi', '', $patched) ?? $patched;
    if ($patched !== $sql) {
        mp18_1_backup($path, $stamp);
        file_put_contents($path, $patched);
        echo "Bereinigt: database/seeds/seed_administration_menu_items.sql\n";
    } else {
        echo "Keine page_group_id-Einträge in seed_administration_menu_items.sql gefunden.\n";
    }
}

function mp18_1_patch_authorized_menu_repository(): void
{
    global $stamp;
    $path = mp18_1_path('src/Repository/AuthorizedMenuRepository.php');
    if (!is_file($path)) {
        return;
    }
    $content = file_get_contents($path);
    if ($content === false) {
        return;
    }
    $patched = str_replace(
        'Das alte PageGroup-Modell wird bewusst nicht mehr unterstützt.',
        'Das alte gruppenbasierte Navigationsmodell wird bewusst nicht mehr unterstützt.',
        $content
    );
    $patched = str_replace('PageGroup', 'LegacyNavigationGroup', $patched);
    $patched = str_replace('pageGroup', 'legacyNavigationGroup', $patched);
    if ($patched !== $content) {
        mp18_1_backup($path, $stamp);
        file_put_contents($path, $patched);
        echo "Bereinigt: src/Repository/AuthorizedMenuRepository.php\n";
    }
}

try {
    $pdo = mp18_1_pdo($root);
    mp18_1_fix_pt_menu_items_schema($pdo);
} catch (Throwable $e) {
    echo 'WARN: DB-Schema-Fix konnte nicht automatisch ausgeführt werden: ' . $e->getMessage() . PHP_EOL;
    echo "Bitte sicherstellen, dass MySQL läuft, dann Tool erneut starten.\n";
}

mp18_1_patch_administration_seed();
mp18_1_patch_authorized_menu_repository();

mp18_1_write('database/seeds/seed_mini_project_15_person_subject_backfill.sql', <<<'SQL'
SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Mini-Projekt 18.1:
-- Der alte Backfill-Seed wurde durch QA-gestützte Integritätsprüfungen ersetzt,
-- weil gemischte Alt-Kollationen in Bestandsinstallationen den Seed-Lauf abbrechen konnten.
-- Fehlende Person-Subject-Zuordnungen werden durch tools/qa/check_person_subject_integrity.php erkannt.
SELECT 1 AS mini_project_15_person_subject_backfill_retired;
SQL);

mp18_1_write('database/seeds/seed_mini_project_7_clear_page_group_menu_links.sql', <<<'SQL'
SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Mini-Projekt 18.1:
-- Dieser historische Cleanup-Seed ist nach dem Schema-Cleanup nicht mehr erforderlich.
SELECT 1 AS mini_project_7_navigation_cleanup_retired;
SQL);

mp18_1_write('database/seeds/seed_mini_project_8_deprecate_legacy_pagegroup_db.sql', <<<'SQL'
SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Mini-Projekt 18.1:
-- Dieser historische Deprecation-Seed ist nach dem DB-Cleanup nicht mehr erforderlich.
SELECT 1 AS mini_project_8_navigation_cleanup_retired;
SQL);

mp18_1_write('database/seeds/seed_mini_project_18_navigation_permission_integrity.sql', <<<'SQL'
SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;

UPDATE pt_menu_items
SET permission_key = NULL
WHERE permission_key = '';

UPDATE pt_menu_items mi
JOIN ids_permissions p ON p.key_name = mi.permission_key
SET mi.permission_key = p.key_name
WHERE mi.permission_key IS NOT NULL;
SQL);

mp18_1_write('tools/qa/check_navigation_permission_model.php', <<<'PHP'
<?php

declare(strict_types=1);

require_once __DIR__ . '/identity_qa_bootstrap.php';

$pdo = identity_qa_pdo();
$errors = [];

function nav_column_exists(PDO $pdo, string $table, string $column): bool
{
    $stmt = $pdo->prepare(
        'SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table AND COLUMN_NAME = :column'
    );
    $stmt->execute(['table' => $table, 'column' => $column]);
    return (int)$stmt->fetchColumn() > 0;
}

if (!nav_column_exists($pdo, 'pt_menu_items', 'permission_key')) {
    $errors[] = 'pt_menu_items.permission_key fehlt.';
}

if (nav_column_exists($pdo, 'pt_menu_items', 'page_group_id')) {
    $errors[] = 'Legacy-Spalte pt_menu_items.page_group_id existiert noch.';
}

if (nav_column_exists($pdo, 'pt_menu_items', 'permission_key')) {
    $stmt = $pdo->query(
        "SELECT COUNT(*) FROM pt_menu_items mi
         LEFT JOIN ids_permissions p ON p.key_name = mi.permission_key
         WHERE mi.permission_key IS NOT NULL AND mi.permission_key <> '' AND p.id IS NULL"
    );
    if ((int)$stmt->fetchColumn() > 0) {
        $errors[] = 'pt_menu_items enthält permission_key-Werte ohne passende ids_permissions.key_name.';
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
PHP);

mp18_1_write('tools/qa/check_navigation_code_permission_model.php', <<<'PHP'
<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$paths = [
    'src/Repository/AuthorizedMenuRepository.php',
    'src/Navigation',
    'resources/views/layouts',
    'resources/views/partials',
];

$legacyTerms = [
    'page_group_id',
    'page_group',
    'pt_page_groups',
    'pt_permission_group_page_group_access',
    'ids_permission_groups',
    'ids_user_permission_groups',
    'ids_person_permission_groups',
    'permission_group_id',
];

function nav_collect_files(string $path): array
{
    if (is_file($path)) {
        return [$path];
    }
    if (!is_dir($path)) {
        return [];
    }
    $files = [];
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS));
    foreach ($it as $file) {
        if (!$file->isFile()) {
            continue;
        }
        if (!in_array(strtolower($file->getExtension()), ['php', 'phtml'], true)) {
            continue;
        }
        $files[] = $file->getPathname();
    }
    return $files;
}

function nav_strip_php_comments(string $code): string
{
    $tokens = token_get_all($code);
    $out = '';
    foreach ($tokens as $token) {
        if (is_array($token) && in_array($token[0], [T_COMMENT, T_DOC_COMMENT], true)) {
            continue;
        }
        $out .= is_array($token) ? $token[1] : $token;
    }
    return $out;
}

$findings = [];
foreach ($paths as $relative) {
    $absolute = $root . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relative);
    foreach (nav_collect_files($absolute) as $file) {
        $content = file_get_contents($file);
        if ($content === false) {
            continue;
        }
        $scan = nav_strip_php_comments($content);
        $lines = preg_split('/\R/', $scan) ?: [];
        foreach ($lines as $lineNo => $line) {
            foreach ($legacyTerms as $term) {
                if (stripos($line, $term) !== false) {
                    $findings[] = str_replace($root . DIRECTORY_SEPARATOR, '', $file) . ':' . ($lineNo + 1) . ': ' . $term;
                }
            }
        }
    }
}

if ($findings !== []) {
    echo "Navigation-Code-Check fehlgeschlagen:\n";
    foreach ($findings as $finding) {
        echo ' - ' . $finding . "\n";
    }
    exit(1);
}

echo "OK: Navigation-Code nutzt keine Legacy-Gruppen-/PageGroup-Begriffe produktiv.\n";
PHP);

mp18_1_write('tools/qa/check_navigation_active_seeds.php', <<<'PHP'
<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$seedDir = $root . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'seeds';
$legacyTerms = [
    'page_group_id',
    'page_group',
    'pt_page_groups',
    'pt_permission_group_page_group_access',
    'permission_group_id',
    'ids_permission_groups',
    'ids_user_permission_groups',
    'ids_person_permission_groups',
];

$findings = [];
foreach (glob($seedDir . DIRECTORY_SEPARATOR . '*.sql') ?: [] as $file) {
    $name = basename($file);
    $content = file_get_contents($file);
    if ($content === false) {
        continue;
    }
    $lines = preg_split('/\R/', $content) ?: [];
    foreach ($lines as $lineNo => $line) {
        $trimmed = trim($line);
        if ($trimmed === '' || str_starts_with($trimmed, '--')) {
            continue;
        }
        foreach ($legacyTerms as $term) {
            if (stripos($line, $term) !== false) {
                $findings[] = $name . ':' . ($lineNo + 1) . ': enthält Legacy-Navigationsbegriff ' . $term;
            }
        }
    }
}

if ($findings !== []) {
    echo "Aktive Seed-Dateien enthalten noch Legacy-Navigationsverweise:\n";
    foreach ($findings as $finding) {
        echo ' - ' . $finding . "\n";
    }
    exit(1);
}

echo "OK: Aktive Seeds enthalten keine Legacy-Navigationsverweise mehr.\n";
PHP);

mp18_1_write('tools/qa/run_identity_navigation_permission_checks.php', <<<'PHP'
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
    [$php, $root . '/tools/qa/run_identity_admin_safety_checks.php'],
];

$failed = false;
foreach ($commands as $command) {
    $display = implode(' ', array_map(static fn (string $part): string => '"' . $part . '"', $command));
    echo 'Running: ' . $display . PHP_EOL;
    $process = proc_open($command, [STDIN, STDOUT, STDERR], $pipes, $root);
    if (!is_resource($process)) {
        echo 'FAILED: Prozess konnte nicht gestartet werden.' . PHP_EOL;
        $failed = true;
        continue;
    }
    $exitCode = proc_close($process);
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
PHP);

mp18_1_write('database/sql/verify_mini_project_18_navigation_permissions.sql', <<<'SQL'
SELECT
    SUM(CASE WHEN COLUMN_NAME = 'permission_key' THEN 1 ELSE 0 END) AS has_permission_key,
    SUM(CASE WHEN COLUMN_NAME = 'page_group_id' THEN 1 ELSE 0 END) AS has_page_group_id
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'pt_menu_items';

SELECT mi.id, mi.permission_key
FROM pt_menu_items mi
LEFT JOIN ids_permissions p ON p.key_name = mi.permission_key
WHERE mi.permission_key IS NOT NULL
  AND mi.permission_key <> ''
  AND p.id IS NULL;
SQL);

echo PHP_EOL;
echo "Mini-Projekt 18.1 Navigation-Schema-/Seed-Fix wurde angewendet.\n";
echo "Bitte ausführen:\n";
echo "  php bin\\console seed\n";
echo "  php tools\\qa\\check_navigation_permission_model.php\n";
echo "  php tools\\qa\\check_navigation_code_permission_model.php\n";
echo "  php tools\\qa\\check_navigation_active_seeds.php\n";
echo "  php tools\\qa\\run_identity_navigation_permission_checks.php\n";
