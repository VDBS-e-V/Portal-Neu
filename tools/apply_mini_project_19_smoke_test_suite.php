<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$stamp = date('Ymd-His');

function mp19_write_file(string $relative, string $content, string $root, string $stamp): void
{
    $target = $root . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relative);
    $dir = dirname($target);
    if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
        throw new RuntimeException('Verzeichnis konnte nicht erstellt werden: ' . $dir);
    }
    if (is_file($target)) {
        $backup = $target . '.bak-mini-project-19-' . $stamp;
        if (!copy($target, $backup)) {
            throw new RuntimeException('Backup konnte nicht erstellt werden: ' . $backup);
        }
        echo 'Backup: ' . $backup . PHP_EOL;
    }
    if (file_put_contents($target, $content) === false) {
        throw new RuntimeException('Datei konnte nicht geschrieben werden: ' . $target);
    }
    echo 'Aktualisiert: ' . $relative . PHP_EOL;
}

mp19_write_file('tools/qa/identity_smoke_bootstrap.php', <<<'PHPFILE'
<?php

declare(strict_types=1);

function mp19_project_root(): string
{
    return dirname(__DIR__, 2);
}

/** @return array<string,string> */
function mp19_load_env(string $root): array
{
    $env = [];
    $file = $root . DIRECTORY_SEPARATOR . '.env';
    if (!is_file($file)) {
        return $env;
    }

    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return $env;
    }

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        if ($value !== '' && (($value[0] === '"' && str_ends_with($value, '"')) || ($value[0] === "'" && str_ends_with($value, "'")))) {
            $value = substr($value, 1, -1);
        }
        $env[$key] = $value;
    }

    return $env;
}

function mp19_env(array $env, array $keys, ?string $default = null): ?string
{
    foreach ($keys as $key) {
        if (array_key_exists($key, $env) && $env[$key] !== '') {
            return $env[$key];
        }
        $value = getenv($key);
        if (is_string($value) && $value !== '') {
            return $value;
        }
    }
    return $default;
}

function mp19_pdo(string $root): PDO
{
    $env = mp19_load_env($root);
    $host = mp19_env($env, ['DB_HOST'], '127.0.0.1') ?? '127.0.0.1';
    $port = mp19_env($env, ['DB_PORT'], '3306') ?? '3306';
    $db = mp19_env($env, ['DB_DATABASE', 'DB_NAME'], 'vdbs_sys') ?? 'vdbs_sys';
    $user = mp19_env($env, ['DB_USERNAME', 'DB_USER'], 'root') ?? 'root';
    $pass = mp19_env($env, ['DB_PASSWORD', 'DB_PASS'], '') ?? '';

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $db);
    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
    } catch (PDOException $exception) {
        if ($host !== 'localhost') {
            $dsn = sprintf('mysql:host=localhost;port=%s;dbname=%s;charset=utf8mb4', $port, $db);
            $pdo = new PDO($dsn, $user, $pass, $options);
        } else {
            throw $exception;
        }
    }

    $pdo->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
    return $pdo;
}

function mp19_table_exists(PDO $pdo, string $table): bool
{
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = :table');
    $stmt->execute(['table' => $table]);
    return (int) $stmt->fetchColumn() > 0;
}

function mp19_column_exists(PDO $pdo, string $table, string $column): bool
{
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = :table AND column_name = :column');
    $stmt->execute(['table' => $table, 'column' => $column]);
    return (int) $stmt->fetchColumn() > 0;
}

function mp19_count(PDO $pdo, string $sql): int
{
    return (int) $pdo->query($sql)->fetchColumn();
}

/** @param array<int,string> $errors */
function mp19_finish(array $errors, string $successMessage): void
{
    if ($errors !== []) {
        echo "Check fehlgeschlagen:" . PHP_EOL;
        foreach ($errors as $error) {
            echo ' - ' . $error . PHP_EOL;
        }
        exit(1);
    }
    echo $successMessage . PHP_EOL;
}

