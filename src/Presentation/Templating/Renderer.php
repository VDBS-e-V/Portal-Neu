<?php

declare(strict_types=1);

namespace App\Presentation\Templating;

final class Renderer
{
	public function __construct(private string $basePath) {}

	public function render(string $view, array $params = []): string
	{
		$file = rtrim($this->basePath, '/\\') . '/' . str_replace('.', '/', $view) . '.php';
		if (!is_file($file)) {
			throw new \RuntimeException("View not found: {$file}");
		}

		extract($params, EXTR_SKIP);

		ob_start();
		require $file;
		return (string) ob_get_clean();
	}

	/**
	 * Rendert eine Page-View und wrappt sie automatisch in ein Layout.
	 * - $view: z.B. 'pages/home/index'
	 * - $layout: z.B. 'layouts/base'
	 */
	public function renderPage(string $view, array $params = [], string $layout = 'layouts/base'): string
	{
		// 1) Page rendern -> ergibt Content
		$content = $this->render($view, $params);

		// 2) Layout rendern, bekommt $content + alle bisherigen params
		return $this->render($layout, array_merge($params, [
			'content' => $content,
		]));
	}
}