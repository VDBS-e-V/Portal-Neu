<?php

declare(strict_types=1);

use App\Http\Controller\StyleGuideController;
use App\Http\Controller\HomeController;
use App\Http\Controller\NewsController;
use App\Http\Controller\DevelopmentController;
use App\Http\Routing\Route;

return [
	new Route('GET', '/', HomeController::class, 'index'),
	new Route('GET', '/health', HomeController::class, 'health'),
	new Route('GET', '/api/health', HomeController::class, 'apiHealth'),

    // News
    new Route('GET', '/news', NewsController::class, 'index'),
    new Route('GET', '/news/{id}', NewsController::class, 'show'),

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

	// Development web control: Menu Items
	new Route('POST', '/development/web-control/menu-items/create', DevelopmentController::class, 'menuItemsCreate'),
	new Route('POST', '/development/web-control/menu-items/edit', DevelopmentController::class, 'menuItemsEdit'),
	new Route('POST', '/development/web-control/menu-items/delete', DevelopmentController::class, 'menuItemsDelete'),

	// Style Guide
	new Route('GET', '/styleguide', StyleGuideController::class, 'index'),
	new Route('GET', '/styleguide/buttons', StyleGuideController::class, 'buttons'),
    new Route('GET', '/styleguide/buttons/generator', StyleGuideController::class, 'buttonGenerator'),
    new Route('GET','/styleguide/containers', StyleGuideController::class, 'containers'),
    new Route('GET','/styleguide/forms', StyleGuideController::class, 'forms'),
    new Route('GET','/styleguide/grids', StyleGuideController::class,'grids'),
    new Route('GET','/styleguide/heros', StyleGuideController::class, 'heros'),
    new Route('GET','/styleguide/links', StyleGuideController::class, 'links'),
    new Route('GET','/styleguide/media', StyleGuideController::class, 'media'),
    new Route('GET','/styleguide/popovers', StyleGuideController::class, 'popovers'),
    new Route('GET','/styleguide/summaries', StyleGuideController::class, 'summaries'),
    new Route('GET','/styleguide/tables', StyleGuideController::class, 'tables'),
	new Route('GET', '/styleguide/icons', StyleGuideController::class, 'icons'),
	new Route('GET', '/styleguide/icons/generator', StyleGuideController::class, 'iconGenerator'),
];

