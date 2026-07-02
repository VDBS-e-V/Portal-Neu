<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$stamp = 'mini-project-13-' . date('Ymd-His');
$archiveRoot = $root . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'archive' . DIRECTORY_SEPARATOR . $stamp . '-legacy-identity-permission-groups';

function project_path(string $relative): string
{
    global $root;
    return $root . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relative);
}

function ensure_dir(string $dir): void
{
    if (!is_dir($dir) && !mkdir($dir, 0777, true) && !is_dir($dir)) {
        throw new RuntimeException('Verzeichnis konnte nicht erstellt werden: ' . $dir);
    }
}

function backup_file(string $file, string $stamp): void
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

function write_project_file(string $relative, string $content, string $stamp): void
{
    $file = project_path($relative);
    ensure_dir(dirname($file));
    backup_file($file, $stamp);
    if (file_put_contents($file, $content) === false) {
        throw new RuntimeException('Datei konnte nicht geschrieben werden: ' . $file);
    }
    echo 'Aktualisiert: ' . $relative . PHP_EOL;
}

function update_file(string $relative, callable $callback, string $stamp): void
{
    $file = project_path($relative);
    if (!is_file($file)) {
        echo 'Übersprungen, Datei fehlt: ' . $relative . PHP_EOL;
        return;
    }
    $content = file_get_contents($file);
    if ($content === false) {
        throw new RuntimeException('Datei konnte nicht gelesen werden: ' . $file);
    }
    $newContent = $callback($content);
    if ($newContent === $content) {
        echo 'Unverändert: ' . $relative . PHP_EOL;
        return;
    }
    backup_file($file, $stamp);
    if (file_put_contents($file, $newContent) === false) {
        throw new RuntimeException('Datei konnte nicht geschrieben werden: ' . $file);
    }
    echo 'Aktualisiert: ' . $relative . PHP_EOL;
}

function archive_path_for(string $relative): string
{
    global $archiveRoot;
    return $archiveRoot . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relative);
}

function archive_file(string $relative): void
{
    $source = project_path($relative);
    if (!is_file($source)) {
        echo 'Bereits entfernt/fehlt: ' . $relative . PHP_EOL;
        return;
    }
    $target = archive_path_for($relative);
    ensure_dir(dirname($target));
    if (!rename($source, $target)) {
        throw new RuntimeException('Archivieren fehlgeschlagen: ' . $relative);
    }
    echo 'Archiviert: ' . $relative . ' -> ' . $target . PHP_EOL;
}

function remove_lines_containing(string $content, array $needles): string
{
    $lines = preg_split('/\R/', $content);
    if ($lines === false) {
        return $content;
    }
    $kept = [];
    foreach ($lines as $line) {
        $remove = false;
        foreach ($needles as $needle) {
            if (str_contains($line, $needle)) {
                $remove = true;
                break;
            }
        }
        if (!$remove) {
            $kept[] = $line;
        }
    }
    return implode(PHP_EOL, $kept);
}

function replace_function_body(string $content, string $functionName, string $replacement): string
{
    $pattern = '/(?:public|protected|private)\s+function\s+' . preg_quote($functionName, '/') . '\s*\(/';
    if (!preg_match($pattern, $content, $match, PREG_OFFSET_CAPTURE)) {
        return $content;
    }

    $start = (int) $match[0][1];
    $open = strpos($content, '{', $start);
    if ($open === false) {
        return $content;
    }

    $length = strlen($content);
    $depth = 0;
    $inString = null;
    $escape = false;
    $close = null;

    for ($i = $open; $i < $length; $i++) {
        $ch = $content[$i];
        if ($inString !== null) {
            if ($escape) {
                $escape = false;
                continue;
            }
            if ($ch === '\\') {
                $escape = true;
                continue;
            }
            if ($ch === $inString) {
                $inString = null;
            }
            continue;
        }
        if ($ch === '"' || $ch === "'") {
            $inString = $ch;
            continue;
        }
        if ($ch === '{') {
            $depth++;
            continue;
        }
        if ($ch === '}') {
            $depth--;
            if ($depth === 0) {
                $close = $i;
                break;
            }
        }
    }

    if ($close === null) {
        return $content;
    }

    return substr($content, 0, $start) . rtrim($replacement) . substr($content, $close + 1);
}

