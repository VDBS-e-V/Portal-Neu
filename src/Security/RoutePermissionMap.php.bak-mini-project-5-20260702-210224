<?php

declare(strict_types=1);

namespace App\Security;

/**
 * Zentrale Abbildung von HTTP-Routen auf feine Permissions.
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

            // Bestehende Portal-/Verwaltungsrouten als Kompatibilitätsnetz.
            $this->get('/verwaltung', 'portal.verwaltung.dashboard.view'),
            $this->get('/verwaltung/personen', 'portal.verwaltung.personen.view'),
            $this->get('/verwaltung/personen/create', 'portal.verwaltung.personen.create'),
            $this->get('/verwaltung/personen/{id}', 'portal.verwaltung.personen.view'),
            $this->get('/verwaltung/personen/{id}/edit', 'portal.verwaltung.personen.edit'),
            $this->post('/verwaltung/personen', 'portal.verwaltung.personen.create'),
            $this->post('/verwaltung/personen/{id}', 'portal.verwaltung.personen.edit'),
            $this->post('/verwaltung/personen/{id}/delete', 'portal.verwaltung.personen.delete'),
            $this->get('/verwaltung/personen/export', 'portal.verwaltung.personen.export'),
            $this->get('/verwaltung/gruppen', 'identity.gruppen.view'),
            $this->get('/verwaltung/berechtigungen', 'identity.permissions.view'),
            $this->get('/verwaltung/audit', 'portal.verwaltung.audit.view'),
        ];
    }

    public function permissionFor(string $method, string $path): ?string
    {
        $method = strtoupper(trim($method));
        $path = $this->normalizePath($path);
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