function mp19_run_command(array $command): int
{
    $display = implode(' ', array_map(static fn(string $p): string => '"' . $p . '"', $command));
    echo 'Running: ' . $display . PHP_EOL;

    $descriptorSpec = [
        0 => STDIN,
        1 => STDOUT,
        2 => STDERR,
    ];

    $process = proc_open($command, $descriptorSpec, $pipes);
    if (!is_resource($process)) {
        echo 'FAILED: Prozess konnte nicht gestartet werden.' . PHP_EOL;
        return 1;
    }

    $exitCode = proc_close($process);
    if ($exitCode !== 0) {
        echo 'FAILED: ' . $display . PHP_EOL;
    }
    return $exitCode;
}
PHPFILE, $root, $stamp);

mp19_write_file('tools/qa/check_identity_core_smoke.php', <<<'PHPFILE'
<?php

declare(strict_types=1);

require_once __DIR__ . '/identity_smoke_bootstrap.php';

$root = mp19_project_root();
$pdo = mp19_pdo($root);
$errors = [];

$requiredTables = [
    'ids_subjects',
    'ids_persons',
    'ids_users',
    'ids_systems',
    'ids_groups',
    'ids_permissions',
    'ids_group_permissions',
    'ids_subject_groups',
    'pt_menu_items',
];

foreach ($requiredTables as $table) {
    if (!mp19_table_exists($pdo, $table)) {
        $errors[] = 'Pflichttabelle fehlt: ' . $table;
    }
}

$removedTables = [
    'pt_page_groups',
    'pt_permission_group_page_group_access',
    'ids_permission_groups',
    'ids_user_permission_groups',
    'ids_person_permission_groups',
];
foreach ($removedTables as $table) {
    if (mp19_table_exists($pdo, $table)) {
        $errors[] = 'Legacy-Tabelle existiert noch: ' . $table;
    }
}

if (mp19_table_exists($pdo, 'pt_menu_items')) {
    if (!mp19_column_exists($pdo, 'pt_menu_items', 'permission_key')) {
        $errors[] = 'pt_menu_items.permission_key fehlt.';
    }
    if (mp19_column_exists($pdo, 'pt_menu_items', 'page_group_id')) {
        $errors[] = 'Legacy-Spalte pt_menu_items.page_group_id existiert noch.';
    }
}

if (mp19_table_exists($pdo, 'ids_persons') && mp19_table_exists($pdo, 'ids_subjects') && mp19_column_exists($pdo, 'ids_persons', 'subject_id')) {
    $count = mp19_count($pdo, 'SELECT COUNT(*) FROM ids_persons p LEFT JOIN ids_subjects s ON s.id = p.subject_id WHERE p.subject_id IS NULL OR s.id IS NULL');
    if ($count > 0) {
        $errors[] = 'Personen ohne gültiges Subject: ' . $count;
    }
}

if (mp19_table_exists($pdo, 'ids_users') && mp19_table_exists($pdo, 'ids_persons') && mp19_column_exists($pdo, 'ids_users', 'person_id')) {
    $count = mp19_count($pdo, 'SELECT COUNT(*) FROM ids_users u LEFT JOIN ids_persons p ON p.id = u.person_id WHERE u.person_id IS NOT NULL AND p.id IS NULL');
    if ($count > 0) {
        $errors[] = 'User mit ungültiger person_id: ' . $count;
    }
}

if (mp19_table_exists($pdo, 'ids_subject_groups')) {
    $count = mp19_count($pdo, 'SELECT COUNT(*) FROM ids_subject_groups sg LEFT JOIN ids_subjects s ON s.id = sg.subject_id LEFT JOIN ids_groups g ON g.id = sg.group_id WHERE s.id IS NULL OR g.id IS NULL');
    if ($count > 0) {
        $errors[] = 'Ungültige Subject-Gruppen-Zuordnungen: ' . $count;
    }
}

if (mp19_table_exists($pdo, 'ids_group_permissions')) {
    $count = mp19_count($pdo, 'SELECT COUNT(*) FROM ids_group_permissions gp LEFT JOIN ids_groups g ON g.id = gp.group_id LEFT JOIN ids_permissions p ON p.id = gp.permission_id WHERE g.id IS NULL OR p.id IS NULL');
    if ($count > 0) {
        $errors[] = 'Ungültige Gruppen-Permission-Zuordnungen: ' . $count;
    }

    if (mp19_column_exists($pdo, 'ids_groups', 'system_id') && mp19_column_exists($pdo, 'ids_permissions', 'system_id')) {
        $count = mp19_count($pdo, 'SELECT COUNT(*) FROM ids_group_permissions gp INNER JOIN ids_groups g ON g.id = gp.group_id INNER JOIN ids_permissions p ON p.id = gp.permission_id WHERE g.system_id <> p.system_id');
        if ($count > 0) {
            $errors[] = 'Systemfremde Gruppen-Permission-Zuordnungen: ' . $count;
        }
    }
}

if (mp19_table_exists($pdo, 'pt_menu_items') && mp19_column_exists($pdo, 'pt_menu_items', 'permission_key') && mp19_table_exists($pdo, 'ids_permissions')) {
    $menuKeys = $pdo->query("SELECT DISTINCT permission_key FROM pt_menu_items WHERE permission_key IS NOT NULL AND TRIM(permission_key) <> ''")->fetchAll(PDO::FETCH_COLUMN);
    $permissionKeys = $pdo->query("SELECT key_name FROM ids_permissions WHERE is_active = 1")->fetchAll(PDO::FETCH_COLUMN);
    $permissionKeySet = array_flip(array_map('strval', $permissionKeys));
    $missing = [];
    foreach ($menuKeys as $key) {
        $key = (string) $key;
        if (!isset($permissionKeySet[$key])) {
            $missing[] = $key;
        }
    }
    $missing = array_values(array_unique($missing));
    if ($missing !== []) {
        $errors[] = 'Menü-Permissions ohne aktive ids_permissions: ' . implode(', ', array_slice($missing, 0, 10)) . (count($missing) > 10 ? ' ...' : '');
    }
}

$env = mp19_load_env($root);
$initialAdminEmail = mp19_env($env, ['INITIAL_ADMIN_EMAIL'], null);
if ($initialAdminEmail !== null && mp19_table_exists($pdo, 'ids_users') && mp19_table_exists($pdo, 'ids_persons') && mp19_table_exists($pdo, 'ids_subject_groups')) {
    $stmt = $pdo->prepare('SELECT s.id AS subject_id FROM ids_users u INNER JOIN ids_persons p ON p.id = u.person_id INNER JOIN ids_subjects s ON s.id = p.subject_id WHERE u.email = :email LIMIT 1');
    $stmt->execute(['email' => $initialAdminEmail]);
    $subjectId = $stmt->fetchColumn();
    if ($subjectId === false) {
        $errors[] = 'Initial-Admin aus .env wurde nicht als Subject gefunden: ' . $initialAdminEmail;
    } else {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM ids_subject_groups sg INNER JOIN ids_groups g ON g.id = sg.group_id INNER JOIN ids_systems sys ON sys.id = g.system_id WHERE sg.subject_id = :subject_id AND g.key_name = 'administrator' AND (sg.expires_at IS NULL OR sg.expires_at > NOW()) AND sys.is_active = 1");
        $stmt->execute(['subject_id' => (int) $subjectId]);
        if ((int) $stmt->fetchColumn() === 0) {
            $errors[] = 'Initial-Admin hat keine aktive Administrator-Zuordnung.';
        }
    }
}

mp19_finish($errors, 'OK: Identity-Core-Smoke-Check bestanden.');
PHPFILE, $root, $stamp);

