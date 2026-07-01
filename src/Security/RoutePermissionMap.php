<?php

declare(strict_types=1);

namespace App\Security;

/**
 * Zentrale Abbildung von HTTP-Routen auf feine Permissions.
 *
 * Ziel: Controller müssen nicht mehr requirePageGroupAccess(...) kennen.
 * Der Router kann vor dem Controller-Aufruf anhand dieser Map prüfen,
 * ob die aktuelle Identität die passende Permission besitzt.
 */
final class RoutePermissionMap
{
    /**
     * @return array<int,array{methods:array<int,string>,pattern:string,permission:string}>
     */
    public function entries(): array
    {
        return [
            // Neue Zielrouten unter /administration.
            $this->get('/administration', 'portal.verwaltung.dashboard.view'),

            $this->get('/administration/systeme', 'identity.systeme.view'),
            $this->get('/administration/systeme/{id}', 'identity.systeme.view'),
            $this->post('/administration/systeme', 'identity.systeme.create'),
            $this->post('/administration/systeme/{id}', 'identity.systeme.edit'),
            $this->delete('/administration/systeme/{id}', 'identity.systeme.delete'),

            $this->get('/administration/gruppen', 'identity.gruppen.view'),
            $this->get('/administration/gruppen/{id}', 'identity.gruppen.view'),
            $this->get('/administration/gruppen/{id}/permissions', 'identity.gruppen.permissions.manage'),
            $this->post('/administration/gruppen', 'identity.gruppen.create'),
            $this->post('/administration/gruppen/{id}', 'identity.gruppen.edit'),
            $this->post('/administration/gruppen/{id}/permissions', 'identity.gruppen.permissions.manage'),
            $this->delete('/administration/gruppen/{id}', 'identity.gruppen.delete'),

            $this->get('/administration/permissions', 'identity.permissions.view'),
            $this->get('/administration/permissions/{id}', 'identity.permissions.view'),
            $this->post('/administration/permissions', 'identity.permissions.create'),
            $this->post('/administration/permissions/{id}', 'identity.permissions.edit'),
            $this->delete('/administration/permissions/{id}', 'identity.permissions.delete'),

            $this->get('/administration/personen/{id}/gruppen', 'identity.subjects.groups.view'),
            $this->post('/administration/personen/{id}/gruppen', 'identity.subjects.groups.assign'),
            $this->delete('/administration/personen/{id}/gruppen/{groupId}', 'identity.subjects.groups.remove'),
            $this->get('/administration/subjects/{id}', 'identity.subjects.view'),

            // Bestehende Portal-/Verwaltungsrouten, damit der Umbau schrittweise funktioniert.
            $this->get('/verwaltung', 'portal.verwaltung.dashboard.view'),

            $this->get('/verwaltung/personen', 'portal.verwaltung.personen.view'),
            $this->get('/verwaltung/personen/create', 'portal.verwaltung.personen.create'),
            $this->get('/verwaltung/personen/{id}', 'portal.verwaltung.personen.view'),
            $this->get('/verwaltung/personen/{id}/edit', 'portal.verwaltung.personen.edit'),
            $this->post('/verwaltung/personen', 'portal.verwaltung.personen.create'),
            $this->post('/verwaltung/personen/{id}', 'portal.verwaltung.personen.edit'),
            $this->delete('/verwaltung/personen/{id}', 'portal.verwaltung.personen.delete'),
            $this->get('/verwaltung/personen/export', 'portal.verwaltung.personen.export'),

            $this->get('/verwaltung/gruppen', 'identity.gruppen.view'),
            $this->get('/verwaltung/gruppen/{id}', 'identity.gruppen.view'),
            $this->post('/verwaltung/gruppen', 'identity.gruppen.create'),
            $this->post('/verwaltung/gruppen/{id}', 'identity.gruppen.edit'),
            $this->delete('/verwaltung/gruppen/{id}', 'identity.gruppen.delete'),

            $this->get('/verwaltung/berechtigungen', 'identity.permissions.view'),
            $this->post('/verwaltung/berechtigungen', 'identity.gruppen.permissions.manage'),
            $this->get('/verwaltung/permissions', 'identity.permissions.view'),
            $this->post('/verwaltung/permissions', 'identity.permissions.edit'),

            $this->get('/verwaltung/einladungen', 'portal.verwaltung.einladungen.view'),
            $this->post('/verwaltung/einladungen', 'portal.verwaltung.einladungen.create'),
            $this->post('/verwaltung/einladungen/{id}/widerrufen', 'portal.verwaltung.einladungen.revoke'),

            $this->get('/verwaltung/datenschutz', 'portal.verwaltung.datenschutz.view'),
            $this->post('/verwaltung/datenschutz/{id}', 'portal.verwaltung.datenschutz.edit'),
            $this->post('/verwaltung/datenschutz/{id}/freigeben', 'portal.verwaltung.datenschutz.approve'),

            $this->get('/verwaltung/audit', 'portal.verwaltung.audit.view'),
            $this->get('/verwaltung/audit/{id}', 'portal.verwaltung.audit.view'),

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

        foreach ($this->entries() as $entry) {
            if (!in_array($method, $entry['methods'], true)) {
                continue;
            }

            if ($this->matches($entry['pattern'], $path)) {
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

    /** @return array{methods:array<int,string>,pattern:string,permission:string} */
    private function delete(string $pattern, string $permission): array
    {
        return $this->route(['DELETE', 'POST'], $pattern, $permission);
    }

    /**
     * @param array<int,string> $methods
     * @return array{methods:array<int,string>,pattern:string,permission:string}
     */
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

        $parameterNames = [];
        $segments = explode('/', trim($pattern, '/'));
        $regexSegments = [];

        foreach ($segments as $segment) {
            if (preg_match('/^\{([a-zA-Z_][a-zA-Z0-9_]*)\}$/', $segment, $matches) === 1) {
                $parameterNames[] = $matches[1];
                $regexSegments[] = '[^/]+';
                continue;
            }

            $regexSegments[] = preg_quote($segment, '#');
        }

        $regex = '#^/' . implode('/', $regexSegments) . '$#';
        return preg_match($regex, $path) === 1;
    }

    private function normalizePath(string $path): string
    {
        $path = parse_url($path, PHP_URL_PATH) ?: '/';
        $path = '/' . ltrim($path, '/');
        if ($path !== '/') {
            $path = rtrim($path, '/');
        }

        return $path;
    }
}
