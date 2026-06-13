<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Http\Request\Request;
use App\Http\Response\Response;
use App\Presentation\Templating\Renderer;
use App\Repository\AreaRepository;
use App\Repository\MenuItemRepository;
use App\Repository\MenuRepository;

final class NewsController extends Controller
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
}