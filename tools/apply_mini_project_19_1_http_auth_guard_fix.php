<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$stamp = date('Ymd-His');

function mp191_write_file(string $relative, string $content, string $root, string $stamp): void
{
    $target = $root . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relative);
    $dir = dirname($target);
    if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
        throw new RuntimeException('Verzeichnis konnte nicht erstellt werden: ' . $dir);
    }
    if (is_file($target)) {
        $backup = $target . '.bak-mini-project-19-1-' . $stamp;
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

mp191_write_file('src/Security/RoutePermissionGuard.php', <<<'PHPFILE'
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
PHPFILE, $root, $stamp);

mp191_write_file('tools/qa/check_route_permission_guard_http_auth.php', <<<'PHPFILE'
<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$file = $root . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Security' . DIRECTORY_SEPARATOR . 'RoutePermissionGuard.php';
$errors = [];

if (!is_file($file)) {
    $errors[] = 'RoutePermissionGuard.php fehlt.';
} else {
    $code = (string) file_get_contents($file);
    $required = [
        'respondUnauthenticated' => 'Guard behandelt anonyme Zugriffe explizit.',
        'respondForbidden' => 'Guard behandelt fehlende Permissions explizit.',
        'Location: ' => 'Guard leitet Web-401 auf Login um.',
        'jsonAndExit' => 'Guard beantwortet API-401/403 als JSON.',
        'isLoggedIn()' => 'Guard prüft Login vor requirePermission().',
    ];

    foreach ($required as $needle => $message) {
        if (!str_contains($code, $needle)) {
            $errors[] = $message . ' Erwarteter Marker fehlt: ' . $needle;
        }
    }

    if (str_contains($code, 'pageGroup') || str_contains($code, 'PageGroup')) {
        $errors[] = 'RoutePermissionGuard enthält noch PageGroup-Begriffe.';
    }
}

if ($errors !== []) {
    echo 'RoutePermissionGuard-HTTP-Auth-Check fehlgeschlagen:' . PHP_EOL;
    foreach ($errors as $error) {
        echo ' - ' . $error . PHP_EOL;
    }
    exit(1);
}

echo 'OK: RoutePermissionGuard behandelt anonyme HTTP-Zugriffe ohne 500er.' . PHP_EOL;
PHPFILE, $root, $stamp);

echo PHP_EOL;
echo 'Mini-Projekt 19.1 HTTP-Auth-Guard-Fix wurde angewendet.' . PHP_EOL;
echo 'Bitte ausführen:' . PHP_EOL;
echo '  php -l src\\Security\\RoutePermissionGuard.php' . PHP_EOL;
echo '  php tools\\qa\\check_route_permission_guard_http_auth.php' . PHP_EOL;
echo '  set SMOKE_BASE_URL=http://vdbs-portal.localhost' . PHP_EOL;
echo '  php tools\\qa\\check_identity_http_smoke.php' . PHP_EOL;
echo '  php tools\\qa\\run_identity_smoke_suite.php --with-seed' . PHP_EOL;