mp19_write_file('tools/qa/check_route_smoke_matrix.php', <<<'PHPFILE'
<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$routesFile = $root . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'routes.php';
$errors = [];

if (!is_file($routesFile)) {
    $errors[] = 'config/routes.php fehlt.';
} else {
    $content = file_get_contents($routesFile);
    if ($content === false) {
        $errors[] = 'config/routes.php konnte nicht gelesen werden.';
    } else {
        $requiredRoutes = [
            ["GET", "/konto"],
            ["GET", "/konto/einstellungen"],
            ["GET", "/identity/me"],
            ["GET", "/administration"],
            ["GET", "/administration/gruppen"],
            ["GET", "/administration/permissions"],
            ["GET", "/administration/personen"],
            ["GET", "/verwaltung"],
            ["GET", "/verwaltung/personen"],
            ["GET", "/verwaltung/audit"],
            ["GET", "/verwaltung/einladungen"],
            ["GET", "/verwaltung/datenschutz"],
        ];

        foreach ($requiredRoutes as [$method, $path]) {
            $pattern = "/new\\s+Route\\s*\\(\\s*['\"]" . preg_quote($method, '/') . "['\"]\\s*,\\s*['\"]" . preg_quote($path, '/') . "['\"]/";
            if (!preg_match($pattern, $content)) {
                $errors[] = 'Erwartete Route fehlt: ' . $method . ' ' . $path;
            }
        }

        $legacyWebRoutes = [
            "/user",
            "/user/settings",
            "/user/password",
            "/konto/profil",
            "/konto/profil/bearbeiten",
            "/konto/kontakte",
            "/konto/adressen",
            "/konto/sicherheit",
        ];
        foreach ($legacyWebRoutes as $path) {
            $pattern = "/new\\s+Route\\s*\\(\\s*['\"](?:GET|POST)['\"]\\s*,\\s*['\"]" . preg_quote($path, '/') . "['\"]/";
            if (preg_match($pattern, $content)) {
                $errors[] = 'Alte Web-Route ist wieder aktiv: ' . $path;
            }
        }

        $forbiddenControllerFragments = [
            'PageGroupRepository',
            'PageGroupAccessRepository',
            'PermissionGroupRepository::class',
            'PersonPermissionGroupRepository::class',
            'requirePageGroupAccess',
            'BerechtigungenController::class',
        ];
        foreach ($forbiddenControllerFragments as $fragment) {
            if (str_contains($content, $fragment)) {
                $errors[] = 'Legacy-Routen-/Servicefragment in routes.php gefunden: ' . $fragment;
            }
        }
    }
}

if ($errors !== []) {
    echo 'Route-Smoke-Matrix fehlgeschlagen:' . PHP_EOL;
    foreach ($errors as $error) {
        echo ' - ' . $error . PHP_EOL;
    }
    exit(1);
}

echo 'OK: Route-Smoke-Matrix bestanden.' . PHP_EOL;
PHPFILE, $root, $stamp);

