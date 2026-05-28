<?php

declare(strict_types=1);

use App\Http\Controller\StyleGuideController;
use App\Http\Controller\HomeController;
use App\Http\Routing\Route;

return [
	new Route('GET', '/', HomeController::class, 'index'),
	new Route('GET', '/health', HomeController::class, 'health'),
	new Route('GET', '/api/health', HomeController::class, 'apiHealth'),

	new Route('GET', '/styleguide', StyleGuideController::class, 'index'),
	new Route('GET', '/styleguide/buttons', StyleGuideController::class, 'buttons'),
    new Route('GET', '/styleguide/buttons/generator', StyleGuideController::class, 'buttonGenerator'),
	new Route('GET', '/styleguide/cards', StyleGuideController::class, 'cards'),
	new Route('GET', '/styleguide/containers', StyleGuideController::class, 'containers'),
	new Route('GET', '/styleguide/error-pages', StyleGuideController::class, 'errorPages'),
	new Route('GET', '/styleguide/forms', StyleGuideController::class, 'forms'),
	new Route('GET', '/styleguide/grids', StyleGuideController::class, 'grids'),
	new Route('GET', '/styleguide/hero', StyleGuideController::class, 'hero'),
	new Route('GET', '/styleguide/icons', StyleGuideController::class, 'icons'),
	new Route('GET', '/styleguide/icons/generator', StyleGuideController::class, 'iconGenerator'),
	new Route('GET', '/styleguide/links', StyleGuideController::class, 'links'),
	new Route('GET', '/styleguide/lists', StyleGuideController::class, 'lists'),
	new Route('GET', '/styleguide/pop-ups', StyleGuideController::class, 'popUps'),
	new Route('GET', '/styleguide/search', StyleGuideController::class, 'search'),
	new Route('GET', '/styleguide/sections', StyleGuideController::class, 'sections'),
	new Route('GET', '/styleguide/stats-grid', StyleGuideController::class, 'statsGrid'),
	new Route('GET', '/styleguide/status-messages', StyleGuideController::class, 'statusMessages'),
	new Route('GET', '/styleguide/submenu', StyleGuideController::class, 'submenu'),
	new Route('GET', '/styleguide/tables', StyleGuideController::class, 'tables'),
	new Route('GET', '/styleguide/blog-post', StyleGuideController::class, 'blogPost'),
];

