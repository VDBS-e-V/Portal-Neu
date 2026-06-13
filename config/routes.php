<?php

declare(strict_types=1);

use App\Http\Controller\DevelopmentController;
use App\Http\Controller\AuthController;
use App\Http\Controller\HomeController;
use App\Http\Controller\NewsController;
use App\Http\Controller\StyleGuideController;
use App\Http\Controller\HealthController;
use App\Http\Controller\UserAccountController;
use App\Http\Routing\Route;

return [
    new Route('GET', '/', HomeController::class, 'index'),
    
    // Auth / user
    new Route('GET', '/login', AuthController::class, 'loginForm'),
    new Route('POST', '/login', AuthController::class, 'login'),
    new Route('POST', '/logout', AuthController::class, 'logout'),

    // User account
    new Route('GET', '/user', UserAccountController::class, 'profile'),
    new Route('POST', '/user/profile', UserAccountController::class, 'updateProfile'),
    new Route('GET', '/user/settings', UserAccountController::class, 'settings'),
    new Route('POST', '/user/settings', UserAccountController::class, 'updateSettings'),
    new Route('POST', '/user/password', UserAccountController::class, 'updatePassword'),

    new Route('GET', '/api/user/me', AuthController::class, 'apiMe'),

    new Route('GET', '/health', HealthController::class, 'health'),
    new Route('GET', '/api/health', HealthController::class, 'apiHealth'),

    // News
    new Route('GET', '/news', NewsController::class, 'index'),
    new Route('GET', '/news/{id}', NewsController::class, 'show'),

    // Development web control: Dashboard
    new Route('GET', '/development/web-control', DevelopmentController::class, 'webControlDashboard'),

    // Development web control: Areas
    new Route('GET', '/development/web-control/areas', DevelopmentController::class, 'areasIndex'),

    new Route('GET', '/development/web-control/areas/create', DevelopmentController::class, 'areasCreateForm'),
    new Route('POST', '/development/web-control/areas/create', DevelopmentController::class, 'areasCreate'),

    // Neue dynamische Area-Routen
    new Route('GET', '/development/web-control/areas/{id}/edit', DevelopmentController::class, 'areasEditForm'),
    new Route('POST', '/development/web-control/areas/{id}/edit', DevelopmentController::class, 'areasEdit'),
    new Route('POST', '/development/web-control/areas/{id}/delete', DevelopmentController::class, 'areasDelete'),

    // Alte Area-Routen als Übergang, damit vorhandene Templates/Formulare weiter funktionieren
    new Route('GET', '/development/web-control/areas/edit', DevelopmentController::class, 'areasEditForm'),
    new Route('POST', '/development/web-control/areas/edit', DevelopmentController::class, 'areasEdit'),
    new Route('POST', '/development/web-control/areas/delete', DevelopmentController::class, 'areasDelete'),

    // Development web control: Menus
    new Route('GET', '/development/web-control/menus', DevelopmentController::class, 'menusIndex'),

    new Route('GET', '/development/web-control/menus/create', DevelopmentController::class, 'menusCreateForm'),
    new Route('POST', '/development/web-control/menus/create', DevelopmentController::class, 'menusCreate'),

    // Neue dynamische Menü-Routen
    new Route('GET', '/development/web-control/menus/{id}/edit', DevelopmentController::class, 'menusEditForm'),
    new Route('POST', '/development/web-control/menus/{id}/edit', DevelopmentController::class, 'menusEdit'),
    new Route('POST', '/development/web-control/menus/{id}/delete', DevelopmentController::class, 'menusDelete'),

    // Alte Menü-Routen als Übergang, damit vorhandene Templates/Formulare weiter funktionieren
    new Route('GET', '/development/web-control/menus/edit', DevelopmentController::class, 'menusEditForm'),
    new Route('POST', '/development/web-control/menus/edit', DevelopmentController::class, 'menusEdit'),
    new Route('POST', '/development/web-control/menus/delete', DevelopmentController::class, 'menusDelete'),

    // Development web control: Menu Items, neue dynamische Routen
    new Route('POST', '/development/web-control/menus/{menuId}/items/create', DevelopmentController::class, 'menuItemsCreate'),
    new Route('POST', '/development/web-control/menus/{menuId}/items/{itemId}/edit', DevelopmentController::class, 'menuItemsEdit'),
    new Route('POST', '/development/web-control/menus/{menuId}/items/{itemId}/delete', DevelopmentController::class, 'menuItemsDelete'),

    // Alte Menu-Item-Routen als Übergang
    new Route('POST', '/development/web-control/menu-items/create', DevelopmentController::class, 'menuItemsCreate'),
    new Route('POST', '/development/web-control/menu-items/edit', DevelopmentController::class, 'menuItemsEdit'),
    new Route('POST', '/development/web-control/menu-items/delete', DevelopmentController::class, 'menuItemsDelete'),

    // Style Guide
    new Route('GET', '/styleguide', StyleGuideController::class, 'index'),

    new Route('GET', '/styleguide/buttons', StyleGuideController::class, 'buttons'),
    new Route('GET', '/styleguide/buttons/generator', StyleGuideController::class, 'buttonGenerator'),

    new Route('GET', '/styleguide/containers', StyleGuideController::class, 'containers'),
    new Route('GET', '/styleguide/forms', StyleGuideController::class, 'forms'),

    new Route('GET', '/styleguide/grids', StyleGuideController::class, 'grids'),
    new Route('GET', '/styleguide/grids/generator', StyleGuideController::class, 'gridsGenerator'),

    new Route('GET', '/styleguide/heros', StyleGuideController::class, 'heros'),
    new Route('GET', '/styleguide/heros/generator', StyleGuideController::class, 'herosGenerator'),

    new Route('GET', '/styleguide/links', StyleGuideController::class, 'links'),
    new Route('GET', '/styleguide/media', StyleGuideController::class, 'media'),

    new Route('GET', '/styleguide/popovers', StyleGuideController::class, 'popovers'),
    new Route('GET', '/styleguide/popovers/generator', StyleGuideController::class, 'popoversGenerator'),

    new Route('GET', '/styleguide/summaries', StyleGuideController::class, 'summaries'),
    new Route('GET', '/styleguide/tables', StyleGuideController::class, 'tables'),

    new Route('GET', '/styleguide/icons', StyleGuideController::class, 'icons'),
    new Route('GET', '/styleguide/icons/generator', StyleGuideController::class, 'iconsGenerator'),
];