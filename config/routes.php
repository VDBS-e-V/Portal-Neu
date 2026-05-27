<?php

declare(strict_types=1);

use App\Http\Controller\HomeController;
use App\Http\Routing\Route;

return [
	new Route('GET', '/', HomeController::class, 'index'),
	new Route('GET', '/health', HomeController::class, 'health'),
	new Route('GET', '/api/health', HomeController::class, 'apiHealth'),

	new Route('GET', '/style-guide', StyleGuideController::class, 'index'),
	new Route('GET', '/style-guide/icons', StyleGuideController::class, 'icons'),
];

