<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Http\Request\Request;
use App\Http\Response\Response;
use App\Presentation\Templating\Renderer;
use App\Repository\AreaRepository;
use App\Repository\MenuItemRepository;
use App\Repository\MenuRepository;

final class NewsController extends PageController
{
    public function __construct(
        Renderer $renderer,
        AreaRepository $areas,
        MenuRepository $menus,
        MenuItemRepository $menuItems
    ) {
        parent::__construct($renderer, $areas, $menus, $menuItems);
    }

    public function index(Request $request): Response
    {
        return $this->renderPage($request, 'pages/news/index', [
            'title' => 'News',
            'areaName' => 'Newsroom',
            'pageTitle' => 'News',
            'areaRootLink' => '/news',
            'headerAreaKey' => 'portal',
        ]);
    }

    public function show(Request $request): Response
    {
        $id = $this->routeInt($request, 'id');

        if ($id <= 0) {
            return $this->redirect('/news');
        }

        return $this->renderPage($request, 'pages/news/show', [
            'title' => 'News',
            'areaName' => 'Newsroom',
            'pageTitle' => 'News',
            'areaRootLink' => '/news',
            'headerAreaKey' => 'portal',
            'id' => $id,
        ]);
    }
}