function patch_services(string $content): string
{
    $content = preg_replace('/^\s*use App\\\\Repository\\\\PermissionGroupRepository;\R/m', '', $content) ?? $content;
    $content = preg_replace('/^\s*use App\\\\Repository\\\\PersonPermissionGroupRepository;\R/m', '', $content) ?? $content;

    $content = preg_replace(
        '/\R\s*PermissionGroupRepository::class\s*=>\s*static function\s*\(Container \$container\):\s*PermissionGroupRepository\s*\{\s*return new PermissionGroupRepository\(\$container->get\(PDO::class\)\);\s*\},/s',
        '',
        $content
    ) ?? $content;
    $content = preg_replace(
        '/\R\s*PersonPermissionGroupRepository::class\s*=>\s*static function\s*\(Container \$container\):\s*PersonPermissionGroupRepository\s*\{\s*return new PersonPermissionGroupRepository\(\$container->get\(PDO::class\)\);\s*\},/s',
        '',
        $content
    ) ?? $content;

    $content = remove_lines_containing($content, [
        '$container->get(PermissionGroupRepository::class)',
        '$container->get(PersonPermissionGroupRepository::class)',
    ]);

    return $content;
}

function patch_audit_logger(string $content): string
{
    $content = remove_lines_containing($content, [
        'use App\\Repository\\PersonPermissionGroupRepository;',
        'PersonPermissionGroupRepository $personGroups',
        'PersonPermissionGroupRepository $personGroup',
        'private readonly PersonPermissionGroupRepository',
    ]);
    $content = str_replace('ids_permission_groups', 'ids_groups', $content);
    $content = str_replace('ids_user_permission_groups', 'ids_subject_groups', $content);
    return $content;
}

function patch_personen_controller(string $content): string
{
    $content = remove_lines_containing($content, [
        'use App\\Repository\\PermissionGroupRepository;',
        'use App\\Repository\\PersonPermissionGroupRepository;',
        'PermissionGroupRepository $permissionGroups',
        'PersonPermissionGroupRepository $personGroups',
        'private readonly PermissionGroupRepository $permissionGroups',
        'private readonly PersonPermissionGroupRepository $personGroups',
    ]);

    $content = str_replace('$this->permissionGroups->findAll()', '[]', $content);
    $content = str_replace('$this->personGroups->groupsForPerson($personId)', '[]', $content);

    $groupsReplacement = <<<'PHPFUNC'
public function groups(Request $request): Response
    {
        $this->authorization->requirePermission('portal.verwaltung.personen.view');
        $personId = $this->routeInt($request, 'id');
        return $this->redirect('/administration/personen/' . $personId . '/gruppen');
    }
PHPFUNC;
    $content = replace_function_body($content, 'groups', $groupsReplacement);

    $updateGroupsReplacement = <<<'PHPFUNC'
public function updateGroups(Request $request): Response
    {
        $this->authorization->requirePermission('portal.verwaltung.personen.view');
        $personId = $this->routeInt($request, 'id');
        return $this->redirect('/administration/personen/' . $personId . '/gruppen');
    }
PHPFUNC;
    $content = replace_function_body($content, 'updateGroups', $updateGroupsReplacement);

    $content = replace_function_body($content, 'groupIdsFromBody', <<<'PHPFUNC'
private function groupIdsFromBody(array $body): array
    {
        return [];
    }
PHPFUNC);

    return $content;
}