mp19_write_file('tools/qa/check_identity_http_smoke.php', <<<'PHPFILE'
<?php

declare(strict_types=1);

require_once __DIR__ . '/identity_smoke_bootstrap.php';

$args = $argv;
array_shift($args);
$baseUrl = null;
foreach ($args as $arg) {
    if (str_starts_with($arg, '--base-url=')) {
        $baseUrl = substr($arg, strlen('--base-url='));
    }
}

if ($baseUrl === null || $baseUrl === '') {
    $env = mp19_load_env(mp19_project_root());
    $baseUrl = mp19_env($env, ['SMOKE_BASE_URL'], null);
}

if ($baseUrl === null || trim($baseUrl) === '') {
    echo 'OK: HTTP-Smoke-Test übersprungen. Setze SMOKE_BASE_URL oder nutze --base-url=..., um Endpunkte zu prüfen.' . PHP_EOL;
    exit(0);
}

$baseUrl = rtrim($baseUrl, '/');
$paths = [
    '/',
    '/login',
    '/konto',
    '/konto/einstellungen',
    '/identity/me',
    '/administration',
    '/administration/gruppen',
    '/administration/permissions',
    '/administration/personen',
    '/verwaltung',
    '/verwaltung/personen',
    '/verwaltung/audit',
    '/verwaltung/einladungen',
    '/verwaltung/datenschutz',
];

$errors = [];
foreach ($paths as $path) {
    $url = $baseUrl . $path;
    $headers = [];
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'ignore_errors' => true,
            'timeout' => 10,
            'header' => "User-Agent: VDBS Identity Smoke Test\r\n",
        ],
    ]);

    $body = @file_get_contents($url, false, $context);
    $headers = $http_response_header ?? [];
    $status = 0;
    foreach ($headers as $header) {
        if (preg_match('/^HTTP\/\S+\s+(\d{3})/', $header, $matches)) {
            $status = (int) $matches[1];
            break;
        }
    }

    if ($body === false && $status === 0) {
        $errors[] = $path . ': keine Antwort von ' . $url;
        continue;
    }

    echo $path . ' -> HTTP ' . $status . PHP_EOL;
    if ($status >= 500 || $status === 0) {
        $errors[] = $path . ': unerwarteter HTTP-Status ' . $status;
    }
}

if ($errors !== []) {
    echo 'HTTP-Smoke-Test fehlgeschlagen:' . PHP_EOL;
    foreach ($errors as $error) {
        echo ' - ' . $error . PHP_EOL;
    }
    exit(1);
}

echo 'OK: HTTP-Smoke-Test bestanden.' . PHP_EOL;
PHPFILE, $root, $stamp);

