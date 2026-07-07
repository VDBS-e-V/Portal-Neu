<?php

declare(strict_types=1);

namespace App\Security;

use App\Http\Request\Request;
use Throwable;

/**
 * Erzwingt Permission-Prüfungen auf Routenebene.
 *
 * Wichtig für HTTP-Routen: anonyme Zugriffe auf geschützte Seiten dürfen nicht
 * als ungefangene AuthorizationException bis zum globalen Error-Handler laufen,
 * weil daraus sonst HTTP 500 entsteht. Für Web-Routen wird deshalb sauber auf
 * /login umgeleitet; API-Routen erhalten JSON 401/403.
 */
final class RoutePermissionGuard
{
    public function __construct(
        private readonly AuthorizationService $authorization,
        private readonly RoutePermissionMap $permissionMap
    ) {
    }

    public function guard(Request $request): void
    {
        $permission = $this->permissionMap->permissionFor($request->method, $request->path);
        if ($permission === null) {
            return;
        }

        if (!$this->authorization->isLoggedIn()) {
            $this->respondUnauthenticated($request);
        }

        try {
            $this->authorization->requirePermission($permission);
        } catch (AuthorizationException $exception) {
            $status = (int) $exception->getCode();
            if ($status === 401) {
                $this->respondUnauthenticated($request);
            }

            $this->respondForbidden($request, $exception->getMessage() !== '' ? $exception->getMessage() : 'Kein Zugriff.');
        } catch (Throwable $exception) {
            // Andere technische Fehler sollen weiterhin echte 500er bleiben.
            throw $exception;
        }
    }

    private function respondUnauthenticated(Request $request): never
    {
        if ($request->isApi()) {
            $this->jsonAndExit(401, 'Bitte zuerst anmelden.');
        }

        $target = '/login?redirect=' . rawurlencode($request->path ?: '/');
        if (!headers_sent()) {
            header('Location: ' . $target, true, 302);
        } else {
            http_response_code(302);
            echo '<!doctype html><meta http-equiv="refresh" content="0;url=' . htmlspecialchars($target, ENT_QUOTES, 'UTF-8') . '">';
        }
        exit;
    }

    private function respondForbidden(Request $request, string $message): never
    {
        if ($request->isApi()) {
            $this->jsonAndExit(403, $message);
        }

        http_response_code(403);
        if (!headers_sent()) {
            header('Content-Type: text/html; charset=utf-8');
        }

        echo '<!doctype html><html lang="de"><head><meta charset="utf-8"><title>403 - Kein Zugriff</title></head><body>';
        echo '<h1>403 - Kein Zugriff</h1>';
        echo '<p>' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p>';
        echo '<p><a href="/">Zur Startseite</a></p>';
        echo '</body></html>';
        exit;
    }

    private function jsonAndExit(int $status, string $message): never
    {
        http_response_code($status);
        if (!headers_sent()) {
            header('Content-Type: application/json; charset=utf-8');
        }

        echo json_encode([
            'status' => 'error',
            'message' => $message,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
}