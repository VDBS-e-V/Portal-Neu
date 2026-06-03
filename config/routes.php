<?php

declare(strict_types=1);

use App\Http\Controller\StyleGuideController;
use App\Http\Controller\HomeController;
use App\Http\Controller\DevelopmentController;
use App\Http\Routing\Route;

return [
	new Route('GET', '/', HomeController::class, 'index'),
	new Route('GET', '/health', HomeController::class, 'health'),
	new Route('GET', '/api/health', HomeController::class, 'apiHealth'),

	// Areas management
	new Route('GET', '/areas', DevelopmentController::class, 'areasIndex'),
	new Route('GET', '/areas/create', DevelopmentController::class, 'areasCreateForm'),
	new Route('POST', '/areas/create', DevelopmentController::class, 'areasCreate'),
	new Route('GET', '/areas/edit', DevelopmentController::class, 'areasEditForm'),
	new Route('POST', '/areas/edit', DevelopmentController::class, 'areasEdit'),
	new Route('POST', '/areas/delete', DevelopmentController::class, 'areasDelete'),

	// Development web control: Areas
	new Route('GET', '/development/web-control/areas', DevelopmentController::class, 'areasIndex'),
	new Route('GET', '/development/web-control/areas/create', DevelopmentController::class, 'areasCreateForm'),
	new Route('POST', '/development/web-control/areas/create', DevelopmentController::class, 'areasCreate'),
	new Route('GET', '/development/web-control/areas/edit', DevelopmentController::class, 'areasEditForm'),
	new Route('POST', '/development/web-control/areas/edit', DevelopmentController::class, 'areasEdit'),
	new Route('POST', '/development/web-control/areas/delete', DevelopmentController::class, 'areasDelete'),

	// Development web control: Menus
	new Route('GET', '/development/web-control/menus', DevelopmentController::class, 'menusIndex'),
	new Route('GET', '/development/web-control/menus/create', DevelopmentController::class, 'menusCreateForm'),
	new Route('POST', '/development/web-control/menus/create', DevelopmentController::class, 'menusCreate'),
	new Route('GET', '/development/web-control/menus/edit', DevelopmentController::class, 'menusEditForm'),
	new Route('POST', '/development/web-control/menus/edit', DevelopmentController::class, 'menusEdit'),
	new Route('POST', '/development/web-control/menus/delete', DevelopmentController::class, 'menusDelete'),

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