mp19_write_file('tools/qa/run_identity_smoke_suite.php', <<<'PHPFILE'
<?php

declare(strict_types=1);

require_once __DIR__ . '/identity_smoke_bootstrap.php';

$root = dirname(__DIR__, 2);
$php = PHP_BINARY;
$args = array_slice($argv, 1);
$withSeed = in_array('--with-seed', $args, true);
$exitCode = 0;

$commands = [];
$commands[] = [$php, '-l', $root . '/config/routes.php'];
$commands[] = [$php, '-l', $root . '/config/services.php'];
$commands[] = [$php, '-l', $root . '/src/Security/AuthorizationService.php'];
$commands[] = [$php, '-l', $root . '/src/Security/RoutePermissionMap.php'];

if ($withSeed) {
    $commands[] = [$php, $root . '/bin/console', 'seed'];
}

$optionalExistingChecks = [
    'tools/qa/run_identity_navigation_permission_checks.php',
    'tools/qa/run_identity_admin_safety_checks.php',
    'tools/qa/run_identity_audit_erasure_checks.php',
    'tools/qa/run_personen_identity_checks.php',
    'tools/qa/run_identity_permission_group_cleanup_checks.php',
    'tools/qa/run_identity_legacy_identity_group_drop_checks.php',
];

foreach ($optionalExistingChecks as $relative) {
    $path = $root . '/' . $relative;
    if (is_file($path)) {
        $commands[] = [$php, $path];
    }
}

$commands[] = [$php, $root . '/tools/qa/check_identity_core_smoke.php'];
$commands[] = [$php, $root . '/tools/qa/check_route_smoke_matrix.php'];
$commands[] = [$php, $root . '/tools/qa/check_identity_http_smoke.php'];

foreach ($commands as $command) {
    $code = mp19_run_command($command);
    if ($code !== 0) {
        $exitCode = 1;
    }
}

if ($exitCode !== 0) {
    echo 'Mini-Projekt-19-Smoke-Test-Suite hat Fehler gefunden.' . PHP_EOL;
    exit(1);
}

echo 'OK: Mini-Projekt-19-Smoke-Test-Suite bestanden.' . PHP_EOL;
PHPFILE, $root, $stamp);

mp19_write_file('database/sql/verify_mini_project_19_smoke_baseline.sql', <<<'SQL'
-- Mini-Projekt 19: Smoke baseline queries
SELECT 'subjects' AS metric, COUNT(*) AS value FROM ids_subjects
UNION ALL
SELECT 'persons', COUNT(*) FROM ids_persons
UNION ALL
SELECT 'users', COUNT(*) FROM ids_users
UNION ALL
SELECT 'systems', COUNT(*) FROM ids_systems
UNION ALL
SELECT 'groups', COUNT(*) FROM ids_groups
UNION ALL
SELECT 'permissions', COUNT(*) FROM ids_permissions
UNION ALL
SELECT 'subject_groups', COUNT(*) FROM ids_subject_groups
UNION ALL
SELECT 'group_permissions', COUNT(*) FROM ids_group_permissions;
SQL, $root, $stamp);

echo PHP_EOL;
echo 'Mini-Projekt 19 Smoke-Test-Suite wurde angewendet.' . PHP_EOL;
echo 'Bitte ausführen:' . PHP_EOL;
echo '  php -l tools\qa\identity_smoke_bootstrap.php' . PHP_EOL;
echo '  php -l tools\qa\check_identity_core_smoke.php' . PHP_EOL;
echo '  php -l tools\qa\check_route_smoke_matrix.php' . PHP_EOL;
echo '  php -l tools\qa\check_identity_http_smoke.php' . PHP_EOL;
echo '  php -l tools\qa\run_identity_smoke_suite.php' . PHP_EOL;
echo '  php tools\qa\run_identity_smoke_suite.php' . PHP_EOL;
echo PHP_EOL;
echo 'Optional mit Seed:' . PHP_EOL;
echo '  php tools\qa\run_identity_smoke_suite.php --with-seed' . PHP_EOL;
echo PHP_EOL;
echo 'Optional HTTP-Smoke:' . PHP_EOL;
echo '  set SMOKE_BASE_URL=http://localhost/vdbs_portal/public' . PHP_EOL;
echo '  php tools\qa\check_identity_http_smoke.php' . PHP_EOL;
