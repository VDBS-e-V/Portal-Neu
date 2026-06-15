<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Http\Request\Request;
use App\Http\Response\Response;

final class HomeController extends PageController
{
    public function index(Request $request): Response
    {
        return $this->renderPage($request, 'pages/home/index', [
            'title' => 'Startseite',
            'areaName' => 'VDBS Portal',
            'pageTitle' => 'Startseite',
            'areaRootLink' => '/',
            'headerAreaKey' => 'portal',
        ]);
    }
}