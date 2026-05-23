<?php

use App\Http\Controller\HomeController;
use App\Http\Controller\Api\UserController;

return [
    // Web
    ['GET',  '/', [HomeController::class, 'index']],

    // API
    ['GET',  '/api/users', [UserController::class, 'list']],
];