function patch_entity_audit_controller(string $content): string
{
    $content = remove_lines_containing($content, [
        'use App\\Repository\\PermissionGroupRepository;',
        'PermissionGroupRepository $groups',
        'private readonly PermissionGroupRepository $groups',
    ]);

    $groupReplacement = <<<'PHPFUNC'
public function group(Request $request): Response
    {
        $this->authorization->requirePermission('portal.verwaltung.entity-audit.view');
        $groupId = $this->routeInt($request, 'id');

        return $this->renderPage($request, 'pages/verwaltung/audit/entity', $this->pageParams([
            'title' => 'Audit: Gruppe',
            'pageTitle' => 'Audit: Gruppe #' . $groupId,
            'activeKey' => 'gruppen',
            'backHref' => '/administration/gruppen/' . $groupId,
            'backLabel' => 'Zur Gruppe',
            'entityTitle' => 'Gruppe #' . $groupId,
            'entitySubtitle' => 'Identity-Gruppe #' . $groupId,
            'entityType' => 'ids_groups',
            'entityId' => $groupId,
            'entries' => $this->audit->forGroup($groupId),
        ]));
    }
PHPFUNC;

    $content = replace_function_body($content, 'group', $groupReplacement);
    $content = str_replace('ids_permission_groups', 'ids_groups', $content);
    $content = str_replace('ids_user_permission_groups', 'ids_subject_groups', $content);
    return $content;
}

function patch_entity_audit_repository(string $content): string
{
    $content = str_replace('ids_permission_groups', 'ids_groups', $content);
    $content = str_replace('ids_user_permission_groups', 'ids_subject_groups', $content);
    return $content;
}

function patch_verwaltung_stats_repository(string $content): string
{
    $content = str_replace("'groups_total' => \$this->countTable('ids_permission_groups')", "'groups_total' => \$this->countTable('ids_groups')", $content);
    $content = str_replace("'groups_system' => \$this->countWhere('ids_permission_groups', 'is_system = 1')", "'groups_system' => \$this->countWhere('ids_groups', 'is_active = 1')", $content);
    $content = str_replace('ids_permission_groups', 'ids_groups', $content);
    $content = str_replace('ids_user_permission_groups', 'ids_subject_groups', $content);
    return $content;
}

