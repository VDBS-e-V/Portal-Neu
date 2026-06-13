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
        return $this->page($request, 'pages/news/index', [
            'title' => 'News',
            'areaName' => 'VDBS Portal',
            'pageTitle' => 'News',
            'areaRootLink' => '/',
            'headerAreaKey' => 'portal',
        ]);
    }

    public function show(Request $request): Response
    {
        $id = $request->routeInt('id');

        if ($id <= 0) {
            return $this->redirect('/news');
        }

        return $this->page($request, 'pages/news/show', [
            'title' => 'News',
            'areaName' => 'VDBS Portal',
            'pageTitle' => 'News',
            'areaRootLink' => '/',
            'headerAreaKey' => 'portal',
            'id' => $id,
        ]);
    }
}