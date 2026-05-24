<?php

use App\Http\Controller\HomeController;
use App\Http\Controller\StyleGuideController;
use App\Http\Controller\DevelopmentController;
use App\Http\Controller\Api\UserController;

return [
    // 
    // Startbereich
    //
    ['GET',  '/', [HomeController::class, 'index']],


    //
    // StyleGuide
    //
    ['GET',  '/styleguide', [StyleGuideController::class, 'index']],
    ['GET',  '/styleguide/cards', [StyleGuideController::class, 'cards']],


    // 
    // Development
    //
    ['GET',  '/development', [DevelopmentController::class, 'index']],

    // Areas
    ['GET',  '/development/areas', [DevelopmentController::class, 'areas']],
    ['GET',  '/development/areas/{id}', [DevelopmentController::class, 'area']],
    ['GET',  '/development/areas/{id}/edit', [DevelopmentController::class, 'edit']],
    ['DELETE',  '/development/areas/{id}/delete', [DevelopmentController::class, 'delete']],
    ['UPDATE', '/development/areas/{id}', [DevelopmentController::class, 'update']],
    ['POST', '/development/areas', [DevelopmentController::class, 'create']],
    ['GET',  '/development/areas/create', [DevelopmentController::class, 'createForm']],


    // 
    // API
    //
    ['GET',  '/api/users', [UserController::class, 'list']],
];