function patch_person_repository(string $content): string
{
    $searchPersons = <<<'PHPFUNC'
private function searchPersons(array $filters): array
    {
        $joins = [
            'LEFT JOIN ids_person_names n ON n.person_id = p.id',
            'LEFT JOIN ids_users u ON u.person_id = p.id',
        ];
        $where = [];
        $params = [];

        $hasIdentityGroups = $this->tableExists('ids_subject_groups')
            && $this->tableExists('ids_groups')
            && $this->columnExists('ids_persons', 'subject_id');

        $groupSelect = 'NULL AS group_keys';
        if ($hasIdentityGroups) {
            $joins[] = 'LEFT JOIN ids_subject_groups sg ON sg.subject_id = p.subject_id AND (sg.expires_at IS NULL OR sg.expires_at > CURRENT_TIMESTAMP)';
            $joins[] = 'LEFT JOIN ids_groups g ON g.id = sg.group_id';
            $groupSelect = 'GROUP_CONCAT(DISTINCT g.group_key ORDER BY g.group_key SEPARATOR ", ") AS group_keys';
        }

        $q = trim((string) ($filters['q'] ?? ''));
        if ($q !== '') {
            $where[] = '(
                p.display_name LIKE :q
                OR n.first_name LIKE :q
                OR n.last_name LIKE :q
                OR n.preferred_name LIKE :q
                OR u.email LIKE :q
            )';
            $params['q'] = '%' . $q . '%';
        }

        $status = trim((string) ($filters['status'] ?? ''));
        if ($status !== '') {
            $where[] = 'p.status = :status';
            $params['status'] = $status;
        }

        $groupId = (int) ($filters['group_id'] ?? 0);
        if ($groupId > 0 && $hasIdentityGroups) {
            $where[] = 'sg.group_id = :group_id';
            $params['group_id'] = $groupId;
        }

        $limit = max(1, min(250, (int) ($filters['limit'] ?? 100)));
        $offset = max(0, (int) ($filters['offset'] ?? 0));

        $sql = 'SELECT p.*, HEX(p.person_uuid) AS person_uuid_hex,
                n.first_name, n.last_name, n.preferred_name,
                u.id AS user_id, u.email AS login_email, u.status AS login_status,
                ' . $groupSelect . '
            FROM ids_persons p '
            . implode("\n", $joins);

        if ($where !== []) {
            $sql .= "\nWHERE " . implode("\n AND ", $where);
        }

        $sql .= ' GROUP BY p.id, n.id, u.id
            ORDER BY p.display_name ASC, n.last_name ASC, n.first_name ASC, p.id ASC
            LIMIT :limit OFFSET :offset';

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
PHPFUNC;

    $searchLegacyUsers = <<<'PHPFUNC'
private function searchLegacyUsers(array $filters): array
    {
        $where = [];
        $params = [];

        $q = trim((string) ($filters['q'] ?? ''));
        if ($q !== '') {
            $where[] = '(
                u.display_name LIKE :q
                OR u.email LIKE :q
                OR n.first_name LIKE :q
                OR n.last_name LIKE :q
                OR n.preferred_name LIKE :q
            )';
            $params['q'] = '%' . $q . '%';
        }

        $status = trim((string) ($filters['status'] ?? ''));
        if ($status !== '') {
            $where[] = 'u.status = :status';
            $params['status'] = $status;
        }

        $limit = max(1, min(250, (int) ($filters['limit'] ?? 100)));
        $offset = max(0, (int) ($filters['offset'] ?? 0));

        $sql = 'SELECT u.id, u.id AS user_id, u.display_name, u.email AS login_email,
                u.status, u.status AS login_status, u.created_at, u.updated_at,
                n.first_name, n.last_name, n.preferred_name,
                NULL AS group_keys
            FROM ids_users u
            LEFT JOIN ids_user_names n ON n.user_id = u.id';

        if ($where !== []) {
            $sql .= "\nWHERE " . implode("\n AND ", $where);
        }

        $sql .= ' GROUP BY u.id, n.id
            ORDER BY u.display_name ASC, u.email ASC, u.id ASC
            LIMIT :limit OFFSET :offset';

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
PHPFUNC;

    $content = replace_function_body($content, 'searchPersons', $searchPersons);
    $content = replace_function_body($content, 'searchLegacyUsers', $searchLegacyUsers);

    $content = str_replace('ids_person_permission_groups', 'ids_subject_groups', $content);
    $content = str_replace('ids_permission_groups', 'ids_groups', $content);
    $content = str_replace('ids_user_permission_groups', 'ids_subject_groups', $content);
    $content = str_replace('permission_group_id', 'group_id', $content);

    return $content;
}

// 1) Produktive Dateien patchen.
update_file('config/services.php', 'patch_services', $stamp);
update_file('src/Audit/AuditLogger.php', 'patch_audit_logger', $stamp);
update_file('src/Http/Controller/Verwaltung/PersonenController.php', 'patch_personen_controller', $stamp);
update_file('src/Http/Controller/Verwaltung/EntityAuditController.php', 'patch_entity_audit_controller', $stamp);
update_file('src/Repository/EntityAuditRepository.php', 'patch_entity_audit_repository', $stamp);
update_file('src/Repository/VerwaltungStatsRepository.php', 'patch_verwaltung_stats_repository', $stamp);
update_file('src/Repository/PersonRepository.php', 'patch_person_repository', $stamp);

// 2) Alte Legacy-Repositories und alte Services-Block-Fragmente archivieren.
foreach ([
    'src/Repository/PermissionGroupRepository.php',
    'src/Repository/PersonPermissionGroupRepository.php',
    'config/services_berechtigungen_block.php',
    'config/services_entity_audit_block.php',
    'config/services_gruppen_block.php',
    'config/services_personen_block.php',
    'config/services_security_block.php',
] as $relative) {
    archive_file($relative);
}

