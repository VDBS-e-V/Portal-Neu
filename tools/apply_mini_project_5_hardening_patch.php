<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$now = date('Ymd-His');

patchAuthorizationService($root, $now);
patchRoutePermissionMap($root, $now);

fwrite(STDOUT, "Mini-Projekt 5 Hardening-Patch wurde angewendet.\n");
fwrite(STDOUT, "Bitte ausführen:\n");
fwrite(STDOUT, "  php -l src\\Security\\AuthorizationService.php\n");
fwrite(STDOUT, "  php -l src\\Security\\RoutePermissionMap.php\n");
fwrite(STDOUT, "  php bin\\console seed\n");
fwrite(STDOUT, "  php tools\\qa\\run_identity_rights_checks.php\n");

function patchAuthorizationService(string $root, string $now): void
{
    $file = $root . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Security' . DIRECTORY_SEPARATOR . 'AuthorizationService.php';
    if (!is_file($file)) {
        fail('AuthorizationService.php nicht gefunden: ' . $file);
    }

    $code = file_get_contents($file);
    if ($code === false) {
        fail('AuthorizationService.php konnte nicht gelesen werden.');
    }

    if (str_contains($code, 'legacyPageGroupPermissionKey(')) {
        fwrite(STDOUT, "AuthorizationService.php enthält Legacy-Bridge bereits.\n");
        return;
    }

    $marker = "    /** @param array<string,mixed> \$user */\n    private function isActiveUser(array \$user): bool\n";
    if (!str_contains($code, $marker)) {
        fail('Marker für isActiveUser() nicht gefunden. AuthorizationService.php wurde nicht geändert.');
    }

    $insert = <<<'PHP_CODE'
    public function currentPersonId(): ?int
    {
        $user = $this->currentUser();
        if ($user === []) {
            return null;
        }

        $personId = $user['person_id'] ?? null;
        if ($personId === null || $personId === '') {
            return null;
        }

        return (int) $personId;
    }

    /**
     * Temporäre Kompatibilitätsbrücke für alte Verwaltung-Controller.
     *
     * Neue Implementierungen müssen requirePermission() verwenden. Diese Methode
     * verhindert aber, dass noch aktive Legacy-Routen mit einem Fatal Error
     * abbrechen, solange die alten Controller schrittweise entfernt werden.
     *
     * @return array<string,mixed>
     */
    public function requirePageGroupAccess(string $areaKey, string $pageGroupKey): array
    {
        return $this->requirePermission(
            $this->legacyPageGroupPermissionKey($areaKey, $pageGroupKey)
        );
    }

    public function canAccessPageGroup(string $areaKey, string $pageGroupKey): bool
    {
        return $this->can(
            $this->legacyPageGroupPermissionKey($areaKey, $pageGroupKey)
        );
    }

    public function currentUserCanAccessPageGroup(string $areaKey, string $pageGroupKey): bool
    {
        return $this->canAccessPageGroup($areaKey, $pageGroupKey);
    }

    private function legacyPageGroupPermissionKey(string $areaKey, string $pageGroupKey): string
    {
        $areaKey = $this->normalizeLegacyKey($areaKey);
        $pageGroupKey = $this->normalizeLegacyKey($pageGroupKey);
        $compound = $areaKey . '.' . $pageGroupKey;

        $map = [
            'verwaltung.dashboard' => 'portal.verwaltung.dashboard.view',
            'verwaltung.personen' => 'portal.verwaltung.personen.view',
            'verwaltung.gruppen' => 'identity.gruppen.view',
            'verwaltung.berechtigungen' => 'identity.permissions.view',
            'verwaltung.permissions' => 'identity.permissions.view',
            'verwaltung.audit' => 'portal.verwaltung.audit.view',
            'verwaltung.entity-audit' => 'portal.verwaltung.entity-audit.view',
            'verwaltung.datenschutz' => 'portal.verwaltung.datenschutz.view',
            'verwaltung.einladungen' => 'portal.verwaltung.einladungen.view',
            'verwaltung.schulverzeichnis' => 'portal.verwaltung.schulverzeichnis.view',
            'verwaltung.schulen' => 'portal.verwaltung.schulverzeichnis.view',
        ];

        if (isset($map[$compound])) {
            return $map[$compound];
        }

        if ($areaKey === 'verwaltung') {
            return 'portal.verwaltung.' . $pageGroupKey . '.view';
        }

        if ($areaKey === 'portal') {
            return 'portal.' . $pageGroupKey . '.view';
        }

        return $areaKey . '.' . $pageGroupKey . '.view';
    }

    private function normalizeLegacyKey(string $key): string
    {
        $key = strtolower(trim($key));
        $key = str_replace('_', '-', $key);
        $key = preg_replace('/[^a-z0-9.-]+/', '-', $key) ?? $key;
        $key = preg_replace('/-+/', '-', $key) ?? $key;
        return trim($key, '-.');
    }

PHP_CODE;

    backup($file, $now);
    $code = str_replace($marker, $insert . $marker, $code);
    file_put_contents($file, $code);
    fwrite(STDOUT, "AuthorizationService.php wurde erweitert.\n");
}

function patchRoutePermissionMap(string $root, string $now): void
{
    $file = $root . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Security' . DIRECTORY_SEPARATOR . 'RoutePermissionMap.php';
    if (!is_file($file)) {
        fwrite(STDOUT, "RoutePermissionMap.php nicht gefunden, überspringe Route-Map-Patch.\n");
        return;
    }

    $code = file_get_contents($file);
    if ($code === false) {
        fail('RoutePermissionMap.php konnte nicht gelesen werden.');
    }

    if (str_contains($code, "'/verwaltung/datenschutz'")) {
        fwrite(STDOUT, "RoutePermissionMap.php enthält Mini-Projekt-5-Routen bereits.\n");
        return;
    }

    $needle = "            \$this->get('/verwaltung/audit', 'portal.verwaltung.audit.view'),\n";
    if (!str_contains($code, $needle)) {
        fwrite(STDOUT, "Marker für /verwaltung/audit nicht gefunden, Route-Map-Patch übersprungen.\n");
        return;
    }

    $extra = $needle
        . "            \$this->get('/verwaltung/entity-audit', 'portal.verwaltung.entity-audit.view'),\n"
        . "            \$this->get('/verwaltung/datenschutz', 'portal.verwaltung.datenschutz.view'),\n"
        . "            \$this->get('/verwaltung/einladungen', 'portal.verwaltung.einladungen.view'),\n"
        . "            \$this->get('/verwaltung/schulverzeichnis', 'portal.verwaltung.schulverzeichnis.view'),\n";

    backup($file, $now);
    $code = str_replace($needle, $extra, $code);
    file_put_contents($file, $code);
    fwrite(STDOUT, "RoutePermissionMap.php wurde erweitert.\n");
}

function backup(string $file, string $now): void
{
    $backup = $file . '.bak-mini-project-5-' . $now;
    if (!copy($file, $backup)) {
        fail('Backup konnte nicht erstellt werden: ' . $backup);
    }
    fwrite(STDOUT, "Backup: {$backup}\n");
}

function fail(string $message): never
{
    fwrite(STDERR, $message . "\n");
    exit(1);
}
