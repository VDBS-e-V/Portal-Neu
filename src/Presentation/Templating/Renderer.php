<?php

declare(strict_types=1);

namespace App\Presentation\Templating;

interface Renderer
{
    /**
     * @param array<string, mixed> $parameters
     */
    public function render(string $view, array $parameters = []): string;

    /**
     * @param array<string, mixed> $parameters
     */
    public function renderPage(string $view, array $parameters = [], string $layout = 'layouts/base'): string;
}