<?php

declare(strict_types=1);

namespace App\Bootstrap;

final class Container
{
    /**
     * Minimaler DI-Container:
     * - $definitions: class-string => factory(array $container): object
     * - zusätzlich einfache String-Keys für Paths/Config
     */
    public static function build(): array
    {
        $root = dirname(__DIR__, 2);

        $container = [
            'paths.root' => $root,
            'paths.views' => $root . '/resources/views',
            'config.app' => require $root . '/config/app.php',
            'config.db' => require $root . '/config/database.php',
            'definitions' => require $root . '/config/services.php',
            'instances' => [],
        ];

        return $container;
    }

    public static function get(array &$c, string $id): mixed
    {
        // string keys
        if (array_key_exists($id, $c) && $id !== 'definitions' && $id !== 'instances') {
            return $c[$id];
        }

        // cached instance?
        if (isset($c['instances'][$id])) {
            return $c['instances'][$id];
        }

        // factory?
        $defs = $c['definitions'] ?? [];
        if (isset($defs[$id]) && is_callable($defs[$id])) {
            $c['instances'][$id] = $defs[$id]($c);
            return $c['instances'][$id];
        }

        throw new \RuntimeException("Service not found: {$id}");
    }
}