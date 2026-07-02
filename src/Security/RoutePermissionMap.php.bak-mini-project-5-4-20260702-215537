<?php

declare(strict_types=1);

namespace App\Security;

/**
 * Zentrale Abbildung von HTTP-Routen auf feine Permissions.
 *
 * Konto-Seiten brauchen nur einen eingeloggten User und keine Vereins-/Admin-Permission.
 * Die Login-Prüfung passiert im UserAccountController.
 */
final class RoutePermissionMap
{
    /** @return array<int,array{methods:array<int,string>,pattern:string,permission:string}> */
    public function entries(): array
    {
        return [
            $this->get('/administration', 'portal.verwaltung.dashboard.view'),

            $this->get('/administration/systeme', 'identity.systeme.view'),
            $this->get('/administration/systeme/create', 'identity.systeme.create'),
            $this->post('/administration/systeme/create', 'identity.systeme.create'),
            $this->get('/administration/systeme/{id}', 'identity.systeme.view'),
            $this->get('/administration/systeme/{id}/edit', 'identity.systeme.edit'),
            $this->post('/administration/systeme/{id}/edit', 'identity.systeme.edit'),

            $this->get('/administration/gruppen', 'identity.gruppen.view'),
            $this->get('/administration/gruppen/create', 'identity.gruppen.create'),
            $this->post('/administration/gruppen/create', 'identity.gruppen.create'),
            $this->get('/administration/gruppen/{id}', 'identity.gruppen.view'),
            $this->get('/administration/gruppen/{id}/edit', 'identity.gruppen.edit'),
            $this->post('/administration/gruppen/{id}/edit', 'identity.gruppen.edit'),
            $this->get('/administration/gruppen/{id}/permissions', 'identity.gruppen.permissions.manage'),
            $this->post('/administration/gruppen/{id}/permissions', 'identity.gruppen.permissions.manage'),
            $this->post('/administration/gruppen/{id}/delete', 'identity.gruppen.delete'),

            $this->get('/administration/permissions', 'identity.permissions.view'),
            $this->get('/administration/permissions/create', 'identity.permissions.create'),
            $this->post('/administration/permissions/create', 'identity.permissions.create'),
            $this->get('/administration/permissions/{id}', 'identity.permissions.view'),
            $this->get('/administration/permissions/{id}/edit', 'identity.permissions.edit'),
            $this->post('/administration/permissions/{id}/edit', 'identity.permissions.edit'),
            $this->post('/administration/permissions/{id}/deactivate', 'identity.permissions.delete'),

            $this->get('/administration/personen', 'identity.subjects.groups.view'),
            $this->get('/administration/personen/{id}/gruppen', 'identity.subjects.groups.view'),
            $this->post('/administration/personen/{id}/gruppen', 'identity.subjects.groups.assign'),
            $this->post('/administration/personen/{id}/gruppen/{groupId}/remove', 'identity.subjects.groups.remove'),
            $this->get('/administration/subjects/{id}', 'identity.subjects.view'),

            // Legacy-Verwaltung-Aliase, die auf neue Administration-Controller zeigen.
            $this->get('/verwaltung/gruppen', 'identity.gruppen.view'),
            $this->get('/verwaltung/gruppen/create', 'identity.gruppen.create'),
            $this->post('/verwaltung/gruppen/create', 'identity.gruppen.create'),
            $this->get('/verwaltung/gruppen/{id}', 'identity.gruppen.view'),
            $this->get('/verwaltung/gruppen/{id}/edit', 'identity.gruppen.edit'),
            $this->post('/verwaltung/gruppen/{id}/edit', 'identity.gruppen.edit'),
            $this->get('/verwaltung/gruppen/{id}/permissions', 'identity.gruppen.permissions.manage'),
            $this->post('/verwaltung/gruppen/{id}/permissions', 'identity.gruppen.permissions.manage'),
            $this->post('/verwaltung/gruppen/{id}/delete', 'identity.gruppen.delete'),

            $this->get('/verwaltung/berechtigungen', 'identity.permissions.view'),
            $this->get('/verwaltung/permissions', 'identity.permissions.view'),
            $this->get('/verwaltung/permissions/create', 'identity.permissions.create'),
            $this->post('/verwaltung/permissions/create', 'identity.permissions.create'),
            $this->get('/verwaltung/permissions/{id}', 'identity.permissions.view'),
            $this->get('/verwaltung/permissions/{id}/edit', 'identity.permissions.edit'),
            $this->post('/verwaltung/permissions/{id}/edit', 'identity.permissions.edit'),
            $this->post('/verwaltung/permissions/{id}/deactivate', 'identity.permissions.delete'),

            $this->get('/verwaltung/personen-gruppen', 'identity.subjects.groups.view'),
            $this->get('/verwaltung/personen/{id}/gruppen', 'identity.subjects.groups.view'),
            $this->post('/verwaltung/personen/{id}/gruppen', 'identity.subjects.groups.assign'),
            $this->post('/verwaltung/personen/{id}/gruppen/{groupId}/remove', 'identity.subjects.groups.remove'),
            $this->get('/verwaltung/systeme', 'identity.systeme.view'),

            // Bestehende Portal-/Verwaltungsrouten als Kompatibilitätsnetz.
            $this->get('/verwaltung', 'portal.verwaltung.dashboard.view'),
            $this->get('/verwaltung/personen', 'portal.verwaltung.personen.view'),
            $this->get('/verwaltung/personen/create', 'portal.verwaltung.personen.create'),
            $this->post('/verwaltung/personen/create', 'portal.verwaltung.personen.create'),
            $this->get('/verwaltung/personen/{id}', 'portal.verwaltung.personen.view'),
            $this->get('/verwaltung/personen/{id}/edit', 'portal.verwaltung.personen.edit'),
            $this->post('/verwaltung/personen/{id}/edit', 'portal.verwaltung.personen.edit'),
            $this->post('/verwaltung/personen/{id}/status', 'portal.verwaltung.personen.edit'),
            $this->get('/verwaltung/personen/export', 'portal.verwaltung.personen.export'),

            $this->get('/verwaltung/personen/{id}/kontakte', 'portal.verwaltung.personen.view'),
            $this->post('/verwaltung/personen/{id}/kontakte', 'portal.verwaltung.personen.edit'),
            $this->post('/verwaltung/personen/{id}/kontakte/{contactId}/delete', 'portal.verwaltung.personen.edit'),
            $this->get('/verwaltung/personen/{id}/adressen', 'portal.verwaltung.personen.view'),
            $this->post('/verwaltung/personen/{id}/adressen', 'portal.verwaltung.personen.edit'),
            $this->post('/verwaltung/personen/{id}/adressen/{addressId}/delete', 'portal.verwaltung.personen.edit'),

            $this->get('/verwaltung/audit', 'portal.verwaltung.audit.view'),
            $this->get('/verwaltung/audit/{id}', 'portal.verwaltung.audit.view'),
            $this->get('/verwaltung/personen/{id}/audit', 'portal.verwaltung.audit.view'),
            $this->get('/verwaltung/gruppen/{id}/audit', 'portal.verwaltung.audit.view'),

            $this->get('/verwaltung/einladungen', 'portal.verwaltung.einladungen.view'),
            $this->post('/verwaltung/personen/{id}/einladung', 'portal.verwaltung.einladungen.create'),
            $this->post('/verwaltung/einladungen/{id}/revoke', 'portal.verwaltung.einladungen.revoke'),

            $this->get('/verwaltung/datenschutz', 'portal.verwaltung.datenschutz.view'),
            $this->get('/verwaltung/personen/{id}/datenschutz/loeschung', 'portal.verwaltung.datenschutz.create'),
            $this->post('/verwaltung/personen/{id}/datenschutz/loeschung', 'portal.verwaltung.datenschutz.create'),
            $this->get('/verwaltung/datenschutz/{id}', 'portal.verwaltung.datenschutz.view'),
            $this->post('/verwaltung/datenschutz/{id}/approve', 'portal.verwaltung.datenschutz.approve'),
            $this->post('/verwaltung/datenschutz/{id}/reject', 'portal.verwaltung.datenschutz.edit'),
            $this->post('/verwaltung/datenschutz/{id}/cancel', 'portal.verwaltung.datenschutz.edit'),
            $this->post('/verwaltung/datenschutz/{id}/complete', 'portal.verwaltung.datenschutz.edit'),
            $this->get('/verwaltung/datenschutz/{id}/audit', 'portal.verwaltung.audit.view'),

            $this->get('/verwaltung/schulverzeichnis', 'portal.verwaltung.schulverzeichnis.view'),
            $this->get('/verwaltung/schulverzeichnis/{id}', 'portal.verwaltung.schulverzeichnis.view'),
            $this->post('/verwaltung/schulverzeichnis', 'portal.verwaltung.schulverzeichnis.edit'),
            $this->post('/verwaltung/schulverzeichnis/{id}', 'portal.verwaltung.schulverzeichnis.edit'),
        ];
    }

