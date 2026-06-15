<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Http\Request\Request;
use App\Http\Response\Response;

final class StyleGuideController extends PageController
{
    private const WCL_BASE_PATH = '/development/wcl';

    public function index(Request $request): Response
    {
        return $this->renderPage($request, 'pages/development/wcl/index', [
            'title' => 'WCL · Übersicht',
            'areaName' => 'StyleGuide',
            'pageTitle' => 'WCL · Übersicht',
            'areaRootLink' => self::WCL_BASE_PATH,
            'headerAreaKey' => 'development',
            'areaNav' => $this->wclNav('index'),
        ]);
    }

    public function buttons(Request $request): Response
    {
        return $this->renderPage($request, 'pages/development/wcl/buttons', [
            'title' => 'WCL · Buttons',
            'areaName' => 'StyleGuide',
            'pageTitle' => 'WCL · Buttons',
            'areaRootLink' => self::WCL_BASE_PATH,
            'headerAreaKey' => 'development',
            'areaNav' => $this->wclNav('buttons'),
        ]);
    }

    public function buttonGenerator(Request $request): Response
    {
        return $this->renderPage($request, 'pages/development/wcl/buttons-generator', [
            'title' => 'WCL · Buttons Generator',
            'areaName' => 'StyleGuide',
            'pageTitle' => 'WCL · Buttons Generator',
            'areaRootLink' => self::WCL_BASE_PATH,
            'headerAreaKey' => 'development',
            'areaNav' => $this->wclNav('buttons'),
        ]);
    }

    public function icons(Request $request): Response
    {
        return $this->renderPage($request, 'pages/development/wcl/icons', [
            'title' => 'WCL · Icons',
            'areaName' => 'StyleGuide',
            'pageTitle' => 'WCL · Icons',
            'areaRootLink' => self::WCL_BASE_PATH,
            'headerAreaKey' => 'development',
            'areaNav' => $this->wclNav('icons'),
        ]);
    }

    public function iconsGenerator(Request $request): Response
    {
        return $this->renderPage($request, 'pages/development/wcl/icons-generator', [
            'title' => 'WCL · Icons Generator',
            'areaName' => 'StyleGuide',
            'pageTitle' => 'WCL · Icons Generator',
            'areaRootLink' => self::WCL_BASE_PATH,
            'headerAreaKey' => 'development',
            'areaNav' => $this->wclNav('icons'),
        ]);
    }

    public function containers(Request $request): Response
    {
        return $this->renderPage($request, 'pages/development/wcl/containers', [
            'title' => 'WCL · Containers',
            'areaName' => 'StyleGuide',
            'pageTitle' => 'WCL · Containers',
            'areaRootLink' => self::WCL_BASE_PATH,
            'headerAreaKey' => 'development',
            'areaNav' => $this->wclNav('containers'),
        ]);
    }

    public function forms(Request $request): Response
    {
        return $this->renderPage($request, 'pages/development/wcl/forms', [
            'title' => 'WCL · Forms',
            'areaName' => 'StyleGuide',
            'pageTitle' => 'WCL · Forms',
            'areaRootLink' => self::WCL_BASE_PATH,
            'headerAreaKey' => 'development',
            'areaNav' => $this->wclNav('forms'),
        ]);
    }

    public function grids(Request $request): Response
    {
        return $this->renderPage($request, 'pages/development/wcl/grids', [
            'title' => 'WCL · Grids',
            'areaName' => 'StyleGuide',
            'pageTitle' => 'WCL · Grids',
            'areaRootLink' => self::WCL_BASE_PATH,
            'headerAreaKey' => 'development',
            'areaNav' => $this->wclNav('grids'),
        ]);
    }

    public function gridsGenerator(Request $request): Response
    {
        return $this->renderPage($request, 'pages/development/wcl/grids-generator', [
            'title' => 'WCL · Grids Generator',
            'areaName' => 'StyleGuide',
            'pageTitle' => 'WCL · Grids Generator',
            'areaRootLink' => self::WCL_BASE_PATH,
            'headerAreaKey' => 'development',
            'areaNav' => $this->wclNav('grids'),
        ]);
    }

    public function heros(Request $request): Response
    {
        return $this->renderPage($request, 'pages/development/wcl/heros', [
            'title' => 'WCL · Heros',
            'areaName' => 'StyleGuide',
            'pageTitle' => 'WCL · Heros',
            'areaRootLink' => self::WCL_BASE_PATH,
            'headerAreaKey' => 'development',
            'areaNav' => $this->wclNav('heros'),
        ]);
    }

