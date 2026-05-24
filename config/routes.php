<?php

use App\Http\Controller\HomeController;
use App\Http\Controller\StyleGuideController;
use App\Http\Controller\Api\UserController;

return [
    // Web
    ['GET',  '/', [HomeController::class, 'index']],

    // StyleGuide
    ['GET',  '/styleguide', [StyleGuideController::class, 'index']],
    ['GET',  '/styleguide/cards', [StyleGuideController::class, 'cards']],

    // API
    ['GET',  '/api/users', [UserController::class, 'list']],
];