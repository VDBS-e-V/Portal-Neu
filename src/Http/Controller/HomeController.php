<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Http\Request\Request;
use App\Http\Response\Response;
use App\Presentation\Templating\Renderer;
use App\Repository\AreaRepository;

final class HomeController extends Controller
{
    public function __construct(
        Renderer $renderer,
        AreaRepository $areas
    ) {
        parent::__construct($renderer, $areas);
    }

    public function index(Request $request): Response
    {
        return $this->page($request, 'pages/home/index', [
            'title' => 'Startseite',
            'areaName' => 'VDBS Portal',
            'pageTitle' => 'Startseite',
        ]);
    }

    public function health(Request $request): Response
    {
        return $this->text('ok');
    }

    public function apiHealth(Request $request): Response
    {
        return $this->json(['status' => 'ok']);
    }
}