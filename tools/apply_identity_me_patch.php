<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$servicesPath = $root . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'services.php';
$routesPath = $root . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'routes.php';

patchServices($servicesPath);
patchRoutes($routesPath);

echo "Mini-Projekt 4 wurde in config/services.php und config/routes.php eingetragen.\n";
echo "Bitte prüfen:\n";
echo "  php -l config\\services.php\n";
echo "  php -l config\\routes.php\n";
echo "Danach testen:\n";
echo "  /identity/me\n";
echo "  /identity/me?system=portal\n";

function patchServices(string $path): void
{
    if (!is_file($path)) {
        throw new RuntimeException('config/services.php nicht gefunden: ' . $path);
    }

    $code = file_get_contents($path);
    if ($code === false) {
        throw new RuntimeException('config/services.php konnte nicht gelesen werden.');
    }

    $original = $code;

    if (!str_contains($code, 'use App\\Repository\\IdentityMeRepository;')) {
        $anchor = 'use App\\Repository\\IdentityAdministrationRepository;' . "\n";
        if (str_contains($code, $anchor)) {
            $code = str_replace($anchor, $anchor . 'use App\\Repository\\IdentityMeRepository;' . "\n", $code);
        } else {
            $code = insertBeforeFirst($code, "\nuse App\\Repository\\LoginEventRepository;", "\nuse App\\Repository\\IdentityMeRepository;");
        }
    }

    if (!str_contains($code, 'IdentityMeRepository::class =>')) {
        $block = <<<'PHP_BLOCK'

    IdentityMeRepository::class => static function (Container $container): IdentityMeRepository {
        return new IdentityMeRepository($container->get(PDO::class));
    },
PHP_BLOCK;

        $anchor = "    /*\n    |--------------------------------------------------------------------------\n    | Verwaltung-Repositories";
        if (str_contains($code, $anchor)) {
            $code = str_replace($anchor, $block . "\n\n" . $anchor, $code);
        } else {
            $code = insertBeforeLast($code, "\n];", $block . "\n");
        }
    }

    if ($code !== $original) {
        backup($path, 'identity-me');
        file_put_contents($path, $code);
    }
}

function patchRoutes(string $path): void
{
    if (!is_file($path)) {
        throw new RuntimeException('config/routes.php nicht gefunden: ' . $path);
    }

    $code = file_get_contents($path);
    if ($code === false) {
        throw new RuntimeException('config/routes.php konnte nicht gelesen werden.');
    }

    $original = $code;

    if (!str_contains($code, 'use App\\Http\\Controller\\Identity\\IdentityMeController;')) {
        $marker = "use App\\Http\\Controller\\";
        $lastUsePos = strrpos($code, "use ");
        if ($lastUsePos !== false && str_contains(substr($code, $lastUsePos, 120), ';')) {
            $semicolon = strpos($code, ';', $lastUsePos);
            if ($semicolon !== false) {
                $code = substr($code, 0, $semicolon + 1)
                    . "\nuse App\\Http\\Controller\\Identity\\IdentityMeController;"
                    . substr($code, $semicolon + 1);
            }
        } else {
            $code = str_replace("declare(strict_types=1);\n", "declare(strict_types=1);\n\nuse App\\Http\\Controller\\Identity\\IdentityMeController;\n", $code);
        }
    }

    if (!str_contains($code, "'/identity/me'")) {
        $route = "    new Route('GET', '/identity/me', IdentityMeController::class, 'index'),\n";
        $code = preg_replace('/return\s*\[\s*/', "return [\n" . $route, $code, 1) ?? $code;
    }

    if ($code !== $original) {
        backup($path, 'identity-me');
        file_put_contents($path, $code);
    }
}

function insertBeforeFirst(string $code, string $needle, string $insert): string
{
    $pos = strpos($code, $needle);
    if ($pos === false) {
        return $code . $insert;
    }

    return substr($code, 0, $pos) . $insert . substr($code, $pos);
}

function insertBeforeLast(string $code, string $needle, string $insert): string
{
    $pos = strrpos($code, $needle);
    if ($pos === false) {
        return $code . $insert;
    }

    return substr($code, 0, $pos) . $insert . substr($code, $pos);
}

function backup(string $path, string $suffix): void
{
    $backup = $path . '.bak-' . $suffix . '-' . date('Ymd-His');
    copy($path, $backup);
    echo "Backup: {$backup}\n";
}
