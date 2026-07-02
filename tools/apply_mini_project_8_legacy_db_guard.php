<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$stamp = 'mini-project-8-' . date('Ymd-His');

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

function write_file_with_backup(string $relative, string $content, string $stamp): void
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
    echo 'Aktualisiert: ' . $path . PHP_EOL;
}

function read_existing(string $relative): ?string
{
    $path = project_path($relative);
    if (!is_file($path)) {
        return null;
    }
    $content = file_get_contents($path);
    if ($content === false) {
        throw new RuntimeException('Datei konnte nicht gelesen werden: ' . $path);
    }
    return $content;
}

function patch_verwaltung_stats_repository(string $stamp): void
{
    $relative = 'src/Repository/VerwaltungStatsRepository.php';
    $content = read_existing($relative);
    if ($content === null) {
        echo 'Hinweis: VerwaltungStatsRepository.php nicht vorhanden, übersprungen.' . PHP_EOL;
        return;
    }

    $original = $content;

    $content = str_replace(
        "'page_groups_total' => \$this->countTable('pt_page_groups'),",
        "'permissions_total' => \$this->countTable('ids_permissions'),",
        $content
    );
    $content = str_replace(
        '"page_groups_total" => $this->countTable("pt_page_groups"),',
        '"permissions_total" => $this->countTable("ids_permissions"),',
        $content
    );

    $content = str_replace(
        "'page_groups_active' => \$this->countWhere('pt_page_groups', 'is_active = 1'),",
        "'permissions_active' => \$this->countActivePermissions(),",
        $content
    );
    $content = str_replace(
        '"page_groups_active" => $this->countWhere("pt_page_groups", "is_active = 1"),',
        '"permissions_active" => $this->countActivePermissions(),',
        $content
    );

    if (str_contains($content, "pt_page_groups")) {
        $content = str_replace(
            "'page_groups_total' => $this->countTable('pt_page_groups'),",
            "'permissions_total' => $this->countTable('ids_permissions'),",
            $content
        );
        $content = str_replace(
            "'page_groups_active' => $this->countWhere('pt_page_groups', 'is_active = 1'),",
            "'permissions_active' => $this->countActivePermissions(),",
            $content
        );
    }

    if (!str_contains($content, 'function countActivePermissions(')) {
        $helper = <<<'PHP_HELPER'

    private function countActivePermissions(): int
    {
        if (!$this->tableExists('ids_permissions')) {
            return 0;
        }

        if ($this->columnExists('ids_permissions', 'is_active')) {
            return $this->countWhere('ids_permissions', 'is_active = 1');
        }

        if ($this->columnExists('ids_permissions', 'status')) {
            return $this->countWhere('ids_permissions', 'status = :status', ['status' => 'active']);
        }

        return $this->countTable('ids_permissions');
    }

    private function columnExists(string $table, string $column): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table_name AND COLUMN_NAME = :column_name'
        );
        $stmt->execute([
            'table_name' => $table,
            'column_name' => $column,
        ]);

        return (int) $stmt->fetchColumn() > 0;
    }
PHP_HELPER;

        $needle = 'private function countTable(string $table): int';
        if (str_contains($content, $needle)) {
            $content = str_replace($needle, $helper . "\n\n    " . $needle, $content);
        } else {
            echo 'WARN: countTable() in VerwaltungStatsRepository.php nicht gefunden; Helper nicht eingefügt.' . PHP_EOL;
        }
    }

    if ($content !== $original) {
        write_file_with_backup($relative, $content, $stamp);
        return;
    }

    echo 'OK: VerwaltungStatsRepository.php enthält keine bekannten alten Statistik-Verweise oder war bereits bereinigt.' . PHP_EOL;
}