    public function permissionFor(string $method, string $path): ?string
    {
        $method = strtoupper(trim($method));
        $path = $this->normalizePath($path);

        if ($this->isAccountRoute($path)) {
            return null;
        }

        foreach ($this->entries() as $entry) {
            if (in_array($method, $entry['methods'], true) && $this->matches($entry['pattern'], $path)) {
                return $entry['permission'];
            }
        }

        return null;
    }

    /** @return array{methods:array<int,string>,pattern:string,permission:string} */
    private function get(string $pattern, string $permission): array
    {
        return $this->route(['GET'], $pattern, $permission);
    }

    /** @return array{methods:array<int,string>,pattern:string,permission:string} */
    private function post(string $pattern, string $permission): array
    {
        return $this->route(['POST'], $pattern, $permission);
    }

    /** @param array<int,string> $methods @return array{methods:array<int,string>,pattern:string,permission:string} */
    private function route(array $methods, string $pattern, string $permission): array
    {
        return [
            'methods' => array_values(array_map('strtoupper', $methods)),
            'pattern' => $this->normalizePath($pattern),
            'permission' => $permission,
        ];
    }

    private function isAccountRoute(string $path): bool
    {
        return $path === '/konto'
            || $path === '/konto/einstellungen'
            || $path === '/konto/passwort';
    }

    private function matches(string $pattern, string $path): bool
    {
        if ($pattern === $path) {
            return true;
        }

        $segments = explode('/', trim($pattern, '/'));
        $regexSegments = [];
        foreach ($segments as $segment) {
            $regexSegments[] = preg_match('/^\{[a-zA-Z_][a-zA-Z0-9_]*\}$/', $segment) === 1
                ? '[^/]+'
                : preg_quote($segment, '#');
        }

        return preg_match('#^/' . implode('/', $regexSegments) . '$#', $path) === 1;
    }

    private function normalizePath(string $path): string
    {
        $path = parse_url($path, PHP_URL_PATH) ?: '/';
        $path = '/' . ltrim($path, '/');
        return $path === '/' ? '/' : rtrim($path, '/');
    }
}
