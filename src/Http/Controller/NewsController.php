<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Http\Request\Request;
use App\Http\Response\Response;
use App\Presentation\Templating\Renderer;
use App\Repository\AreaRepository;

final class NewsController extends Controller
{
    public function __construct(
        Renderer $renderer,
        AreaRepository $areas
    ) {
        parent::__construct($renderer, $areas);
    }

    public function index(Request $request): Response
    {
        return $this->page($request, 'pages/news/index', [
            'title' => 'News',
            'areaName' => 'VDBS Portal',
            'pageTitle' => 'News',
        ]);
    }

    
}