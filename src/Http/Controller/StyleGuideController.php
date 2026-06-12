<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Http\Request\Request;
use App\Http\Response\Response;
use App\Presentation\Templating\Renderer;
use App\Repository\AreaRepository;

final class StyleGuideController extends Controller
{
    public function __construct(
        Renderer $renderer,
        AreaRepository $areas
    ) {
        parent::__construct($renderer, $areas);
    }

    public function index(Request $request): Response
    {
        return $this->stylePage($request, 'pages/style-guide/index', 'Übersicht');
    }

    public function buttons(Request $request): Response
    {
        return $this->stylePage($request, 'pages/style-guide/buttons', 'Buttons');
    }

    public function buttonGenerator(Request $request): Response
    {
        return $this->stylePage($request, 'pages/style-guide/buttons-generator', 'Buttons Generator');
    }

    public function icons(Request $request): Response
    {
        return $this->stylePage($request, 'pages/style-guide/icons', 'Icons');
    }

    public function iconsGenerator(Request $request): Response
    {
        return $this->stylePage($request, 'pages/style-guide/icons-generator', 'Icons Generator');
    }

    public function containers(Request $request): Response
    {
        return $this->stylePage($request, 'pages/style-guide/containers', 'Containers');
    }

    public function forms(Request $request): Response
    {
        return $this->stylePage($request, 'pages/style-guide/forms', 'Forms');
    }

    public function grids(Request $request): Response
    {
        return $this->stylePage($request, 'pages/style-guide/grids', 'Grids');
    }

    public function gridsGenerator(Request $request): Response
    {
        return $this->stylePage($request, 'pages/style-guide/grids-generator', 'Grids Generator');
    }

    public function heros(Request $request): Response
    {
        return $this->stylePage($request, 'pages/style-guide/heros', 'Heros');
    }

    public function herosGenerator(Request $request): Response
    {
        return $this->stylePage($request, 'pages/style-guide/heros-generator', 'Heros Generator');
    }

    public function links(Request $request): Response
    {
        return $this->stylePage($request, 'pages/style-guide/links', 'Links');
    }

    public function media(Request $request): Response
    {
        return $this->stylePage($request, 'pages/style-guide/media', 'Media');
    }

    public function popovers(Request $request): Response
    {
        return $this->stylePage($request, 'pages/style-guide/popovers', 'Popovers');
    }

    public function popoversGenerator(Request $request): Response
    {
        return $this->stylePage($request, 'pages/style-guide/popovers-generator', 'Popovers Generator');
    }

    public function summaries(Request $request): Response
    {
        return $this->stylePage($request, 'pages/style-guide/summaries', 'Summaries');
    }

    public function tables(Request $request): Response
    {
        return $this->stylePage($request, 'pages/style-guide/tables', 'Tables');
    }

    private function stylePage(Request $request, string $view, string $title): Response
    {
        return $this->page($request, $view, [
            'title' => $title,
            'areaName' => 'Style Guide',
            'pageTitle' => $title,
            'areaRootLink' => '/',
            'areaNav' => $this->startNav(),
        ]);
    }
}