// 3) QA-Tools installieren.
write_project_file('tools/qa/check_no_productive_legacy_identity_permission_group_usage.php', <<<'PHPFILE'
<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$targets = [
    'src',
    'config',
    'resources/views',
];
$needles = [
    'ids_permission_groups',
    'ids_user_permission_groups',
    'ids_person_permission_groups',
    'PermissionGroupRepository',
    'PersonPermissionGroupRepository',
    'permission_group_id',
];
$allowedFiles = [
    str_replace('/', DIRECTORY_SEPARATOR, 'tools/qa/check_legacy_identity_permission_group_usage.php'),
    str_replace('/', DIRECTORY_SEPARATOR, 'tools/qa/check_no_productive_legacy_identity_permission_group_usage.php'),
];

$hits = [];
foreach ($targets as $target) {
    $base = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $target);
    if (!is_dir($base)) {
        continue;
    }
    $iterator = new RecursiveIteratorIterator(
        new RecursiveCallbackFilterIterator(
            new RecursiveDirectoryIterator($base, FilesystemIterator::SKIP_DOTS),
            static function (SplFileInfo $file): bool {
                $path = $file->getPathname();
                if (str_contains($path, DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR)) {
                    return false;
                }
                if (str_contains($path, DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR)) {
                    return false;
                }
                return true;
            }
        )
    );

    foreach ($iterator as $file) {
        if (!$file instanceof SplFileInfo || !$file->isFile()) {
            continue;
        }
        $ext = strtolower($file->getExtension());
        if (!in_array($ext, ['php', 'sql', 'phtml'], true)) {
            continue;
        }
        $relative = ltrim(str_replace($root, '', $file->getPathname()), DIRECTORY_SEPARATOR);
        if (in_array($relative, $allowedFiles, true)) {
            continue;
        }
        $content = file_get_contents($file->getPathname());
        if ($content === false) {
            continue;
        }
        $lines = preg_split('/\R/', $content) ?: [];
        foreach ($lines as $index => $line) {
            foreach ($needles as $needle) {
                if (stripos($line, $needle) !== false) {
                    $hits[] = $relative . ':' . ($index + 1) . ': ' . $needle . ': ' . trim($line);
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

echo "OK: Keine produktiven Legacy-Identity-PermissionGroup-Verweise gefunden.\n";
PHPFILE, $stamp);

write_project_file('tools/qa/check_identity_permission_group_repositories_archived.php', <<<'PHPFILE'
<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$mustBeGone = [
    'src/Repository/PermissionGroupRepository.php',
    'src/Repository/PersonPermissionGroupRepository.php',
    'config/services_berechtigungen_block.php',
    'config/services_entity_audit_block.php',
    'config/services_gruppen_block.php',
    'config/services_personen_block.php',
    'config/services_security_block.php',
];

$errors = [];
foreach ($mustBeGone as $relative) {
    $path = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative);
    if (is_file($path)) {
        $errors[] = $relative . ' existiert noch im produktiven Pfad.';
    }
}

$archiveBase = $root . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'archive';
$hasArchive = false;
if (is_dir($archiveBase)) {
    foreach (new DirectoryIterator($archiveBase) as $entry) {
        if ($entry->isDir() && !$entry->isDot() && str_contains($entry->getFilename(), 'legacy-identity-permission-groups')) {
            $hasArchive = true;
            break;
        }
    }
}

if (!$hasArchive) {
    $errors[] = 'Kein Archivordner für legacy-identity-permission-groups gefunden.';
}

if ($errors !== []) {
    echo "Legacy-Identity-PermissionGroup-Archiv-Check fehlgeschlagen:\n";
    foreach ($errors as $error) {
        echo ' - ' . $error . "\n";
    }
    exit(1);
}

echo "OK: Legacy-Identity-PermissionGroup-Repositories und alte Service-Blöcke sind archiviert.\n";
PHPFILE, $stamp);

write_project_file('tools/qa/run_identity_permission_group_cleanup_checks.php', <<<'PHPFILE'
<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$php = PHP_BINARY;

$commands = [
    [$php, '-l', $root . '/config/services.php'],
    [$php, '-l', $root . '/src/Repository/PersonRepository.php'],
    [$php, '-l', $root . '/src/Repository/VerwaltungStatsRepository.php'],
    [$php, '-l', $root . '/src/Repository/EntityAuditRepository.php'],
    [$php, '-l', $root . '/src/Http/Controller/Verwaltung/PersonenController.php'],
    [$php, '-l', $root . '/src/Http/Controller/Verwaltung/EntityAuditController.php'],
    [$php, $root . '/tools/qa/run_identity_integrity_checks.php'],
    [$php, $root . '/tools/qa/check_identity_permission_group_repositories_archived.php'],
    [$php, $root . '/tools/qa/check_no_productive_legacy_identity_permission_group_usage.php'],
];

$failed = false;
foreach ($commands as $command) {
    $escaped = array_map(static fn (string $part): string => escapeshellarg($part), $command);
    echo 'Running: ' . implode(' ', $escaped) . "\n";
    passthru(implode(' ', $escaped), $exitCode);
    if ($exitCode !== 0) {
        echo 'FAILED: ' . implode(' ', $escaped) . "\n";
        $failed = true;
    }
}

if ($failed) {
    echo "Mini-Projekt-13-Identity-PermissionGroup-Cleanup-Checks haben Fehler gefunden.\n";
    exit(1);
}

echo "OK: Mini-Projekt-13-Identity-PermissionGroup-Cleanup-Checks bestanden.\n";
PHPFILE, $stamp);

// 4) Deprecation-Seed installieren. Tabellen werden noch nicht gelöscht.
write_project_file('database/seeds/seed_mini_project_13_deprecate_legacy_identity_permission_groups.sql', <<<'SQL'
CREATE TABLE IF NOT EXISTS ids_legacy_deprecations (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  object_type VARCHAR(64) NOT NULL,
  object_name VARCHAR(191) NOT NULL,
  deprecated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  replacement VARCHAR(191) NULL,
  note TEXT NULL,
  UNIQUE KEY uq_ids_legacy_deprecations_object (object_type, object_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO ids_legacy_deprecations (object_type, object_name, replacement, note)
VALUES
  ('table', 'ids_permission_groups', 'ids_groups', 'Alter Identity-Gruppenbestand ist aus produktivem Code entfernt. Tabelle erst nach finaler Datenprüfung droppen.'),
  ('table', 'ids_user_permission_groups', 'ids_subject_groups', 'Alte User-Gruppen-Zuordnung ist aus produktivem Code entfernt. Tabelle erst nach finaler Datenprüfung droppen.'),
  ('table', 'ids_person_permission_groups', 'ids_subject_groups', 'Alte Personen-Gruppen-Zuordnung ist aus produktivem Code entfernt. Tabelle erst nach finaler Datenprüfung droppen.')
ON DUPLICATE KEY UPDATE
  replacement = VALUES(replacement),
  note = VALUES(note);
SQL, $stamp);

// 5) Manifest schreiben.
$manifest = [
    'project' => 'identity-rights-mini-project-13',
    'stamp' => $stamp,
    'archive' => $archiveRoot,
    'archived_files' => [
        'src/Repository/PermissionGroupRepository.php',
        'src/Repository/PersonPermissionGroupRepository.php',
        'config/services_berechtigungen_block.php',
        'config/services_entity_audit_block.php',
        'config/services_gruppen_block.php',
        'config/services_personen_block.php',
        'config/services_security_block.php',
    ],
];
ensure_dir($archiveRoot);
file_put_contents($archiveRoot . DIRECTORY_SEPARATOR . 'manifest.json', json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
echo 'Manifest: ' . $archiveRoot . DIRECTORY_SEPARATOR . 'manifest.json' . PHP_EOL;

echo PHP_EOL;
echo "Mini-Projekt 13 Legacy-Identity-PermissionGroup-Cleanup wurde angewendet.\n";
echo "Bitte ausführen:\n";
echo "  php -l config\\services.php\n";
echo "  php -l src\\Repository\\PersonRepository.php\n";
echo "  php -l src\\Http\\Controller\\Verwaltung\\PersonenController.php\n";
echo "  php bin\\console seed\n";
echo "  php tools\\qa\\check_identity_permission_group_repositories_archived.php\n";
echo "  php tools\\qa\\check_no_productive_legacy_identity_permission_group_usage.php\n";
echo "  php tools\\qa\\run_identity_permission_group_cleanup_checks.php\n";
