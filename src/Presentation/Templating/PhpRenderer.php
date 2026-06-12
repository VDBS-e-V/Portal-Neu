<?php

declare(strict_types=1);

namespace App\Presentation\Templating;

use App\Bootstrap\Container;
use App\Presentation\Navigation\HeaderDataProvider;
use RuntimeException;

final class PhpRenderer implements Renderer
{
    private ?Container $container;

    public function __construct(private string $basePath, ?Container $container = null)
    {
        $this->container = $container;
    }

    public function render(string $view, array $parameters = []): string
    {
        $file = rtrim($this->basePath, '/\\') . '/' . str_replace('.', '/', $view) . '.php';

        if (!is_file($file)) {
            throw new RuntimeException('View not found: ' . $file);
        }

        return $this->includeFile($file, $parameters);
    }

    public function renderPage(string $view, array $parameters = [], string $layout = 'layouts/base'): string
    {
        $global = [];

        if ($this->container !== null) {
            try {
                if ($this->container->has(HeaderDataProvider::class)) {
                    $global = array_merge($global, $this->container->get(HeaderDataProvider::class)->build($parameters));
                }
            } catch (\Throwable $e) {
                // ignore header data on error
            }
        }

        $global['headerMenus'] = $global['headerMenus'] ?? [];

        $parameters = array_merge($global, $parameters);

        $content = $this->render($view, $parameters);

        return $this->render($layout, array_merge($parameters, [
            'content' => $content,
        ]));
    }

    /**
     * @param array<string, mixed> $parameters
     */
    private function includeFile(string $file, array $parameters): string
    {
        ob_start();

        (static function (string $__file, array $__parameters): void {
            extract($__parameters, EXTR_SKIP);
            require $__file;
        })($file, $parameters);

        return (string) ob_get_clean();
    }
}