function patch_entity_audit_repository(string $stamp): void
{
    $relative = 'src/Repository/EntityAuditRepository.php';
    $content = read_existing($relative);
    if ($content === null) {
        echo 'Hinweis: EntityAuditRepository.php nicht vorhanden, übersprungen.' . PHP_EOL;
        return;
    }

    $original = $content;

    $content = str_replace(
        "['pt_permission_group_page_group_access', \$groupId],",
        "['ids_groups', \$groupId],\n            ['ids_subject_groups', \$groupId],\n            ['ids_group_permissions', \$groupId],",
        $content
    );
    $content = str_replace(
        '["pt_permission_group_page_group_access", $groupId],',
        '["ids_groups", $groupId],' . "\n            " . '["ids_subject_groups", $groupId],' . "\n            " . '["ids_group_permissions", $groupId],',
        $content
    );
    $content = str_replace(
        "'pt_permission_group_page_group_access'",
        "'ids_group_permissions'",
        $content
    );
    $content = str_replace(
        '"pt_permission_group_page_group_access"',
        '"ids_group_permissions"',
        $content
    );

    if ($content !== $original) {
        write_file_with_backup($relative, $content, $stamp);
        return;
    }

    echo 'OK: EntityAuditRepository.php enthält keinen alten PageGroup-Audit-Tabellenverweis oder war bereits bereinigt.' . PHP_EOL;
}

function patch_verwaltung_dashboard_view(string $stamp): void
{
    $relative = 'resources/views/pages/verwaltung/index.php';
    $content = read_existing($relative);
    if ($content === null) {
        echo 'Hinweis: Verwaltung-Dashboard-View nicht vorhanden, übersprungen.' . PHP_EOL;
        return;
    }

    $original = $content;

    $replacements = [
        'page_groups_total' => 'permissions_total',
        'page_groups_active' => 'permissions_active',
        'page_group_id' => 'permission_id',
        'page_groups' => 'permissions',
        'page_group' => 'permission',
        'PageGroups' => 'Permissions',
        'Page Groups' => 'Permissions',
        'Seitengruppen' => 'Berechtigungen',
        'Seitengruppe' => 'Berechtigung',
        'aktive Seitengruppen' => 'aktive Berechtigungen',
        'Aktive Seitengruppen' => 'Aktive Berechtigungen',
    ];

    $content = strtr($content, $replacements);

    if ($content !== $original) {
        write_file_with_backup($relative, $content, $stamp);
        return;
    }

    echo 'OK: Verwaltung-Dashboard-View enthält keine bekannten PageGroup-Statistikreste oder war bereits bereinigt.' . PHP_EOL;
}

function install_project_file(string $relative, string $stamp): void
{
    $source = __DIR__ . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relative);
    $target = project_path($relative);

    if (!is_file($source)) {
        throw new RuntimeException('Quelldatei fehlt: ' . $source);
    }

    $content = file_get_contents($source);
    if ($content === false) {
        throw new RuntimeException('Quelldatei konnte nicht gelesen werden: ' . $source);
    }

    write_file_with_backup($relative, $content, $stamp);
}

patch_verwaltung_stats_repository($stamp);
patch_entity_audit_repository($stamp);
patch_verwaltung_dashboard_view($stamp);

install_project_file('tools/qa/check_no_productive_legacy_pagegroup_db_usage.php', $stamp);
install_project_file('tools/qa/check_legacy_db_deprecation_markers.php', $stamp);
install_project_file('tools/qa/check_legacy_service_references.php', $stamp);
install_project_file('tools/qa/run_identity_db_legacy_checks.php', $stamp);
install_project_file('database/seeds/seed_mini_project_8_deprecate_legacy_pagegroup_db.sql', $stamp);
install_project_file('database/sql/verify_mini_project_8_legacy_db_guard.sql', $stamp);

echo PHP_EOL;
echo 'Mini-Projekt 8 Legacy-DB-Absicherung wurde angewendet.' . PHP_EOL;
echo 'Bitte ausführen:' . PHP_EOL;
echo '  php -l src\\Repository\\VerwaltungStatsRepository.php' . PHP_EOL;
echo '  php -l src\\Repository\\EntityAuditRepository.php' . PHP_EOL;
echo '  php bin\\console seed' . PHP_EOL;
echo '  php tools\\qa\\check_no_productive_legacy_pagegroup_db_usage.php' . PHP_EOL;
echo '  php tools\\qa\\check_legacy_db_deprecation_markers.php' . PHP_EOL;
echo '  php tools\\qa\\run_identity_db_legacy_checks.php' . PHP_EOL;