    public function herosGenerator(Request $request): Response
    {
        return $this->renderPage($request, 'pages/development/wcl/heros-generator', [
            'title' => 'WCL · Heros Generator',
            'areaName' => 'StyleGuide',
            'pageTitle' => 'WCL · Heros Generator',
            'areaRootLink' => self::WCL_BASE_PATH,
            'headerAreaKey' => 'development',
            'areaNav' => $this->wclNav('heros'),
        ]);
    }

    public function links(Request $request): Response
    {
        return $this->renderPage($request, 'pages/development/wcl/links', [
            'title' => 'WCL · Links',
            'areaName' => 'StyleGuide',
            'pageTitle' => 'WCL · Links',
            'areaRootLink' => self::WCL_BASE_PATH,
            'headerAreaKey' => 'development',
            'areaNav' => $this->wclNav('links'),
        ]);
    }

    public function media(Request $request): Response
    {
        return $this->renderPage($request, 'pages/development/wcl/media', [
            'title' => 'WCL · Media',
            'areaName' => 'StyleGuide',
            'pageTitle' => 'WCL · Media',
            'areaRootLink' => self::WCL_BASE_PATH,
            'headerAreaKey' => 'development',
            'areaNav' => $this->wclNav('media'),
        ]);
    }

    public function popovers(Request $request): Response
    {
        return $this->renderPage($request, 'pages/development/wcl/popovers', [
            'title' => 'WCL · Popovers',
            'areaName' => 'StyleGuide',
            'pageTitle' => 'WCL · Popovers',
            'areaRootLink' => self::WCL_BASE_PATH,
            'headerAreaKey' => 'development',
            'areaNav' => $this->wclNav('popovers'),
        ]);
    }

    public function popoversGenerator(Request $request): Response
    {
        return $this->renderPage($request, 'pages/development/wcl/popovers-generator', [
            'title' => 'WCL · Popovers Generator',
            'areaName' => 'StyleGuide',
            'pageTitle' => 'WCL · Popovers Generator',
            'areaRootLink' => self::WCL_BASE_PATH,
            'headerAreaKey' => 'development',
            'areaNav' => $this->wclNav('popovers'),
        ]);
    }

    public function summaries(Request $request): Response
    {
        return $this->renderPage($request, 'pages/development/wcl/summaries', [
            'title' => 'WCL · Summaries',
            'areaName' => 'StyleGuide',
            'pageTitle' => 'WCL · Summaries',
            'areaRootLink' => self::WCL_BASE_PATH,
            'headerAreaKey' => 'development',
            'areaNav' => $this->wclNav('summaries'),
        ]);
    }

    public function tables(Request $request): Response
    {
        return $this->renderPage($request, 'pages/development/wcl/tables', [
            'title' => 'WCL · Tables',
            'areaName' => 'StyleGuide',
            'pageTitle' => 'WCL · Tables',
            'areaRootLink' => self::WCL_BASE_PATH,
            'headerAreaKey' => 'development',
            'areaNav' => $this->wclNav('tables'),
        ]);
    }

    private function wclNav(string $activeKey): array
    {
        return [
            ['label' => 'Übersicht', 'href' => self::WCL_BASE_PATH, 'active' => $activeKey === 'index'],
            ['label' => 'Buttons', 'href' => self::WCL_BASE_PATH . '/buttons', 'active' => $activeKey === 'buttons'],
            ['label' => 'Containers', 'href' => self::WCL_BASE_PATH . '/containers', 'active' => $activeKey === 'containers'],
            ['label' => 'Forms', 'href' => self::WCL_BASE_PATH . '/forms', 'active' => $activeKey === 'forms'],
            ['label' => 'Grids', 'href' => self::WCL_BASE_PATH . '/grids', 'active' => $activeKey === 'grids'],
            ['label' => 'Heros', 'href' => self::WCL_BASE_PATH . '/heros', 'active' => $activeKey === 'heros'],
            ['label' => 'Links', 'href' => self::WCL_BASE_PATH . '/links', 'active' => $activeKey === 'links'],
            ['label' => 'Media', 'href' => self::WCL_BASE_PATH . '/media', 'active' => $activeKey === 'media'],
            ['label' => 'Popovers', 'href' => self::WCL_BASE_PATH . '/popovers', 'active' => $activeKey === 'popovers'],
            ['label' => 'Summaries', 'href' => self::WCL_BASE_PATH . '/summaries', 'active' => $activeKey === 'summaries'],
            ['label' => 'Tables', 'href' => self::WCL_BASE_PATH . '/tables', 'active' => $activeKey === 'tables'],
            ['label' => 'Icons', 'href' => self::WCL_BASE_PATH . '/icons', 'active' => $activeKey === 'icons'],
        ];
    }
}